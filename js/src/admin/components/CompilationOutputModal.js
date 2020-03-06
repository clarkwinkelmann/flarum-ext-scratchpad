import Modal from 'flarum/components/Modal';

/* global m */

export default class RequestErrorModal extends Modal {
    className() {
        return 'ScratchpadCompilationModal Modal--large';
    }

    title() {
        return 'Compilation output';
    }

    content() {
        return m('.Modal-body', [
            m('h3', 'Webpack output'),
            m('pre', this.props.webpackOutput),
            m('h3', 'NPM output'),
            this.props.npmOutput === false ? m('p', 'NPM did not run') : m('pre', this.props.npmOutput),
        ]);
    }
}
