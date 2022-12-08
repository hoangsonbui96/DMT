<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ErrorReview extends Model
{
    //
    protected $guarded = [];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'AcceptedByID');
    }
}
