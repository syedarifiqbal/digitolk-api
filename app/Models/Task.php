<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['summary', 'description', 'due_at', 'completed', 'next_notification_at', 'notified'];

    protected $casts = ['due_at' => 'datetime', 'next_notification_at' => 'datetime', 'completed' => 'boolean'];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function getDueAtAttribute($value)
    {
        return Carbon::createFromTimeString($value)->format('Y-m-d h:i');
    }
}
