<?php

namespace PixiiBomb\Api\Policies;

use App\Models\User;

class ResourcePolicy
{
    public function get(User $user, mixed $record): bool
    {
        return true;
    }

    public function getAll(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $this->hasRole($user, ['admin', 'super_admin']);
    }

    public function update(User $user, mixed $record): bool
    {
        return $this->owns($user, $record)
            || $this->hasRole($user, ['admin', 'super_admin']);
    }

    public function delete(User $user, mixed $record): bool
    {
        return ! ($record->is_system ?? false)
            && (
                $this->owns($user, $record)
                || $this->hasRole($user, ['super_admin'])
            );
    }

    public function deleteAll(User $user): bool
    {
        return $this->hasRole($user, ['super_admin']);
    }

    protected function hasRole(User $user, array $roles): bool
    {
        return in_array($user->role?->key, $roles, true);
    }

    protected function owns(User $user, mixed $record): bool
    {
        return isset($record->user_id) && (int) $record->user_id === (int) $user->id;
    }
}
