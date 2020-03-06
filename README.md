# Scratchpad

[![MIT license](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/clarkwinkelmann/flarum-ext-scratchpad/blob/master/LICENSE.md) [![Latest Stable Version](https://img.shields.io/packagist/v/clarkwinkelmann/flarum-ext-scratchpad.svg)](https://packagist.org/packages/clarkwinkelmann/flarum-ext-scratchpad) [![Total Downloads](https://img.shields.io/packagist/dt/clarkwinkelmann/flarum-ext-scratchpad.svg)](https://packagist.org/packages/clarkwinkelmann/flarum-ext-scratchpad) [![Donate](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://www.paypal.me/clarkwinkelmann)

> PLEASE DO NOT INSTALL THIS EXTENSION UNLESS YOU KNOW WHAT YOU ARE DOING
>
> Any user with administrative access is able to inject any javascript and PHP code on the website without any restriction!

This extension adds a scratchpad feature to the admin panel of Flarum.

It's intended to be used:

- On a local website only
- With debug mode turned on

Each scratchpad can contain javascript, less and PHP code just like any extension.

You can give names to individual scratchpads and enable/disable them via a checkbox.

This is still very much **beta**. Use at your own risks. Texts will be made translatable in a future release.

There's a good chance that despite the checks in place you will be able to save invalid code.
If that happens, the easier is to go in the database and disable the scratchpad that's responsible.

In this first release there are no validation checks for less and javascript.

Javascript compilation is done locally by calling node through PHP.
A `scratchpad` folder will be created under `storage`.
The first time you compile, `npm install` will run.
Subsequent compilations will re-use the installed dependencies.

If you end up with an invalid javascript setup, you can delete the `storage/scratchpad` folder and the extension will re-install everything on the next compilation.

## Links

- [GitHub](https://github.com/clarkwinkelmann/flarum-ext-scratchpad)
- [Packagist](https://packagist.org/packages/clarkwinkelmann/flarum-ext-scratchpad)
