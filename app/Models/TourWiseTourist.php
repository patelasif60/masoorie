<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TourWiseTourist extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

     /**
     * Get user detail.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

     /**
     * Get user detail.
     */
    public function tourDetail()
    {
        return $this->belongsTo(\App\Models\TourDetail::class, 'tour_id');
    }
}
