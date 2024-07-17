<?php

namespace App\Http\Resources\TourWiseTourists;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourWiseTourists extends JsonResource
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
        'user' => $this->user->id,
        'name' => $this->name,
        'age'  => $this->age,
        'gender' => $this->gender,
      ];
    }
}
