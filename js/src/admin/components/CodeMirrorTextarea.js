import app from 'flarum/app';
import Component from 'flarum/Component';
import CodeMirror from 'codemirror';
import 'codemirror/mode/javascript/javascript';
import 'codemirror/mode/css/css';
import 'codemirror/mode/php/php';

/* global m */

export default class CodeMirrorTextarea extends Component {
    view() {
        return m('div', {
            config: (element, isInitialized) => {
                if (isInitialized) {
                    return;
                }

                const document = CodeMirror(element, {
                    value: this.props.value || '',
                    indentUnit: app.data.settings['scratchpad.indent'] || 4,
                    theme: app.forum.attribute('scratchpadTheme') || 'default',
                    lineNumbers: true,
                    mode: this.props.mode,
                }).getDoc();

                document.on('change', () => {
                    this.props.onchange(document.getValue());
                });
            },
        });
    }
}
