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

defined("ICMS_ROOT_PATH") or die("ICMS root path niet gedefiniëerd");

// content
define("_CO_CONTENT_CONTENT_CONTENT_PID", "Bovenliggende pagina");
define("_CO_CONTENT_CONTENT_CONTENT_PID_DSC", "Indien ingevuld, de pagina waaronder deze pagina hangt als kind ");
define("_CO_CONTENT_CONTENT_CONTENT_UID", "Auteur");
define("_CO_CONTENT_CONTENT_CONTENT_UID_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_TITLE", "Titel");
define("_CO_CONTENT_CONTENT_CONTENT_TITLE_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_BODY", "Inhoud");
define("_CO_CONTENT_CONTENT_CONTENT_BODY_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_CSS", "Custom CSS");
define("_CO_CONTENT_CONTENT_CONTENT_CSS_DSC", 'Laat toe om specifiek voor deze pagina met CSS regels de weergave aan te passen. Voor gevorderde gebruikers..');
define("_CO_CONTENT_CONTENT_CONTENT_TAGS", "Tags");
define("_CO_CONTENT_CONTENT_CONTENT_TAGS_DSC", "Tags afscheiden met '<font color=red>,</font>'");
define("_CO_CONTENT_CONTENT_CONTENT_VISIBILITY", "Toon de link in");
define("_CO_CONTENT_CONTENT_CONTENT_VISIBILITY_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_PUBLISHED_DATE", "Publicatiedatum");
define("_CO_CONTENT_CONTENT_CONTENT_PUBLISHED_DATE_DSC", "Datum van allereerste publicatie ");
define("_CO_CONTENT_CONTENT_CONTENT_UPDATED_DATE", "Update datum");
define("_CO_CONTENT_CONTENT_CONTENT_UPDATED_DATE_DSC", "Datum van de laatste aanpassing ");
define("_CO_CONTENT_CONTENT_CONTENT_WEIGHT", "Gewicht");
define("_CO_CONTENT_CONTENT_CONTENT_WEIGHT_DSC", "Bepaal de volgorde tussen pagina's. Hoogste waarde is zwaarder, en komt dus onderaan. ");
define("_CO_CONTENT_CONTENT_CONTENT_STATUS", "Status");
define("_CO_CONTENT_CONTENT_CONTENT_STATUS_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_MAKESYMLINK", "Symlink aanmaken?");
define("_CO_CONTENT_CONTENT_CONTENT_MAKESYMLINK_DSC", "Kies <b>Ja</b> om automatisch een symlink voor deze pagina aan te maken.");
define("_CO_CONTENT_CONTENT_READ", "Bekijk rechten");
define("_CO_CONTENT_CONTENT_READ_DSC", "Kies de groepen die recht hebben om de pagina te zien. Dat betekent dat een gebruiker uit deze groepen de pagina zal kunnen zien van zodra ze gepubliceerd is.");
define("_CO_CONTENT_CONTENT_CONTENT_SUBS", "Onderliggende pagina's");
define("_CO_CONTENT_CONTENT_CONTENT_SUBS_DSC", "Pagina's die als sub-pagina van deze pagina zijn aangegeven");
define("_CO_CONTENT_CONTENT_CONTENT_CANCOMMENT", "Mag commentaar krijgen?");
define("_CO_CONTENT_CONTENT_CONTENT_CANCOMMENT_DSC", "Laat toe dat gebruikers van de site commentaar geven op de pagina, volgens de algemene regels van de website.");
define("_CO_CONTENT_CONTENT_CONTENT_SHOWSUBS", "Toon onderliggende pagina's");
define("_CO_CONTENT_CONTENT_CONTENT_SHOWSUBS_DSC", "Wanneer <b>'Toon onderliggende pagina's'</b> in de algemene module instellingen op <b>'JA'</b> staat, dan kan dat hier worden overschreven voor deze specifieke pagina.");
define("_CO_CONTENT_CONTENT_INFO", "Gepubliceerd door %s op %s. (%u x gelezen)");
define("_CO_CONTENT_CONTENT_FROM_USER", "Alle inhoud van %s");
define("_CO_CONTENT_CONTENT_COMMENTS_INFO", "%d commentaren");
define("_CO_CONTENT_CONTENT_NO_COMMENT", "Geen commentaren");

//Status
define("_CO_CONTENT_CONTENT_STATUS_PUBLISHED", "Gepubliceerd");
define("_CO_CONTENT_CONTENT_STATUS_PENDING", "Wacht op nazicht");
define("_CO_CONTENT_CONTENT_STATUS_DRAFT", "Klad");
define("_CO_CONTENT_CONTENT_STATUS_PRIVATE", "Privé");
define("_CO_CONTENT_CONTENT_STATUS_EXPIRED", "Verlopen");

//Visibility
define("_CO_CONTENT_CONTENT_VISIBLE_MENUOLNY", "Enkel in Menu");
define("_CO_CONTENT_CONTENT_VISIBLE_SUBSONLY", "Enkel in onderliggende pagina's");
define("_CO_CONTENT_CONTENT_VISIBLE_MENUSUBS", "Menu en onderliggende pagina's");
define("_CO_CONTENT_CONTENT_VISIBLE_DONTSHOW", "Niet weergeven");