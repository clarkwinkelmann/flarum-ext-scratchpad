import {extend} from 'flarum/common/extend';
import app from 'flarum/admin/app';
import Application from 'flarum/common/Application';

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
