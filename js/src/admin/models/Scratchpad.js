import Model from 'flarum/Model';

export default class Scratchpad extends Model {
    title = Model.attribute('title');
    enabled = Model.attribute('enabled');
    admin_js = Model.attribute('admin_js');
    forum_js = Model.attribute('forum_js');
    admin_less = Model.attribute('admin_less');
    forum_less = Model.attribute('forum_less');
    php = Model.attribute('php');
}
