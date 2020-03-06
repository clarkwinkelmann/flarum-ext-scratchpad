import app from 'flarum/app';
import Page from 'flarum/components/Page';
import Button from 'flarum/components/Button';
import Switch from 'flarum/components/Switch';
import LoadingIndicator from 'flarum/components/LoadingIndicator';
import extractText from 'flarum/utils/extractText';
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

        const indent = ' '.repeat(app.data.settings['scratchpad.indent'] || 4);

        this.scratchpad = app.store.createRecord('scratchpads', {
            id: random,
            attributes: {
                title: app.translator.trans('clarkwinkelmann-scratchpad.admin.placeholders.untitled'),
                enabled: true,
                admin_js: 'app.initializers.add(\'scratchpad-' + random + '\', () => {\n' +
                    indent + 'console.log(' + JSON.stringify(extractText(app.translator.trans('clarkwinkelmann-scratchpad.admin.placeholders.js-log', {
                        frontend: 'admin',
                    }))) + ');\n' +
                    '});\n',
                forum_js: 'app.initializers.add(\'scratchpad-' + random + '\', () => {\n' +
                    indent + 'console.log(' + JSON.stringify(extractText(app.translator.trans('clarkwinkelmann-scratchpad.admin.placeholders.js-log', {
                        frontend: 'forum',
                    }))) + ');\n' +
                    '});\n',
                admin_less: '',
                forum_less: '',
                php: '<?php\n' +
                    '\n' +
                    'use Flarum\\Extend;\n' +
                    '\n' +
                    'return [\n' +
                    indent + '// ' + extractText(app.translator.trans('clarkwinkelmann-scratchpad.admin.placeholders.php-comment')) + '\n' +
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
                        children: app.translator.trans('clarkwinkelmann-scratchpad.admin.controls.refresh'),
                        icon: 'fas fa-sync',
                    }),
                ]),
                m('.ScratchpadList-item', {
                    className: this.scratchpad.exists ? '' : 'active',
                    onclick: () => {
                        if (!this.scratchpad.exists && !confirm(extractText(app.translator.trans('clarkwinkelmann-scratchpad.admin.controls.new-confirmation')))) {
                            return;
                        }

                        this.startNewScratchpad();
                    },
                }, m('h5', app.translator.trans('clarkwinkelmann-scratchpad.admin.controls.new'))),
                this.scratchpads === null ? m('div', LoadingIndicator.component({})) : this.scratchpads.map(scratchpad => m('.ScratchpadList-item', {
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
                            if (!confirm(extractText(app.translator.trans('clarkwinkelmann-scratchpad.admin.controls.delete-confirmation', {
                                title: scratchpad.title(),
                            })))) {
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
                        children: app.translator.trans('clarkwinkelmann-scratchpad.admin.controls.delete'),
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
