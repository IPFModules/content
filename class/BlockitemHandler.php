<?php
/**
 * Handler for the building blocks placed on a content page
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		2.0
 * @author		David Janssens (fiammybe)
 * @package		content
 */

defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

class mod_content_BlockitemHandler extends icms_ipf_Handler
{
    public string $parentName = 'blockitem_pid';

    public function __construct(&$db)
    {
        parent::__construct($db, 'blockitem', 'blockitem_id', 'blockitem_slot', 'blockitem_slot', 'content');

        icms_loadLanguageFile(basename(dirname(__FILE__, 2)), 'common');
    }

    /** @return array<int, mod_content_Blockitem> */
    public function getItemsForContent(int $contentId): array
    {
        $criteria = new icms_db_criteria_Compo(new icms_db_criteria_Item('blockitem_content_id', $contentId));
        $criteria->setSort('weight');
        $criteria->setOrder('ASC');

        return $this->getObjects($criteria, true);
    }

    /**
     * All building blocks of a page as a tree. Items are loaded by weight because icms_ipf_Tree keeps the input order
     */
    public function getTree(int $contentId): icms_ipf_Tree
    {
        $items = $this->getItemsForContent($contentId);

        return new icms_ipf_Tree($items, 'blockitem_id', 'blockitem_pid');
    }

    /** @return array<int, int> */
    public function getContentIdsByBlocktype(int $blocktypeId): array
    {
        $criteria = new icms_db_criteria_Item('blockitem_blocktype_id', $blocktypeId);

        $contentIds = array_map(
            static fn (mod_content_Blockitem $item): int => (int) $item->getVar('blockitem_content_id'),
            $this->getObjects($criteria)
        );

        return array_values(array_unique($contentIds));
    }

    public function deleteForContent(int $contentId): void
    {
        $criteria = new icms_db_criteria_Compo(new icms_db_criteria_Item('blockitem_content_id', $contentId));
        $criteria->add(new icms_db_criteria_Item('blockitem_pid', 0));

        $this->deleteAll($criteria);
    }

    /**
     * Copies the building blocks and their zones of a page to another page
     */
    public function cloneTree(int $fromContentId, int $toContentId): void
    {
        $tree = $this->getTree($fromContentId);
        $zoneHandler = icms_getModuleHandler('zone', basename(dirname(__FILE__, 2)), 'content');

        $this->cloneChildren($tree, $zoneHandler, 0, 0, $toContentId);
    }

    private function cloneChildren(icms_ipf_Tree $tree, mod_content_ZoneHandler $zoneHandler, int $fromPid, int $toPid, int $toContentId): void
    {
        foreach ($tree->getFirstChild($fromPid) as $item) {
            $copy = $this->create();
            $copy->setVar('blockitem_content_id', $toContentId);
            $copy->setVar('blockitem_pid', $toPid);
            $copy->setVar('blockitem_slot', $item->getVar('blockitem_slot', 'n'));
            $copy->setVar('blockitem_blocktype_id', $item->getVar('blockitem_blocktype_id'));
            $copy->setVar('weight', $item->getVar('weight'));

            if (!$this->insert($copy, true)) {
                continue;
            }

            $zones = $zoneHandler->getZonesForBlockitems([(int) $item->getVar('blockitem_id')]);

            foreach ($zones[(int) $item->getVar('blockitem_id')] ?? [] as $zone) {
                $zoneCopy = $zoneHandler->create();
                $zoneCopy->setVars($zone->getValues(null, 'n'), true);
                $zoneCopy->setVar('zone_id', 0);
                $zoneCopy->setVar('zone_blockitem_id', $copy->getVar('blockitem_id'));
                $zoneHandler->insert($zoneCopy, true);
            }

            $this->cloneChildren($tree, $zoneHandler, (int) $item->getVar('blockitem_id'), (int) $copy->getVar('blockitem_id'), $toContentId);
        }
    }

    /**
     * Removes the zones and the nested building blocks of a deleted building block.
     * deleteAll() deletes object per object, so this event cascades down the tree
     */
    protected function afterDelete(&$obj): bool
    {
        $id = (int) $obj->getVar('blockitem_id');

        icms_getModuleHandler('zone', basename(dirname(__FILE__, 2)), 'content')
            ->deleteAll(new icms_db_criteria_Item('zone_blockitem_id', $id));

        $this->deleteAll(new icms_db_criteria_Item('blockitem_pid', $id));

        return true;
    }
}
