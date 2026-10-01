<?php
/**
 * Building block type: the markup, zones and nesting rules of a reusable building block
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		2.0
 * @author		David Janssens (fiammybe)
 * @package		content
 */

defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

class mod_content_Blocktype extends icms_ipf_Object
{
    private ?mod_content_BlockTemplate $template = null;

    public function __construct(&$handler)
    {
        parent::__construct($handler);

        $this->quickInitVar('blocktype_id', XOBJ_DTYPE_INT, true);
        $this->quickInitVar('blocktype_title', XOBJ_DTYPE_TXTBOX, true);
        $this->quickInitVar('blocktype_key', XOBJ_DTYPE_TXTBOX, true);
        $this->quickInitVar('blocktype_category', XOBJ_DTYPE_TXTBOX, false, false, false, _CO_CONTENT_BLOCKTYPE_CATEGORY_DEFAULT);
        $this->quickInitVar('blocktype_description', XOBJ_DTYPE_TXTBOX);
        $this->quickInitVar('blocktype_icon', XOBJ_DTYPE_IMAGE);
        $this->quickInitVar('blocktype_template', XOBJ_DTYPE_SOURCE, true);
        $this->quickInitVar('blocktype_css', XOBJ_DTYPE_SOURCE);
        $this->quickInitVar('blocktype_accepts', XOBJ_DTYPE_SIMPLE_ARRAY, false, false, false, '');
        $this->quickInitVar('blocktype_root', XOBJ_DTYPE_INT, false, false, false, 1);
        $this->quickInitVar('blocktype_status', XOBJ_DTYPE_INT, false, false, false, 1);
        $this->quickInitVar('blocktype_system', XOBJ_DTYPE_INT, false, false, false, 0);

        $this->initCommonVar('weight');

        $this->setControl('blocktype_icon', 'image');
        $this->setControl('blocktype_template', ['name' => 'source', 'syntax' => 'html', 'height' => '300px']);
        $this->setControl('blocktype_css', ['name' => 'source', 'syntax' => 'css', 'height' => '200px']);
        $this->setControl('blocktype_accepts', ['name' => 'selectmulti', 'itemHandler' => 'blocktype', 'method' => 'getBlocktypeList', 'module' => 'content']);
        $this->setControl('blocktype_root', 'yesno');
        $this->setControl('blocktype_status', 'yesno');
        $this->setControl('blocktype_system', 'yesno');

        $this->makeFieldReadOnly('blocktype_system');
        $this->hideFieldFromSingleView('blocktype_template');
        $this->hideFieldFromSingleView('blocktype_css');
    }

    public function getVar($key, $format = 's')
    {
        if ($format === 's' && in_array($key, ['blocktype_root', 'blocktype_status', 'blocktype_system'], true)) {
            return (int) parent::getVar($key, 'e') === 1 ? _YES : _NO;
        }

        return parent::getVar($key, $format);
    }

    public function getTemplate(): mod_content_BlockTemplate
    {
        if ($this->template === null) {
            $this->template = new mod_content_BlockTemplate((string) $this->getVar('blocktype_template', 'n'));
        }

        return $this->template;
    }

    public function resetTemplate(): void
    {
        $this->template = null;
    }

    public function isActive(): bool
    {
        return (int) $this->getVar('blocktype_status', 'e') === 1;
    }

    public function isAllowedAtRoot(): bool
    {
        return (int) $this->getVar('blocktype_root', 'e') === 1;
    }

    /** @return array<int, string> keys of the block types allowed in the slots, empty when any type is allowed */
    public function getAcceptedKeys(): array
    {
        return array_values(array_filter((array) $this->getVar('blocktype_accepts', 'n'), static fn ($key): bool => $key !== ''));
    }

    public function accepts(mod_content_Blocktype $child): bool
    {
        $accepted = $this->getAcceptedKeys();

        if ($accepted === []) {
            return true;
        }

        return in_array($child->getVar('blocktype_key', 'n'), $accepted, true);
    }

    public function getIconUrl(): string
    {
        $icon = (string) $this->getVar('blocktype_icon', 'n');

        if ($icon === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $icon)) {
            return $icon;
        }

        return $this->handler->getImageUrl() . rawurlencode($icon);
    }

    /**
     * Definition of this block type as used by the page builder
     *
     * @return array<string, mixed>
     */
    public function toBuilderArray(): array
    {
        $template = $this->getTemplate();

        return [
            'id' => (int) $this->getVar('blocktype_id'),
            'key' => $this->getVar('blocktype_key', 'n'),
            'title' => $this->getVar('blocktype_title', 'n'),
            'category' => $this->getVar('blocktype_category', 'n'),
            'description' => $this->getVar('blocktype_description', 'n'),
            'icon' => $this->getIconUrl(),
            'template' => $this->getVar('blocktype_template', 'n'),
            'root' => $this->isAllowedAtRoot(),
            'accepts' => $this->getAcceptedKeys(),
            'zones' => $template->getZones(),
            'slots' => $template->getSlots(),
        ];
    }

    public function getPreviewLink(): string
    {
        $id = $this->getVar('blocktype_id', 'e');
        $title = _AM_CONTENT_BLOCKTYPE_SHOW;
        $image = ICMS_IMAGES_SET_URL . '/actions/viewmag.png';

        return "<a href=\"{$this->handler->_moduleUrl}admin/blocktype.php?op=view&amp;blocktype_id={$id}\" title=\"{$title}\"><img src=\"{$image}\" alt=\"{$title}\" /></a>";
    }

    public function getTitleLink(): string
    {
        $id = $this->getVar('blocktype_id', 'e');
        $title = $this->getVar('blocktype_title');

        return "<a href=\"{$this->handler->_moduleUrl}admin/blocktype.php?op=view&amp;blocktype_id={$id}\">{$title}</a>";
    }
}
