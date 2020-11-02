import {extend} from 'flarum/extend';
import app from 'flarum/app';
import AdminNav from 'flarum/components/AdminNav';
import AdminLinkButton from 'flarum/components/AdminLinkButton';
import Scratchpad from './models/Scratchpad';
import ScratchpadPage from './components/ScratchpadPage';
import showPhpErrors from '../common/showPhpErrors';

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

    showPhpErrors();
});
