<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mutation extends Model
{
    protected $table = 'dna';

    protected $fillable = [
        'dna',
        'isMutant'
    ];

}
