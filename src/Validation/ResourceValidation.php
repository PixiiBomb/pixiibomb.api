<?php

namespace PixiiBomb\Api\Validation;

use PixiiBomb\Api\Enums\ConsoleColors;

abstract class ResourceValidation
{
    abstract protected static function fields(): array;

    protected static function requiredFor(ConsoleColors $action): array
    {
        return [];
    }

    public static function rules(ConsoleColors $action, ?array $only = null): array
    {
        $fields = static::fields();

        if ($only !== null) {
            $fields = array_intersect_key($fields, array_flip($only));
        }

        $rules = [];

        foreach ($fields as $field => $fieldRules) {
            $fieldRules = is_array($fieldRules)
                ? $fieldRules
                : [$fieldRules];

            if (in_array($field, static::requiredFor($action), true)) {
                array_unshift($fieldRules, 'required');
            } else {
                array_unshift(
                    $fieldRules,
                    $action === ConsoleColors::PATCH
                        ? 'sometimes'
                        : 'nullable'
                );
            }

            $rules[$field] = $fieldRules;
        }

        return $rules;
    }
}
