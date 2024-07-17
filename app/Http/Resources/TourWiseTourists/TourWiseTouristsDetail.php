<?php

namespace App\Http\Resources\TourWiseTourists;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourWiseTouristsDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       
      return [
        'id'                => $this->id,
        'from_date'         => $this->from_date,
        'to_date'           => $this->to_date,
        'no_of_turist'      => $this->TourWiseTourist->count(),
        'registration_number' => $this->registration_number
      ];
    }
}
