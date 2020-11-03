import {extend} from 'flarum/extend';
import app from 'flarum/app';
import AdminNav from 'flarum/components/AdminNav';
import AdminLinkButton from 'flarum/components/AdminLinkButton';
import RequestErrorModal from 'flarum/components/RequestErrorModal';
import Scratchpad from './models/Scratchpad';
import ScratchpadPage from './components/ScratchpadPage';
import showPhpErrors from '../common/showPhpErrors';

/* global m */

app.initializers.add('clarkwinkelmann-scratchpad', () => {
    app.store.models['scratchpads'] = Scratchpad;

    app.routes['scratchpad'] = {
        path: '/scratchpad',
        component: ScratchpadPage,
    };

    app.extensionSettings['clarkwinkelmann-scratchpad'] = () => m.route.set(app.route('scratchpad'));

    extend(AdminNav.prototype, 'items', items => {
        items.add('scratchpad', AdminLinkButton.component({
            href: app.route('scratchpad'),
            icon: 'fas fa-code',
            description: app.translator.trans('clarkwinkelmann-scratchpad.admin.menu.description'),
        }, app.translator.trans('clarkwinkelmann-scratchpad.admin.menu.title')));
    });

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
