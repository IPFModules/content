<?php
/**
 * Spotlight Content block file
 *
 * Displays a content page preceded by a spotlight text
 *
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.3.3
 * @author		David Janssens (fiammybe)
 * @package		content
 */

defined('ICMS_ROOT_PATH') or die('ICMS root path not defined');

/**
 * @param array<int, string> $options page id (0 for the latest page) and the spotlight text
 * @return array<string, mixed>
 */
function content_content_spotlight_show(array $options): array
{
    global $xoTheme;

    $moduleDir = basename(dirname(__FILE__, 2));

    include_once ICMS_ROOT_PATH . "/modules/{$moduleDir}/include/common.php";

    $xoTheme->addStylesheet(ICMS_URL . "/modules/{$moduleDir}/module.css");
    $xoTheme->addStylesheet(ICMS_URL . "/modules/{$moduleDir}/include/content.css");

    $contentHandler = icms_getModuleHandler('content', $moduleDir, 'content');

    $contentId = (int) ($options[0] ?? 0);

    if ($contentId === 0) {
        $contentId = (int) $contentHandler->getLastestCreated(false);
    }

    $content = $contentHandler->get($contentId);

    if (!$content || $content->isNew() || !$content->accessGranted()) {
        return [];
    }

    $contentArray = $content->toArray();
    $contentArray['spotlight_text'] = icms_core_HTMLFilter::filterHTML((string) ($options[1] ?? ''));

    return [
        'content_content' => $contentArray,
        'showInfo' => false,
        'showSubs' => false,
    ];
}

/** @param array<int, string> $options */
function content_content_spotlight_edit(array $options): string
{
    $moduleDir = basename(dirname(__FILE__, 2));

    include_once ICMS_ROOT_PATH . "/modules/{$moduleDir}/include/common.php";

    $contentHandler = icms_getModuleHandler('content', $moduleDir, 'content');

    $page = new icms_form_elements_Select('', 'options[0]', $options[0] ?? 0);
    $page->addOptionArray($contentHandler->getContentList());

    $text = new icms_form_elements_Textarea('', 'options[1]', $options[1] ?? '', 5, 50);

    $pageLabel = _MB_CONTENT_CONTENT_SELPAGE;
    $textLabel = _MB_CONTENT_CONTENT_SPOTLIGHT_TEXT;

    return <<<HTML
        <table width="100%">
            <tr>
                <td width="30%">{$pageLabel}</td>
                <td>{$page->render()}</td>
            </tr>
            <tr>
                <td>{$textLabel}</td>
                <td>{$text->render()}</td>
            </tr>
        </table>
        HTML;
}
