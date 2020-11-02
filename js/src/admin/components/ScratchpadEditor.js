import app from 'flarum/app';
import Button from 'flarum/components/Button';
import Switch from 'flarum/components/Switch';
import saveSettings from 'flarum/utils/saveSettings';
import TabbedEditor from './TabbedEditor';
import CompilationOutputModal from './CompilationOutputModal';

/* global m */

const TABS = [
    {
        key: 'admin_js',
        mode: 'javascript',
    },
    {
        key: 'forum_js',
        mode: 'javascript',
    },
    {
        key: 'admin_less',
        mode: 'less',
    },
    {
        key: 'forum_less',
        mode: 'less',
    },
    {
        key: 'php',
        mode: 'php',
    },
];

export default class ScratchpadEditor {
    oninit() {
        this.dirty = false;
        this.dirtyJs = false;
        this.saving = false;
        this.compiling = false;
    }

    compile(scratchpad) {
        this.compiling = true;

        app.request({
            method: 'POST',
            url: app.forum.attribute('apiUrl') + scratchpad.apiEndpoint() + '/compile',
            errorHandler: e => {
                this.compiling = false;
                this.compileAlert(e.response, true);
                m.redraw();
            },
        }).then(response => {
            this.compiling = false;
            this.compileAlert(response);
            m.redraw();
        });
    }

    compileAlert(response, error = false) {
        const alertId = app.alerts.show({
            type: error ? 'error' : 'success',
            controls: [Button.component({
                className: 'Button Button--link',
                onclick: () => {
                    app.alerts.dismiss(alertId);
                    app.modal.show(CompilationOutputModal, response);
                    alert = null;
                },
            }, app.translator.trans('clarkwinkelmann-scratchpad.admin.compilation-alert.view-output'))],
        }, app.translator.trans('clarkwinkelmann-scratchpad.admin.compilation-alert.' + (error ? 'failure' : 'success')));

        if (!error) {
            setTimeout(() => {
                if (!alertId) {
                    return;
                }

                app.alerts.dismiss(alertId);
                alert = null;
            }, 5000);
        }
    }

    view(vnode) {
        const {scratchpad} = vnode.attrs;

        const onchange = (key, value) => {
            scratchpad.pushAttributes({
                [key]: value,
            });

            this.dirty = true;

            if (key.indexOf('_js') !== -1) {
                this.dirtyJs = true;
            }

            m.redraw();
        };

        return m('.ScratchpadEditor', [
            m('.ScratchpadHeader', [
                m('input', {
                    className: 'FormControl',
                    type: 'text',
                    value: scratchpad.title(),
                    oninput: event => {
                        scratchpad.pushAttributes({
                            title: event.target.value,
                        });
                        this.dirty = true;
                    },
                    title: app.translator.trans('clarkwinkelmann-scratchpad.admin.fields.title'),
                }),
                Switch.component({
                    state: app.data.settings['scratchpad.compileAutomatically'] === '1',
                    onchange: state => {
                        saveSettings({
                            'scratchpad.compileAutomatically': state ? '1' : '0',
                        });
                    },
                }, app.translator.trans('clarkwinkelmann-scratchpad.admin.settings.compile-automatically')),
                Button.component({
                    className: 'Button',
                    onclick: () => {
                        this.saving = true;

                        const willBeNewOne = !scratchpad.exists;
                        const shouldRecompile = app.data.settings['scratchpad.compileAutomatically'] === '1' && this.dirtyJs;

                        scratchpad.save({
                            title: scratchpad.title(),
                            admin_js: scratchpad.admin_js(),
                            forum_js: scratchpad.forum_js(),
                            admin_less: scratchpad.admin_less(),
                            forum_less: scratchpad.forum_less(),
                            php: scratchpad.php(),
                        }).then(scratchpad => {
                            this.saving = false;
                            this.dirty = false;
                            this.dirtyJs = false;

                            if (willBeNewOne) {
                                vnode.attrs.onsave(scratchpad);
                            }

                            if (shouldRecompile) {
                                this.compile(scratchpad);
                            }

                            m.redraw();
                        }).catch(e => {
                            this.saving = false;
                            m.redraw();
                            throw e;
                        });
                    },
                    icon: 'fas fa-save',
                    loading: this.saving,
                    disabled: !this.dirty && scratchpad.exists,
                }, app.translator.trans('clarkwinkelmann-scratchpad.admin.controls.save')),
                Button.component({
                    className: 'Button',
                    onclick: () => {
                        this.compile(scratchpad);
                    },
                    icon: 'fas fa-file-import',
                    loading: this.compiling,
                    disabled: this.dirty || !scratchpad.exists || app.data.settings['scratchpad.compileAutomatically'] === '1',
                }, app.translator.trans('clarkwinkelmann-scratchpad.admin.controls.compile')),
            ]),
            m('.ScratchpadColumns', app.data.settings['scratchpad.singleColumn'] === '1' ? m(TabbedEditor, {
                tabs: TABS,
                scratchpad,
                onchange,
            }) : ['javascript', 'less', 'php'].map(mode => m(TabbedEditor, {
                tabs: TABS.filter(tab => tab.mode === mode),
                scratchpad,
                onchange,
            }))),
        ]);
    }
}
