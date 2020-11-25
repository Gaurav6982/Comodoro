<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;
use App\User;
use App\Mail\SendOTP;
use Illuminate\Support\Facades\Mail;
use DateTime;
use DateInterval;
use Carbon\Carbon;
use Session;
class JWTAuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','logout']]);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $user = auth()->user();
        if($user->verified==1)
        {
            if($request->has('name'))
            $user->name=$request->name;
            if($request->has('phone'))
            $user->phone=$request->phone;
            if($user->save())
            return response()->json([
                'status'=>'OK',
                'data'=>'Registered',
            ], 200);
            else
            return response()->json([
                'status'=>'NOT OK',
                'data'=>'NOT Registered',
            ], 400);
        }
        else
        return response()->json([
            'status'=>'NOT OK',
            'data'=>'NOT Verified',
        ], 400);
        
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        Session::put('email', $request->email);
        $new_user=false;
        $user=User::where('email',$request->email)->first();
        if(is_null($user))
        {
            $user = User::create(array_merge(
                $validator->validated(),
                ['password' => bcrypt("123456")]
            ));
            $new_user=true;
        }
        if (! $token = auth()->attempt(['email'=>$request->email,'password'=>"123456"])) {
            return response()->json(['status'=>'ERROR','data'=>['MSG'=>'OTP NOT SENT'] ], 401);
        }
        $otp=mt_rand(1111,9999);
        $user->otp=$otp;
        $minutes_to_add = 10;

        $time = new DateTime;
        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        $dt= $time->format('Y-m-d H:i:s');
        // $datetime->format('g:i:s');
        $user->otp_expires=$dt;
        $user->save();
        Mail::to($request->email)->send(new SendOTP($otp));
        if($new_user)
        $msg='Account Created';
        else
        $msg='Welcome Back!';
        return response()->json([
            'status'=>'OK',
            'data'=>['Acc_Status'=>$msg,'MSG'=>'OTP SENT','token'=>$token],
            // 'token'=>$token,
        ], 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // return "awd";
        // $user = JWTAuth::setToken($token)->toUser();
        // return $user;
        if(Auth::check()){
            auth()->logout();
            return response()->json(['status'=>'OK','data'=> 'Successfully logged out'],200);
        }
        else
        return response()->json(['status'=>'NOT OK','data'=> 'Something Went Wrong!'],400);

    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    protected function verifyOtp(Request $request){
        $otp=$request->otp;
        $carbon = Carbon::now();
        
        $expire_time=Carbon::create(auth()->user()->otp_expires);
        // return $now." ".auth()->user()->otp_expires;
        // return ;
        // return auth()->user()->otp." ".$otp;
        if(auth()->user()->verified!=1){
        if(auth()->user()->otp==$otp)
        {
            if($carbon->lessthan($expire_time))
            {
                $user=auth()->user();
                $user->verified=1;
                $user->otp=null;
                $user->email_verified_at=Carbon::now();
                $user->save();
                // return view('register');
                return response()->json(['status'=>'OK','data'=>'Verified'], 200);
            }
            else
            return response()->json(['status'=>'NOT OK','data'=>'OTP EXPIRED'], 400);
        }
        else 
        return response()->json(['status'=>'NOT OK','data'=>'INCORRECT OTP'], 400);}
        else
        return response()->json(['status'=>'OK','data'=>'Already Verified'], 200);
    }

    public function checkVerify(){
        if(auth()->user()->verified)
        return response()->json(['status'=>'OK','data'=>'Verified'], 200);
        return response()->json(['status'=>'NOT OK','data'=>'NEED REGISTERATION'], 200);
    }
}
