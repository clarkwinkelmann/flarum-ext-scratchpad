# Developer Scratchpad

[![MIT license](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/clarkwinkelmann/flarum-ext-scratchpad/blob/master/LICENSE.md) [![Latest Stable Version](https://img.shields.io/packagist/v/clarkwinkelmann/flarum-ext-scratchpad.svg)](https://packagist.org/packages/clarkwinkelmann/flarum-ext-scratchpad) [![Total Downloads](https://img.shields.io/packagist/dt/clarkwinkelmann/flarum-ext-scratchpad.svg)](https://packagist.org/packages/clarkwinkelmann/flarum-ext-scratchpad) [![Donate](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://www.paypal.me/clarkwinkelmann)

> PLEASE DO NOT INSTALL THIS EXTENSION UNLESS YOU KNOW WHAT YOU ARE DOING!
>
> Any user with administrative access is able to run any javascript and PHP code on the server and website without any restriction!

This extension adds a scratchpad feature to the admin panel of Flarum to ease testing and development for developers.

While the feature is restricted to admin users, I still recommend installing this on local environments only.

This is still very experimental. Use at your own risks.

Requirements:

- Debug mode must be turned on
- `eval()` is used to run PHP code
- `shell_exec()` is used to run Node
- The web user must be able to run `npm` and `node` commands

Each scratchpad can contain javascript, Less and PHP code just like any extension.

You can give names to individual scratchpads and enable/disable them via a checkbox.

There's a good chance that despite the checks in place you will be able to save invalid code.
If that happens, the easier is to go in the database and disable the scratchpad that's responsible.

PHP and Less are validated by doing a "stateless" background request to the forum and admin homepage with the new code when saving.
If the background request fails, a validation error is shown and the scratchpad is not saved.
This background request can be disabled in the extension settings.

Javascript is not validated during save, but if the compilation fails, the compiled code will not be loaded on the forum and you will see a message in the editor.

Javascript compilation is done locally by calling node through PHP.
A `scratchpad` folder will be created under `storage`.
The first time you compile, `npm install` will run.
Subsequent compilations will re-use the installed dependencies.

If you end up with an invalid javascript setup, you can delete the `storage/scratchpad` folder and the extension will re-install everything on the next compilation.

The text editor on the Scratchpad page is [CodeMirror](https://codemirror.net/).
You can customize the theme and indentation via the cog icon above the editor.
For now the settings are global and apply to all languages.

## Installation

Please read the disclaimers and requirements above before installing.

    composer require clarkwinkelmann/flarum-ext-scratchpad

## Customizing the NPM and Webpack commands

The default commands should work fine on most Linux systems, but they are known to fail on some configurations, including (unsurprisingly) Windows.

You can edit the commands by editing the settings named "NPM installl command" and "Webpack command" in the modal that can be accessed via the cog icon of the editor.

Below are the default commands.

`{{path}}` must be kept verbatim and will be replaced with the path to the scratchpad folder, which is the equivalent of the `js` folder of an extension.

`2>&1` is necessary at the end to redirect errors to standard output so the compiler can inspect the output and look for error messages.

NPM install:

    cd {{path}} && npm install 2>&1

Webpack:

    cd {{path}} && node_modules/.bin/webpack --mode development --config node_modules/flarum-webpack-config/index.js 2>&1

## Links

- [GitHub](https://github.com/clarkwinkelmann/flarum-ext-scratchpad)
- [Packagist](https://packagist.org/packages/clarkwinkelmann/flarum-ext-scratchpad)
- [Discuss](https://discuss.flarum.org/d/23016)
