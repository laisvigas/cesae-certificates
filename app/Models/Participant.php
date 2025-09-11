<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'document_type',
        'document_number',
    ];

    /**
     * Events this participant is assigned to.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_participant')
                    ->withTimestamps();
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
