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
define("_CO_CONTENT_CONTENT_CONTENT_PID", "Bovenliggende pagina");
define("_CO_CONTENT_CONTENT_CONTENT_PID_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_UID", "Auteur");
define("_CO_CONTENT_CONTENT_CONTENT_UID_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_TITLE", "Titel");
define("_CO_CONTENT_CONTENT_CONTENT_TITLE_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_BODY", "Inhoud tekst");
define("_CO_CONTENT_CONTENT_CONTENT_BODY_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_CSS", "Eigen CSS");
define("_CO_CONTENT_CONTENT_CONTENT_CSS_DSC", 'If you want to personalize the visual of the page you can define here some css styles for this purpose. <br />Click <a href="javascript:openWithSelfMain(\''.ICMS_URL.'/modules/content/images/content-help.png\', \'content_help\', 1000, 600);">here</a> to see the css classes and Ids avaliable.<br />Recommended only for advanced users.');
define("_CO_CONTENT_CONTENT_CONTENT_TAGS", "Tags");
define("_CO_CONTENT_CONTENT_CONTENT_TAGS_DSC", "Scheid de tags met '<font color=red>,</font>'");
define("_CO_CONTENT_CONTENT_CONTENT_VISIBILITY", "Link weergeven in");
define("_CO_CONTENT_CONTENT_CONTENT_VISIBILITY_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_PUBLISHED_DATE", "Publicatie datum");
define("_CO_CONTENT_CONTENT_CONTENT_PUBLISHED_DATE_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_UPDATED_DATE", "Bijgewerkt op");
define("_CO_CONTENT_CONTENT_CONTENT_UPDATED_DATE_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_WEIGHT", "Gewicht");
define("_CO_CONTENT_CONTENT_CONTENT_WEIGHT_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_STATUS", "Status");
define("_CO_CONTENT_CONTENT_CONTENT_STATUS_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_MAKESYMLINK", "Symlink maken?");
define("_CO_CONTENT_CONTENT_CONTENT_MAKESYMLINK_DSC", "Zet op <b>JA</b> om automatisch een symlink te maken voor deze inhoudspagina.");
define("_CO_CONTENT_CONTENT_READ", "Permissie bekijken");
define("_CO_CONTENT_CONTENT_READ_DSC", "Selecteer welke groepen toestemming hebben om deze pagina te bekijken. Dit betekent dat een gebruiker die behoort tot een van deze groepen de pagina kan bekijken wanneer deze pagina is geactiveerd op de site.");
define("_CO_CONTENT_CONTENT_CONTENT_SUBS", "Verwante pagina's");
define("_CO_CONTENT_CONTENT_CONTENT_SUBS_DSC", "");
define("_CO_CONTENT_CONTENT_CONTENT_CANCOMMENT", "Kan reageren?");
define("_CO_CONTENT_CONTENT_CONTENT_CANCOMMENT_DSC", "");
define("_CO_CONTENT_CONTENT_CONTENT_SHOWSUBS", "Verwante pagina's tonen");
define("_CO_CONTENT_CONTENT_CONTENT_SHOWSUBS_DSC", "Als de <b>\"Toon gerelateerde pagina's\"</b> in de voorkeuren van deze module is ingesteld op <b>\"JA\"</b> dan kunt u deze instelling voor deze pagina overschrijven en de weergave van de gerelateerde pagina's van deze pagina inschakelen of uitschakelen.");
define("_CO_CONTENT_CONTENT_INFO", "Gepubliceerd door %s op %s. (%u lezen)");
define("_CO_CONTENT_CONTENT_FROM_USER", "Alle inhoud van %s");
define("_CO_CONTENT_CONTENT_COMMENTS_INFO", "%d reacties");
define("_CO_CONTENT_CONTENT_NO_COMMENT", "Geen reactie");

//Status
define("_CO_CONTENT_CONTENT_STATUS_PUBLISHED", "Gepubliceerd");
define("_CO_CONTENT_CONTENT_STATUS_PENDING", "Wachten op review");
define("_CO_CONTENT_CONTENT_STATUS_DRAFT", "Kladversie");
define("_CO_CONTENT_CONTENT_STATUS_PRIVATE", "Privé");
define("_CO_CONTENT_CONTENT_STATUS_EXPIRED", "Vervallen");

//Visibility
define("_CO_CONTENT_CONTENT_VISIBLE_MENUOLNY", "Alleen in het menu");
define("_CO_CONTENT_CONTENT_VISIBLE_SUBSONLY", "Alleen op verwante pagina's");
define("_CO_CONTENT_CONTENT_VISIBLE_MENUSUBS", "Menu en verwante pagina's");
define("_CO_CONTENT_CONTENT_VISIBLE_DONTSHOW", "Link niet weergeven");