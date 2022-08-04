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
define("_CO_CONTENT_CONTENT_CONTENT_PID", "Übergeordnete Seite");
define("_CO_CONTENT_CONTENT_CONTENT_PID_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_UID", "Benutzer");
define("_CO_CONTENT_CONTENT_CONTENT_UID_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_TITLE", "Titel");
define("_CO_CONTENT_CONTENT_CONTENT_TITLE_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_BODY", "Inhalt Text");
define("_CO_CONTENT_CONTENT_CONTENT_BODY_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_CSS", "Custom CSS");
define("_CO_CONTENT_CONTENT_CONTENT_CSS_DSC", 'If you want to personalize the visual of the page you can define here some css styles for this purpose. <br />Click <a href="javascript:openWithSelfMain(\''.ICMS_URL.'/modules/content/images/content-help.png\', \'content_help\', 1000, 600);">here</a> to see the css classes and Ids avaliable.<br />Recommended only for advanced users.');
define("_CO_CONTENT_CONTENT_CONTENT_TAGS", "Schlagwörter");
define("_CO_CONTENT_CONTENT_CONTENT_TAGS_DSC", "Trenne die Schlagwörter mit '<font color=red>,</font>'");
define("_CO_CONTENT_CONTENT_CONTENT_VISIBILITY", "Link anzeigen");
define("_CO_CONTENT_CONTENT_CONTENT_VISIBILITY_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_PUBLISHED_DATE", "Veröffentlichungsdatum");
define("_CO_CONTENT_CONTENT_CONTENT_PUBLISHED_DATE_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_UPDATED_DATE", "Aktualisiertes Datum");
define("_CO_CONTENT_CONTENT_CONTENT_UPDATED_DATE_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_WEIGHT", "Wert");
define("_CO_CONTENT_CONTENT_CONTENT_WEIGHT_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_STATUS", "Status");
define("_CO_CONTENT_CONTENT_CONTENT_STATUS_DSC", " ");
define("_CO_CONTENT_CONTENT_CONTENT_MAKESYMLINK", "Symlink erstellen?");
define("_CO_CONTENT_CONTENT_CONTENT_MAKESYMLINK_DSC", "Setze auf <b>JA</b> um automatisch einen symbolischen Link für diese Inhaltsseite zu erstellen.");
define("_CO_CONTENT_CONTENT_READ", "Berechtigung anzeigen");
define("_CO_CONTENT_CONTENT_READ_DSC", "Wählen Sie aus, welche Gruppen die Zugriffsberechtigung für diese Inhaltsseite haben sollen. Das bedeutet, dass ein Benutzer, der einer dieser Gruppen angehört, die Inhaltsseite sehen kann, wenn er auf der Seite aktiviert ist.");
define("_CO_CONTENT_CONTENT_CONTENT_SUBS", "Verwandte Seiten");
define("_CO_CONTENT_CONTENT_CONTENT_SUBS_DSC", "");
define("_CO_CONTENT_CONTENT_CONTENT_CANCOMMENT", "Darf kommentieren?");
define("_CO_CONTENT_CONTENT_CONTENT_CANCOMMENT_DSC", "");
define("_CO_CONTENT_CONTENT_CONTENT_SHOWSUBS", "Ähnliche Seiten anzeigen");
define("_CO_CONTENT_CONTENT_CONTENT_SHOWSUBS_DSC", "Wenn die <b>\"Verwandte Seiten anzeigen\"</b> in den Einstellungen dieses Moduls auf <b>\"Ja\"</b> gesetzt ist, können Sie diese Konfiguration überschreiben und die Anzeige der verknüpften Seiten dieser Seite aktivieren oder deaktivieren.");
define("_CO_CONTENT_CONTENT_INFO", "Veröffentlicht von %s am %s. (%u Lesen)");
define("_CO_CONTENT_CONTENT_FROM_USER", "Alle Inhalte von %s");
define("_CO_CONTENT_CONTENT_COMMENTS_INFO", "%d Kommentare");
define("_CO_CONTENT_CONTENT_NO_COMMENT", "Kein Kommentar");

//Status
define("_CO_CONTENT_CONTENT_STATUS_PUBLISHED", "Veröffentlicht");
define("_CO_CONTENT_CONTENT_STATUS_PENDING", "Ausstehende Bewertung");
define("_CO_CONTENT_CONTENT_STATUS_DRAFT", "Entwurf");
define("_CO_CONTENT_CONTENT_STATUS_PRIVATE", "Privat");
define("_CO_CONTENT_CONTENT_STATUS_EXPIRED", "Abgelaufen");

//Visibility
define("_CO_CONTENT_CONTENT_VISIBLE_MENUOLNY", "Nur im Menü");
define("_CO_CONTENT_CONTENT_VISIBLE_SUBSONLY", "Nur auf verwandten Seiten");
define("_CO_CONTENT_CONTENT_VISIBLE_MENUSUBS", "Menü und verwandte Seiten");
define("_CO_CONTENT_CONTENT_VISIBLE_DONTSHOW", "Link nicht anzeigen");