<?php

namespace App\Http\Resources\District;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class District extends JsonResource
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
        'stateId' => $this->state_id,
        'stateName'=>$this->state->name,
      ];
    }
}
