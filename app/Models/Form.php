<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    //

    protected $fillable = [
        'schema',
        'meta'
    ];

    protected $casts = [
        'meta' => 'array',
        'schema' => 'array',
    ];
}
