import {extend} from 'flarum/extend';
import app from 'flarum/app';
import Application from 'flarum/Application';

export default function () {
    extend(Application.prototype, 'mount', function () {
        const errors = app.forum.attribute('scratchpadPhpErrors');

        if (errors) {
            app.alerts.show({
                type: 'error',
            }, errors.join('\n'));
        }
    });
}
