import app from 'flarum/app';
import Component from 'flarum/Component';
import Button from 'flarum/components/Button';
import CodeMirrorTextarea from './CodeMirrorTextarea';
import CodeMirrorSettingsModal from './CodeMirrorSettingsModal';

/* global m */

export default class TabbedEditor extends Component {
    init() {
        this.tab = this.props.tabs[0];
    }

    view() {
        const {scratchpad} = this.props;

        return m('.ScratchpadTabbed', [
            m('.ScratchpadTabbed-tabs', [
                this.props.tabs.map(tab => m('.ScratchpadTabbed-tab', {
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
                        app.modal.show(new CodeMirrorSettingsModal());
                    },
                }),
            ]),
            m('.ScratchpadTabbed-editor', CodeMirrorTextarea.component({
                key: scratchpad.id() + '-' + this.tab.key,
                value: scratchpad[this.tab.key](),
                onchange: value => {
                    this.props.onchange(this.tab.key, value);
                },
                mode: this.tab.mode,
            })),
        ]);
    }
}
