import app from 'flarum/app';
import Page from 'flarum/components/Page';
import Button from 'flarum/components/Button';
import Switch from 'flarum/components/Switch';
import ScratchpadEditor from './ScratchpadEditor';

/* global m */

export default class ScratchpadPage extends Page {
    init() {
        super.init();

        this.refreshScratchpads();
        this.startNewScratchpad();
    }

    refreshScratchpads() {
        this.scratchpads = null;

        app.request({
            method: 'GET',
            url: app.forum.attribute('apiUrl') + '/scratchpads',
        }).then(result => {
            this.scratchpads = app.store.pushPayload(result);
            m.redraw();
        });
    }

    startNewScratchpad() {
        const random = Math.random().toString(36).substring(7);

        this.scratchpad = app.store.createRecord('scratchpads', {
            id: random,
            attributes: {
                title: 'Untitled',
                enabled: true,
                admin_js: 'app.initializers.add(\'scratchpad-' + random + '\', () => {\n' +
                    '    console.log(\'Hello, admin!\');\n' +
                    '});\n',
                forum_js: 'app.initializers.add(\'scratchpad-' + random + '\', () => {\n' +
                    '    console.log(\'Hello, forum!\');\n' +
                    '});\n',
                admin_less: '',
                forum_less: '',
                php: '<?php\n' +
                    '\n' +
                    'use Flarum\\Extend;\n' +
                    '\n' +
                    'return [\n' +
                    '    // Register extenders here\n' +
                    '];\n',
            },
        });
    }

    view() {
        return m('.ScratchpadPage', [
            m('.ScratchpadList', [
                m('.ScratchpadHeader', [
                    Button.component({
                        className: 'Button',
                        onclick: () => {
                            this.refreshScratchpads();
                        },
                        children: 'Refresh',
                        icon: 'fas fa-sync',
                    }),
                ]),
                m('.ScratchpadList-item', {
                    className: this.scratchpad.exists ? '' : 'active',
                    onclick: () => {
                        if (!this.scratchpad.exists && !confirm('Current scratchpad is not saved. Drop it and create new one ?')) {
                            return;
                        }

                        this.startNewScratchpad();
                    },
                }, m('h5', 'New')),
                this.scratchpads === null ? m('div', 'Loading...') : this.scratchpads.map(scratchpad => m('.ScratchpadList-item', {
                    className: this.scratchpad.id() === scratchpad.id() ? 'active' : '',
                }, [
                    Switch.component({
                        state: scratchpad.enabled(),
                        onchange: enabled => {
                            scratchpad.save({
                                enabled,
                            }).then(() => {
                                m.redraw();
                            });
                        },
                    }),
                    m('h5', {
                        onclick: () => {
                            this.scratchpad = scratchpad;
                        },
                    }, scratchpad.title()),
                    Button.component({
                        className: 'Button',
                        onclick: () => {
                            if (!confirm('Delete?')) {
                                return;
                            }

                            const wasActive = this.scratchpad.id() === scratchpad.id();

                            scratchpad.delete().then(() => {
                                this.refreshScratchpads();

                                if (wasActive) {
                                    this.startNewScratchpad();
                                }
                            });
                        },
                        children: 'Delete',
                    }),
                ])),
            ]),
            this.scratchpad ? ScratchpadEditor.component({
                scratchpad: this.scratchpad,
                oncreate: scratchpad => {
                    this.scratchpad = scratchpad;
                    this.refreshScratchpads();
                },
            }) : null,
        ]);
    }
}
