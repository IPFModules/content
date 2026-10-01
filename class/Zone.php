<?php
/**
 * Editable zone of a building block placed on a content page
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		2.0
 * @author		David Janssens (fiammybe)
 * @package		content
 */

defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

class mod_content_Zone extends icms_ipf_Object
{
    private const FILTER_MARKERS = ['<!-- input filtered -->', '<!-- filtered with htmlpurifier -->'];

    public function __construct(&$handler)
    {
        parent::__construct($handler);

        $this->quickInitVar('zone_id', XOBJ_DTYPE_INT, true);
        $this->quickInitVar('zone_blockitem_id', XOBJ_DTYPE_INT, true);
        $this->quickInitVar('zone_key', XOBJ_DTYPE_TXTBOX, true);
        $this->quickInitVar('zone_type', XOBJ_DTYPE_INT, true, false, false, CONTENT_ZONE_TYPE_TEXT);
        $this->quickInitVar('zone_text', XOBJ_DTYPE_TXTAREA);
        $this->quickInitVar('zone_plain', XOBJ_DTYPE_TXTBOX);
        $this->quickInitVar('zone_image', XOBJ_DTYPE_TXTBOX);
        $this->quickInitVar('zone_alt', XOBJ_DTYPE_TXTBOX);
        $this->quickInitVar('zone_url', XOBJ_DTYPE_TXTBOX);
        $this->quickInitVar('zone_target', XOBJ_DTYPE_TXTBOX, false, false, false, '_self');

        $this->initCommonVar('dohtml', false, true);
        $this->initCommonVar('dobr', false, false);

        $this->setControl('zone_text', 'dhtmltextarea');
        $this->setControl('zone_type', ['itemHandler' => 'zone', 'method' => 'getZone_typeArray', 'module' => 'content']);
    }

    /**
     * The purified HTML of a text zone, without the markers the core filter adds on input
     */
    public function getTextHtml(): string
    {
        return trim(str_replace(self::FILTER_MARKERS, '', (string) $this->getVar('zone_text', 'n')));
    }

    public function getImageSrc(): string
    {
        return $this->handler->getImageSrc((string) $this->getVar('zone_image', 'n'));
    }

    /**
     * Value of the zone in the format exchanged with the page builder
     *
     * @return array<string, string>
     */
    public function toBuilderArray(): array
    {
        return match ((int) $this->getVar('zone_type')) {
            CONTENT_ZONE_TYPE_TEXT => ['html' => $this->getTextHtml()],
            CONTENT_ZONE_TYPE_IMAGE => [
                'src' => $this->getImageSrc(),
                'alt' => (string) $this->getVar('zone_alt', 'n'),
            ],
            CONTENT_ZONE_TYPE_LINK => [
                'text' => (string) $this->getVar('zone_plain', 'n'),
                'href' => (string) $this->getVar('zone_url', 'n'),
                'target' => (string) $this->getVar('zone_target', 'n'),
            ],
            default => ['text' => (string) $this->getVar('zone_plain', 'n')],
        };
    }
}
