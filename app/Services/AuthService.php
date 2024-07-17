<?php
namespace App\Services;

use Carbon\Carbon;
use App\Mail\MyEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\OtpCode;
use App\Models\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTFactory;

// use App\Repositories\TourRepository;
// use App\Http\Resources\TourDetail\TourDetail as TourDetailResource;
class AuthService
{
	public function getOtp($request)
	{
        $otpCode = mt_rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);
        if($request->userType == 'indian' || $request->userType == 'admin'){
            if($request->userType == 'admin'){
                $adminUser = User::where('username', $request->username)->where('user_type','admin')->first();
                if (!$adminUser) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
            }
            $SenderID= env('MUS_SENDERID');
            $TemplateID = env('MUS_TEMPLETEID');
            $EntityID= env('MUS_ENTITYID');
            $Msg = 'Your OTP '.$otpCode.' for Mussoorie visit registration ! UTDB -EILTSM';
            $apiResponse = http_post($SenderID,$request->username,$TemplateID,$EntityID,$Msg);
            $apiResponse = json_decode($apiResponse);
            //return response()->json($apiResponse);
           // if ($apiResponse->Status=="OK"){
                OtpCode::create([
                    'username' => $request->username,
                    'code' => $otpCode,
                    'expires_at' => $expiresAt,
                ]);
                $response = ['message' => 'OTP has been sent successully.','status'=>true,"statusCode" => 200];
          //  }else{
                $response = ['message' => 'Failed.','status'=>false,"statusCode" => 500];
          //  }
            return response()->json($response);
        }else{
            Mail::to($request->username)->send(new MyEmail('otp',$otpCode));
            OtpCode::create([
                'username' => $request->username,
                'code' => $otpCode,
                'expires_at' => $expiresAt,
            ]);
            $response = ['message' => 'OTP has been sent successully.','status'=>true,"statusCode" => 200];
            return response()->json($response);
        }
    }
    public function verifyOtp($request){
        $otpRecord = OtpCode::where('username', $request->username)->latest()->first();
        if (!$otpRecord) {
            return response()->json(['error' => 'User Name not found','status'=>false,"statusCode" => 200], 200);
        }
        if ($otpRecord->code != $request->otp) {
            return response()->json(['error' => 'Invalid OTP','status'=>false,"statusCode" => 200], 200);
        }
        $currentTime = now();
        $expirationTime = $otpRecord->expires_at;
    
        if ($currentTime > $expirationTime) {
            return response()->json(['error' => 'OTP has expired','status'=>false,"statusCode" => 200], 200);
        }
        if($request->userType == 'admin'){
            $adminUser = User::where('username', $request->username)->where('user_type','admin')->first();
            if (!$adminUser) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $expirationTime = $request->mode_of_login=='Web' ? Carbon::now()->addDay(1) : Carbon::now()->addDays(14);
            JWTFactory::setTTL($expirationTime->diffInMinutes());
            $token = JWTAuth::fromUser($adminUser);
            return response()->json(['message' => 'OTP verified successfully', 'status' => true, 'token' => $token, "statusCode" => 200], 200);
        }else{
            if(User::where('username',$request->username)->count() == 0){
                $user = new User();
                $user->username = $request->username;
                $user->user_type = $request->userType == 'indian' ? 'indian': 'foreigner';
                $user->password = bcrypt(123);
                $user->save();
            }
        }
        $input = $request->only('username');
        $input['password'] = 123;
        $expirationTime = $request->mode_of_login=='Web' ? Carbon::now()->addDay(1) : Carbon::now()->addDays(14);
        JWTFactory::setTTL($expirationTime->diffInMinutes());
        $jwt_token = JWTAuth::attempt($input);
        return response()->json(['message' => 'OTP verified successfully','status'=>true,"statusCode" => 200,'token'=>$jwt_token],200);
    }
}