<?php

namespace PixiiBomb\Api\Policies;

use App\Models\User;

class RolePolicy extends ResourcePolicy
{
    public function delete(User $user, mixed $record): bool
    {
        return ! $record->is_system
            && $this->hasRole($user, ['super_admin']);
    }
}
