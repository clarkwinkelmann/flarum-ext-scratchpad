import app from 'flarum/admin/app';
import SettingsModal from 'flarum/admin/components/SettingsModal';
import Select from 'flarum/common/components/Select';
import Switch from 'flarum/common/components/Switch';

/* global m */

const settingsPrefix = 'scratchpad.';
const translationPrefix = 'clarkwinkelmann-scratchpad.admin.settings.';

const THEMES = [
    '3024-day',
    'base16-dark',
    'dracula',
    'hopscotch',
    'material',
    'monokai',
    'panda-syntax',
    'shadowfox',
    'twilight',
    '3024-night',
    'base16-light',
    'duotone-dark',
    'icecoder',
    'material-darker',
    'moxer',
    'paraiso-dark',
    'solarized',
    'vibrant-ink',
    'abcdef',
    'bespin',
    'duotone-light',
    'idea',
    'material-ocean',
    'neat',
    'paraiso-light',
    'ssms',
    'xq-dark',
    'ambiance',
    'blackboard',
    'eclipse',
    'isotope',
    'material-palenight',
    'neo',
    'pastel-on-dark',
    'the-matrix',
    'xq-light',
    'ambiance-mobile',
    'cobalt',
    'elegant',
    'lesser-dark',
    'mbo',
    'night',
    'railscasts',
    'tomorrow-night-bright',
    'yeti',
    'ayu-dark',
    'colorforth',
    'erlang-dark',
    'liquibyte',
    'mdn-like',
    'nord',
    'rubyblue',
    'tomorrow-night-eighties',
    'yonce',
    'ayu-mirage',
    'darcula',
    'gruvbox-dark',
    'lucario',
    'midnight',
    'oceanic-next',
    'seti',
    'ttcn',
    'zenburn',
];

export default class CodeMirrorSettingsModal extends SettingsModal {
    title() {
        return app.translator.trans(translationPrefix + 'title');
    }

    form() {
        const themeOptions = {
            auto: app.translator.trans(translationPrefix + 'theme-auto'),
        };

        THEMES.forEach(theme => {
            themeOptions[theme] = theme;
        });

        return [
            m('.Form-group', [
                Switch.component({
                    state: this.setting(settingsPrefix + 'singleColumn')() === '1',
                    onchange: value => {
                        this.setting(settingsPrefix + 'singleColumn')(value ? '1' : '0');
                    },
                }, app.translator.trans(translationPrefix + 'single-column')),
            ]),
            m('.Form-group', [
                m('label', app.translator.trans(translationPrefix + 'theme')),
                Select.component({
                    value: this.setting(settingsPrefix + 'theme', 'auto')(),
                    onchange: this.setting(settingsPrefix + 'theme'),
                    options: themeOptions,
                }),
            ]),
            m('.Form-group', [
                m('label', app.translator.trans(translationPrefix + 'indent')),
                m('input.FormControl', {
                    type: 'number',
                    bidi: this.setting(settingsPrefix + 'indent', 4),
                }),
            ]),
            m('.Form-group', [
                m('label', app.translator.trans(translationPrefix + 'npm-install-command')),
                m('input.FormControl', {
                    type: 'text',
                    bidi: this.setting(settingsPrefix + 'npmInstallCommand'),
                    placeholder: app.translator.trans(translationPrefix + 'empty-for-default'),
                }),
            ]),
            m('.Form-group', [
                m('label', app.translator.trans(translationPrefix + 'webpack-command')),
                m('input.FormControl', {
                    type: 'text',
                    bidi: this.setting(settingsPrefix + 'webpackCommand'),
                    placeholder: app.translator.trans(translationPrefix + 'empty-for-default'),
                }),
            ]),
            m('.Form-group', [
                Switch.component({
                    state: this.setting(settingsPrefix + 'validateLive')() !== '0',
                    onchange: value => {
                        this.setting(settingsPrefix + 'validateLive')(value ? '1' : '0');
                    },
                }, app.translator.trans(translationPrefix + 'validate-live')),
            ]),
        ];
    }
}
