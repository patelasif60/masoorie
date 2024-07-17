<?php

namespace App\Repositories;

use App\Models\{User,TourDetail,TourWiseTourist};
use App\Mail\MyEmail;
use Illuminate\Support\Facades\Mail;

class TourRepository extends BaseRepository
{
    public function create($user, $data)
    {
        $registrationNumber = $this->generateRegistrationNumber();
        $tourId = $data['id']; // Assuming the 'id' is present in the request
        $tour = TourDetail::find($tourId);
        if ($tour) {
            $tour->delete();
            $tour->TourWiseTourist()->delete();
        }
        $tour = new TourDetail;
        $tour->registration_number = $registrationNumber;
        $tour->user_id = $user->id;
        $tour->country_id = $data['country'];
        $tour->state_id = $data['state'];
        $tour->district_id = $data['district'];
        $tour->city = $data['city'];
        $tour->from_date = $data['from_date'];
        $tour->to_date = $data['to_date'];
        $tour->name = $data['name'];
        $tour->age = $data['age'];
        $tour->gender = $data['gender'];
        $tour->mobile_country_code = $data['mobile_country_code'];
        $tour->mobile_no = $data['mobile_no'];
        $tour->email = $data['email'];
        $tour->emergency_country_code = $data['emergency_country_code'];
        $tour->emergency_contact_no = $data['emergency_contact_no'];
        $tour->mode_of_travel = $data['mode_of_travel'];
        $tour->vehicle_no = $data['vehicle_no'];
        $tour->accommodation = $data['accommodation'];
        $tour->name_Of_accommodation = $data['name_Of_accommodation'];
        $tour->id_proof = $data['id_proof'];
        $tour->id_number = $data['id_number'];
        $tour->Proof_image_1_url = $data['Proof_image_1_url'];
        $tour->Proof_image_1_file_name = $data['Proof_image_1_file_name'];
        $tour->mode_of_tour_generate = $data['mode_of_tour_generate'];
        $tour->save();
        foreach($data['tourists'] as $key=>$val){
            $is_primary = $key == 0 ? 1 : 0 ;
            TourWiseTourist::create([
                'tour_id'   => $tour->id,
                'user_id'   => $user->id,
                'name'      => $val['name'],
                'age'       => $val['age'],
                'gender'    => $val['gender'],
                'is_primary' => $is_primary,
            ]);
        }
        $noturists = count($data['tourists']) - 1;
        if($user->user_type == 'indian'){
            $SenderID='EILTSM';
            $TemplateID='1707170549903427098';
            $EntityID='1701159187680330643';
            $Msg = 'Welcome to Mussoorie! Your registration of tourist 1+'.$noturists.' is successful. Your Reg. No is :'.$registrationNumber . '. Enjoy your visit. -EILTSM';
            $apiResponse = http_post($SenderID,$user->username,$TemplateID,$EntityID,$Msg);
        }else{
            $noturists = count($data['tourists']) - 1;
            $Msg = 'Welcome to Mussoorie! Your registration of tourist is successful.Your Reg. No is : '.$registrationNumber.'. Enjoy your visit.';
            if($noturists>0){
                $Msg = 'Welcome to Mussoorie! Your registration of tourist 1 + '.$noturists.' is successful.Your Reg. No is : '.$registrationNumber.'. Enjoy your visit.';
            }
            Mail::to($user->username)->send(new MyEmail('Tour',$Msg));
        }
        return $tour;
    }
    function generateRegistrationNumber()
    {
        // Get the current date in the format ddmmyy
        $currentDate = 4 .date('dmy');
        //dd($currentDate);
        // Get the maximum registration number for the current date
        $maxRegistrationNumber = TourDetail::withTrashed()->where('registration_number', 'like', "{$currentDate}%")
            ->max('registration_number');
        //dd($maxRegistrationNumber);
        // Extract the counter part and increment it
        $counter = (int)substr($maxRegistrationNumber, 7) + 1;

        // Format the counter with leading zeros
        $formattedCounter = sprintf('%05d', $counter);

        // Concatenate the date and counter to form the registration number
        $registrationNumber = $currentDate . $formattedCounter;

        return $registrationNumber;
    }
}