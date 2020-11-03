<?php

namespace ClarkWinkelmann\Scratchpad;

use ClarkWinkelmann\Scratchpad\ErrorHandling\ValidationExceptionWithMeta;
use Flarum\Foundation\Config;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ScratchpadRepository
{
    protected function query()
    {
        return Scratchpad::query()->orderBy('updated_at', 'desc');
    }

    /**
     * @return Scratchpad[]|Collection
     */
    public function all()
    {
        return $this->query()->get();
    }

    /**
     * @return Scratchpad[]|Collection
     */
    public function allEnabled()
    {
        return $this->query()->where('enabled', true)->get();
    }

    public function validateAndFill(Scratchpad $scratchpad, array $attributes, User $actor)
    {
        /**
         * @var $validation Factory
         */
        $validation = app(Factory::class);

        $rules = [
            'title' => 'required|string|max:255',
            'admin_js' => 'nullable|string|max:16777215',
            'forum_js' => 'nullable|string|max:16777215',
            'admin_less' => 'nullable|string|max:16777215',
            'forum_less' => 'nullable|string|max:16777215',
            'php' => 'nullable|string|max:16777215',
        ];

        $validation->make($attributes, $rules)->validate();

        $scratchpad->forceFill(Arr::only($attributes, array_keys($rules)));

        if ($scratchpad->php || $scratchpad->admin_less) {
            $this->makeTestRequest('admin', $scratchpad, $actor);
        }

        if ($scratchpad->php || $scratchpad->forum_less) {
            $this->makeTestRequest('forum', $scratchpad, $actor);
        }
    }

    protected function makeTestRequest(string $frontend, Scratchpad $scratchpad, User $actor)
    {
        /**
         * @var $settings SettingsRepositoryInterface
         */
        $settings = app(SettingsRepositoryInterface::class);

        if ($settings->get('scratchpad.validateLive') === '0') {
            return;
        }

        // Create a token that will authenticate ourselves with the live test data
        $token = Str::random(32);
        $settings->set('scratchpad.liveCodeToken', $token);

        $params = [
            'scratchpadLiveToken' => $token,
            'scratchpadLiveActorId' => $actor->id,
        ];

        if ($scratchpad->exists) {
            $params['scratchpadLiveIgnoreId'] = $scratchpad->id;
        }

        if ($scratchpad->admin_less) {
            $params['scratchpadLiveAdminLess'] = $scratchpad->admin_less;
        }

        if ($scratchpad->forum_less) {
            $params['scratchpadLiveForumLess'] = $scratchpad->forum_less;
        }

        if ($scratchpad->php) {
            $params['scratchpadLivePhp'] = $scratchpad->php;
        }

        try {
            $response = (new Client())->post(app(Config::class)->url() . '/' . ($frontend === 'admin' ? 'admin/' : '') . 'scratchpad/test', [
                'http_errors' => false,
                'form_params' => $params,
            ]);

            $body = $response->getBody()->getContents();

            // Unfortunately boot errors due to syntax errors are 200 responses which need to be detected separately
            if ($response->getStatusCode() !== 200 || str_contains($body, 'Flarum encountered a boot error')) {
                $detail = "PHP validation error in $frontend frontend!";

                if (preg_match('~^Less_Exception_[A-Za-z]+:\s*(.+in [A-Za-z0-9-]+\.less)~m', $body, $matches)) {
                    $detail = "Less validation error in $frontend frontend: $matches[1]";
                }

                throw new ValidationExceptionWithMeta('live-test', $detail, [
                    'body' => $body,
                ]);
            }
        } finally {
            $settings->delete('scratchpad.liveCodeToken');
        }
    }
}
