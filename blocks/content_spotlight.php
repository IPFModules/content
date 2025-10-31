<?php
/**
 * Display Content block file
 *
 * This file holds the functions needed for the display content block
 *
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		TheRplima aka Rodrigo Pereira Lima <therplima@impresscms.org>
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH" ) or die("ICMS root path not defined");

function content_content_spotlight_show($options) {
	global $xoTheme;

	$block = array();

	include_once ICMS_ROOT_PATH . '/modules/' . basename(dirname(__FILE__, 2)) . '/include/common.php';

	$content_content_handler = icms_getModuleHandler('content', basename(dirname(__FILE__, 2)), 'content');

	if ($options[0] == 0) {
		$options[0] = $content_content_handler->getLastestCreated(false);
	}

	$contentObj = $content_content_handler->get($options[0]);
	if ($contentObj && ! $contentObj->isNew() && $contentObj->accessGranted()) {
		$block['content_content'] = $contentObj->toArray();
	}
    $block['content_content']['spotlight_text'] = $options[1];
    $block['content_content']['button_text'] = $options[2];

	return $block;
}

function content_content_spotlight_edit($options) {
	include_once ICMS_ROOT_PATH . '/modules/' . basename(dirname(__FILE__, 2)) . '/include/common.php';

	$content_content_handler = icms_getModuleHandler('content', basename(dirname(__FILE__, 2)), 'content');

	$selpages = new icms_form_elements_Select('', 'options[0]', $options[0]);
	$selpages->addOptionArray($content_content_handler->getContentList());

    $spotlight_text = new icms_form_elements_Textarea('','options[1]', $options[1]);
    $spotlight_button_text = new icms_form_elements_Text('','options[2]', $options[2],30);

    $form = '<table width="100%">';
    $form .= '<tr>';
    $form .= '<td width="30%">' . _MB_CONTENT_CONTENT_SELPAGE . '</td>';
    $form .= '<td>' . $selpages->render() . '</td>';
    $form .= '</tr>';
    $form .= '<tr>';
    $form .= '<td width="30%">' . 'Alternatieve tekst' . '</td>';
    $form .= '<td>' . $spotlight_text->render() . '</td>';
    $form .= '</tr>';
    $form .= '<tr>';
    $form .= '<td width="30%">' . 'Tekst op de knop' . '</td>';
    $form .= '<td>' . $spotlight_button_text->render() . '</td>';
    $form .= '</tr>';

    $form .= '</table>';

	return $form;
}