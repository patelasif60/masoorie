<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
      /**
     * Get State detail.
     */
    public function state()
    {
        return $this->belongsTo(\App\Models\State::class, 'state_id');
    }
}
