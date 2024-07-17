<?php

namespace App\Http\Controllers\API;

//use Validator;
use App\Models\{User,TourDetail,TourWiseTourist};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TourService;
use JWTAuth;
use Validator;
use App\Http\Resources\TourDetail\TourDetail as TourDetailResource;
use App\Http\Resources\TourWiseTourists\TourWiseTouristsDetail as TourwiseTouristsDetailResource;
use App\Http\Resources\DumpReport\DumpReport as DumpReportResource;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;
use File;
use Illuminate\Support\Facades\DB;



class TourController extends Controller
{
    /**
	 * Create a hospitality suite service variable.
	 *
	 * @return void
	 */
	protected $service;

    /**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(TourService $service)
	{
		$this->service = $service;
	}

    public function addTour(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' =>'required',
            'to_date' =>'required',
            'name' =>'required',
            'age' =>'required',
            'gender' =>'required',
            'emergency_contact_no' =>'required',
            'country' =>'required',
            'state' =>'required_if:country,1',
            'mode_of_travel' =>'required',
            'id_proof' =>'required',
            'id_number' =>[
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    // Check ID proof type and validate length accordingly
                    $idProofType = $request->input('id_proof');
                    if ($idProofType == 1 && strlen($value) !== 12) {
                        $fail('The ID Number must be 12 characters.');
                    } elseif (($idProofType == 3 || $idProofType == 4) && strlen($value) !== 10) {
                        $fail('The ID Number must be 10 characters.');
                    } elseif (($idProofType == 2 || $idProofType == 5) && strlen($value) > 15) {
                        $fail('The ID Number must be 15 characters or less.');
                    }
                },
            ],
        ],[
            'state.required_if' => 'The state field is required when the country is India.',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(),'status'=>false,"statusCode" => 401], 401);
        }
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$request->proof_image_1));
        $sizeInKB = strlen($imageData)/1024;
        if ($sizeInKB > 2048) {
            return response()->json(['error' => 'Validation failed.Proof image 1 size exceeds the limit.','status'=>false,"statusCode" => 401], 401);
        }
        $user = JWTAuth::user();
        return $this->service->saveTour($user, $request->all());
    }
    public function getTour($id){
        $user = JWTAuth::user();
        $TourDetail = TourDetail::where('id',$id)->get();
        if ($TourDetail && $TourDetail->first()->user_id == $user->id) {
            return response()->json(['data' => TourDetailResource::collection($TourDetail)]);
        }else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function myTrip(){
        $user = JWTAuth::user();
        $TourDetail = TourDetail::where('user_id',$user->id)->orderBy('id', 'desc')->get();
        return response()->json(['data' => TourwiseTouristsDetailResource::collection($TourDetail)]);
    }
    public function tourDownload($id){
        return $this->service->tourDownload($id);
    }
    public function deleteTour($id){
        $user = JWTAuth::user();
        $TourDetail = TourDetail::find($id);
        if ($TourDetail && $TourDetail->user_id == $user->id) {
            $TourDetail->delete();
            return response()->json(['message' => 'Tour delete successfully','status'=>true,"statusCode" => 200],200);
        }else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function touristsCount(Request $request){
        return $this->service->touristsCount($request);
    }
    public function dumpReport(Request $request){
        return $this->service->dumpReport($request);
    }
    public function dateWiseReport(Request $request){
        return $this->service->dateWiseReport($request);
    }
    public function dateWiseVehicleReport(Request $request){
        return $this->service->dateWiseVehicleReport($request);
    }
    public function stateWiseTouriest(Request $request){
        return $this->service->stateWiseTouriest($request);
    }
}