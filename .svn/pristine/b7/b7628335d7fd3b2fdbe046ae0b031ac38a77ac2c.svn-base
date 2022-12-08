<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    //
    protected $fillable = ['UserID', 'WorkTaskID'];
    public function user(){
        return $this->belongsTo(User::class, "WorkTaskID");
    }
}
