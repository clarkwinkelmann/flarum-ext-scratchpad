import app from 'flarum/admin/app';
import CodeMirror from 'codemirror';
import 'codemirror/mode/javascript/javascript';
import 'codemirror/mode/css/css';
import 'codemirror/mode/php/php';

/* global m */

export default class CodeMirrorTextarea {
    view() {
        return m('div');
    }

    oncreate(vnode) {
        const document = CodeMirror(vnode.dom, {
            value: vnode.attrs.value || '',
            indentUnit: app.data.settings['scratchpad.indent'] || 4,
            theme: app.forum.attribute('scratchpadTheme') || 'default',
            lineNumbers: true,
            mode: vnode.attrs.mode,
        }).getDoc();

        document.on('change', () => {
            vnode.attrs.onchange(document.getValue());
        });
    }
}
