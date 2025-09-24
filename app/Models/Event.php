<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;


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
        // referência ao template
        'template_id',
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

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }


    // facilita mostrar a imagem de assinatura na view
    public function getIssuerSignatureUrlAttribute(): ?string
    {
        return $this->issuer_signature_path
            ? Storage::url($this->issuer_signature_path)
            : null;
    }

    /** Filtra por tipos (ids ou 'null') */
    public function scopeFilterTypes(Builder $q, array $selected): Builder
    {
        if (empty($selected)) return $q;

        $wantsNull = in_array('null', $selected, true);
        $ids = array_filter($selected, fn($v) => $v !== 'null');

        return $q->where(function ($qq) use ($ids, $wantsNull) {
            if (!empty($ids)) {
                $qq->whereIn('event_type_id', $ids);
            }
            if ($wantsNull) {
                $qq->orWhereNull('event_type_id');
            }
        });
    }

    /** Filtra por status (past|ongoing|upcoming) */
    public function scopeFilterStatus(Builder $q, array $selectedStatus, ?Carbon $now = null): Builder
    {
        if (empty($selectedStatus)) return $q;
        $now ??= Carbon::now();

        return $q->where(function ($qq) use ($selectedStatus, $now) {
            if (in_array('past', $selectedStatus, true)) {
                $qq->orWhere('end_at', '<', $now);
            }
            if (in_array('ongoing', $selectedStatus, true)) {
                $qq->orWhere(function ($q2) use ($now) {
                    $q2->where('start_at', '<=', $now)
                    ->where('end_at', '>=', $now);
                });
            }
            if (in_array('upcoming', $selectedStatus, true)) {
                $qq->orWhere('start_at', '>', $now);
            }
        });
    }

        public function getStatusAttribute(): string
    {
        $now = now();
        if ($this->end_at?->lt($now)) return 'Encerrado';
        if ($this->start_at?->gt($now)) return 'Agendado';
        return 'A decorrer';
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->status) {
            'Encerrado'  => 'bg-gray-100 text-gray-700',
            'Agendado'   => 'bg-blue-100 text-blue-800',
            'A decorrer' => 'bg-green-100 text-green-800',
            default      => 'bg-gray-100 text-gray-700',
        };
    }

}
