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
                    indentUnit: 4,
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
