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
class JWTAuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users|max:50',
        ]);
        if($validator->fails())
         return response()->json([
            'status'=>'ERROR',
            'data'=>$validator->errors(),
            // 'token'=>$token,
        ], 422);
        // return response()->json($validator->errors(),422);
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt("123456")]
                ));
        if (! $token = auth()->attempt(['email'=>$user->email,'password'=>"123456"])) {
            return response()->json([
                'status'=>'ERROR',
                'data'=>['MSG'=>'OTP NOT SENT'],
                // 'token'=>$token,
            ], 401);
        }
        $otp=mt_rand(1111,9999);
        $user->otp=$otp;
        $minutes_to_add = 10;

        $time = new DateTime;
        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        $dt= $time->format('y-m-d H:i:s');
        // $datetime->format('g:i:s');
        $user->otp_expires=$dt;
        $user->save();
        Mail::to('gaurav.jss.027@gmail.com')->send(new SendOTP($otp));
        return response()->json([
            'status'=>'OK',
            'data'=>['MSG'=>'OTP SENT','token'=>$token],
            // 'token'=>$token,
        ], 201);
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
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $otp=mt_rand(1111,9999);
        Mail::to('gaurav.jss.027@gmail.com')->send(new SendOTP($otp));
        return $this->createNewToken($token);
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
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
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
        if(auth()->user()->otp==$otp && $carbon->lessthan($expire_time))
        {
            $user=auth()->user();
            $user->verified=1;
            $user->save();
            // return view('register');
            return response()->json(['status'=>'OK','data'=>'Verified'], 201);
        }
        else 
        return response()->json(['status'=>'NOT OK','data'=>'Error'], 400);;
    }
}
