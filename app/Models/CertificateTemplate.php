<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'name',
        'options' // json file
    ];

    protected $casts = [ // $casts transforma o arquivo json em um array
        'options' => 'array',
    ];


    public function events()
    {
        return $this->hasMany(\App\Models\Event::class, 'template_id');
    }

}
