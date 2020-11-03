import app from 'flarum/app';
import Button from 'flarum/components/Button';
import CodeMirrorTextarea from './CodeMirrorTextarea';
import CodeMirrorSettingsModal from '../modals/CodeMirrorSettingsModal';

/* global m */

export default class TabbedEditor {
    oninit(vnode) {
        this.tab = vnode.attrs.tabs[0];
    }

    view(vnode) {
        const {scratchpad} = vnode.attrs;

        return m('.ScratchpadTabbed', [
            m('.ScratchpadTabbed-tabs', [
                vnode.attrs.tabs.map(tab => m('.ScratchpadTabbed-tab', {
                    className: this.tab.key === tab.key ? 'active' : '',
                    onclick: () => {
                        this.tab = tab;
                    },
                }, app.translator.trans('clarkwinkelmann-scratchpad.admin.fields.' + tab.key))),
                Button.component({
                    className: 'ScratchpadTabbedSettings Button Button--icon Button--link',
                    icon: 'fas fa-cog',
                    title: app.translator.trans('clarkwinkelmann-scratchpad.admin.controls.settings'),
                    onclick() {
                        app.modal.show(CodeMirrorSettingsModal);
                    },
                }),
            ]),
            m('.ScratchpadTabbed-editor', m(CodeMirrorTextarea, {
                key: scratchpad.id() + '-' + this.tab.key,
                value: scratchpad[this.tab.key](),
                onchange: value => {
                    vnode.attrs.onchange(this.tab.key, value);
                },
                mode: this.tab.mode,
            })),
        ]);
    }
}
