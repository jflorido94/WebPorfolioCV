<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait AuthorizesOwnership
{
    /**
     * Abort with 403 unless the authenticated user owns the given model.
     */
    protected function authorizeOwnership(Model $model, string $foreignKey = 'user_id'): void
    {
        abort_unless($model->getAttribute($foreignKey) === Auth::id(), 403);
    }
}
