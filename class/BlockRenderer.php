<?php
/**
 * Renders the building blocks of a content page to HTML
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		2.0
 * @author		David Janssens (fiammybe)
 * @package		content
 */

defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

class mod_content_BlockRenderer
{
    private const BUILDER_ATTRIBUTES = ['data-zone', 'data-zone-type', 'data-zone-label', 'data-slot'];

    private DOMDocument $output;

    private icms_ipf_Tree $tree;

    /** @var array<int, mod_content_Blocktype> */
    private array $blocktypes = [];

    /** @var array<int, array<string, mod_content_Zone>> */
    private array $zones = [];

    /** @var array<int, string> */
    private array $usedCss = [];

    public function __construct(
        private mod_content_BlocktypeHandler $blocktypeHandler,
        private mod_content_BlockitemHandler $blockitemHandler,
        private mod_content_ZoneHandler $zoneHandler,
    ) {
    }

    public static function create(): self
    {
        $moduleDir = basename(dirname(__FILE__, 2));

        return new self(
            icms_getModuleHandler('blocktype', $moduleDir, 'content'),
            icms_getModuleHandler('blockitem', $moduleDir, 'content'),
            icms_getModuleHandler('zone', $moduleDir, 'content'),
        );
    }

    /**
     * @return array{
     *     html: string,
     *     css: string
     * }
     */
    public function renderContent(int $contentId): array
    {
        $items = $this->blockitemHandler->getItemsForContent($contentId);

        return $this->render(
            new icms_ipf_Tree($items, 'blockitem_id', 'blockitem_pid'),
            $this->zoneHandler->getZonesForBlockitems(array_keys($items))
        );
    }

    /**
     * Renders a single block type with its template content, used as a preview in the admin
     *
     * @return array{
     *     html: string,
     *     css: string
     * }
     */
    public function renderBlocktype(mod_content_Blocktype $blocktype): array
    {
        $item = $this->blockitemHandler->create();
        $item->setVar('blockitem_id', -1);
        $item->setVar('blockitem_pid', 0);
        $item->setVar('blockitem_blocktype_id', $blocktype->getVar('blocktype_id'));

        $items = [-1 => $item];

        $this->blocktypes = [(int) $blocktype->getVar('blocktype_id') => $blocktype];

        return $this->render(new icms_ipf_Tree($items, 'blockitem_id', 'blockitem_pid'), []);
    }

    /**
     * @param array<int, array<string, mod_content_Zone>> $zones
     * @return array{
     *     html: string,
     *     css: string
     * }
     */
    private function render(icms_ipf_Tree $tree, array $zones): array
    {
        $this->tree = $tree;
        $this->zones = $zones;
        $this->usedCss = [];
        $this->blocktypes = $this->blocktypes ?: $this->blocktypeHandler->getAllById();
        $this->output = mod_content_BlockTemplate::createDocument('');

        $body = mod_content_BlockTemplate::getBody($this->output);

        foreach ($this->tree->getFirstChild(0) as $item) {
            $element = $this->renderItem($item);

            if ($element !== null) {
                $body->appendChild($element);
            }
        }

        $html = '';

        foreach ($body->childNodes as $node) {
            $html .= $this->output->saveHTML($node);
        }

        return [
            'html' => $html,
            'css' => implode("\n", $this->usedCss),
        ];
    }

    private function renderItem(mod_content_Blockitem $item): ?DOMElement
    {
        $blocktype = $this->blocktypes[(int) $item->getVar('blockitem_blocktype_id')] ?? null;

        if ($blocktype === null) {
            return null;
        }

        if (!$blocktype->isActive()) {
            return null;
        }

        $template = new mod_content_BlockTemplate((string) $blocktype->getVar('blocktype_template', 'n'));

        if (!$template->isValid()) {
            return null;
        }

        $key = $blocktype->getVar('blocktype_key', 'n');
        $this->usedCss[$key] ??= (string) $blocktype->getVar('blocktype_css', 'n');

        $root = $this->output->importNode($template->getRoot(), true);
        $root->setAttribute('class', trim("{$root->getAttribute('class')} content-block content-block-{$key}"));

        $itemId = (int) $item->getVar('blockitem_id');
        $xpath = new DOMXPath($this->output);

        foreach (iterator_to_array($xpath->query('descendant-or-self::*[@data-zone]', $root)) as $element) {
            $zone = $this->zones[$itemId][$element->getAttribute('data-zone')] ?? null;

            if ($zone !== null) {
                $this->fillZone($element, $zone);
            }
        }

        foreach (iterator_to_array($xpath->query('descendant-or-self::img[@data-zone][not(@src) or @src=""]', $root)) as $emptyImage) {
            $emptyImage->parentNode?->removeChild($emptyImage);
        }

        $slots = iterator_to_array($xpath->query('descendant-or-self::*[@data-slot]', $root));

        foreach ($slots as $slot) {
            $this->fillSlot($slot, $itemId);
        }

        foreach (iterator_to_array($xpath->query('descendant-or-self::*', $root)) as $element) {
            foreach (self::BUILDER_ATTRIBUTES as $attribute) {
                $element->removeAttribute($attribute);
            }
        }

        return $root;
    }

    private function fillZone(DOMElement $element, mod_content_Zone $zone): void
    {
        switch ((int) $zone->getVar('zone_type')) {
            case CONTENT_ZONE_TYPE_TEXT:
                $this->replaceChildren($element);
                $this->appendHtml($element, $zone->getTextHtml());
                break;

            case CONTENT_ZONE_TYPE_IMAGE:
                $src = $zone->getImageSrc();

                if ($src === '') {
                    $element->parentNode?->removeChild($element);
                    break;
                }

                $element->setAttribute('src', $src);
                $element->setAttribute('alt', (string) $zone->getVar('zone_alt', 'n'));
                break;

            case CONTENT_ZONE_TYPE_LINK:
                $element->textContent = (string) $zone->getVar('zone_plain', 'n');
                $element->setAttribute('href', (string) $zone->getVar('zone_url', 'n') ?: '#');
                $this->setLinkTarget($element, (string) $zone->getVar('zone_target', 'n'));
                break;

            default:
                $element->textContent = (string) $zone->getVar('zone_plain', 'n');
                break;
        }
    }

    private function setLinkTarget(DOMElement $element, string $target): void
    {
        if ($target !== '_blank') {
            $element->removeAttribute('target');

            return;
        }

        $element->setAttribute('target', '_blank');
        $element->setAttribute('rel', 'noopener');
    }

    private function fillSlot(DOMElement $slot, int $itemId): void
    {
        $name = $slot->getAttribute('data-slot');

        $this->replaceChildren($slot);

        foreach ($this->tree->getFirstChild($itemId) as $child) {
            if ($child->getVar('blockitem_slot', 'n') !== $name) {
                continue;
            }

            $element = $this->renderItem($child);

            if ($element !== null) {
                $slot->appendChild($element);
            }
        }
    }

    private function replaceChildren(DOMElement $element): void
    {
        while ($element->firstChild !== null) {
            $element->removeChild($element->firstChild);
        }
    }

    private function appendHtml(DOMElement $element, string $html): void
    {
        if ($html === '') {
            return;
        }

        $fragment = mod_content_BlockTemplate::createDocument($html);

        foreach (mod_content_BlockTemplate::getBody($fragment)->childNodes as $node) {
            $element->appendChild($this->output->importNode($node, true));
        }
    }
}
