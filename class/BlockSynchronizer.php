<?php
/**
 * Stores the building block tree sent by the page builder as building block and zone objects
 *
 * The builder sends a normalized tree, each node being:
 * {id?: int, type: string, slot?: string, zones: {key: {...}}, children: [...]}
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		2.0
 * @author		David Janssens (fiammybe)
 * @package		content
 */

defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

class mod_content_BlockSynchronizer
{
    private const MAX_DEPTH = 20;

    private const MAX_NODES = 1000;

    private const MAX_TEXT_LENGTH = 60000;

    private int $contentId = 0;

    private int $nodeCount = 0;

    /** @var array<int, mod_content_Blockitem> */
    private array $existingItems = [];

    /** @var array<int, array<string, mod_content_Zone>> */
    private array $existingZones = [];

    /** @var array<string, mod_content_Blocktype> */
    private array $blocktypes = [];

    /** @var array<int, bool> */
    private array $seenIds = [];

    /** @var array<int, string> */
    private array $errors = [];

    public function __construct(
        private mod_content_ContentHandler $contentHandler,
        private mod_content_BlocktypeHandler $blocktypeHandler,
        private mod_content_BlockitemHandler $blockitemHandler,
        private mod_content_ZoneHandler $zoneHandler,
    ) {
    }

    public static function create(): self
    {
        $moduleDir = basename(dirname(__FILE__, 2));

        return new self(
            icms_getModuleHandler('content', $moduleDir, 'content'),
            icms_getModuleHandler('blocktype', $moduleDir, 'content'),
            icms_getModuleHandler('blockitem', $moduleDir, 'content'),
            icms_getModuleHandler('zone', $moduleDir, 'content'),
        );
    }

    /**
     * The building blocks of a page in the format sent to the page builder
     *
     * @return array<int, array<string, mixed>>
     */
    public function export(int $contentId): array
    {
        $items = $this->blockitemHandler->getItemsForContent($contentId);
        $tree = new icms_ipf_Tree($items, 'blockitem_id', 'blockitem_pid');
        $zones = $this->zoneHandler->getZonesForBlockitems(array_keys($items));
        $blocktypes = $this->blocktypeHandler->getAllById();

        return $this->exportChildren($tree, $zones, $blocktypes, 0);
    }

    /**
     * @param array<int, mixed> $nodes
     * @return array<int, string> validation errors, empty when the tree was stored
     */
    public function sync(mod_content_Content $content, array $nodes): array
    {
        $this->contentId = (int) $content->getVar('content_id');
        $this->nodeCount = 0;
        $this->seenIds = [];
        $this->errors = [];
        $this->existingItems = $this->blockitemHandler->getItemsForContent($this->contentId);
        $this->existingZones = $this->zoneHandler->getZonesForBlockitems(array_keys($this->existingItems));
        $this->blocktypes = $this->blocktypeHandler->getAllByKey();

        $this->validateNodes($nodes, null, 1);

        if ($this->errors !== []) {
            return array_values(array_unique($this->errors));
        }

        $keptIds = $this->storeNodes($nodes, 0);

        foreach (array_diff(array_keys($this->existingItems), $keptIds) as $removedId) {
            $item = $this->blockitemHandler->get($removedId);

            if ($item->isNew()) {
                continue;
            }

            $this->blockitemHandler->delete($item, true);
        }

        $this->contentHandler->renderBlocks($content);

        return $this->errors;
    }

    /**
     * @param array<int, array<string, mod_content_Zone>> $zones
     * @param array<int, mod_content_Blocktype> $blocktypes
     * @return array<int, array<string, mixed>>
     */
    private function exportChildren(icms_ipf_Tree $tree, array $zones, array $blocktypes, int $pid): array
    {
        $nodes = [];

        foreach ($tree->getFirstChild($pid) as $item) {
            $id = (int) $item->getVar('blockitem_id');
            $blocktype = $blocktypes[(int) $item->getVar('blockitem_blocktype_id')] ?? null;

            if ($blocktype === null) {
                continue;
            }

            $nodes[] = [
                'id' => $id,
                'type' => $blocktype->getVar('blocktype_key', 'n'),
                'slot' => $item->getVar('blockitem_slot', 'n'),
                'zones' => array_map(static fn (mod_content_Zone $zone): array => $zone->toBuilderArray(), $zones[$id] ?? []),
                'children' => $this->exportChildren($tree, $zones, $blocktypes, $id),
            ];
        }

        return $nodes;
    }

    /** @param array<int, mixed> $nodes */
    private function validateNodes(array $nodes, ?mod_content_Blocktype $parent, int $depth): void
    {
        if ($depth > self::MAX_DEPTH) {
            $this->errors[] = _AM_CONTENT_BUILDER_ERR_DEPTH;

            return;
        }

        foreach ($nodes as $node) {
            $this->validateNode($node, $parent, $depth);
        }
    }

    private function validateNode(mixed $node, ?mod_content_Blocktype $parent, int $depth): void
    {
        if (++$this->nodeCount > self::MAX_NODES) {
            $this->errors[] = _AM_CONTENT_BUILDER_ERR_SIZE;

            return;
        }

        if (!is_array($node) || !is_string($node['type'] ?? null)) {
            $this->errors[] = _AM_CONTENT_BUILDER_ERR_INVALID;

            return;
        }

        $blocktype = $this->blocktypes[$node['type']] ?? null;

        if ($blocktype === null || !$blocktype->isActive()) {
            $this->errors[] = sprintf(_AM_CONTENT_BUILDER_ERR_TYPE, $node['type']);

            return;
        }

        $title = $blocktype->getVar('blocktype_title', 'n');

        if ($parent === null && !$blocktype->isAllowedAtRoot()) {
            $this->errors[] = sprintf(_AM_CONTENT_BUILDER_ERR_ROOT, $title);
        }

        if ($parent !== null && !$parent->accepts($blocktype)) {
            $this->errors[] = sprintf(_AM_CONTENT_BUILDER_ERR_ACCEPTS, $title, $parent->getVar('blocktype_title', 'n'));
        }

        if ($parent !== null && !in_array($node['slot'] ?? null, $parent->getTemplate()->getSlots(), true)) {
            $this->errors[] = sprintf(_AM_CONTENT_BUILDER_ERR_SLOT, $title);
        }

        $id = (int) ($node['id'] ?? 0);

        if ($id > 0 && (!isset($this->existingItems[$id]) || isset($this->seenIds[$id]))) {
            $this->errors[] = _AM_CONTENT_BUILDER_ERR_INVALID;
        }

        $this->seenIds[$id] = true;

        foreach ($blocktype->getTemplate()->getZones() as $key => $zone) {
            $this->validateZone($node['zones'][$key] ?? [], $zone['type'], $title);
        }

        $children = $node['children'] ?? [];

        if (!is_array($children)) {
            $this->errors[] = _AM_CONTENT_BUILDER_ERR_INVALID;

            return;
        }

        $this->validateNodes($children, $blocktype, $depth + 1);
    }

    private function validateZone(mixed $value, string $type, string $title): void
    {
        if (!is_array($value)) {
            $this->errors[] = _AM_CONTENT_BUILDER_ERR_INVALID;

            return;
        }

        foreach ($value as $property) {
            if (!is_string($property)) {
                $this->errors[] = _AM_CONTENT_BUILDER_ERR_INVALID;

                return;
            }
        }

        if ($type === 'text' && strlen($value['html'] ?? '') > self::MAX_TEXT_LENGTH) {
            $this->errors[] = sprintf(_AM_CONTENT_BUILDER_ERR_TEXT_LENGTH, $title);
        }
    }

    /**
     * @param array<int, array<string, mixed>> $nodes
     * @return array<int, int> ids of the stored building blocks
     */
    private function storeNodes(array $nodes, int $pid): array
    {
        $storedIds = [];

        foreach (array_values($nodes) as $weight => $node) {
            $blocktype = $this->blocktypes[$node['type']];
            $id = (int) ($node['id'] ?? 0);

            $item = $this->existingItems[$id] ?? $this->blockitemHandler->create();
            $item->setVar('blockitem_content_id', $this->contentId);
            $item->setVar('blockitem_pid', $pid);
            $item->setVar('blockitem_slot', $pid > 0 ? (string) $node['slot'] : '');
            $item->setVar('blockitem_blocktype_id', $blocktype->getVar('blocktype_id'));
            $item->setVar('weight', $weight);

            if (!$this->blockitemHandler->insert($item, true)) {
                $this->errors[] = sprintf(_AM_CONTENT_BUILDER_ERR_STORE, $blocktype->getVar('blocktype_title', 'n'));

                continue;
            }

            $id = (int) $item->getVar('blockitem_id');
            $storedIds[] = $id;

            $this->storeZones($id, $blocktype, $node['zones'] ?? []);

            array_push($storedIds, ...$this->storeNodes($node['children'] ?? [], $id));
        }

        return $storedIds;
    }

    /** @param array<string, array<string, string>> $values */
    private function storeZones(int $itemId, mod_content_Blocktype $blocktype, array $values): void
    {
        $definitions = $blocktype->getTemplate()->getZones();
        $existing = $this->existingZones[$itemId] ?? [];

        foreach ($existing as $key => $zone) {
            if (isset($definitions[$key])) {
                continue;
            }

            $this->zoneHandler->delete($zone, true);
        }

        foreach ($definitions as $key => $definition) {
            $zone = $existing[$key] ?? $this->zoneHandler->create();
            $zone->setVar('zone_blockitem_id', $itemId);
            $zone->setVar('zone_key', $key);
            $zone->setVar('zone_type', mod_content_ZoneHandler::TYPES[$definition['type']]);

            $this->assignZoneValue($zone, $definition['type'], $values[$key] ?? []);

            $this->zoneHandler->insert($zone, true);
        }
    }

    /** @param array<string, string> $value */
    private function assignZoneValue(mod_content_Zone $zone, string $type, array $value): void
    {
        switch ($type) {
            case 'text':
                $zone->setVar('zone_text', $value['html'] ?? '');
                break;

            case 'image':
                $zone->setVar('zone_image', $this->zoneHandler->normalizeImageValue($value['src'] ?? ''));
                $zone->setVar('zone_alt', $this->plainText($value['alt'] ?? ''));
                break;

            case 'link':
                $zone->setVar('zone_plain', $this->plainText($value['text'] ?? ''));
                $zone->setVar('zone_url', $this->sanitizeHref($value['href'] ?? ''));
                $zone->setVar('zone_target', in_array($value['target'] ?? '', mod_content_ZoneHandler::LINK_TARGETS, true) ? $value['target'] : '_self');
                break;

            default:
                $zone->setVar('zone_plain', $this->plainText($value['text'] ?? ''));
                break;
        }
    }

    /**
     * Plain zones are always rendered as escaped text, so only whitespace and length are normalized here
     */
    private function plainText(string $text): string
    {
        return mb_substr(trim(preg_replace('/\s+/u', ' ', $text) ?? ''), 0, 255);
    }

    /**
     * Allows absolute http(s) URLs, mailto/tel links, site relative paths and anchors
     */
    private function sanitizeHref(string $href): string
    {
        $href = trim($href);

        if ($href === '' || strlen($href) > 255) {
            return '';
        }

        if (preg_match('#^(https?://|mailto:|tel:)#i', $href)) {
            return filter_var($href, FILTER_SANITIZE_URL);
        }

        if (preg_match('#^(/(?!/)|\#|\?)#', $href)) {
            return filter_var($href, FILTER_SANITIZE_URL);
        }

        return '';
    }
}
