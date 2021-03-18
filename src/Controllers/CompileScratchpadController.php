<?php

namespace ClarkWinkelmann\Scratchpad\Controllers;

use ClarkWinkelmann\Scratchpad\Scratchpad;
use Flarum\Foundation\Paths;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CompileScratchpadController implements RequestHandlerInterface
{
    protected $settings;
    protected $paths;

    public function __construct(SettingsRepositoryInterface $settings, Paths $paths)
    {
        $this->settings = $settings;
        $this->paths = $paths;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $request->getAttribute('actor')->assertAdmin();

        $id = Arr::get($request->getQueryParams(), 'id');

        /**
         * @var $scratchpad Scratchpad
         */
        $scratchpad = Scratchpad::query()->findOrFail($id);

        $path = $this->paths->storage . '/scratchpad';

        if (!file_exists($path)) {
            mkdir($path);
        }

        if (!file_exists("$path/package.json")) {
            file_put_contents("$path/package.json", '{"name":"scratchpad","private":true,"dependencies":{"flarum-webpack-config":"0.1.0-beta.10","webpack":"^4.0.0","webpack-cli":"^3.0.7"}}');
        }

        $npmOutput = false;

        if (!file_exists("$path/node_modules")) {
            $npmCommand = $this->settings->get('scratchpad.npmInstallCommand') ?: 'cd {{path}} && npm install 2>&1';

            $npmOutput = shell_exec(str_replace('{{path}}', $path, $npmCommand));
        }

        file_put_contents("$path/admin.js", $scratchpad->admin_js);
        file_put_contents("$path/forum.js", $scratchpad->forum_js);

        $webpackCommand = $this->settings->get('scratchpad.webpackCommand') ?: 'cd {{path}} && node_modules/.bin/webpack --mode development --config node_modules/flarum-webpack-config/index.js 2>&1';

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
