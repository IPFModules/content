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
define("_MI_CONTENT_MD_NAME", "Inhalt");
define("_MI_CONTENT_MD_DESC", "ImpressCMS Content Manager-Modul");
define("_MI_CONTENT_CONTENTS", "Inhalte");

//Menu
define("_MI_CONTENT_CONTENT_ADD", "Absenden");

// Configs
define("_MI_CONTENT_CONTPAGE", "Standardseite");
define("_MI_CONTENT_CONTPAGEDSC", "Wählen Sie die Standardseite, die dem Benutzer im Content-Manager angezeigt werden soll. Leer lassen, um den Content-Manager auf die zuletzt erstellte Seite zu setzen.");
define("_MI_CONTENT_AUTHORGR", "Gruppen, die Inhalte hinzufügen dürfen");
define("_MI_CONTENT_AUTHORGRDSC", "Wählen Sie die Gruppen, die neue Inhalte erstellen dürfen. Bitte beachten Sie, dass ein Benutzer, der zu einer dieser Gruppen gehört, in der Lage sein wird, sich direkt auf der Website zu informieren. Das Modul hat derzeit keine Moderations-Funktion.");
define("_MI_CONTENT_LIMIT", "Inhalts-Limit");
define("_MI_CONTENT_LIMITDSC", "Anzahl der Inhalte, die auf der Benutzerseite angezeigt werden.");
define("_MI_CONTENT_SHOWBREADCRUMB", "Breadcrumbmenü anzeigen");
define("_MI_CONTENT_SHOWBREADCRUMBDSC", "Auf Ja setzen um ein Breadcrumb (Navigationsmenü) auf der Benutzerseite anzuzeigen.");
define("_MI_CONTENT_SHOWRELATEDS", "Verwandte Seiten anzeigen");
define("_MI_CONTENT_SHOWRELATEDSDSC", "Auf Ja setzen, um die verwandten Seiten nach dem Seiteninhalt anzuzeigen.");
define("_MI_CONTENT_SHOWINFO", "Autor und veröffentlichte Informationen anzeigen");
define("_MI_CONTENT_SHOWINFODSC", "Setzen Sie auf JA, um Informationen über den Autor und die Veröffentlichung der Seite anzuzeigen.");

// Blocks
define("_MI_CONTENT_CONTENTDISPLAY", "Inhalt");
define("_MI_CONTENT_CONTENTDISPLAYDSC", "Zeigt die gewünschte Seite mit einigen definierten Konfigurationen an.");
define("_MI_CONTENT_CONTENTMENU", "Inhalts-Menü");
define("_MI_CONTENT_CONTENTMENUDSC", "Zeige einen Block mit einem Menü von Inhaltsseiten.");

// Notifications
define("_MI_CONTENT_GLOBAL_NOTIFY", "Alle Inhalte");
define("_MI_CONTENT_GLOBAL_NOTIFY_DSC", "Benachrichtigungen zu allen Inhalten des Moduls");
define("_MI_CONTENT_GLOBAL_CONTENT_PUBLISHED_NOTIFY", "Neuer Beitrag veröffentlicht");
define("_MI_CONTENT_GLOBAL_CONTENT_PUBLISHED_NOTIFY_CAP", "Benachrichtigen, wenn ein neuer Inhalt veröffentlicht wird");
define("_MI_CONTENT_GLOBAL_CONTENT_PUBLISHED_NOTIFY_DSC", "Erhalten Sie eine Benachrichtigung, wenn neue Inhalte veröffentlicht werden.");
define("_MI_CONTENT_GLOBAL_CONTENT_PUBLISHED_NOTIFY_SBJ", "[{X_SITENAME}] {X_MODULE} Auto-Benachrichtigung : Neuer Inhalt veröffentlicht");