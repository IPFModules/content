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

defined("ICMS_ROOT_PATH") or die("ICMS root path niet gedefiniëerd");

// Module Info
define("_MI_CONTENT_MD_NAME", "Inhoud");
define("_MI_CONTENT_MD_DESC", "ImpressCMS Content Manager module");
define("_MI_CONTENT_CONTENTS", "Contents");

//Menu
define("_MI_CONTENT_CONTENT_ADD", "Verzenden");

// Configs
define("_MI_CONTENT_CONTPAGE", "Startpagina");
define("_MI_CONTENT_CONTPAGEDSC", "Kies de standaard pagina om weer te geven. Laat leeg om altijd de laatst aangemaakte pagina weer te geven..");
define("_MI_CONTENT_AUTHORGR", "Groepen die inhoud mogen toevoegen");
define("_MI_CONTENT_AUTHORGRDSC", "Kies de groepen die nieuwe inhoud mogen aanmaken. Gebruikers in deze groepen kunnen inhoud rechtstreeks publiceren.");
define("_MI_CONTENT_LIMIT", "Inhoud per keer");
define("_MI_CONTENT_LIMITDSC", "Hoeveel pagina's er zullen getoond worden op de startpagina langs de gebruikerskant.");
define("_MI_CONTENT_SHOWBREADCRUMB", "Toon breadcrumb");
define("_MI_CONTENT_SHOWBREADCRUMBDSC", "Kies JA om een breadcrumb (navigatie menu) aan de gebruikerskant te tonen.");
define("_MI_CONTENT_SHOWRELATEDS", "Toon onderliggende pagina's");
define("_MI_CONTENT_SHOWRELATEDSDSC", "Zet op JA om de onderliggende pagina's weer te geven na de pagina inhoud.");
define("_MI_CONTENT_SHOWINFO", "Toon auteur en publicatie informatie");
define("_MI_CONTENT_SHOWINFODSC", "Kies JA om op de pagina inforamtie weer te geven over de auteur en de publicatie van de pagina.");

// Blocks
define("_MI_CONTENT_CONTENTDISPLAY", "Inhoud");
define("_MI_CONTENT_CONTENTDISPLAYDSC", "Toon de gewenste pagina met bijkomende configuratie.");
define("_MI_CONTENT_CONTENTMENU", "Inhoud Menu");
define("_MI_CONTENT_CONTENTMENUDSC", "Toon een blok met een menu van inhoudspagina's.");

// Notifications
define("_MI_CONTENT_GLOBAL_NOTIFY", "Alle inhoud");
define("_MI_CONTENT_GLOBAL_NOTIFY_DSC", "Meldingen voor alle inhoud in de module");
define("_MI_CONTENT_GLOBAL_CONTENT_PUBLISHED_NOTIFY", "Nieuwe inhoud gepubliceerd");
define("_MI_CONTENT_GLOBAL_CONTENT_PUBLISHED_NOTIFY_CAP", "Meld me wanneer een nieuwe pagina is gepubliceerd");
define("_MI_CONTENT_GLOBAL_CONTENT_PUBLISHED_NOTIFY_DSC", "Ontvang meldingen wanneer nieuwe inhoud werd gepubliceerd.");
define("_MI_CONTENT_GLOBAL_CONTENT_PUBLISHED_NOTIFY_SBJ", "[{X_SITENAME}] {X_MODULE} auto-melding : Nieuwe inhoud gepubliceerd.");