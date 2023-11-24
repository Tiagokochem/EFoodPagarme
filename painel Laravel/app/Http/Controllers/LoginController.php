<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\VendorEmployee;
use Illuminate\Support\Carbon;
use App\Models\BusinessSetting;
use App\CentralLogics\SMS_module;
use Illuminate\Support\Facades\DB;
use App\Models\SubscriptionPackage;
use Gregwar\Captcha\CaptchaBuilder;
use App\Http\Controllers\Controller;
use App\Mail\PasswordResetRequestMail;
use App\Models\PhoneVerification;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin,vendor', ['except' => 'logout']);
    }

    public function login()
    {
        $custome_recaptcha = new CaptchaBuilder;
        $custome_recaptcha->build();
        Session::put('six_captcha', $custome_recaptcha->getPhrase());
        $email = null;
        $password = null;
        $role = null;
            if (Cache::has('email_token')) {
                $data= Cache::get('email_token');
                $role = $data['role'];
                $email = Crypt::decryptString($data['e_token']);
                $password = Crypt::decryptString($data['p_token']);
            }
        return view('auth.login', compact('custome_recaptcha','email','password','role'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        $recaptcha = Helpers::get_business_settings('recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1) {
            $request->validate([
                'g-recaptcha-response' => [
                    function ($attribute, $value, $fail) {
                        $secret_key = Helpers::get_business_settings('recaptcha')['secret_key'];
                        $response = $value;
                        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response;
                        $response = Http::get($url);
                        $response = $response->json();
                        if (!isset($response['success']) || !$response['success']) {
                            $fail(translate('messages.ReCAPTCHA Failed'));
                        }
                    },
                ],
            ]);
        } else if(strtolower(session('six_captcha')) != strtolower($request->custome_recaptcha))
        {
            Toastr::error(translate('messages.ReCAPTCHA Failed'));
            return back();
        }

        if($request->role == 'admin' ){
            if (auth('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
                if(auth('admin')?->user()?->role_id == 1 ){
                        if ($request->remember) {
                            $data=[
                                'role' => $request->role ,
                                'token'=> auth('admin')?->user()?->remember_token,
                                'e_token'=>  Crypt::encryptString($request->email),
                                'p_token'=> Crypt::encryptString($request->password),
                            ];
                            Cache::Put('email_token' , $data);
                        } else {
                            $user = auth('admin')?->user();
                            $user?->update([
                                'remember_token' => null
                            ]);
                            Cache::forget('email_token');
                        }
                            return redirect()->route('admin.dashboard');
                    }
                    else{
                        auth()?->guard('admin')?->logout();
                        return redirect()->back()->withInput($request->only('email', 'remember'))
                            ->withErrors(['Credentials does not match.']);
                    }
                }
        }
        elseif( $request->role == 'admin_employee' ){
            if (auth('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
                if(auth('admin')?->user()?->role_id == 1 ){
                    auth()?->guard('admin')?->logout();
                    return redirect()->back()->withInput($request->only('email', 'remember'))
                        ->withErrors(['Credentials does not match.']);
                }
                if ($request->remember) {
                    $data=[
                        'role' => $request->role ,
                        'token'=> auth('admin')?->user()?->remember_token,
                        'e_token'=>  Crypt::encryptString($request->email),
                        'p_token'=> Crypt::encryptString($request->password),
                    ];
                    Cache::Put('email_token' , $data);
                } else {
                    $user = auth('admin')?->user();
                    $user?->update([
                        'remember_token' => null
                    ]);
                    Cache::forget('email_token');
                }
                    return redirect()->route('admin.dashboard');
            }
        }

        elseif($request->role == 'vendor'){
            $vendor = Vendor::where('email', $request->email)->first();
            if($vendor){
                if( $vendor?->restaurants[0]?->restaurant_model == 'none'){
                    $admin_commission= BusinessSetting::where('key','admin_commission')->first();
                    $business_name= BusinessSetting::where('key','business_name')->first();
                    $packages= SubscriptionPackage::where('status',1)->get();
                        return view('vendor-views.auth.register-step-2',[
                            'restaurant_id' => $vendor?->restaurants[0]?->id,
                            'packages' =>$packages,
                            'business_name' =>$business_name?->value,
                            'admin_commission' =>$admin_commission?->value,
                        ]);
                }

                if($vendor?->restaurants[0]?->status == 0 &&  $vendor->status == 0)
                {
                    return redirect()->back()->withInput($request->only('email', 'remember'))
                ->withErrors([translate('messages.inactive_vendor_warning')]);
                }
            }
                if (auth('vendor')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
                    if ($request->remember) {
                        $data=[
                            'role' => 'vendor' ,
                            'token'=> auth('vendor')?->user()?->remember_token,
                            'e_token'=>  Crypt::encryptString($request->email),
                            'p_token'=> Crypt::encryptString($request->password),
                        ];
                        Cache::Put('email_token' , $data);
                    } else {
                        $user = auth('vendor')?->user();
                        $user?->update([
                            'remember_token' => null
                        ]);
                        Cache::forget('email_token');
                    }

                    return redirect()->route('vendor.dashboard');
                }
        }
        elseif($request->role == 'vendor_employee'){
            $employee = VendorEmployee::where('email', $request->email)->first();
            if($employee)
            {
                if($employee?->restaurant?->status == 0)
                {
                    return redirect()->back()->withInput($request->only('email', 'remember'))
                ->withErrors([translate('messages.inactive_vendor_warning')]);
                }
            }
            if (auth('vendor_employee')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {


                if ($request->remember) {
                    $data=[
                        'role' => 'vendor_employee' ,
                        'token'=> auth('vendor_employee')?->user()?->remember_token,
                        'e_token'=>  Crypt::encryptString($request->email),
                        'p_token'=> Crypt::encryptString($request->password),
                    ];
                    Cache::Put('email_token' , $data);
                } else {
                    $user = auth('vendor_employee')?->user();
                    $user?->update([
                        'remember_token' => null
                    ]);
                    Cache::forget('email_token');
                }
                return redirect()->route('vendor.dashboard');
            }
        }


        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors(['Credentials does not match.']);
    }

    public function reloadCaptcha()
    {
        $custome_recaptcha = new CaptchaBuilder;
        $custome_recaptcha->build();
        Session::put('six_captcha', $custome_recaptcha->getPhrase());

        return response()->json([
            'view' => view('auth.custom-captcha', compact('custome_recaptcha'))->render()
        ], 200);
    }

    public function logout(Request $request)
    {
        if(auth('vendor')?->check()){
            auth()->guard('vendor')->logout();
        }
        elseif(auth('vendor_employee')?->check()){
            auth()->guard('vendor_employee')->logout();
        }
        else{
            auth()?->guard('admin')?->logout();
        }
        return redirect()->route('login');
    }

}
