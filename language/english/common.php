<?php
/**
 * English language constants commonly used in the module
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Rodrigo P Lima aka TheRplima <therplima@impresscms.org>
 * @package		content
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

// content
define("_CO_CONTENT_CONTENT_CONTENT_PID", "Parent Page");
define("_CO_CONTENT_CONTENT_CONTENT_PID_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_UID", "Poster");
define("_CO_CONTENT_CONTENT_CONTENT_UID_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_TITLE", "Title");
define("_CO_CONTENT_CONTENT_CONTENT_TITLE_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_BODY", "Content Body");
define("_CO_CONTENT_CONTENT_CONTENT_BODY_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_CSS", "Custom CSS");
define("_CO_CONTENT_CONTENT_CONTENT_CSS_DSC", 'If you want to personalize the visual of the page you can define here some css styles for this purpose. <br />Click <a href="javascript:openWithSelfMain(\''.ICMS_URL.'/modules/content/images/content-help.png\', \'content_help\', 1000, 600);">here</a> to see the css classes and Ids avaliable.<br />Recommended only for advanced users.');
define("_CO_CONTENT_CONTENT_CONTENT_TAGS", "Tags");
define("_CO_CONTENT_CONTENT_CONTENT_TAGS_DSC", "Separate the tags with '<font color=red>,</font>'");
define("_CO_CONTENT_CONTENT_CONTENT_VISIBILITY", "Show link in");
define("_CO_CONTENT_CONTENT_CONTENT_VISIBILITY_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_PUBLISHED_DATE", "Published Date");
define("_CO_CONTENT_CONTENT_CONTENT_PUBLISHED_DATE_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_UPDATED_DATE", "Updated Date");
define("_CO_CONTENT_CONTENT_CONTENT_UPDATED_DATE_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_WEIGHT", "Weight");
define("_CO_CONTENT_CONTENT_CONTENT_WEIGHT_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_STATUS", "Status");
define("_CO_CONTENT_CONTENT_CONTENT_STATUS_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_MAKESYMLINK", "Create Symlink?");
define("_CO_CONTENT_CONTENT_CONTENT_MAKESYMLINK_DSC", "Set to <b>YES</b> to create automaticaly a symlink for this content page.");
define("_CO_CONTENT_CONTENT_READ", "View Permission");
define("_CO_CONTENT_CONTENT_READ_DSC", "Select which groups will have view permission for this content page. This means that a user belonging to one of these groups will be able to view the content page when it is activated in the site.");
define("_CO_CONTENT_CONTENT_CONTENT_SUBS", "Related Pages");
define("_CO_CONTENT_CONTENT_CONTENT_SUBS_DSC", "");
define("_CO_CONTENT_CONTENT_CONTENT_CANCOMMENT", "Can comment ?");
define("_CO_CONTENT_CONTENT_CONTENT_CANCOMMENT_DSC", "");
define("_CO_CONTENT_CONTENT_CONTENT_SHOWSUBS", "Show Related Pages");
define("_CO_CONTENT_CONTENT_CONTENT_SHOWSUBS_DSC", "If the <b>\"Show Related Pages\"</b> in the preferences of this module is set to <b>\"YES\"</b> then you can override this config and enable or disable the display of the Related Pages of this Page.");
define("_CO_CONTENT_CONTENT_INFO", "Published by %s on %s. (%u reads)");
define("_CO_CONTENT_CONTENT_FROM_USER", "All contents of %s");
define("_CO_CONTENT_CONTENT_COMMENTS_INFO", "%d comments");
define("_CO_CONTENT_CONTENT_NO_COMMENT", "No comment");

//Status
define("_CO_CONTENT_CONTENT_STATUS_PUBLISHED", "Published");
define("_CO_CONTENT_CONTENT_STATUS_PENDING", "Pending review");
define("_CO_CONTENT_CONTENT_STATUS_DRAFT", "Draft");
define("_CO_CONTENT_CONTENT_STATUS_PRIVATE", "Private");
define("_CO_CONTENT_CONTENT_STATUS_EXPIRED", "Expired");

//Visibility
define("_CO_CONTENT_CONTENT_VISIBLE_MENUOLNY", "Only in Menu");
define("_CO_CONTENT_CONTENT_VISIBLE_SUBSONLY", "Only in Related Pages");
define("_CO_CONTENT_CONTENT_VISIBLE_MENUSUBS", "Menu and Related Pages");
define("_CO_CONTENT_CONTENT_VISIBLE_DONTSHOW", "Don't show link");

//Layout
define("_CO_CONTENT_CONTENT_CONTENT_LAYOUT", "Layout");
define("_CO_CONTENT_CONTENT_CONTENT_LAYOUT_DSC", "Choose <b>Building blocks</b> to compose this page with the visual page builder instead of the content body. Use the builder icon in the list of contents to edit the building blocks.");
define("_CO_CONTENT_CONTENT_LAYOUT_CLASSIC", "Content body");
define("_CO_CONTENT_CONTENT_LAYOUT_BLOCKS", "Building blocks");

// blocktype
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_TITLE", "Title");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_TITLE_DSC", "Name of the building block, as shown in the page builder.");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_KEY", "Key");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_KEY_DSC", "Unique identifier of the building block: lowercase letters, digits and dashes.");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_CATEGORY", "Category");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_CATEGORY_DSC", "Building blocks are grouped by category in the page builder.");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_DESCRIPTION", "Description");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_DESCRIPTION_DSC", " ");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_ICON", "Icon");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_ICON_DSC", "Image shown for this building block in the page builder.");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_TEMPLATE", "Template");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_TEMPLATE_DSC", "HTML of the building block, with a single root element. Mark editable zones with <code>data-zone=\"key\" data-zone-type=\"text|plain|image|link\"</code> (an optional <code>data-zone-label</code> names the zone) and areas that can contain other building blocks with <code>data-slot=\"name\"</code>. Image zones must be <code>&lt;img&gt;</code> elements, link zones <code>&lt;a&gt;</code> elements.");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_CSS", "CSS");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_CSS_DSC", "Styles of this building block, added to the pages that use it.");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_ACCEPTS", "Allowed nested blocks");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_ACCEPTS_DSC", "Building blocks that can be placed in the slots of this building block. Select none to allow all building blocks.");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_ROOT", "Allowed at page level");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_ROOT_DSC", "Set to <b>NO</b> when this building block can only be used inside another building block.");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_STATUS", "Active");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_STATUS_DSC", "Inactive building blocks are not offered in the page builder and are not displayed on the pages.");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_SYSTEM", "Shipped with the module");
define("_CO_CONTENT_BLOCKTYPE_BLOCKTYPE_SYSTEM_DSC", " ");
define("_CO_CONTENT_BLOCKTYPE_WEIGHT", "Weight");
define("_CO_CONTENT_BLOCKTYPE_WEIGHT_DSC", " ");
define("_CO_CONTENT_BLOCKTYPE_ACTIVE", "Active");
define("_CO_CONTENT_BLOCKTYPE_INACTIVE", "Inactive");
define("_CO_CONTENT_BLOCKTYPE_CATEGORY_DEFAULT", "Basic");
define("_CO_CONTENT_BLOCKTYPE_CATEGORY_LAYOUT", "Layout");
define("_CO_CONTENT_BLOCKTYPE_CATEGORY_MEDIA", "Media");
define("_CO_CONTENT_BLOCKTYPE_SEED_SECTION", "Section");
define("_CO_CONTENT_BLOCKTYPE_SEED_COLUMNS2", "2 columns");
define("_CO_CONTENT_BLOCKTYPE_SEED_COLUMNS3", "3 columns");
define("_CO_CONTENT_BLOCKTYPE_SEED_HEADING", "Heading");
define("_CO_CONTENT_BLOCKTYPE_SEED_TEXT", "Text");
define("_CO_CONTENT_BLOCKTYPE_SEED_IMAGE", "Image");
define("_CO_CONTENT_BLOCKTYPE_SEED_IMAGETEXT", "Image and text");
define("_CO_CONTENT_BLOCKTYPE_SEED_BUTTON", "Button");
define("_CO_CONTENT_BLOCKTYPE_SEED_CARD", "Card");
define("_CO_CONTENT_BLOCKTYPE_ERR_KEY", "The key may only contain lowercase letters, digits and dashes, and must start with a letter or a digit.");
define("_CO_CONTENT_BLOCKTYPE_ERR_KEY_EXISTS", "A building block with the key '%s' already exists.");
define("_CO_CONTENT_BLOCKTYPE_ERR_SINGLE_ROOT", "The template must contain exactly one root element.");
define("_CO_CONTENT_BLOCKTYPE_ERR_ZONE_NAME", "Invalid zone name '%s': use lowercase letters, digits, dashes and underscores.");
define("_CO_CONTENT_BLOCKTYPE_ERR_ZONE_DUPLICATE", "The zone '%s' is defined more than once.");
define("_CO_CONTENT_BLOCKTYPE_ERR_ZONE_TYPE", "The zone '%s' has an unknown type '%s'. Use text, plain, image or link.");
define("_CO_CONTENT_BLOCKTYPE_ERR_ZONE_TAG", "The zone '%s' must be a &lt;%s&gt; element.");
define("_CO_CONTENT_BLOCKTYPE_ERR_ZONE_NESTED", "The zone '%s' can't be placed inside another zone or a slot.");
define("_CO_CONTENT_BLOCKTYPE_ERR_SLOT_NAME", "Invalid slot name '%s': use lowercase letters, digits, dashes and underscores.");
define("_CO_CONTENT_BLOCKTYPE_ERR_SLOT_DUPLICATE", "The slot '%s' is defined more than once.");
define("_CO_CONTENT_BLOCKTYPE_ERR_SLOT_NESTED", "The slot '%s' can't be a zone or be placed inside a zone or another slot.");
define("_CO_CONTENT_BLOCKTYPE_ERR_IN_USE", "This building block is used %u times on pages and can't be deleted. Set it inactive instead.");

// blockitem
define("_CO_CONTENT_BLOCKITEM_BLOCKITEM_CONTENT_ID", "Page");
define("_CO_CONTENT_BLOCKITEM_BLOCKITEM_CONTENT_ID_DSC", " ");
define("_CO_CONTENT_BLOCKITEM_BLOCKITEM_PID", "Parent building block");
define("_CO_CONTENT_BLOCKITEM_BLOCKITEM_PID_DSC", " ");
define("_CO_CONTENT_BLOCKITEM_BLOCKITEM_SLOT", "Slot");
define("_CO_CONTENT_BLOCKITEM_BLOCKITEM_SLOT_DSC", " ");
define("_CO_CONTENT_BLOCKITEM_BLOCKITEM_BLOCKTYPE_ID", "Building block type");
define("_CO_CONTENT_BLOCKITEM_BLOCKITEM_BLOCKTYPE_ID_DSC", " ");
define("_CO_CONTENT_BLOCKITEM_WEIGHT", "Weight");
define("_CO_CONTENT_BLOCKITEM_WEIGHT_DSC", " ");

// zone
define("_CO_CONTENT_ZONE_ZONE_BLOCKITEM_ID", "Building block");
define("_CO_CONTENT_ZONE_ZONE_BLOCKITEM_ID_DSC", " ");
define("_CO_CONTENT_ZONE_ZONE_KEY", "Zone");
define("_CO_CONTENT_ZONE_ZONE_KEY_DSC", " ");
define("_CO_CONTENT_ZONE_ZONE_TYPE", "Type");
define("_CO_CONTENT_ZONE_ZONE_TYPE_DSC", " ");
define("_CO_CONTENT_ZONE_ZONE_TEXT", "Text");
define("_CO_CONTENT_ZONE_ZONE_TEXT_DSC", " ");
define("_CO_CONTENT_ZONE_ZONE_PLAIN", "Plain text");
define("_CO_CONTENT_ZONE_ZONE_PLAIN_DSC", " ");
define("_CO_CONTENT_ZONE_ZONE_IMAGE", "Image");
define("_CO_CONTENT_ZONE_ZONE_IMAGE_DSC", " ");
define("_CO_CONTENT_ZONE_ZONE_ALT", "Alternative text");
define("_CO_CONTENT_ZONE_ZONE_ALT_DSC", " ");
define("_CO_CONTENT_ZONE_ZONE_URL", "Link");
define("_CO_CONTENT_ZONE_ZONE_URL_DSC", " ");
define("_CO_CONTENT_ZONE_ZONE_TARGET", "Link target");
define("_CO_CONTENT_ZONE_ZONE_TARGET_DSC", " ");
define("_CO_CONTENT_ZONE_TYPE_TEXT", "Text");
define("_CO_CONTENT_ZONE_TYPE_PLAIN", "Plain text");
define("_CO_CONTENT_ZONE_TYPE_IMAGE", "Image");
define("_CO_CONTENT_ZONE_TYPE_LINK", "Link");
