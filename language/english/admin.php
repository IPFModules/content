<?php
/**
 * English language constants used in admin section of the module
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Rodrigo P Lima aka TheRplima <therplima@impresscms.org>
 * @package		content
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

// Requirements
define("_AM_CONTENT_REQUIREMENTS", "content Requirements");
define("_AM_CONTENT_REQUIREMENTS_INFO", "We've reviewed your system, unfortunately it doesn't meet all the requirements needed for content to function. Below are the requirements needed.");
define("_AM_CONTENT_REQUIREMENTS_ICMS_BUILD", "content requires at least ImpressCMS 1.1.1 RC 1.");
define("_AM_CONTENT_REQUIREMENTS_SUPPORT", "Should you have any question or concerns, please visit our forums at <a href='http://community.impresscms.org'>http://community.impresscms.org</a>.");

// general
define("_AM_CONTENT_CONTENTS", "Contents");

// Content
define("_AM_CONTENT_CONTENT_CREATE", "Add a content");
define("_AM_CONTENT_CONTENT", "Content");
define("_AM_CONTENT_CONTENT_EDIT", "Edit this content");
define("_AM_CONTENT_CONTENT_MODIFIED", "The content was successfully modified.");
define("_AM_CONTENT_CONTENT_CREATED", "The content has been successfully created.");
define("_AM_CONTENT_CONTENT_CLONE", "Clone this content");
define("_AM_CONTENT_PREVIEW", "Preview Content");
define("_AM_CONTENT_VIEW", "View Full Content Info");
define("_AM_CONTENT_CONTENT_BUILD", "Edit the building blocks of this page");

// Building block types
define("_AM_CONTENT_BLOCKTYPES", "Building blocks");
define("_AM_CONTENT_BLOCKTYPE_CREATE", "Add a building block");
define("_AM_CONTENT_BLOCKTYPE_EDIT", "Edit this building block");
define("_AM_CONTENT_BLOCKTYPE_CREATED", "The building block has been successfully created.");
define("_AM_CONTENT_BLOCKTYPE_MODIFIED", "The building block was successfully modified.");
define("_AM_CONTENT_BLOCKTYPE_SHOW", "View this building block");
define("_AM_CONTENT_BLOCKTYPE_PREVIEW", "Preview");
define("_AM_CONTENT_BLOCKTYPE_ZONES", "Zones");
define("_AM_CONTENT_BLOCKTYPE_SLOTS", "Slots");

// Page builder
define("_AM_CONTENT_BUILDER", "Page builder");
define("_AM_CONTENT_BUILDER_NOT_BLOCKS", "This page doesn't use the building blocks layout. Edit the page and set its layout to building blocks first.");
define("_AM_CONTENT_BUILDER_SAVE", "Save");
define("_AM_CONTENT_BUILDER_SAVED", "The building blocks of this page were saved.");
define("_AM_CONTENT_BUILDER_UNSAVED", "There are unsaved changes.");
define("_AM_CONTENT_BUILDER_VIEW", "View page");
define("_AM_CONTENT_BUILDER_BACK", "Back to the contents");
define("_AM_CONTENT_BUILDER_ERROR", "The building blocks could not be saved");
define("_AM_CONTENT_BUILDER_BLOCKS", "Building blocks");
define("_AM_CONTENT_BUILDER_LAYERS", "Structure");
define("_AM_CONTENT_BUILDER_SETTINGS", "Settings");
define("_AM_CONTENT_BUILDER_ALT", "Alternative text");
define("_AM_CONTENT_BUILDER_HREF", "Link");
define("_AM_CONTENT_BUILDER_TARGET", "Open in");
define("_AM_CONTENT_BUILDER_TARGET_SELF", "Same window");
define("_AM_CONTENT_BUILDER_TARGET_BLANK", "New window");
define("_AM_CONTENT_BUILDER_ERR_TOKEN", "Your session has expired. Reload the page builder and try again.");
define("_AM_CONTENT_BUILDER_ERR_INVALID", "The building blocks sent by the page builder are invalid.");
define("_AM_CONTENT_BUILDER_ERR_DEPTH", "The building blocks are nested too deep.");
define("_AM_CONTENT_BUILDER_ERR_SIZE", "The page contains too many building blocks.");
define("_AM_CONTENT_BUILDER_ERR_TYPE", "The building block '%s' doesn't exist or is inactive.");
define("_AM_CONTENT_BUILDER_ERR_ROOT", "The building block '%s' can't be placed at page level.");
define("_AM_CONTENT_BUILDER_ERR_ACCEPTS", "The building block '%s' can't be placed in '%s'.");
define("_AM_CONTENT_BUILDER_ERR_SLOT", "The building block '%s' is placed in an unknown slot.");
define("_AM_CONTENT_BUILDER_ERR_TEXT_LENGTH", "A text in the building block '%s' is too long.");
define("_AM_CONTENT_BUILDER_ERR_STORE", "The building block '%s' could not be stored.");
define("_AM_CONTENT_BUILDER_ERR_UPLOAD", "The image could not be uploaded");