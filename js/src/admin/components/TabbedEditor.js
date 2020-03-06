import Component from 'flarum/Component';
import CodeMirrorTextarea from './CodeMirrorTextarea';

/* global m */

export default class TabbedEditor extends Component {
    init() {
        this.tab = this.props.tabs[0];
    }

    view() {
        const {scratchpad} = this.props;

        return m('.ScratchpadTabbed', [
            m('ul.ScratchpadTabbed-tabs', this.props.tabs.map(tab => m('li', {
                className: this.tab.key === tab.key ? 'active' : '',
                onclick: () => {
                    this.tab = tab;
                },
            }, tab.title))),
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
