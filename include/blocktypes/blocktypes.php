<?php
/**
 * Default building block types, seeded on install and update of the module
 *
 * The markup of each type is in <key>.html, its optional styles in <key>.css
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		2.0
 * @author		David Janssens (fiammybe)
 * @package		content
 */

defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

$contentBlocks = ['heading', 'text', 'image', 'image-text', 'button', 'card'];

return [
    'section' => [
        'title' => _CO_CONTENT_BLOCKTYPE_SEED_SECTION,
        'category' => _CO_CONTENT_BLOCKTYPE_CATEGORY_LAYOUT,
        'accepts' => ['columns-2', 'columns-3', ...$contentBlocks],
        'weight' => 10,
    ],
    'columns-2' => [
        'title' => _CO_CONTENT_BLOCKTYPE_SEED_COLUMNS2,
        'category' => _CO_CONTENT_BLOCKTYPE_CATEGORY_LAYOUT,
        'accepts' => $contentBlocks,
        'weight' => 20,
    ],
    'columns-3' => [
        'title' => _CO_CONTENT_BLOCKTYPE_SEED_COLUMNS3,
        'category' => _CO_CONTENT_BLOCKTYPE_CATEGORY_LAYOUT,
        'accepts' => $contentBlocks,
        'weight' => 30,
    ],
    'heading' => [
        'title' => _CO_CONTENT_BLOCKTYPE_SEED_HEADING,
        'category' => _CO_CONTENT_BLOCKTYPE_CATEGORY_DEFAULT,
        'weight' => 40,
    ],
    'text' => [
        'title' => _CO_CONTENT_BLOCKTYPE_SEED_TEXT,
        'category' => _CO_CONTENT_BLOCKTYPE_CATEGORY_DEFAULT,
        'weight' => 50,
    ],
    'image' => [
        'title' => _CO_CONTENT_BLOCKTYPE_SEED_IMAGE,
        'category' => _CO_CONTENT_BLOCKTYPE_CATEGORY_MEDIA,
        'weight' => 60,
    ],
    'image-text' => [
        'title' => _CO_CONTENT_BLOCKTYPE_SEED_IMAGETEXT,
        'category' => _CO_CONTENT_BLOCKTYPE_CATEGORY_MEDIA,
        'weight' => 70,
    ],
    'button' => [
        'title' => _CO_CONTENT_BLOCKTYPE_SEED_BUTTON,
        'category' => _CO_CONTENT_BLOCKTYPE_CATEGORY_DEFAULT,
        'weight' => 80,
    ],
    'card' => [
        'title' => _CO_CONTENT_BLOCKTYPE_SEED_CARD,
        'category' => _CO_CONTENT_BLOCKTYPE_CATEGORY_MEDIA,
        'weight' => 90,
    ],
];
