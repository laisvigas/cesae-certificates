<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'event_id',
        'participant_id',
        'ref',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(\App\Models\Event::class);
    }

    public function participant()
    {
        return $this->belongsTo(\App\Models\Participant::class);
    }
}
