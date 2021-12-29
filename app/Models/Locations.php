<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    use HasFactory;
    
    protected $fillable = ['location', 'lat', 'lng'];

    protected $appends = ['time', 'date'];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function getTimeAttribute($value)
    {
        if(!isset($this->attributes['created_at'])){return '';}
        return Carbon::createFromTimestamp($this->attributes['created_at'])->format('h:i A');
    }

    public function getDateAttribute($value)
    {
        if(!isset($this->attributes['created_at'])){return '';}
        return Carbon::createFromTimeString($this->attributes['created_at'])->format('M d, Y');
    }
}
