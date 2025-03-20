<?php

use App\Models\Setting;

if(!function_exists('uploadFile')) {
    function uploadFile($file,  $path, $fileName = null) {
        if ($file) {
            if(!$fileName){
                $fileName = uniqid();
            }

            $imageName = $fileName . '.' . $file->getClientOriginalExtension();
            $imagePath = $path . $imageName;
            $file->move(public_path($path), $imageName);

            return $imagePath;
        }

        return null;
    }
}

if(!function_exists('setting')) {
    function setting($setting_key){
        $setting = Setting::where('key', $setting_key)->first();
        $value = '';
        if ($setting) {
            $value = $setting->value;
        }
        return $value;
    }
}

if(!function_exists('encryptData')) {
    function encryptData($data){
        $key = 'KULAR_FASHION';
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivLength);
    
        $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    
        return base64_encode($encryptedData . '::' . $iv);
    }
}

if(!function_exists('decryptData')) {
    function decryptData($encryptedData) {
        if(!$encryptedData){
            return '';
        }

        $key = 'KULAR_FASHION';
        $decodedData = base64_decode($encryptedData);
        
        $parts = explode('::', $decodedData);
        
        if (count($parts) !== 2) {
            return "Decryption error: Invalid data format.";
        }
    
        list($encryptedData, $iv) = $parts;
    
        return openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, $iv);
    }
    
}
