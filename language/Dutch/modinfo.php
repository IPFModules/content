<?php
/**
 * English language constants related to module information
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Rodrigo P Lima aka TheRplima <therplima@impresscms.org>
 * @package		content
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

// Module Info
define("_MI_CONTENT_MD_NAME", "Content");
define("_MI_CONTENT_MD_DESC", "ImpressCMS Contentbeheer module");
define("_MI_CONTENT_CONTENTS", "Contents");

//Menu
define("_MI_CONTENT_CONTENT_ADD", "Verzenden");

// Configs
define("_MI_CONTENT_CONTPAGE", "Standaard pagina");
define("_MI_CONTENT_CONTPAGEDSC", "Selecteer de standaard pagina om te tonen aan de gebruiker in Content Manager. Laat leeg om de standaard Content Manager op de laatst gemaakte pagina te zetten.");
define("_MI_CONTENT_AUTHORGR", "Groepen die inhoud mogen toevoegen");
define("_MI_CONTENT_AUTHORGRDSC", "Selecteer de groepen die toegestaan zijn om nieuwe inhoud te maken. Houd er rekening mee dat een gebruiker die behoort tot een van deze groepen direct inhoud kan hebben op de site. De module heeft op dit moment geen moderatie functie.");
define("_MI_CONTENT_LIMIT", "Inhoud limiet");
define("_MI_CONTENT_LIMITDSC", "Aantal pagina's om weer te geven aan gebruikerszijde.");
define("_MI_CONTENT_SHOWBREADCRUMB", "Breadcrumb tonen");
define("_MI_CONTENT_SHOWBREADCRUMBDSC", "Set to YES to show a breadcrumb (navigation menu) on the user side.");
define("_MI_CONTENT_SHOWRELATEDS", "Show Related Pages");
define("_MI_CONTENT_SHOWRELATEDSDSC", "Set to YES to show the related pages after the page content.");
define("_MI_CONTENT_SHOWINFO", "Show author and published info");
define("_MI_CONTENT_SHOWINFODSC", "Set to YES to show in the page informations about the author and publish of the page.");

// Blocks
define("_MI_CONTENT_CONTENTDISPLAY", "Content");
define("_MI_CONTENT_CONTENTDISPLAYDSC", "Display the desired content page with some defined configurations.");
define("_MI_CONTENT_CONTENTMENU", "Content Menu");
define("_MI_CONTENT_CONTENTMENUDSC", "Show a block with a menu of content pages.");

// Notifications
define("_MI_CONTENT_GLOBAL_NOTIFY", "All contents");
define("_MI_CONTENT_GLOBAL_NOTIFY_DSC", "Notifications related to all contents in the module");
define("_MI_CONTENT_GLOBAL_CONTENT_PUBLISHED_NOTIFY", "New content published");
define("_MI_CONTENT_GLOBAL_CONTENT_PUBLISHED_NOTIFY_CAP", "Notify me when a new content is published");
define("_MI_CONTENT_GLOBAL_CONTENT_PUBLISHED_NOTIFY_DSC", "Receive notification when any new content is published.");
define("_MI_CONTENT_GLOBAL_CONTENT_PUBLISHED_NOTIFY_SBJ", "[{X_SITENAME}] {X_MODULE} auto-notify : New content published");