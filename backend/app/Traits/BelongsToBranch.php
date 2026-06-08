<?php

namespace App\Traits;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToBranch
{
    /**
     * Automatically applies the Branch Scope.
     */
    protected static function bootBelongsToBranch()
    {
        static::addGlobalScope('branch', function (Builder $builder) {
            // Only apply if user is authenticated and not running in console
            if (Auth::check() && !app()->runningInConsole()) {
                $user = Auth::user();
                
                // If the user has explicitly selected a branch (Active branch context)
                if ($user->branch_id) {
                    $builder->where(function($q) use ($builder, $user) {
                        $q->where($builder->getModel()->getTable() . '.branch_id', $user->branch_id);
                        
                        // Handle Global Accounts via is_global flag
                        if ($builder->getModel() instanceof \App\Models\Account) {
                            $q->orWhere('is_global', true);
                        }
                    });
                } else {
                    // If branch_id is null, it means "All Branches" (Consolidated View)
                    // If the user is NOT a Super Admin, restrict them to their authorized branches ONLY
                    if (!$user->hasRole('Super Admin') && $user->email !== 'rebin.maaruf@gmail.com') {
                        $authorizedBranchIds = $user->branches()->pluck('branches.id')->toArray();
                        $builder->where(function($q) use ($builder, $authorizedBranchIds) {
                            $q->whereIn($builder->getModel()->getTable() . '.branch_id', $authorizedBranchIds);
                            
                            // Allow Global Accounts to be seen in Consolidated View
                            if ($builder->getModel() instanceof \App\Models\Account) {
                                $q->orWhere('is_global', true);
                            }
                        });
                    }
                    // For Super Admins, we don't apply any restriction so they see EVERYTHING
                }
            }
        });

        /**
         * Automatically set branch_id when creating a new record.
         */
        static::creating(function (Model $model) {
            if (Auth::check() && !$model->branch_id) {
                // If it is a global account, keep branch_id null
                if ($model instanceof \App\Models\Account && $model->is_global) {
                    return;
                }
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
