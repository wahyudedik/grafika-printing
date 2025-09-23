<?php

namespace App\Models\User;

use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Base model for user tenant context
 * 
 * This model automatically applies user-based tenant scoping
 * to ensure data isolation between different users.
 */
abstract class UserTenantModel extends Model
{
    /**
     * Boot the model and apply tenant scoping.
     */
    protected static function booted()
    {
        static::addGlobalScope('user_tenant', function (Builder $builder) {
            $tenantManager = app(TenantManager::class);

            if ($tenantManager->hasUserContext()) {
                $userId = $tenantManager->getUserId();
                $builder->where('user_id', $userId);
            }
        });

        // Automatically set user_id when creating records
        static::creating(function ($model) {
            $tenantManager = app(TenantManager::class);

            if ($tenantManager->hasUserContext() && !$model->user_id) {
                $model->user_id = $tenantManager->getUserId();
            }
        });
    }

    /**
     * Get the user that owns the model.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Scope a query to only include records for the current user.
     */
    public function scopeForCurrentUser(Builder $query)
    {
        $tenantManager = app(TenantManager::class);

        if ($tenantManager->hasUserContext()) {
            return $query->where('user_id', $tenantManager->getUserId());
        }

        return $query;
    }

    /**
     * Scope a query to only include records for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
