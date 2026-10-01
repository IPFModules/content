<?php
/**
 * Handler for the editable zones of the building blocks placed on a content page
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		2.0
 * @author		David Janssens (fiammybe)
 * @package		content
 */

defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

if (!defined('CONTENT_ZONE_TYPE_TEXT')) {
    define('CONTENT_ZONE_TYPE_TEXT', 1);
    define('CONTENT_ZONE_TYPE_PLAIN', 2);
    define('CONTENT_ZONE_TYPE_IMAGE', 3);
    define('CONTENT_ZONE_TYPE_LINK', 4);
}

class mod_content_ZoneHandler extends icms_ipf_Handler
{
    public const TYPES = [
        'text' => CONTENT_ZONE_TYPE_TEXT,
        'plain' => CONTENT_ZONE_TYPE_PLAIN,
        'image' => CONTENT_ZONE_TYPE_IMAGE,
        'link' => CONTENT_ZONE_TYPE_LINK,
    ];

    public const LINK_TARGETS = ['_self', '_blank'];

    public function __construct(&$db)
    {
        parent::__construct($db, 'zone', 'zone_id', 'zone_key', 'zone_plain', 'content');

        icms_loadLanguageFile(basename(dirname(__FILE__, 2)), 'common');
    }

    /** @return array<int, string> */
    public function getZone_typeArray(): array
    {
        return [
            CONTENT_ZONE_TYPE_TEXT => _CO_CONTENT_ZONE_TYPE_TEXT,
            CONTENT_ZONE_TYPE_PLAIN => _CO_CONTENT_ZONE_TYPE_PLAIN,
            CONTENT_ZONE_TYPE_IMAGE => _CO_CONTENT_ZONE_TYPE_IMAGE,
            CONTENT_ZONE_TYPE_LINK => _CO_CONTENT_ZONE_TYPE_LINK,
        ];
    }

    /**
     * @param array<int, int> $blockitemIds
     * @return array<int, array<string, mod_content_Zone>>
     */
    public function getZonesForBlockitems(array $blockitemIds): array
    {
        if ($blockitemIds === []) {
            return [];
        }

        $criteria = new icms_db_criteria_Item('zone_blockitem_id', '(' . implode(',', array_map('intval', $blockitemIds)) . ')', 'IN');

        $zones = [];

        foreach ($this->getObjects($criteria) as $zone) {
            $zones[(int) $zone->getVar('zone_blockitem_id')][$zone->getVar('zone_key', 'n')] = $zone;
        }

        return $zones;
    }

    /**
     * Turns an image URL as received from the page builder into the value stored in zone_image.
     *
     * Images come from the ImpressCMS image manager. They are stored as a path relative to the site, like the
     * image manager itself does, so they keep working when the site moves. Anything that isn't an image of
     * this site is dropped. The cache buster the image manager adds to file URLs is removed, other query
     * strings are kept since images stored in the database are served by image.php?file=
     */
    public function normalizeImageValue(string $src): string
    {
        $src = trim($src);

        if (str_starts_with($src, ICMS_URL . '/')) {
            $src = substr($src, strlen(ICMS_URL));
        }

        if (!str_starts_with($src, '/') || str_starts_with($src, '//')) {
            return '';
        }

        if (str_contains($src, '..') || preg_match('/[\x00-\x1f"\'<>\\\\]/', $src)) {
            return '';
        }

        $src = preg_replace('/\?\d+$/', '', $src);

        return strlen($src) <= 255 ? $src : '';
    }

    public function getImageSrc(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (str_starts_with($value, '/')) {
            return ICMS_URL . $value;
        }

        return preg_match('#^https?://#i', $value) ? $value : '';
    }
}
