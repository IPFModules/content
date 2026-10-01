<?php
/**
 * Handler for the building block types
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		2.0
 * @author		David Janssens (fiammybe)
 * @package		content
 */

defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

class mod_content_BlocktypeHandler extends icms_ipf_Handler
{
    private const KEY_PATTERN = '/^[a-z0-9][a-z0-9-]*$/';

    /** @var array<string, mod_content_Blocktype>|null */
    private ?array $blocktypesByKey = null;

    public function __construct(&$db)
    {
        parent::__construct($db, 'blocktype', 'blocktype_id', 'blocktype_title', 'blocktype_description', 'content');

        icms_loadLanguageFile(basename(dirname(__FILE__, 2)), 'common');

        $this->enableUpload(['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/svg+xml', 'image/webp'], 102400, 200, 200);
    }

    /** @return array<string, string> block type titles indexed by key */
    public function getBlocktypeList(): array
    {
        return array_map(
            static fn (mod_content_Blocktype $blocktype): string => $blocktype->getVar('blocktype_title', 'n'),
            $this->getAllByKey()
        );
    }

    /** @return array<string, string> */
    public function getCategoryArray(): array
    {
        $categories = [];

        foreach ($this->getAllByKey() as $blocktype) {
            $category = $blocktype->getVar('blocktype_category', 'n');
            $categories[$category] = $category;
        }

        ksort($categories);

        return $categories;
    }

    /** @return array<int, string> */
    public function getStatusArray(): array
    {
        return [1 => _CO_CONTENT_BLOCKTYPE_ACTIVE, 0 => _CO_CONTENT_BLOCKTYPE_INACTIVE];
    }

    /** @return array<string, mod_content_Blocktype> */
    public function getAllByKey(): array
    {
        if ($this->blocktypesByKey !== null) {
            return $this->blocktypesByKey;
        }

        $criteria = new icms_db_criteria_Compo();
        $criteria->setSort('weight');
        $criteria->setOrder('ASC');

        $this->blocktypesByKey = [];

        foreach ($this->getObjects($criteria) as $blocktype) {
            $this->blocktypesByKey[$blocktype->getVar('blocktype_key', 'n')] = $blocktype;
        }

        return $this->blocktypesByKey;
    }

    /** @return array<int, mod_content_Blocktype> */
    public function getAllById(): array
    {
        $blocktypes = [];

        foreach ($this->getAllByKey() as $blocktype) {
            $blocktypes[(int) $blocktype->getVar('blocktype_id')] = $blocktype;
        }

        return $blocktypes;
    }

    public function getByKey(string $key): ?mod_content_Blocktype
    {
        return $this->getAllByKey()[$key] ?? null;
    }

    /**
     * Definitions of the active block types, in the format used by the page builder
     *
     * @return array<string, array<string, mixed>>
     */
    public function getActiveDefinitions(): array
    {
        $definitions = [];

        foreach ($this->getAllByKey() as $key => $blocktype) {
            if (!$blocktype->isActive()) {
                continue;
            }

            if (!$blocktype->getTemplate()->isValid()) {
                continue;
            }

            $definitions[$key] = $blocktype->toBuilderArray();
        }

        return $definitions;
    }

    /**
     * The CSS of all block types, used to style the builder canvas
     */
    public function getAllCss(): string
    {
        $css = array_map(
            static fn (mod_content_Blocktype $blocktype): string => (string) $blocktype->getVar('blocktype_css', 'n'),
            $this->getAllByKey()
        );

        return implode("\n", array_filter($css));
    }

    /**
     * Seeds the default block types shipped with the module. Existing keys are left untouched so admin changes are kept
     *
     * @param array<string, array<string, mixed>> $definitions
     * @return array<int, string> keys of the created block types
     */
    public function seed(array $definitions, string $folder): array
    {
        $created = [];

        foreach ($definitions as $key => $definition) {
            if ($this->getCount(new icms_db_criteria_Item('blocktype_key', $key)) > 0) {
                continue;
            }

            $template = "{$folder}/{$key}.html";
            $css = "{$folder}/{$key}.css";

            if (!is_file($template)) {
                continue;
            }

            $blocktype = $this->create();
            $blocktype->setVar('blocktype_key', $key);
            $blocktype->setVar('blocktype_title', $definition['title']);
            $blocktype->setVar('blocktype_category', $definition['category']);
            $blocktype->setVar('blocktype_description', $definition['description'] ?? '');
            $blocktype->setVar('blocktype_template', trim(file_get_contents($template)));
            $blocktype->setVar('blocktype_css', is_file($css) ? trim(file_get_contents($css)) : '');
            $blocktype->setVar('blocktype_accepts', $definition['accepts'] ?? []);
            $blocktype->setVar('blocktype_root', (int) ($definition['root'] ?? 1));
            $blocktype->setVar('blocktype_status', 1);
            $blocktype->setVar('blocktype_system', 1);
            $blocktype->setVar('weight', (int) ($definition['weight'] ?? 0));

            if ($this->insert($blocktype, true)) {
                $created[] = $key;
            }
        }

        $this->blocktypesByKey = null;

        return $created;
    }

    protected function beforeSave(&$obj): bool
    {
        $key = (string) $obj->getVar('blocktype_key', 'n');

        if (!preg_match(self::KEY_PATTERN, $key)) {
            $obj->setErrors(_CO_CONTENT_BLOCKTYPE_ERR_KEY);

            return false;
        }

        $criteria = new icms_db_criteria_Compo(new icms_db_criteria_Item('blocktype_key', $key));
        $criteria->add(new icms_db_criteria_Item('blocktype_id', (int) $obj->getVar('blocktype_id'), '!='));

        if ($this->getCount($criteria) > 0) {
            $obj->setErrors(sprintf(_CO_CONTENT_BLOCKTYPE_ERR_KEY_EXISTS, $key));

            return false;
        }

        $obj->resetTemplate();
        $template = $obj->getTemplate();

        if (!$template->isValid()) {
            foreach ($template->getErrors() as $error) {
                $obj->setErrors($error);
            }

            return false;
        }

        return true;
    }

    /**
     * Pages using this block type are rendered again so they reflect the changed markup or CSS
     */
    protected function afterSave(&$obj): bool
    {
        $this->blocktypesByKey = null;

        $moduleDir = basename(dirname(__FILE__, 2));
        $contentIds = icms_getModuleHandler('blockitem', $moduleDir, 'content')
            ->getContentIdsByBlocktype((int) $obj->getVar('blocktype_id'));

        if ($contentIds === []) {
            return true;
        }

        $contentHandler = icms_getModuleHandler('content', $moduleDir, 'content');

        foreach ($contentIds as $contentId) {
            $contentHandler->renderBlocksById($contentId);
        }

        return true;
    }

    protected function beforeDelete(&$obj): bool
    {
        $inUse = icms_getModuleHandler('blockitem', basename(dirname(__FILE__, 2)), 'content')
            ->getCount(new icms_db_criteria_Item('blockitem_blocktype_id', (int) $obj->getVar('blocktype_id')));

        if ($inUse > 0) {
            $obj->setErrors(sprintf(_CO_CONTENT_BLOCKTYPE_ERR_IN_USE, $inUse));

            return false;
        }

        return true;
    }

    protected function afterDelete(&$obj): bool
    {
        $this->blocktypesByKey = null;

        $icon = (string) $obj->getVar('blocktype_icon', 'n');

        if ($icon === '' || preg_match('#^https?://#i', $icon)) {
            return true;
        }

        $file = $this->getImagePath() . basename($icon);

        if (is_file($file)) {
            unlink($file);
        }

        return true;
    }
}
