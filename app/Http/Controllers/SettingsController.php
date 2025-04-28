<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function webConfiguration(Request $request)
    {
        $config = [
            'site_logo' => setting('site_logo', '/images/default_logo.png'),
            'favicon' => setting('favicon', '/images/default_favicon.ico'),
            'website_title' => setting('website_title', 'Default Website Title'),
            'contact_number' => setting('contact_number', '+000000000'),
            'contact_email' => setting('contact_email', 'default@example.com')
        ];

        return response()->json($config);
    }

    public function shippingMethods()
    {
        $methods = ['royal_mail', 'dpd'];

        $activeMethods = [];

        foreach ($methods as $method) {
            $status = setting("{$method}_status");

            if ($status && (int)$status === 1) {
                $apiEndpoint = setting("{$method}_api_endpoint");
                $apiKeyOrToken = setting("{$method}_api_key") ?? setting("{$method}_api_token");

                $activeMethods[$method] = [
                    'api_endpoint' => $apiEndpoint,
                    'status' => true,
                    'api_key' => $apiKeyOrToken
                ];
            }
        }

        if (empty($activeMethods)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active shipping methods found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $activeMethods
        ]);
    }

  public function PaymentMethods()
{
    $methods = ['apple_pay', 'clearpay', 'opayo', 'gift_voucher', 'klarna', 'credit_card'];
    $data = [];

    foreach ($methods as $method) {
        $status = setting("{$method}_status");

        if ((int) $status !== 1) continue;

        switch ($method) {
            case 'apple_pay':
                $data['apple_pay'] = [
                    'merchant_identifier' => decryptData(setting('apple_pay_merchant_identifier')),
                    'merchant_name' => decryptData(setting('apple_pay_merchant_name')),
                    'merchant_id' => decryptData(setting('apple_pay_merchant_id')),
                    'certificate_password' => decryptData(setting('apple_pay_certificate_password')),
                    'certificate_url' => setting('apple_pay_merchant_certificate') ? asset(setting('apple_pay_merchant_certificate')) : null,
                    'private_key_url' => setting('apple_pay_merchant_private_key') ? asset(setting('apple_pay_merchant_private_key')) : null,
                    'environment' => setting('apple_pay_environment'),
                ];
                break;

            case 'clearpay':
                $data['clearpay'] = [
                    'merchant_id' => decryptData(setting('clearpay_merchant_id')),
                    'api_key' => decryptData(setting('clearpay_api_key')),
                    'secret_key' => decryptData(setting('clearpay_secret_key')),
                    'environment' => setting('clearpay_environment'),
                ];
                break;

            case 'credit_card':
                $data['credit_card'] = [
                    'publishable_key' => decryptData(setting('credit_card_publishable_key')),
                    'secret_key' => decryptData(setting('credit_card_secret_key')),
                    'environment' => setting('credit_card_environment'),
                ];
                break;

            case 'gift_voucher':
                $data['gift_voucher'] = [
                    'enabled' => true
                ];
                break;

            case 'klarna':
                $data['klarna'] = [
                    'merchant_id' => decryptData(setting('klarna_merchant_id')),
                    'api_username' => setting('klarna_api_username'),
                    'api_password' => decryptData(setting('klarna_api_password')),
                    'client_id' => decryptData(setting('klarna_client_id')),
                    'api_key' => decryptData(setting('klarna_api_key')),
                    'environment' => setting('klarna_environment'),
                ];
                break;

            case 'opayo':
                $data['opayo'] = [
                    'vendor_name' => setting('opayo_vendor_name'),
                    'api_key' => decryptData(setting('opayo_api_key')),
                    'encryption_key' => decryptData(setting('opayo_encryption_key')),
                    'environment' => setting('opayo_environment'),
                ];
                break;
        }
    }

    return response()->json([
        'status' => 'success true',
        'data' => $data,
    ]);
}


}
