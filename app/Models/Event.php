<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_at',
        'end_at',
        'hours',
        'event_type_id',
        // novos campos do emissor:
        'issuer_institution',
        'issuer_name',
        'issuer_role',
        'issuer_signature_path',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
        'hours'    => 'integer',
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

    // facilita mostrar a imagem de assinatura na view
    public function getIssuerSignatureUrlAttribute(): ?string
    {
        return $this->issuer_signature_path
            ? Storage::url($this->issuer_signature_path)
            : null;
    }
}
