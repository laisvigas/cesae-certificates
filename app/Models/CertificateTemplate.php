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
    // This $casts property tells Laravel to automatically convert the options JSON column
    // from a JSON string (when reading from the database) into a native PHP array. Therefore,
    // when the controller show() method is called, $template->options is already a PHP array,
    // so there is no need to use json_decode() (which expects a JSON string)


    public function events()
    {
        return $this->hasMany(\App\Models\Event::class, 'template_id');
    }

}
