<?php

namespace PixiiBomb\Api\Validation;

use Illuminate\Validation\Rule;
use PixiiBomb\Api\Models\Role;
use PixiiBomb\Core\Enums\ConsoleColors;

class RoleValidation extends ResourceValidation
{
    protected static function fields(): array
    {
        return [
            'key' => ['string', 'max:30', Rule::unique(Role::class, 'key')],
            'display_name' => ['string', 'max:255'],
            'description' => ['string'],
            'priority' => ['integer'],
        ];
    }

    protected static function requiredFor(ConsoleColors $action): array
    {
        return match ($action) {
            ConsoleColors::CREATE => [
                'key',
                'display_name',
            ],

            ConsoleColors::UPDATE => [
                'display_name',
            ],

            default => [],
        };
    }
}
