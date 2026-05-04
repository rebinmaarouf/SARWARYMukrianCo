<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToBranch;

class ActivityLog extends Model
{
    use BelongsToBranch;

    protected $guarded = [];

    protected $casts = [
        'before' => 'json',
        'after' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
