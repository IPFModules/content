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

    public const IMAGE_MIMETYPES = [
        'image/gif',
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/x-png',
        'image/webp',
    ];

    public const LINK_TARGETS = ['_self', '_blank'];

    public function __construct(&$db)
    {
        parent::__construct($db, 'zone', 'zone_id', 'zone_key', 'zone_plain', 'content');

        icms_loadLanguageFile(basename(dirname(__FILE__, 2)), 'common');

        $config = icms_getModuleConfig(basename(dirname(__FILE__, 2)));

        $this->enableUpload(
            self::IMAGE_MIMETYPES,
            (int) ($config['builder_image_maxsize'] ?? 2097152),
            (int) ($config['builder_image_maxwidth'] ?? 2400),
            (int) ($config['builder_image_maxheight'] ?? 2400)
        );
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
     * Turns an image value as received from the builder into the value stored in zone_image:
     * a file name for images in the zone upload folder, a full URL for any other image
     */
    public function normalizeImageValue(string $src): string
    {
        $src = trim($src);

        if ($src === '') {
            return '';
        }

        $uploadUrl = $this->getImageUrl();

        if (str_starts_with($src, $uploadUrl)) {
            return basename(substr($src, strlen($uploadUrl)));
        }

        if (!preg_match('#^https?://#i', $src)) {
            return '';
        }

        return filter_var($src, FILTER_VALIDATE_URL) ? $src : '';
    }

    public function getImageSrc(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $value)) {
            return $value;
        }

        return $this->getImageUrl() . rawurlencode($value);
    }

    /**
     * Images uploaded through the builder, newest first, in the GrapesJS asset manager format
     *
     * @return array<int, array{src: string}>
     */
    public function getAssetList(): array
    {
        $path = $this->getImagePath();
        $files = glob("{$path}*.{gif,jpg,jpeg,png,webp}", GLOB_BRACE) ?: [];

        usort($files, static fn (string $a, string $b): int => filemtime($b) <=> filemtime($a));

        return array_map(fn (string $file): array => ['src' => $this->getImageSrc(basename($file))], $files);
    }

    /**
     * Stores an uploaded image in the zone upload folder using the upload configuration of this handler
     *
     * @return array{
     *     src: ?string,
     *     errors: array<int, string>
     * }
     */
    public function storeUploadedImage(string $fieldName, int $index): array
    {
        $uploader = new icms_file_MediaUploadHandler(
            $this->getImagePath(),
            $this->_allowedMimeTypes,
            $this->_maxFileSize,
            $this->_maxWidth,
            $this->_maxHeight
        );

        if (!$uploader->fetchMedia($fieldName, $index)) {
            return ['src' => null, 'errors' => (array) $uploader->getErrors(false)];
        }

        $uploader->setPrefix('zone');

        if (!$uploader->upload()) {
            return ['src' => null, 'errors' => (array) $uploader->getErrors(false)];
        }

        return ['src' => $this->getImageSrc($uploader->getSavedFileName()), 'errors' => []];
    }

    /**
     * Images are shared through the asset manager, so the file is only removed when no zone uses it anymore
     */
    protected function afterDelete(&$obj): bool
    {
        $image = $obj->getVar('zone_image', 'n');

        if ($image === '' || preg_match('#^https?://#i', $image)) {
            return true;
        }

        if ($this->getCount(new icms_db_criteria_Item('zone_image', $image)) > 0) {
            return true;
        }

        $file = $this->getImagePath() . basename($image);

        if (is_file($file)) {
            unlink($file);
        }

        return true;
    }
}
