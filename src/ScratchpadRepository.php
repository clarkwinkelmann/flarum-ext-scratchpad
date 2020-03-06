<?php

namespace ClarkWinkelmann\Scratchpad;

use Flarum\Foundation\ValidationException;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

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

    public function validateAndFill(Scratchpad $scratchpad, array $attributes)
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

        if ($scratchpad->php) {
            try {
                $return = $scratchpad->evaluatePhp();
            } catch (\Throwable $exception) {
                throw new ValidationException([
                    'php' => 'PHP validation error! ' . get_class($exception) . ': ' . $exception->getMessage(),
                ]);
            }

            if (!is_array($return)) {
                throw new ValidationException([
                    'php' => 'PHP code should return an array',
                ]);
            }
        }

        // TODO: validate LESS
    }
}
