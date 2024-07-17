<?php

namespace App\Http\Resources\TourDetail;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Country\Country as CountryResource;
use App\Http\Resources\State\State as StateResource;
use App\Http\Resources\District\District as DistrictResource;
use App\Http\Resources\TourWiseTourists\TourWiseTourists as TourWiseTouristsResource;
use App\Http\Resources\Document\Document as DocumentResource;

class TourDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       
      return [
        'id'                      => $this->id,     
        'user'                    => $this->user->id,
        'country_id'              => $this->country_id,
        'state_id'                => $this->state_id,
        'district_id'             => $this->district_id, 
        'country'                 => new CountryResource($this->country),
        'state'                   => new StateResource($this->state),
        'district'                => new DistrictResource($this->district),
        'city'                    => $this->city,
        'from_date'               => $this->from_date,
        'registration_number'     => $this->registration_number,
        'to_date'                 => $this->to_date,
        'name'                    => $this->name,
        'age'                     => $this->age,
        'gender'                  => $this->gender,
        'mobile_country_code'     => $this->mobile_country_code,
        'mobile_country_code_details'  =>$this->mobileCountryCodeDetails(),
        'country'                 => new CountryResource($this->country),
        'mobile_no'               => $this->mobile_no,
        'email'                   => $this->email,
        'emergency_country_code_details'   => $this->emergencyCountryCodeDetails(),
        'emergency_country_code'  => $this->emergency_country_code,
        'emergency_contact_no'    => $this->emergency_contact_no,
        'mode_of_travel'          => $this->mode_of_travel,
        'vehicle_no'              => $this->vehicle_no,
        'accommodation'           => $this->accommodation,
        'name_Of_accommodation'   => $this->name_Of_accommodation,
        'id_proof'                => new DocumentResource($this->document),
        'id_number'               => $this->id_number,
        'Proof_image_1_url'       => $this->Proof_image_1_url,
        'Proof_image_1_file_name' => $this->Proof_image_1_file_name,
      //  'Proof_image_2_url'       => $this->Proof_image_2_url,
      //'Proof_image_2_file_name' => $this->Proof_image_2_file_name,
        'tourists'               => TourWiseTouristsResource::collection($this->TourWiseTourist)
      ];
    }
}
