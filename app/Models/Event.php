<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_at',
        'end_at',
        'hours',
        'event_type_id',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    public function participants()
    {
    return $this->belongsToMany(Participant::class, 'event_participant')
                ->withTimestamps();
    }

    public function type()
    {
        return $this->belongsTo(\App\Models\EventType::class, 'event_type_id');
    }

}
