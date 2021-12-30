<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [ 'last_active', 'date', 'time' ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'owner_id');
    }

    public function locations()
    {
        return $this->hasMany(Locations::class, 'owner_id');
    }

    public function getLastActiveAttribute($value)
    {
        $updateAt = $this->attributes['updated_at'];
        if(is_null($updateAt)) return null;
        $days = Carbon::createFromTimeString($updateAt)->diffInDays();
        if($days > 0) return $days . " Days";
        
        $hours = Carbon::createFromTimeString($updateAt)->diffInHours();
        if($hours > 0) return $hours . " hours";
        return Carbon::createFromTimeString($updateAt)->diffInMinutes() . " minutes";

    }

    public function getTimeAttribute($value)
    {
        $updateAt = $this->attributes['updated_at'];
        if(is_null($updateAt)) return null;
        return Carbon::createFromTimestamp($updateAt)->format('h:i A');
    }

    public function getDateAttribute($value)
    {
        $updateAt = $this->attributes['updated_at'];
        if(is_null($updateAt)) return null;
        return Carbon::createFromTimeString($updateAt)->format('M d, Y');
    }
}
