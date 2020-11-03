import app from 'flarum/app';
import Modal from 'flarum/components/Modal';

/* global m */

const translationPrefix = 'clarkwinkelmann-scratchpad.admin.compilation-modal.';

export default class RequestErrorModal extends Modal {
    className() {
        return 'ScratchpadCompilationModal Modal--large';
    }

    title() {
        return app.translator.trans(translationPrefix + 'title');
    }

    content() {
        return m('.Modal-body', [
            m('h3', app.translator.trans(translationPrefix + 'webpack')),
            m('pre', this.attrs.webpackOutput),
            m('h3', app.translator.trans(translationPrefix + 'npm')),
            this.attrs.npmOutput === false ? m('p', app.translator.trans(translationPrefix + 'npm-not-run')) : m('pre', this.attrs.npmOutput),
        ]);
    }
}
