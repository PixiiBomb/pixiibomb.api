<?php

namespace PixiiBomb\Api\Policies;

use App\Models\User;

class UserPolicy extends ResourcePolicy
{
    public function create(?User $user = null): bool
    {
        return true;
    }

    public function update(User $user, mixed $record): bool
    {
        return (int)$user->id === (int)$record->id
            || $this->hasRole($user, ['admin', 'super_admin']);
    }

    public function delete(User $user, mixed $record): bool
    {
        return (int)$user->id !== (int)$record->id
            && $this->hasRole($user, ['super_admin']);
    }
}
