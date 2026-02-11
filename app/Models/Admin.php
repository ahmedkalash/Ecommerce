<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Admin Model
 *
 * This model extends User and applies a global scope to ensure
 * only users with user_type 'admin' or 'staff' are retrieved.
 * This provides type safety and prevents accidentally querying
 * customers, sellers, or delivery boys when working with admins.
 */
class Admin extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        parent::booted();

        // Apply global scope to filter only admin and staff users
        static::addGlobalScope('admin_users', function (Builder $query) {
            $query->whereIn('user_type', ['admin', 'staff']);
        });

        // AUTO-ASSIGN TYPE
        // If you create a new Admin via code (Admin::create), automatically set user_type to 'admin'
        static::creating(function (Admin $model) {
            $model->user_type = 'admin';
        });
    }
}
