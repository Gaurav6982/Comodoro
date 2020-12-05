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
use App\TempOtp;
use Storage;
use App\user_activity;
class JWTAuthController extends Controller
{
    public $temp_email=null;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['sendOtp','verifyOtp','logout','del']]);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $user = auth()->user();
            $msg=$user->verified==0?'Registered':'Updated';
            if($request->has('name'))
            $user->name=$request->name;
            if($request->has('age'))
            $user->age=$request->age;
            if($request->has('device_token'))
            $user->device_token=$request->device_token;
            $user->verified=1;
            if($request->hasFile('image'))
            {
                if($user->image!=null)
                    Storage::delete('/public/user_images/'.$user->image);
                $imageExt=$request->file('image')->getClientOriginalExtension();
                $fileName=date('ymd')."_".time().'.'.$imageExt;
                $request->file('image')->move(public_path('/storage/user_images/'),$fileName);
                // return url('/storage/user_images/'.$fileName);
                $user->image=$fileName;
            }
            
            if($user->save())
            {
                return response()->json([
                    'status'=>'OK',
                    'data'=>$msg,
                ], 200);
            }
            else
            return response()->json([
                'status'=>'NOT OK',
                'data'=>'Something Went Wrong!',
            ], 400);    
    }
    // public function update(Request $request)
    // {
    //     $user = auth()->user();
    //     if($user->verified==1)
    //     {
    //         if($request->has('name'))
    //         $user->name=$request->name;
    //         if($request->has('age'))
    //         $user->age=$request->age;
    //         if($request->has('phone'))
    //         $user->phone=$request->phone;
    //         $status=$user->save();
    //         if($status)
    //         return response()->json([
    //             'status'=>'OK',
    //             'data'=>'Updated',
    //         ], 200);
    //         else
    //         return response()->json([
    //             'status'=>'NOT OK',
    //             'data'=>'Something Went Wrong!',
    //         ], 400);
    //     }
    //     else
    //     return response()->json([
    //         'status'=>'NOT OK',
    //         'data'=>'NOT Verified',
    //     ], 400);
        
    // }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOtp(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $email=$request->email;
        Session::put('email',$email);
        $user=new TempOtp;
        // $temp_email=$email;
        $user->email=$email;
        $otp=mt_rand(1111,9999);
        $user->otp=$otp;
        $minutes_to_add = 10;

        $time = new DateTime;
        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        $dt= $time->format('Y-m-d H:i:s');
        // $datetime->format('g:i:s');
        $user->otp_expires=$dt;
        
        Mail::to($request->email)->send(new SendOTP($otp));
        if($user->save())
        return response()->json([
            'status'=>'OK',
            'data'=>'OTP SENT',
        ], 200);
        else
        return response()->json([
            'status'=>'NOT OK',
            'data'=>'OTP NOT SENT',
        ], 200);
    }
    // public function login(Request $request)
    // {
    // 	$validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }
    //     Session::put('email', $request->email);
    //     $new_user=false;
    //     $user=User::where('email',$request->email)->first();
    //     if(is_null($user))
    //     {
    //         $user = User::create(array_merge(
    //             $validator->validated(),
    //             ['password' => bcrypt("123456")]
    //         ));
    //         $new_user=true;
    //     }
    //     if (! $token = auth()->attempt(['email'=>$request->email,'password'=>"123456"])) {
    //         return response()->json(['status'=>'ERROR','data'=>['MSG'=>'OTP NOT SENT'] ], 401);
    //     }
    //     $otp=mt_rand(1111,9999);
    //     $user->otp=$otp;
    //     $minutes_to_add = 10;

    //     $time = new DateTime;
    //     $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
    //     $dt= $time->format('Y-m-d H:i:s');
    //     // $datetime->format('g:i:s');
    //     $user->otp_expires=$dt;
    //     $user->save();
    //     Mail::to($request->email)->send(new SendOTP($otp));
    //     if($new_user)
    //     $msg='Account Created';
    //     else
    //     $msg='Welcome Back!';
    //     return response()->json([
    //         'status'=>'OK',
    //         'data'=>['Acc_Status'=>$msg,'MSG'=>'OTP SENT','token'=>$token],
    //         // 'token'=>$token,
    //     ], 200);
    // }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $user=auth()->user();
        $data=[
            'name'=>$user->name,
            'email'=>$user->email,
            'phone'=>$user->phone,
            'age'=>$user->age,
            'device_token'=>$user->device_token,
            'image'=>url('/storage/user_images/'.$user->image),
        ];
        return response()->json($data);
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
        // $email=Session::get('email');
        // return response()->json(['status'=>'OK','data'=>$email], 200);
        $email=$request->email;
        if($otp=='')
        return response()->json(['status'=>'NOT OK','data'=>'OTP NOT RECIEVED'], 400);
        if($email=='')
        return response()->json(['status'=>'NOT OK','data'=>'EMAIL NOT RECIEVED'], 400);
        
        $user=TempOtp::where('email',$email)->orderBy('created_at','desc')->first();
        if(is_null($user))
        return response()->json(['status'=>'NOT OK','data'=>'INVALID DATA'], 400);
        
        $carbon = Carbon::now();
        $expire_time=Carbon::create($user->otp_expires);
        // return $user->otp." ".$otp;
        if($user->otp==$otp)
        {
            if($carbon->lessthan($expire_time))
            {
                $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                ]);
        
                if ($validator->fails()) {
                    return response()->json($validator->errors(), 422);
                }
                $new_user=false;
                $user=User::where('email',$email)->first();
                if(is_null($user))
                {
                    $user = User::create(array_merge(
                        $validator->validated(),
                        ['password' => bcrypt("123456")]
                    ));
                    $new_user=true;
                }
                if($user->verified!=1)
                $new_user=true;
                if (! $token = auth()->attempt(['email'=>$request->email,'password'=>"123456"])) {
                    return response()->json(['status'=>'ERROR','data'=>'Something Went Wrong'], 401);
                }
                $user->email_verified_at=Carbon::now();
                $user->save();
                // return view('register');
                if($new_user)
                $msg='Account Created';
                else
                $msg='Welcome Back!';
                    return response()->json([
                        'status'=>'OK',
                        'data'=>['Acc_Status'=>$msg,'MSG'=>'OTP Verified','token'=>$token],
                        // 'token'=>$token,
                    ], 200);
            }
            else
            return response()->json(['status'=>'NOT OK','data'=>'OTP EXPIRED'], 400);
        }
        else 
        return response()->json(['status'=>'NOT OK','data'=>'INCORRECT OTP'], 400);
        
    }

    public function checkVerify(){
        if(auth()->user()->verified)
        return response()->json(['status'=>'OK','data'=>'Verified'], 200);
        return response()->json(['status'=>'NOT OK','data'=>'NEED REGISTERATION'], 200);
    }
    public function del(Request $request){
        $user=User::where('email',$request->email)->first();
        if($user->delete())
        return response()->json(['status'=>'OK','data'=>"Account Deleted"],200);
        return response()->json(['status'=>'NOT OK','data'=>"Something Went Wrong"],200);
    }
}
