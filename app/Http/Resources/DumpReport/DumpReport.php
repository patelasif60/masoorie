<?php

namespace App\Http\Resources\DumpReport;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DumpReport extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       
      return [
        'registration_number'     => $this->tourDetail->registration_number,
        'from_date'               => $this->tourDetail->from_date,
        'to_date'                 => $this->tourDetail->to_date,
        'name'                    => $this->name,
        'age'                     => $this->age,
        'gender'                  => $this->gender,
        'id_proof'                => $this->tourDetail->document->name,
        'mobile_no'               => $this->tourDetail->mobile_no,
        'emergency_contact_no'    => $this->tourDetail->emergency_contact_no,
        'mode_of_travel'          => $this->tourDetail->mode_of_travel,
        'vehicle_no'              => $this->tourDetail->vehicle_no,
        'accommodation'           => $this->tourDetail->accommodation,
        'name_Of_accommodation'   => $this->tourDetail->name_Of_accommodation,
        'country'                 => $this->tourDetail->country->name,
        'state'                   => isset($this->tourDetail->state) ? $this->tourDetail->state->name : '',
        'district'                => isset($this->tourDetail->district) ? $this->tourDetail->district->name:'',
        'city'                    => $this->tourDetail->city,
        'is_primary'              => $this->is_primary == 0 ? 'No':'Yes', 
        'user_type'               => $this->tourDetail->user->user_type == 'indian' ? 'Indian' : 'Foreigner',
      ];
    }
}
