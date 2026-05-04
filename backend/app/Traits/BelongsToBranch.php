<?php

namespace App\Traits;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToBranch
{
    /**
     * The "booted" method of the model.
     * Automatically applies the Branch Scope.
     */
    protected static function booted()
    {
        static::addGlobalScope('branch', function (Builder $builder) {
            // Only apply if user is authenticated
            if (Auth::check()) {
                $user = Auth::user();
                
                // Super Admin with specific email can see everything if they want, 
                // but usually we want to see the ACTIVE branch context.
                // We will use the branch_id stored on the user session/model.
                if ($user->branch_id) {
                    $builder->where($builder->getModel()->getTable() . '.branch_id', $user->branch_id);
                } else {
                    // If branch_id is null, it means "All Branches" (Consolidated View)
                    // Only allowed for Super Admin/Owner. 
                    // Regular users should always have a branch_id assigned.
                }
            }
        });

        /**
         * Automatically set branch_id when creating a new record.
         */
        static::creating(function (Model $model) {
            if (Auth::check() && !$model->branch_id) {
                $model->branch_id = Auth::user()->branch_id;
            }
        });
    }

    /**
     * Relationship to the branch.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
