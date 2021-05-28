import {extend} from 'flarum/common/extend';
import app from 'flarum/admin/app';
import RequestErrorModal from 'flarum/common/components/RequestErrorModal';
import Scratchpad from './models/Scratchpad';
import ScratchpadPage from './components/ScratchpadPage';
import showPhpErrors from '../common/showPhpErrors';

/* global m */

app.initializers.add('clarkwinkelmann-scratchpad', () => {
    app.store.models['scratchpads'] = Scratchpad;

    app.extensionData
        .for('clarkwinkelmann-scratchpad')
        .registerPage(ScratchpadPage);

    extend(RequestErrorModal.prototype, 'content', function (vdom) {
        const {error} = this.attrs;

        // Show the additional data we attach to some validation errors
        // We're not placing that data in the main error because it's too long but also
        // because it causes a "malformed URI sequence" error due to the use of decodeURI in Flarum's error handler
        if (error && error.status === 422 && error.response && Array.isArray(error.response.errors)) {
            error.response.errors.forEach(validationError => {
                if (validationError && validationError.meta && validationError.meta.body) {
                    vdom.children.push(m('pre', validationError.meta.body));
                }
            });
        }
    });

    showPhpErrors();
});
