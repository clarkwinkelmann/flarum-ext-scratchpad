<?php

namespace ClarkWinkelmann\Scratchpad\Controllers;

use ClarkWinkelmann\Scratchpad\Scratchpad;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\AssertPermissionTrait;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CompileScratchpadController implements RequestHandlerInterface
{
    use AssertPermissionTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->assertAdmin($request->getAttribute('actor'));

        $id = array_get($request->getQueryParams(), 'id');

        /**
         * @var $scratchpad Scratchpad
         */
        $scratchpad = Scratchpad::query()->findOrFail($id);

        $path = app()->storagePath() . '/scratchpad';

        if (!file_exists($path)) {
            mkdir($path);
        }

        if (!file_exists("$path/package.json")) {
            file_put_contents("$path/package.json", '{"name":"scratchpad","private":true,"dependencies":{"flarum-webpack-config":"0.1.0-beta.10","webpack":"^4.0.0","webpack-cli":"^3.0.7"}}');
        }

        /**
         * @var $settings SettingsRepositoryInterface
         */
        $settings = app(SettingsRepositoryInterface::class);

        $npmOutput = false;

        if (!file_exists("$path/node_modules")) {
            $npmCommand = $settings->get('scratchpad.npmInstallCommand') ?: 'cd {{path}} && npm install 2>&1';

            $npmOutput = shell_exec(str_replace('{{path}}', $path, $npmCommand));
        }

        file_put_contents("$path/admin.js", $scratchpad->admin_js);
        file_put_contents("$path/forum.js", $scratchpad->forum_js);

        $webpackCommand = $settings->get('scratchpad.webpackCommand') ?: 'cd {{path}} && node_modules/.bin/webpack --mode development --config node_modules/flarum-webpack-config/index.js 2>&1';

        $webpackOutput = shell_exec(str_replace('{{path}}', $path, $webpackCommand));

        $failed = false;

        if (str_contains($webpackOutput, 'Module build failed')) {
            $failed = true;
        } else if (file_exists("$path/dist/admin.js") && file_exists("$path/dist/forum.js")) {
            $scratchpad->admin_js_compiled = file_get_contents("$path/dist/admin.js");
            $scratchpad->forum_js_compiled = file_get_contents("$path/dist/forum.js");
            $scratchpad->save();
        } else {
            $failed = true;
        }

        return new JsonResponse([
            'npmOutput' => $npmOutput,
            'webpackOutput' => $webpackOutput,
        ], $failed ? 400 : 200);
    }
}
