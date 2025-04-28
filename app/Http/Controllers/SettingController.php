<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Crypt;

class SettingController extends Controller
{
    public function index()
    {
        if(!Gate::allows('view settings')) {
            abort(403);
        }
        return view('settings.default-images.index');
    }

    public function store(Request $request)
    {
        $skippedArray = array_slice($request->all(), 1, null, true);

        $settings = [
            'default_department_image'  => 'default_department_image',
            'default_product_type_image' => 'default_product_type_image',
            'default_brand_image'       => 'default_brand_image',
            'default_product_image'     => 'default_product_image'
        ];

        foreach ($settings as $settingKey => $imageKey) {
            $setting = Setting::where('key', $settingKey)->first();
            $oldImagePath = $setting ? $setting->value : null;

            if ($request->hasFile($imageKey)) {
                $newImageName = uploadFile($request->file($imageKey), 'uploads/default-images/');
                $fullOldImagePath = public_path($oldImagePath);

                if ($oldImagePath && File::exists($fullOldImagePath)) {
                    File::delete($fullOldImagePath);
                }
                $skippedArray[$settingKey] = $newImageName;
            } else {
                $skippedArray[$settingKey] = $oldImagePath;
            }
        }

        foreach ($skippedArray as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('settings.index')->with('success', 'Default Images updated successfully');
    }

    public function generalSetting()
    {
        return view('settings.general-settings.index');
    }

    public function generalSettingStore(Request $request)
    {

        $datas = $request->all();
        $skippedArray = array_slice($datas, 1, null, true);

        $request->validate([
            'euro_to_pound' => ['required'],
        ]);

        foreach ($skippedArray as $key => $value)
        {
            Setting::updateOrCreate([
                'key' => $key,
            ],[
                'value' => $value
            ]);
        }

        return redirect()->route('general-settings.index')->with('success', 'General Setting updated successfully');
    }


    public function webSetting(){
        return view('settings.web-settings.index');
    }

    public function webSettingStore(Request $request){
        $request->validate([
            'website_title' => 'required',
            "web_contact_email" => 'required',
            "web_contact_no" => 'required',
        ]);

        $skippedArray = array_slice($request->all(), 1, null, true);

        $web_settings = [
            'site_logo'  => 'site_logo',
            'web_favicon' => 'web_favicon',
        ];

        foreach ($web_settings as $settingKey) {
            $oldImagePath = setting($settingKey);

            if ($request->hasFile($settingKey)) {
                $newImageName = uploadFile($request->file($settingKey), 'uploads/default-images/');
                $fullOldImagePath = public_path($oldImagePath);

                if ($oldImagePath && File::exists($fullOldImagePath)) {
                    File::delete($fullOldImagePath);
                }
                $skippedArray[$settingKey] = $newImageName;
            } else {
                $skippedArray[$settingKey] = $oldImagePath;
            }
        }

        foreach ($skippedArray as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('web-settings.index')->with('success', 'Web Setting updated successfully');
    }

    public function paymentMethodSettings(){
        return view('settings.payment-methods.index');
    }

    public function paymentMethodUpdate(Request $request, $method){
        $rules = [
            $method . '_status' => 'required|boolean'
        ];

        if ($method === 'apple_pay') {
            $rules[$method . '_merchant_identifier'] = 'required|string';
            $rules[$method . '_merchant_name'] = 'required|string';
            $rules[$method . '_merchant_id'] = 'required|string';
            $rules[$method . '_certificate_password'] = 'required|string';
            $rules[$method . '_environment'] = 'required|string';

            if(!setting('apple_pay_merchant_certificate')){
                $rules[$method . '_merchant_certificate'] = 'required|file';
            }

            if(!setting('apple_pay_merchant_private_key')){
                $rules[$method . '_merchant_private_key'] = 'required|file';
            }
        }

        if ($method === 'clearpay') {
            $rules[$method . '_merchant_id'] = 'required|string';
            $rules[$method . '_api_key'] = 'required|string';
            $rules[$method . '_secret_key'] = 'required|string';
            $rules[$method . '_environment'] = 'required|string';
        }

        if ($method === 'opayo') {
            $rules[$method . '_vendor_name'] = 'required|string';
            $rules[$method . '_api_key'] = 'required|string';
            $rules[$method . '_encryption_key'] = 'required|string';
            $rules[$method . '_environment'] = 'required|string';
        }

        if ($method === 'klarna') {
            $rules[$method . '_merchant_id'] = 'required|string';
            $rules[$method . '_api_username'] = 'required|string';
            $rules[$method . '_api_password'] = 'required|string';
            $rules[$method . '_client_id'] = 'required|string';
            $rules[$method . '_api_key'] = 'required|string';
            $rules[$method . '_environment'] = 'required|string';
        }

        if ($method === 'credit_card') {
            $rules[$method . '_publishable_key'] = 'required|string';
            $rules[$method . '_secret_key'] = 'required|string';
            $rules[$method . '_environment'] = 'required|string';
        }

        $request->validate($rules);

        $skippedArray = array_slice($request->all(), 1, null, true);

        foreach ($skippedArray as $key => $value) {
            if($key === 'apple_pay_merchant_certificate' || $key === 'apple_pay_merchant_private_key'){
                $filePath = uploadFile($request->file($key), 'credentials/');

                if (setting($key) && File::exists(public_path(setting($key)))) {
                    File::delete(public_path(setting($key)));
                }

                Setting::updateOrCreate(['key' => $key], ['value' => $filePath]);
            } else {
                if (strpos($key, '_merchant') !== false || strpos($key, '_password') !== false || strpos($key, '_id') !== false || strpos($key, '_key') !== false || strpos($key, '_secret') !== false) {
                    $value = encryptData($value);
                }

                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        return redirect()->back()->with('success', 'Payment method settings updated successfully')->with('method', $method);
    }

    public function shippingMethodSettings(){
        return view('settings.shipping-methods.index');
    }

    public function shippingMethodUpdate(Request $request, $method){
        $rules = [
            $method . '_api_endpoint' => 'required|url',
            $method . '_status' => 'required|boolean'
        ];

        if ($method === 'royal_mail') {
            $rules[$method . '_api_key'] = 'required|string';
        }

        if ($method === 'dpd') {
            $rules[$method . '_api_token'] = 'required|string';
        }

        $request->validate($rules);

        $skippedArray = array_slice($request->all(), 1, null, true);

        foreach ($skippedArray as $key => $value) {
            if($key === 'royal_mail_api_key' || $key === 'dpd_api_token'){
                $value = encryptData($value);
            }
            
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->back()->with('success', 'Shipping method settings updated successfully')->with('method', $method);
    }
}
