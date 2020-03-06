import {extend} from 'flarum/extend';
import app from 'flarum/app';
import Application from 'flarum/Application';
import Alert from 'flarum/components/Alert';

export default function () {
    extend(Application.prototype, 'mount', function () {
        const errors = app.forum.attribute('scratchpadPhpErrors');

        if (errors) {
            app.alerts.show(new Alert({
                type: 'error',
                children: errors.join('\n'),
            }));
        }
    });
}
