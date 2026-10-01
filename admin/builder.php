<?php
/**
 * Page builder: edit the building blocks of a content page with GrapesJS
 *
 * op=edit   shows the page builder
 * op=update stores the building block tree posted as JSON by the page builder
 *
 * Images are chosen and uploaded in the ImpressCMS image manager, which the page builder opens in a popup
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		2.0
 * @author		David Janssens (fiammybe)
 * @package		content
 */

/** id of the form element the ImpressCMS image manager puts the chosen image in */
const CONTENT_BUILDER_IMAGE_TARGET = 'content-builder-image';

const CONTENT_BUILDER_JSON_FLAGS = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;

/** @param array<string, mixed> $data */
function respondJson(array $data, int $status = 200): never
{
    icms::$logger->disableLogger();

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');

    echo json_encode($data, CONTENT_BUILDER_JSON_FLAGS);
    exit;
}

function requireValidToken(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        respondJson(['ok' => false, 'errors' => [_AM_CONTENT_BUILDER_ERR_INVALID]], 405);
    }

    $token = (string) ($_SERVER['HTTP_X_ICMS_TOKEN'] ?? '');

    if ($token !== '' && icms::$security->check(false, $token)) {
        return;
    }

    respondJson(['ok' => false, 'errors' => [_AM_CONTENT_BUILDER_ERR_TOKEN]], 403);
}

function editBlocks(mod_content_Content $content): void
{
    global $icmsAdminTpl, $icmsConfig, $xoTheme;

    $moduleDir = basename(dirname(__FILE__, 2));
    $blocktypeHandler = icms_getModuleHandler('blocktype', $moduleDir, 'content');
    $contentId = (int) $content->getVar('content_id', 'e');

    icms::$module->displayAdminMenu(0, _AM_CONTENT_CONTENTS . ' > ' . _AM_CONTENT_BUILDER . ' > ' . $content->getVar('content_title'));

    $xoTheme->addStylesheet(CONTENT_URL . 'assets/grapesjs/css/grapes.min.css');
    $xoTheme->addStylesheet(CONTENT_URL . 'include/builder.css');
    $xoTheme->addScript(CONTENT_URL . 'assets/grapesjs/grapes.min.js');
    $xoTheme->addScript(CONTENT_URL . 'include/builder.js');

    $canvasStyles = [CONTENT_URL . 'module.css', CONTENT_URL . 'include/content.css'];
    $themeStyle = "{$icmsConfig['theme_set']}/style.css";

    if (is_file(ICMS_THEME_PATH . "/{$themeStyle}")) {
        array_unshift($canvasStyles, ICMS_THEME_URL . "/{$themeStyle}");
    }

    $config = [
        'contentId' => $contentId,
        'blocktypes' => $blocktypeHandler->getActiveDefinitions(),
        'tree' => mod_content_BlockSynchronizer::create()->export($contentId),
        'imageManager' => [
            'url' => ICMS_MODULES_URL . '/system/admin/images/browser.php?target=' . CONTENT_BUILDER_IMAGE_TARGET . '&type=ibrow',
            'target' => CONTENT_BUILDER_IMAGE_TARGET,
            'siteUrl' => ICMS_URL,
            'categories' => array_keys(icms::handler('icms_image_category')->getObjects(null, true)),
        ],
        'canvasStyles' => $canvasStyles,
        'canvasCss' => $blocktypeHandler->getAllCss() . "\n" . $content->getVar('content_css', 'n'),
        'token' => icms::$security->createToken(),
        'urls' => [
            'update' => CONTENT_ADMIN_URL . "builder.php?op=update&content_id={$contentId}",
        ],
        'labels' => [
            'blocks' => _AM_CONTENT_BUILDER_BLOCKS,
            'layers' => _AM_CONTENT_BUILDER_LAYERS,
            'settings' => _AM_CONTENT_BUILDER_SETTINGS,
            'saved' => _AM_CONTENT_BUILDER_SAVED,
            'unsaved' => _AM_CONTENT_BUILDER_UNSAVED,
            'error' => _AM_CONTENT_BUILDER_ERROR,
            'chooseImage' => _AM_CONTENT_BUILDER_CHOOSE_IMAGE,
            'popupError' => _AM_CONTENT_BUILDER_ERR_POPUP,
            'alt' => _AM_CONTENT_BUILDER_ALT,
            'href' => _AM_CONTENT_BUILDER_HREF,
            'target' => _AM_CONTENT_BUILDER_TARGET,
            'targetSelf' => _AM_CONTENT_BUILDER_TARGET_SELF,
            'targetBlank' => _AM_CONTENT_BUILDER_TARGET_BLANK,
        ],
    ];

    $icmsAdminTpl->assign('content_builder_config', json_encode($config, CONTENT_BUILDER_JSON_FLAGS));
    $icmsAdminTpl->assign('content_builder_title', $content->getVar('content_title'));
    $icmsAdminTpl->assign('content_builder_view_url', $content->getItemLink(true));
    $icmsAdminTpl->assign('content_builder_back_url', CONTENT_ADMIN_URL . 'content.php?content_pid=' . (int) $content->getVar('content_pid', 'e'));

    $icmsAdminTpl->display('db:content_admin_builder.html');
}

function updateBlocks(mod_content_Content $content): never
{
    requireValidToken();

    $tree = json_decode((string) file_get_contents('php://input'), true);

    if (!is_array($tree) || !array_is_list($tree)) {
        respondJson(['ok' => false, 'errors' => [_AM_CONTENT_BUILDER_ERR_INVALID]], 400);
    }

    $synchronizer = mod_content_BlockSynchronizer::create();
    $errors = $synchronizer->sync($content, $tree);

    if ($errors !== []) {
        respondJson(['ok' => false, 'errors' => $errors], 422);
    }

    respondJson([
        'ok' => true,
        'tree' => $synchronizer->export((int) $content->getVar('content_id', 'e')),
        'token' => icms::$security->createToken(),
    ]);
}

include_once 'admin_header.php';

$contentHandler = icms_getModuleHandler('content', basename(dirname(__FILE__, 2)), 'content');

$validOps = ['edit', 'update'];

$cleanOp = (string) ($_GET['op'] ?? 'edit');
$cleanContentId = (int) ($_GET['content_id'] ?? 0);

if (!in_array($cleanOp, $validOps, true)) {
    redirect_header(ICMS_URL, 3, _NOPERM);
}

$content = $contentHandler->get($cleanContentId);

if ($content->isNew()) {
    redirect_header('content.php', 3, _CO_ICMS_NOT_SELECTED);
}

if (!$content->hasBlockLayout()) {
    redirect_header("content.php?op=mod&content_id={$cleanContentId}", 5, _AM_CONTENT_BUILDER_NOT_BLOCKS);
}

if ($cleanOp === 'update') {
    updateBlocks($content);
}

icms_cp_header();
editBlocks($content);
icms_cp_footer();
