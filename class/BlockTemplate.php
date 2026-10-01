<?php
/**
 * Parser for the markup of a building block type
 *
 * A block type template is a single HTML element. Inside it:
 * - data-zone="key" data-zone-type="text|plain|image|link" [data-zone-label="..."] marks an editable zone
 * - data-slot="name" marks an area where nested building blocks can be dropped
 *
 * @copyright	The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		2.0
 * @author		David Janssens (fiammybe)
 * @package		content
 */

defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

class mod_content_BlockTemplate
{
    public const ZONE_TYPES = ['text', 'plain', 'image', 'link'];

    private const NAME_PATTERN = '/^[a-z0-9][a-z0-9_-]*$/';

    private DOMDocument $document;

    private ?DOMElement $root = null;

    /** @var array<string, array{type: string, label: string}> */
    private array $zones = [];

    /** @var array<int, string> */
    private array $slots = [];

    /** @var array<int, string> */
    private array $errors = [];

    public function __construct(string $markup)
    {
        $this->document = self::createDocument($markup);
        $this->parse();
    }

    /**
     * Loads an HTML fragment as UTF-8 into a document; the fragment's nodes are the children of <body>
     */
    public static function createDocument(string $html): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);

        $document->loadHTML(
            "<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body>{$html}</body></html>"
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $document;
    }

    public static function getBody(DOMDocument $document): DOMElement
    {
        return $document->getElementsByTagName('body')->item(0);
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    /** @return array<int, string> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /** @return array<string, array{type: string, label: string}> */
    public function getZones(): array
    {
        return $this->zones;
    }

    /** @return array<int, string> */
    public function getSlots(): array
    {
        return $this->slots;
    }

    public function getDocument(): DOMDocument
    {
        return $this->document;
    }

    public function getRoot(): ?DOMElement
    {
        return $this->root;
    }

    private function parse(): void
    {
        $elements = [];

        foreach (self::getBody($this->document)->childNodes as $node) {
            if ($node instanceof DOMElement) {
                $elements[] = $node;

                continue;
            }

            if ($node instanceof DOMText && trim($node->textContent) !== '') {
                $this->errors[] = _CO_CONTENT_BLOCKTYPE_ERR_SINGLE_ROOT;

                return;
            }
        }

        if (count($elements) !== 1) {
            $this->errors[] = _CO_CONTENT_BLOCKTYPE_ERR_SINGLE_ROOT;

            return;
        }

        $this->root = $elements[0];

        $xpath = new DOMXPath($this->document);

        foreach ($xpath->query('descendant-or-self::*[@data-zone]', $this->root) as $element) {
            $this->parseZone($element);
        }

        foreach ($xpath->query('descendant-or-self::*[@data-slot]', $this->root) as $element) {
            $this->parseSlot($element);
        }
    }

    private function parseZone(DOMElement $element): void
    {
        $key = $element->getAttribute('data-zone');
        $type = $element->getAttribute('data-zone-type') ?: 'text';

        if (!preg_match(self::NAME_PATTERN, $key)) {
            $this->errors[] = sprintf(_CO_CONTENT_BLOCKTYPE_ERR_ZONE_NAME, htmlspecialchars($key, ENT_QUOTES));

            return;
        }

        if (isset($this->zones[$key])) {
            $this->errors[] = sprintf(_CO_CONTENT_BLOCKTYPE_ERR_ZONE_DUPLICATE, $key);

            return;
        }

        if (!in_array($type, self::ZONE_TYPES, true)) {
            $this->errors[] = sprintf(_CO_CONTENT_BLOCKTYPE_ERR_ZONE_TYPE, $key, htmlspecialchars($type, ENT_QUOTES));

            return;
        }

        if ($type === 'image' && $element->tagName !== 'img') {
            $this->errors[] = sprintf(_CO_CONTENT_BLOCKTYPE_ERR_ZONE_TAG, $key, 'img');

            return;
        }

        if ($type === 'link' && $element->tagName !== 'a') {
            $this->errors[] = sprintf(_CO_CONTENT_BLOCKTYPE_ERR_ZONE_TAG, $key, 'a');

            return;
        }

        if ($this->hasMarkedAncestor($element, ['data-zone', 'data-slot'])) {
            $this->errors[] = sprintf(_CO_CONTENT_BLOCKTYPE_ERR_ZONE_NESTED, $key);

            return;
        }

        $this->zones[$key] = [
            'type' => $type,
            'label' => $element->getAttribute('data-zone-label') ?: $key,
        ];
    }

    private function parseSlot(DOMElement $element): void
    {
        $name = $element->getAttribute('data-slot');

        if (!preg_match(self::NAME_PATTERN, $name)) {
            $this->errors[] = sprintf(_CO_CONTENT_BLOCKTYPE_ERR_SLOT_NAME, htmlspecialchars($name, ENT_QUOTES));

            return;
        }

        if (in_array($name, $this->slots, true)) {
            $this->errors[] = sprintf(_CO_CONTENT_BLOCKTYPE_ERR_SLOT_DUPLICATE, $name);

            return;
        }

        if ($element->hasAttribute('data-zone') || $this->hasMarkedAncestor($element, ['data-zone', 'data-slot'])) {
            $this->errors[] = sprintf(_CO_CONTENT_BLOCKTYPE_ERR_SLOT_NESTED, $name);

            return;
        }

        $this->slots[] = $name;
    }

    /** @param array<int, string> $attributes */
    private function hasMarkedAncestor(DOMElement $element, array $attributes): bool
    {
        for ($parent = $element->parentNode; $parent instanceof DOMElement && $parent !== $this->root->parentNode; $parent = $parent->parentNode) {
            foreach ($attributes as $attribute) {
                if ($parent->hasAttribute($attribute)) {
                    return true;
                }
            }
        }

        return false;
    }
}
