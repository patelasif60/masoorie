<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\{Country, City, State, District, Document};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Country\Country as CountryResource;
use App\Http\Resources\State\State as StateResource;
use App\Http\Resources\District\District as DistrictResource;
use App\Http\Resources\Document\Document as DocumentResource;

class ContryStateCity extends Controller
{
    public function getCountries()
    {
        $country = Country::all();
        return response()->json(['data' => CountryResource::collection($country)]);
    }
    public function getStates($id)
    {
        $state = State::where('country_id',$id)->get();
        return response()->json(['data' => StateResource::collection($state)]);
    }
    public function getDistricts($id)
    {
        $district = District::where('state_id',$id)->get();
        return response()->json(['data' => DistrictResource::collection($district)]);
    }
    public function getDocuments()
    {
        $document = Document::all();
        return response()->json(['data' => DocumentResource::collection($document)]);
    }
}