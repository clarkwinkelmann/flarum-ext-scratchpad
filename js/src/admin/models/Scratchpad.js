import Model from 'flarum/Model';
import mixin from 'flarum/utils/mixin';

export default class Scratchpad extends mixin(Model, {
    title: Model.attribute('title'),
    enabled: Model.attribute('enabled'),
    admin_js: Model.attribute('admin_js'),
    forum_js: Model.attribute('forum_js'),
    admin_less: Model.attribute('admin_less'),
    forum_less: Model.attribute('forum_less'),
    php: Model.attribute('php'),
}) {
    //
}
