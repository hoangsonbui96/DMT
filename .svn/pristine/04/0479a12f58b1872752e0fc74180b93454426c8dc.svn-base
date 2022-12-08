<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
//    public function setBirthdayAttribute( $value ) {
//        $this->attributes['Birthday'] = (new Carbon($value))->format('d/m/Y');
//    }

    public function scopeFullName($query, $value)
    {
        if (!$value) return $query;
        return $query->where("FullName", "like", "%" . $value . "%");
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class, "UserID");
    }

    public function timekeepings()
    {
        return $this->hasMany(\App\Model\Timekeeping::class, "UserID");
    }
    public function timekeepings_new()
    {
        return $this->hasMany(TimekeepingNew::class, "UserID","id");
    }
    public function absences() {
        return $this->hasMany(\App\Model\Absence::class, "UID");
    }
}
