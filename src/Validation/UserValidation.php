<?php

namespace PixiiBomb\Api\Validation;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use PixiiBomb\Core\Enums\ConsoleColors;

class UserValidation extends ResourceValidation
{
    protected static function fields(): array
    {
        return [
            'role_id' => ['integer'],
            'username' => ['string', 'max:255'],
            'email' => [Rule::email(), 'max:255'],
            'password' => [Password::defaults()],
            'avatar' => [
                Rule::imageFile()
                    ->max('5mb')
                    ->dimensions(
                        Rule::dimensions()
                            ->maxWidth(1024)
                            ->maxHeight(1024)
                    ),
            ],
        ];
    }

    protected static function requiredFor(ConsoleColors $action): array
    {
        return match ($action) {
            ConsoleColors::CREATE => [
                'username',
                'email',
                'password',
            ],

            ConsoleColors::UPDATE => [
                'username',
                'email',
            ],

            default => [],
        };
    }
}
