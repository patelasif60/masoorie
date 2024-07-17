<?php

namespace App\Http\Controllers\API;


use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AuthService;

class AuthController extends Controller
{
    public $token = true;
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
	public function __construct(AuthService $service)
	{
		$this->service = $service;
	}

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
 
    public function myProfile()
    {
        $user[] = JWTAuth::user();
        return response()->json(['user' => $user]);
    }
    public function getOtp(Request $request){
        $validator = Validator::make($request->all(), 
        ['username' => 'required']);  
        if ($validator->fails()) {  
            return response()->json(['error'=>$validator->errors(),'status'=>false,"statusCode" => 401], 401); 
        }
        return $this->service->getOtp($request); 
    }
    public function verifyOtp(Request $request){
        $validator = Validator::make($request->all(), ['username' => 'required', 'otp' => 'required']);
        if ($validator->fails()) {  
            return response()->json(['error' => $validator->errors(),'status'=>false,"statusCode" => 200], 200); 
        }
        return $this->service->verifyOtp($request); 
    }
}