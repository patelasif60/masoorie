<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
     /**
     * Get Country detail.
     */
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class, 'country_id');
    }
}
