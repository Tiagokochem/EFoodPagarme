<?php

namespace App\Http\Controllers;

use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class DeliveryManController extends Controller
{
    public function create()
    {
        $status = BusinessSetting::where('key', 'toggle_dm_registration')->first()?->value;
        if($status == 1)
        {
            return view('dm-registration');
        }
        Toastr::error(translate('messages.not_found'));
        return back();
    }

    public function store(Request $request)
    {
        $status = BusinessSetting::where('key', 'toggle_dm_registration')->first()?->value;
        if($status == 0)
        {
            Toastr::error(translate('messages.not_found'));
            return back();
        }

        $request->validate([
            'f_name' => 'required|max:100',
            'l_name' => 'nullable|max:100',
            'identity_number' => 'required|max:30',
            'email' => 'required|email|unique:delivery_men',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:delivery_men',
            'zone_id' => 'required',
            'vehicle_id' => 'required',
            'earning' => 'required',
            'image' => 'nullable|max:2048',
            'identity_image.*' => 'nullable|max:2048',
            'password' => ['required', Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
            ], [
                'f_name.required' => translate('messages.first_name_is_required'),
                'zone_id.required' => translate('messages.select_a_zone'),
                'vehicle_id.required' => translate('messages.select_a_vehicle'),
                'earning.required' => translate('messages.select_dm_type')
            ]);

        if ($request->has('image')) {
            $image_name = Helpers::upload(dir:'delivery-man/',format: 'png', image:$request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                $identity_image = Helpers::upload(dir:'delivery-man/',format: 'png', image:$img);
                array_push($id_img_names, $identity_image);
            }
            $identity_image = json_encode($id_img_names);
        } else {
            $identity_image = json_encode([]);
        }

        $dm = New DeliveryMan();
        $dm->f_name = $request->f_name;
        $dm->l_name = $request->l_name;
        $dm->email = $request->email;
        $dm->phone = $request->phone;
        $dm->identity_number = $request->identity_number;
        $dm->identity_type = $request->identity_type;
        $dm->zone_id = $request->zone_id;
        $dm->vehicle_id = $request->vehicle_id;
        $dm->identity_image = $identity_image;
        $dm->image = $image_name;
        $dm->active = 0;
        $dm->earning = $request->earning;
        $dm->password = bcrypt($request->password);
        $dm->application_status= 'pending';
        $dm->save();
        try{
            if(config('mail.status') && $request['email']){
                Mail::to($request['email'])->send(new \App\Mail\SelfRegistration('pending', $dm->f_name.' '.$dm->l_name));
            }
        }catch(\Exception $ex){
            info($ex->getMessage());
        }

        Toastr::success(translate('messages.application_placed_successfully'));
        return back();
    }
}
