<?php
namespace App\Services;

use App\Repositories\TourRepository;
use App\Http\Resources\TourDetail\TourDetail as TourDetailResource;
use App\Models\{User,TourDetail,TourWiseTourist};
use JWTAuth;
class TourService
{
	/**
	 * The category repository instance.
	 *
	 * @var repository
	 */
	protected $repository;
	


	/**
	 * Create a new service instance.
	 *
	 * @param TourRepository $repository
	 */
	public function __construct(TourRepository $repository)
	{
		$this->repository = $repository;
		$this->imagePath = config('mussoori.IMAGEPATH.tour_proof_photo');
	}
    /**
	 * Handle logic to save hospitality suite notification.
	 *
	 * @param $user
	 * @param $data
	 *
	 * @return mixed
	 */
	public function saveTour($user, $data)
	{
        //dd($data['Proof_image_1']);
		if (isset($data['proof_image_1'])) {
			$banner = uploadBase64ToLocal($data['proof_image_1'], $this->imagePath);
			$data['Proof_image_1_url'] = $banner['url'];
			$data['Proof_image_1_file_name'] = $banner['file_name'];
		}else{
			$data['Proof_image_1_url'] = null;
			$data['Proof_image_1_file_name'] = null;
		}

        if (isset($data['proof_image_2'])) { 
			$banner = uploadBase64ToLocal($data['proof_image_2'], $this->imagePath);
			$data['Proof_image_2_url'] = $banner['url'];
			$data['Proof_image_2_file_name'] = $banner['file_name'];
		}
        $tourDetail = $this->repository->create($user, $data);
		
		if(!$user->name){
			$user->name =  $data['name'];
			$user->age =  $data['age'];
			$user->save();
		}
		
		return response()->json(['message' => 'Tour added successfully','status'=>true,"statusCode" => 200,'tourDetail'=> new TourDetailResource($tourDetail)],200);
    }
	public function tourDownload($id){
		$user = JWTAuth::user();
        $tourDetail = TourDetail::with('TourWiseTourist','country','state','district','document')->find($id)->toArray();
        if ($tourDetail && $tourDetail['user_id'] == $user->id) {
            $qrcodeImageName = time().'.png';
            $path = public_path('images/QrCode/'.$qrcodeImageName);
            $qrCode = \QrCode::format('png')->size(100)->generate($tourDetail['registration_number'], $path);
            $pdf = PDF::loadView('pdfs.tour_detail', ['tourDetail' => $tourDetail , 'qrcodeImageName'=>$qrcodeImageName]); // Blade view for the PDF content
            $pdfContent = $pdf->output();
            $base64 = base64_encode($pdfContent);
            return response()->json(['base64' => $base64,'registration_number'=>$tourDetail['registration_number']]);
        }else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }
	}
	public function touristsCount($request){
        $tourWiseDetail = TourWiseTourist::all();
        $data['totalRegisterdTourist'] = TourWiseTourist::whereHas('tourDetail', function ($query)   use ($request) {
            $query->whereBetween('tour_details.from_date', [$request['from_date'],  $request['to_date']]);
        })->count();
        $data['touristUptoTen'] = TourWiseTourist::whereHas('tourDetail', function ($query)   use ($request) {
            $query->whereBetween('tour_details.from_date', [$request['from_date'],  $request['to_date']]);
        })->where('age','<=',10)->count();
        $data['touristUptoTentoTwenty'] = TourWiseTourist::whereHas('tourDetail', function ($query)   use ($request) {
            $query->whereBetween('tour_details.from_date', [$request['from_date'],  $request['to_date']]);
        })->where('age', '>', 10)->where('age', '<=', 20)->count();
        $data['touristUptoTwentytoforty'] = TourWiseTourist::whereHas('tourDetail', function ($query)   use ($request) {
            $query->whereBetween('tour_details.from_date', [$request['from_date'],  $request['to_date']]);
        })->where('age', '>', 20)->where('age', '<=', 40)->count();
        $data['touristUptoFourtytoSixtyfive'] = TourWiseTourist::whereHas('tourDetail', function ($query)   use ($request) {
            $query->whereBetween('tour_details.from_date', [$request['from_date'],  $request['to_date']]);
        })->where('age', '>', 40)->where('age', '<=', 65)->count();
        $data['touristMoreThanSixtyfive'] = TourWiseTourist::whereHas('tourDetail', function ($query)   use ($request) {
            $query->whereBetween('tour_details.from_date', [$request['from_date'],  $request['to_date']]);
        })->where('age', '>', 65)->count();
        
        
        $data['tourDetailTwowheel'] = TourWiseTourist::whereHas('tourDetail', function ($query)  use ($request) {
            $query->where('mode_of_travel', '2 Wheeler')->whereBetween('tour_details.from_date', [$request['from_date'],  $request['to_date']]);
        })->count();
        $data['tourDetailFourwheel'] = TourWiseTourist::whereHas('tourDetail', function ($query)  use ($request) {
            $query->where('mode_of_travel', '4 Wheeler')->whereBetween('tour_details.from_date', [$request['from_date'],  $request['to_date']]);
        })->count();
        $data['tourDetailBus'] = TourWiseTourist::whereHas('tourDetail', function ($query)  use ($request) {
            $query->where('mode_of_travel', 'Bus')->whereBetween('tour_details.from_date', [$request['from_date'],  $request['to_date']]);
        })->count();
        $data['tourDetailBicycle'] = TourWiseTourist::whereHas('tourDetail', function ($query)  use ($request) {
            $query->where('mode_of_travel', 'Bicycle')->whereBetween('tour_details.from_date', [$request['from_date'],  $request['to_date']]);
        })->count();
        $data['tourDetailCommercialVehicle'] = TourWiseTourist::whereHas('tourDetail', function ($query)  use ($request) {
            $query->where('mode_of_travel', 'Commercial Vehicle')->whereBetween('tour_details.from_date', [$request['from_date'],  $request['to_date']]);
        })->count();
        return response()->json(['data' => array($data)]);

    }
	public function dumpReport($request){
        
        $perPage = $request->input('length', 50);
        $page= ( ($request->start + $perPage) / $perPage);
        $query = TourWiseTourist::with('tourDetail','tourDetail.user','tourDetail.country','tourDetail.state','tourDetail.district');
        
        if (!empty($request->input('from_date'))) {
            $query->whereHas("tourDetail", function ($q) use ($request) {
                $q->whereDate('from_date', '>=', $request->input('from_date'));
            });
        }
        if (!empty($request->input('to_date'))) {
            $query->whereHas("tourDetail", function ($q) use ($request) {
                $q->whereDate('to_date', '<=', $request->input('to_date'));
            });
        }    
        if (!empty($request->input('name'))) {
            $query->where('name', 'like', '%'.$request->input('name').'%');
        }
        if (!empty($request->input('from_age'))) {
            $query->where('age','>=',$request->input('from_age'));
        }
        if (!empty($request->input('to_age'))) {
            $query->where('age','<=',$request->input('to_age'));
        }
        if (!empty($request->input('gender'))) {
            $query->where('gender',$request->input('gender'));
        }
        if (!empty($request->input('country_id'))) {
            $query->whereHas("tourDetail", function ($q) use ($request) {
                $q->where('country_id',$request->input('country_id'));
            });
        }
        if (!empty($request->input('emergency_contact_no'))) {
            $query->whereHas("tourDetail", function ($q) use ($request) {
                $q->where('emergency_contact_no',$request->input('emergency_contact_no'));
            });
        }
        if (!empty($request->input('mode_of_travel'))) {
            $query->whereHas("tourDetail", function ($q) use ($request) {
                $q->where('mode_of_travel',$request->input('mode_of_travel'));
            });
        }
        if (!empty($request->input('accommodation'))) {
            $query->whereHas("tourDetail", function ($q) use ($request) {
                $q->where('accommodation',$request->input('accommodation'));
            });
        }
        if (!empty($request->input('state_id'))) {
            $query->whereHas("tourDetail", function ($q) use ($request) {
                $q->where('state_id',$request->input('state_id'));
            });
        }
        if (!empty($request->input('user_type'))) {
            $query->whereHas("tourDetail", function ($q) use ($request) {
                $q->whereHas("user", function ($q) use ($request) {
                    $q->where('user_type', $request->input('user_type'));
                });
            });
        }
        $totalRecords = $query->count();
        $tourDetails = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json(['data' => DumpReportResource::collection($tourDetails),'totalRecords'=>$totalRecords]);
    }
    public function dateWiseReport($request){
        $startDateStr =  $request['from_date'];
        $endDateStr = $request['to_date'];
        $touristMaleData = $this->queryForDateWise($startDateStr,$endDateStr,'Male','tourist');
        $touristFemaleData = $this->queryForDateWise($startDateStr,$endDateStr,'Female','tourist');
        $touristOtherData =  $this->queryForDateWise($startDateStr,$endDateStr,'Other','tourist');
        $touristAllData = $this->queryForDateWise($startDateStr,$endDateStr,'all','tourist');
        $lableDate = array_column($touristMaleData, 'from_date');
        $maleData = array_column($touristMaleData, 'count');
        $femaleData = array_column($touristFemaleData, 'count');
        $otherData = array_column($touristOtherData, 'count');
        $alleData = array_column($touristAllData, 'count');
        return response()->json(['label' => $lableDate,'maledata'=>$maleData,'femaledata'=>$femaleData,'other'=>$otherData,'alldata'=>$alleData]);
    }
    public function queryForDateWise($startDate,$endDate,$type,$reportType){
        $str = null;
        $modestr= null;
        
        if($type!= 'all' && $reportType == 'tourist'){
            $str = "AND tour_wise_tourists.gender = '".$type."'";
        }
        if($type != 'all' && $reportType == 'vehicle'){
            $modestr = "AND tour_details.mode_of_travel = '".$type."'";
        }
        
        return DB::select("
            SELECT date_range.d as from_date, COALESCE(COUNT(tour_wise_tourists.id), 0) as count
            FROM (
                SELECT DATE_ADD('".$startDate."', INTERVAL a DAY) as d
                FROM (
                    SELECT a + b*10 as a
                    FROM (
                        SELECT 0 as a UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
                        UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9
                    ) a,
                    (SELECT 0 as b UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
                        UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) b
                    ORDER BY a
                ) days
                WHERE DATE_ADD('".$startDate."', INTERVAL a DAY) BETWEEN '".$startDate."' AND '".$endDate."'
            ) date_range
            LEFT JOIN tour_details ON date_range.d = DATE(tour_details.from_date) ".$modestr."
            LEFT JOIN tour_wise_tourists ON tour_details.id = tour_wise_tourists.tour_id ".$str."
            GROUP BY date_range.d
            ORDER BY date_range.d
        ");
    }
    public function dateWiseVehicleReport($request){
        $startDateStr =  $request['from_date'];
        $endDateStr = $request['to_date'];
        $twoWheelerData = $this->queryForDateWise($startDateStr,$endDateStr,'2 Wheeler','vehicle');
        $fourWheelerData = $this->queryForDateWise($startDateStr,$endDateStr,'4 Wheeler','vehicle');
        $busData = $this->queryForDateWise($startDateStr,$endDateStr,'Bus','vehicle');
        $bicycleData = $this->queryForDateWise($startDateStr,$endDateStr,'Bicycle','vehicle');
        $commercialData = $this->queryForDateWise($startDateStr,$endDateStr,'Commercial Vehicle','vehicle');
        $allData = $this->queryForDateWise($startDateStr,$endDateStr,'all','vehicle');
        $twoWheeler = array_column($twoWheelerData, 'count');
        $fourWheeler = array_column($fourWheelerData, 'count');
        $bus = array_column($busData, 'count');
        $bicycle = array_column($bicycleData, 'count');
        $commercial = array_column($commercialData, 'count');
        $all= array_column($allData, 'count');
        $lableDate = array_column($twoWheelerData, 'from_date');
        return response()->json(['label' => $lableDate,'twoWheeler'=>$twoWheeler,'fourWheeler'=>$fourWheeler,'bus'=>$bus,'bicycle'=>$bicycle,'commercial'=>$commercial,'all'=>$all]);
    }
    public function stateWiseTouriest($request){
        $from_date =  $request['from_date'];
        $to_date = $request['to_date'];
        $touristData = DB::table('tour_wise_tourists')
            ->join('tour_details', 'tour_wise_tourists.tour_id', '=', 'tour_details.id')
            ->leftJoin('states', 'tour_details.state_id', '=', 'states.id')
            ->select(DB::raw("COALESCE(states.name, 'Foreign') AS state_name"), DB::raw('COUNT(*) as tourist_count'))
            ->whereBetween('tour_details.from_date', [$from_date, $to_date])
            ->groupBy('state_name')
            ->orderByRaw("state_name = 'Foreign', state_name")
            ->get();
        $stateWiseData = $touristData->pluck('tourist_count','state_name')->toArray();
        return response()->json(['stateWiseData'=>$stateWiseData]);
    }
}