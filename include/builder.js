/**
 * Page builder of the content module, based on GrapesJS
 *
 * The server sends the building block types and the building block tree of the page. The tree is
 * turned into HTML from the block type templates and loaded in GrapesJS. On save, the GrapesJS
 * components are turned back into the same tree format and posted to the server.
 *
 * Tree node: {id?: number, type: string, slot?: string, zones: {key: {...}}, children: [node]}
 */
(function () {
    'use strict';

    const BLOCK = 'icms-block';
    const SLOT = 'icms-slot';
    const ZONE_TEXT = 'icms-zone-text';
    const ZONE_PLAIN = 'icms-zone-plain';
    const ZONE_IMAGE = 'icms-zone-image';
    const ZONE_LINK = 'icms-zone-link';
    const ZONE_TYPES = [ZONE_TEXT, ZONE_PLAIN, ZONE_IMAGE, ZONE_LINK];
    const NO_TOOLBAR_TYPES = [ZONE_PLAIN, ZONE_LINK];

    const CANVAS_CSS = `
        body { margin: 0; padding: 1rem; }
        [data-slot] { min-height: 3rem; outline: 1px dashed #9ca3af; outline-offset: -2px; }
        [data-slot]:empty::before { color: #9ca3af; content: attr(data-slot); display: block; font: 12px sans-serif; padding: 1rem; text-align: center; }
        [data-blocktype] { min-height: 1rem; }
    `;

    function zoneType(el) {
        if (!el.hasAttribute || !el.hasAttribute('data-zone')) {
            return null;
        }

        return el.getAttribute('data-zone-type') || 'text';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;

        return div.innerHTML;
    }

    function textOf(component) {
        const div = document.createElement('div');
        div.innerHTML = component.getInnerHTML();

        return div.textContent.replace(/\s+/g, ' ').trim();
    }

    function blocktypeKey(component) {
        return component.getAttributes()['data-blocktype'];
    }

    function blockitemId(component) {
        return parseInt(component.getAttributes()['data-blockitem-id'] || '0', 10);
    }

    class PageBuilder {
        constructor(config) {
            this.config = config;
            this.blocktypes = config.blocktypes;
            this.token = config.token;
            this.saving = false;
            this.status = document.getElementById('content-builder-status');
        }

        start() {
            this.editor = grapesjs.init({
                container: '#content-builder-canvas',
                height: '100%',
                fromElement: false,
                storageManager: false,
                showDevices: false,
                panels: { defaults: [] },
                protectedCss: `* { box-sizing: border-box; } ${CANVAS_CSS} ${this.config.canvasCss}`,
                canvas: { styles: this.config.canvasStyles },
                deviceManager: {
                    devices: [
                        { id: 'desktop', name: 'Desktop', width: '' },
                        { id: 'tablet', name: 'Tablet', width: '768px', widthMedia: '992px' },
                        { id: 'mobile', name: 'Mobile', width: '375px', widthMedia: '480px' },
                    ],
                },
                blockManager: { appendTo: '#content-builder-blocks' },
                layerManager: { appendTo: '#content-builder-layers' },
                traitManager: { appendTo: '#content-builder-traits' },
                selectorManager: { componentFirst: true },
                plugins: [(editor) => this.registerTypes(editor)],
            });

            this.registerBlocks();
            this.registerImageManager();
            this.bindEvents();

            this.editor.setComponents(this.config.tree.map((node) => this.blockHtml(node)).join(''));
            this.editor.getWrapper().set({ droppable: (source, target) => this.canPlace(source, target) });
            this.editor.UndoManager.clear();
            this.editor.clearDirtyCount();
            this.updateStatus();
        }

        registerTypes(editor) {
            const builder = this;
            const zoneDefaults = {
                draggable: false,
                droppable: false,
                removable: false,
                copyable: false,
                stylable: false,
                locked: false,
            };
            const zoneInit = function () {
                const attributes = this.getAttributes();
                this.set('name', attributes['data-zone-label'] || attributes['data-zone']);
            };

            editor.DomComponents.addType(BLOCK, {
                isComponent: (el) => el.hasAttribute && el.hasAttribute('data-blocktype'),
                model: {
                    defaults: {
                        draggable: (source, target) => builder.canPlace(source, target),
                        droppable: false,
                        stylable: false,
                        locked: false,
                        traits: [],
                    },
                    init() {
                        const blocktype = builder.blocktypes[blocktypeKey(this)];
                        this.set('name', blocktype ? blocktype.title : blocktypeKey(this));
                        builder.lockTemplate(this);
                    },
                },
            });

            editor.DomComponents.addType(SLOT, {
                isComponent: (el) => el.hasAttribute && el.hasAttribute('data-slot'),
                model: {
                    defaults: {
                        draggable: false,
                        droppable: (source, target) => builder.canPlace(source, target),
                        removable: false,
                        copyable: false,
                        selectable: false,
                        stylable: false,
                        locked: false,
                        traits: [],
                    },
                    init() {
                        this.set('name', this.getAttributes()['data-slot']);
                    },
                },
            });

            editor.DomComponents.addType(ZONE_TEXT, {
                extend: 'text',
                isComponent: (el) => zoneType(el) === 'text',
                model: { defaults: { ...zoneDefaults, editable: true, traits: [] }, init: zoneInit },
            });

            editor.DomComponents.addType(ZONE_PLAIN, {
                extend: 'text',
                isComponent: (el) => zoneType(el) === 'plain',
                model: { defaults: { ...zoneDefaults, editable: true, traits: [] }, init: zoneInit },
            });

            editor.DomComponents.addType(ZONE_IMAGE, {
                extend: 'image',
                isComponent: (el) => el.tagName === 'IMG' && zoneType(el) === 'image',
                model: {
                    defaults: {
                        ...zoneDefaults,
                        resizable: false,
                        traits: [
                            {
                                type: 'button',
                                full: true,
                                text: this.config.labels.chooseImage,
                                command: () => this.openImageManager(this.editor.getSelected()),
                            },
                            { type: 'text', name: 'alt', label: this.config.labels.alt },
                        ],
                    },
                    init: zoneInit,
                },
            });

            editor.DomComponents.addType(ZONE_LINK, {
                extend: 'link',
                isComponent: (el) => el.tagName === 'A' && zoneType(el) === 'link',
                model: {
                    defaults: {
                        ...zoneDefaults,
                        editable: true,
                        traits: [
                            { type: 'text', name: 'href', label: this.config.labels.href },
                            {
                                type: 'select',
                                name: 'target',
                                label: this.config.labels.target,
                                options: [
                                    { id: '_self', label: this.config.labels.targetSelf },
                                    { id: '_blank', label: this.config.labels.targetBlank },
                                ],
                            },
                        ],
                    },
                    init: zoneInit,
                },
            });
        }

        registerBlocks() {
            Object.values(this.blocktypes).forEach((blocktype) => {
                const media = blocktype.icon
                    ? `<img src="${escapeHtml(blocktype.icon)}" alt="" class="content-builder__block-icon">`
                    : '<svg viewBox="0 0 24 24" width="40" height="40"><path fill="currentColor" d="M3 3h8v8H3zm10 0h8v8h-8zM3 13h8v8H3zm10 0h8v8h-8z"/></svg>';

                this.editor.BlockManager.add(blocktype.key, {
                    label: escapeHtml(blocktype.title),
                    category: blocktype.category,
                    attributes: { title: blocktype.description || blocktype.title },
                    media,
                    content: this.blockHtml({ type: blocktype.key, zones: {}, children: [] }),
                    select: true,
                });
            });
        }

        bindEvents() {
            const editor = this.editor;

            editor.on('update', () => this.updateStatus());

            editor.on('component:selected', (component) => {
                if (component.getTraits().length > 0) {
                    this.showPane('traits');
                }
            });

            editor.on('rte:enable', (view) => {
                const toolbar = editor.RichTextEditor.getToolbarEl();

                if (toolbar) {
                    toolbar.style.display = NO_TOOLBAR_TYPES.includes(view.model.get('type')) ? 'none' : '';
                }
            });

            editor.on('canvas:frame:load', ({ window }) => {
                window.document.addEventListener('keydown', (event) => {
                    const selected = editor.getSelected();

                    if (event.key === 'Enter' && selected && NO_TOOLBAR_TYPES.includes(selected.get('type'))) {
                        event.preventDefault();
                    }
                }, true);
            });

            document.querySelectorAll('[data-builder-pane]').forEach((button) => {
                button.addEventListener('click', () => this.showPane(button.dataset.builderPane));
            });

            document.querySelectorAll('[data-builder-device]').forEach((button) => {
                button.addEventListener('click', () => editor.setDevice(button.dataset.builderDevice));
            });

            document.querySelectorAll('[data-builder-command]').forEach((button) => {
                button.addEventListener('click', () => this.runCommand(button.dataset.builderCommand));
            });

            window.addEventListener('beforeunload', (event) => {
                if (editor.getDirtyCount() > 0) {
                    event.preventDefault();
                    event.returnValue = '';
                }
            });
        }

        runCommand(command) {
            const editor = this.editor;

            switch (command) {
                case 'undo':
                    editor.UndoManager.undo();
                    break;
                case 'redo':
                    editor.UndoManager.redo();
                    break;
                case 'preview':
                    editor.runCommand('preview');
                    break;
                case 'outline':
                    editor.Commands.isActive('core:component-outline')
                        ? editor.stopCommand('core:component-outline')
                        : editor.runCommand('core:component-outline');
                    break;
                case 'save':
                    this.save();
                    break;
            }
        }

        showPane(name) {
            document.querySelectorAll('[data-builder-pane]').forEach((button) => {
                button.classList.toggle('is-active', button.dataset.builderPane === name);
            });

            document.querySelectorAll('[data-builder-pane-content]').forEach((pane) => {
                pane.hidden = pane.dataset.builderPaneContent !== name;
            });
        }

        /**
         * A building block can be placed at page level when its type allows it, or in a slot of a
         * building block whose type accepts it
         */
        canPlace(source, target) {
            if (!source || source.get('type') !== BLOCK) {
                return false;
            }

            const blocktype = this.blocktypes[blocktypeKey(source)];

            if (!blocktype) {
                return false;
            }

            if (target.get('type') === 'wrapper') {
                return blocktype.root;
            }

            if (target.get('type') !== SLOT) {
                return false;
            }

            const parentBlock = target.closestType(BLOCK);
            const parentType = parentBlock ? this.blocktypes[blocktypeKey(parentBlock)] : null;

            if (!parentType) {
                return false;
            }

            return parentType.accepts.length === 0 || parentType.accepts.includes(blocktype.key);
        }

        /**
         * Only zones and slots of a building block can be edited, the rest of its template is locked
         */
        lockTemplate(block) {
            const lock = (component) => {
                component.components().forEach((child) => {
                    const type = child.get('type');

                    if (type === SLOT || type === BLOCK || ZONE_TYPES.includes(type)) {
                        return;
                    }

                    child.set({
                        locked: true,
                        draggable: false,
                        droppable: false,
                        removable: false,
                        copyable: false,
                        hoverable: false,
                        editable: false,
                        stylable: false,
                    });

                    lock(child);
                });
            };

            lock(block);
        }

        /**
         * HTML of a building block: its template with the zone values and the nested building blocks
         */
        blockHtml(node) {
            const blocktype = this.blocktypes[node.type];

            if (!blocktype) {
                return '';
            }

            const container = document.createElement('div');
            container.innerHTML = blocktype.template.trim();

            let root = container.firstElementChild;

            if (!root) {
                return '';
            }

            Object.entries(blocktype.zones).forEach(([key, zone]) => {
                const element = [root, ...root.querySelectorAll('[data-zone]')].find((el) => el.getAttribute('data-zone') === key);
                const value = (node.zones || {})[key];

                if (element && value) {
                    this.fillZone(element, zone.type, value);
                }
            });

            blocktype.slots.forEach((slot) => {
                const element = [root, ...root.querySelectorAll('[data-slot]')].find((el) => el.getAttribute('data-slot') === slot);

                if (!element) {
                    return;
                }

                element.innerHTML = (node.children || [])
                    .filter((child) => child.slot === slot)
                    .map((child) => this.blockHtml(child))
                    .join('');
            });

            // a block whose root element is a zone gets a wrapper, so the block and the zone are separate components
            if (root.hasAttribute('data-zone')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'content-builder-block';
                wrapper.appendChild(root);
                root = wrapper;
            }

            root.setAttribute('data-blocktype', blocktype.key);

            if (node.id) {
                root.setAttribute('data-blockitem-id', node.id);
            }

            return root.outerHTML;
        }

        fillZone(element, type, value) {
            switch (type) {
                case 'text':
                    element.innerHTML = value.html || '';
                    break;
                case 'image':
                    element.setAttribute('src', value.src || '');
                    element.setAttribute('alt', value.alt || '');
                    break;
                case 'link':
                    element.textContent = value.text || '';
                    element.setAttribute('href', value.href || '#');
                    element.setAttribute('target', value.target || '_self');
                    break;
                default:
                    element.textContent = value.text || '';
            }
        }

        /**
         * @return {{tree: Array, components: Array}} the tree and the block components in the same (pre)order
         */
        serialize() {
            const components = [];
            const usedIds = new Set();

            const serializeBlock = (block, slot) => {
                const node = { type: blocktypeKey(block), zones: {}, children: [] };
                const id = blockitemId(block);

                if (id > 0 && !usedIds.has(id)) {
                    node.id = id;
                    usedIds.add(id);
                }

                if (slot) {
                    node.slot = slot;
                }

                components.push(block);

                const slots = [];

                const walk = (component) => {
                    component.components().forEach((child) => {
                        const type = child.get('type');

                        if (type === SLOT) {
                            slots.push(child);
                            return;
                        }

                        if (ZONE_TYPES.includes(type)) {
                            node.zones[child.getAttributes()['data-zone']] = this.zoneValue(child);
                            return;
                        }

                        walk(child);
                    });
                };

                walk(block);

                slots.forEach((slotComponent) => {
                    const name = slotComponent.getAttributes()['data-slot'];

                    slotComponent.components().forEach((child) => {
                        if (child.get('type') === BLOCK) {
                            node.children.push(serializeBlock(child, name));
                        }
                    });
                });

                return node;
            };

            const tree = this.editor.getWrapper().components()
                .filter((component) => component.get('type') === BLOCK)
                .map((component) => serializeBlock(component, null));

            return { tree, components };
        }

        zoneValue(component) {
            switch (component.get('type')) {
                case ZONE_TEXT:
                    return { html: component.getInnerHTML() };
                case ZONE_IMAGE:
                    return {
                        src: component.isDefaultSrc() ? '' : (component.get('src') || ''),
                        alt: component.getAttributes().alt || '',
                    };
                case ZONE_LINK:
                    return {
                        text: textOf(component),
                        href: component.getAttributes().href || '',
                        target: component.getAttributes().target || '_self',
                    };
                default:
                    return { text: textOf(component) };
            }
        }

        save() {
            if (this.saving) {
                return;
            }

            this.saving = true;
            this.setStatus('', 'is-saving');

            const { tree, components } = this.serialize();

            fetch(this.config.urls.update, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-ICMS-Token': this.token,
                },
                body: JSON.stringify(tree),
            })
                .then((response) => response.json().then((data) => ({ ok: response.ok, data })))
                .then(({ ok, data }) => {
                    if (!ok || !data.ok) {
                        this.showErrors(this.config.labels.error, data.errors || []);
                        return;
                    }

                    this.updateToken(data.token);
                    this.applyIds(components, data.tree);
                    this.editor.clearDirtyCount();
                    this.setStatus(this.config.labels.saved, 'is-saved');
                })
                .catch(() => this.showErrors(this.config.labels.error, []))
                .finally(() => {
                    this.saving = false;
                    this.updateStatus();
                });
        }

        updateToken(token) {
            if (!token) {
                return;
            }

            this.token = token;
        }

        /**
         * Images are chosen and uploaded in the ImpressCMS image manager, which opens in a popup. The popup hands
         * the chosen image over through a select element, the way the image select element of ImpressCMS forms
         * works: an <option> with the site relative URL in the <optgroup> of the category of the image, and an
         * <img> that shows the image. The popup closes itself after a choice.
         */
        registerImageManager() {
            const { target, categories } = this.config.imageManager;

            this.imageSelect = document.createElement('select');
            this.imageSelect.id = target;
            this.imageSelect.hidden = true;

            categories.forEach((id) => {
                const group = document.createElement('optgroup');
                group.id = `img_cat_${id}`;
                this.imageSelect.appendChild(group);
            });

            const preview = document.createElement('img');
            preview.id = `${target}_img`;
            preview.hidden = true;
            preview.alt = '';

            document.body.append(this.imageSelect, preview);

            // a double click on an image opens the asset manager of GrapesJS, use the ImpressCMS image manager instead
            this.editor.Commands.add('open-assets', {
                run: (editor, sender, options) => this.openImageManager((options && options.target) || editor.getSelected()),
            });
        }

        openImageManager(component) {
            if (!component || component.get('type') !== ZONE_IMAGE) {
                return;
            }

            Array.from(this.imageSelect.options).forEach((option) => option.remove());

            const features = 'width=985,height=470,resizable=yes,scrollbars=yes';
            const popup = window.open(this.config.imageManager.url, 'icmsImageManager', features);

            if (!popup) {
                this.showErrors(this.config.labels.popupError, []);
                return;
            }

            const timer = window.setInterval(() => {
                if (!popup.closed) {
                    return;
                }

                window.clearInterval(timer);

                const chosen = this.imageSelect.value;

                if (chosen) {
                    component.set('src', this.config.imageManager.siteUrl + chosen);
                }
            }, 400);
        }

        /**
         * New building blocks get their id from the stored tree, which is in the same order as the sent tree
         */
        applyIds(components, tree) {
            const ids = [];
            const flatten = (nodes) => nodes.forEach((node) => {
                ids.push(node.id);
                flatten(node.children || []);
            });

            flatten(tree || []);

            if (ids.length !== components.length) {
                return;
            }

            components.forEach((component, index) => {
                component.addAttributes({ 'data-blockitem-id': String(ids[index]) }, { avoidStore: true });
            });
        }

        updateStatus() {
            if (this.saving) {
                return;
            }

            if (this.editor.getDirtyCount() > 0) {
                this.setStatus(this.config.labels.unsaved, 'is-dirty');
            }
        }

        setStatus(text, state) {
            if (!this.status) {
                return;
            }

            this.status.textContent = text;
            this.status.className = `content-builder__status ${state}`;
        }

        showErrors(title, errors) {
            this.setStatus(title, 'is-error');

            const list = errors.map((error) => `<li>${escapeHtml(error)}</li>`).join('');

            this.editor.Modal.open({
                title: escapeHtml(title),
                content: `<ul class="content-builder__errors">${list}</ul>`,
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const configElement = document.getElementById('content-builder-config');

        if (!configElement || typeof grapesjs === 'undefined') {
            return;
        }

        new PageBuilder(JSON.parse(configElement.textContent)).start();
    });
}());
