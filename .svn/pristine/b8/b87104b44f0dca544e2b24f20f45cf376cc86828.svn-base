<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Timekeeping extends Model
{
    protected $table = 'timekeepings_new';

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'UserID');
    }
}
