<?php

namespace PixiiBomb\Api\Http\Controllers\Api;

use PixiiBomb\Api\Models\Role;
use PixiiBomb\Api\Validation\RoleValidation;

class RoleController extends ApiController
{
    protected function model(): string
    {
        return Role::class;
    }

    protected function validator(): string
    {
        return RoleValidation::class;
    }
}
