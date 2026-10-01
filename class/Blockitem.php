<?php
/**
 * Building block placed on a content page
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		2.0
 * @author		David Janssens (fiammybe)
 * @package		content
 */

defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

class mod_content_Blockitem extends icms_ipf_Object
{
    public function __construct(&$handler)
    {
        parent::__construct($handler);

        $this->quickInitVar('blockitem_id', XOBJ_DTYPE_INT, true);
        $this->quickInitVar('blockitem_content_id', XOBJ_DTYPE_INT, true);
        $this->quickInitVar('blockitem_pid', XOBJ_DTYPE_INT, false, false, false, 0);
        $this->quickInitVar('blockitem_slot', XOBJ_DTYPE_TXTBOX);
        $this->quickInitVar('blockitem_blocktype_id', XOBJ_DTYPE_INT, true);

        $this->initCommonVar('weight');

        $this->setControl('blockitem_content_id', ['itemHandler' => 'content', 'method' => 'getContentList', 'module' => 'content']);
        $this->setControl('blockitem_blocktype_id', ['itemHandler' => 'blocktype', 'method' => 'getList', 'module' => 'content']);
    }
}
