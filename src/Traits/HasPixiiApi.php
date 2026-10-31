<?php

namespace PixiiBomb\Api\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PixiiBomb\Api\Models\Role;

trait HasPixiiApi
{
    /**
     * Get the role assigned to the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user's avatar asset path.
     */
    public function getAvatarPath(): string
    {
        $avatar = $this->avatar ?: 'default.png';

        return "images/avatars/$avatar";
    }
}
