<?php

namespace App\Http\Resources\Country;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Country extends JsonResource
{
    
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       
      return [
        'id' => $this->id,
        'name' => $this->name,
        'countryCode' => $this->phone_code,
        'iso2' => $this->iso2,
        'iso3' => $this->iso3,
      ];
    }
}
