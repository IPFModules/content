<?php
/**
 * Admin page to manage the building block types
 *
 * List, add, edit, view and delete building block types
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		2.0
 * @author		David Janssens (fiammybe)
 * @package		content
 */

function editBlocktype(mod_content_BlocktypeHandler $handler, int $blocktypeId): void
{
    global $icmsAdminTpl;

    $blocktype = $handler->get($blocktypeId);

    if (!$blocktype->isNew()) {
        $blocktype->makeFieldReadOnly('blocktype_key');
    }

    $title = $blocktype->isNew()
        ? _AM_CONTENT_BLOCKTYPE_CREATE
        : _AM_CONTENT_BLOCKTYPE_EDIT;

    icms::$module->displayAdminMenu(1, _AM_CONTENT_BLOCKTYPES . ' > ' . $title);

    $form = $blocktype->getSecureForm($title, 'store');
    $form->assign($icmsAdminTpl, 'content_blocktype_form');

    $icmsAdminTpl->display('db:content_admin_blocktype.html');
}

function showBlocktype(mod_content_BlocktypeHandler $handler, int $blocktypeId): void
{
    global $icmsAdminTpl, $xoTheme;

    $blocktype = $handler->get($blocktypeId);

    if ($blocktype->isNew()) {
        redirect_header('blocktype.php', 3, _CO_ICMS_NOT_SELECTED);
    }

    icms::$module->displayAdminMenu(1, _AM_CONTENT_BLOCKTYPES . ' > ' . $blocktype->getVar('blocktype_title'));

    $preview = mod_content_BlockRenderer::create()->renderBlocktype($blocktype);
    $template = $blocktype->getTemplate();

    $xoTheme->addStylesheet(CONTENT_URL . 'include/builder.css');

    $icmsAdminTpl->assign('content_blocktype_singleview', $blocktype->displaySingleObject(true, false, ['edit', 'delete']));
    $icmsAdminTpl->assign('content_blocktype_preview', $preview['html']);
    $icmsAdminTpl->assign('content_blocktype_preview_css', $preview['css']);
    $icmsAdminTpl->assign('content_blocktype_zones', $template->getZones());
    $icmsAdminTpl->assign('content_blocktype_slots', $template->getSlots());
    $icmsAdminTpl->assign('content_blocktype_errors', $template->getErrors());

    $icmsAdminTpl->display('db:content_admin_blocktype.html');
}

function listBlocktypes(mod_content_BlocktypeHandler $handler): void
{
    global $icmsAdminTpl;

    icms::$module->displayAdminMenu(1, _AM_CONTENT_BLOCKTYPES);

    $criteria = new icms_db_criteria_Compo();

    $table = new icms_ipf_view_Table($handler, $criteria);
    $table->addColumn(new icms_ipf_view_Column('blocktype_title', _GLOBAL_LEFT, false, 'getTitleLink'));
    $table->addColumn(new icms_ipf_view_Column('blocktype_key', _GLOBAL_LEFT, 150));
    $table->addColumn(new icms_ipf_view_Column('blocktype_category', 'center', 150));
    $table->addColumn(new icms_ipf_view_Column('blocktype_status', 'center', 100));
    $table->addColumn(new icms_ipf_view_Column('weight', 'center', 80));

    $table->setDefaultSort('weight');
    $table->setDefaultOrder('ASC');

    $table->addCustomAction('getPreviewLink');
    $table->addIntroButton('addblocktype', 'blocktype.php?op=mod', _AM_CONTENT_BLOCKTYPE_CREATE);
    $table->addQuickSearch(['blocktype_title', 'blocktype_key', 'blocktype_description']);
    $table->addFilter('blocktype_category', 'getCategoryArray');
    $table->addFilter('blocktype_status', 'getStatusArray');

    $icmsAdminTpl->assign('content_blocktype_table', $table->fetch());
    $icmsAdminTpl->display('db:content_admin_blocktype.html');
}

include_once 'admin_header.php';

$blocktypeHandler = icms_getModuleHandler('blocktype', basename(dirname(__FILE__, 2)), 'content');

$validOps = ['mod', 'store', 'view', 'del', ''];

$cleanOp = (string) ($_POST['op'] ?? $_GET['op'] ?? '');
$cleanBlocktypeId = (int) ($_POST['blocktype_id'] ?? $_GET['blocktype_id'] ?? 0);

if (!in_array($cleanOp, $validOps, true)) {
    redirect_header(ICMS_URL, 3, _NOPERM);
}

switch ($cleanOp) {
    case 'mod':
        icms_cp_header();
        editBlocktype($blocktypeHandler, $cleanBlocktypeId);
        break;

    case 'store':
        if (!icms::$security->check()) {
            redirect_header('blocktype.php', 3, implode('<br />', icms::$security->getErrors()));
        }

        // an empty multiple select isn't posted, without this all accepted block types could never be removed
        $_POST['blocktype_accepts'] ??= [];

        $controller = new icms_ipf_Controller($blocktypeHandler);
        $controller->storeFromDefaultForm(_AM_CONTENT_BLOCKTYPE_CREATED, _AM_CONTENT_BLOCKTYPE_MODIFIED, 'blocktype.php');
        break;

    case 'view':
        icms_cp_header();
        showBlocktype($blocktypeHandler, $cleanBlocktypeId);
        break;

    case 'del':
        $controller = new icms_ipf_Controller($blocktypeHandler);
        $controller->handleObjectDeletion();
        break;

    default:
        icms_cp_header();
        listBlocktypes($blocktypeHandler);
        break;
}

icms_cp_footer();
