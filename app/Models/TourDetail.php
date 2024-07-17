<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourDetail extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];
    
     /**
     * Get Country detail.
     */
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class, 'country_id');
    }
      /**
     * Get State detail.
     */
    public function state()
    {
        return $this->belongsTo(\App\Models\State::class, 'state_id');
    }
    /**
     * Get user detail.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    /**
     * Get State detail.
     */
    public function district()
    {
        return $this->belongsTo(\App\Models\District::class, 'district_id');
    }
    /**
     * Get a tourwise tourist detail.
     */
    public function TourWiseTourist()
    {
        return $this->hasMany(\App\Models\TourWiseTourist::class, 'tour_id');
    }
     /**
     * Get Document detail.
     */
    public function document()
    {
       return $this->belongsTo(\App\Models\Document::class, 'id_proof');

    }
    public function mobileCountryCodeDetails()
    {
        $data = \DB::select("select * FROM countries WHERE phone_code='$this->mobile_country_code' limit 1"); 
        return $data;
    }
    public function emergencyCountryCodeDetails()
    {
        $data = \DB::select("select * FROM countries WHERE phone_code='$this->emergency_country_code' limit 1");
        return $data;
    }
    
}
