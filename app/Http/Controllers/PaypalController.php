<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Models\UserLicense;
use App\Movies;
use App\Settings;
use Illuminate\Http\Request;
use Str;

use URL;
use Session;


use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    //private $config;

    public function __construct()
    {
        //parent::__construct();

        $client_id = getPaymentGatewayInfo(1, 'paypal_client_id');
        $secret = getPaymentGatewayInfo(1, 'paypal_secret');
        $mode = getPaymentGatewayInfo(1, 'mode');

        $this->config = [
            'mode'    => $mode,
            'sandbox' => [
                'client_id'         => $client_id,
                'client_secret'     => $secret,
                'app_id'            => '',
            ],
            'live' => [
                'client_id'         => $client_id,
                'client_secret'     => $secret,
                'app_id'            => '',
            ],

            'payment_action' => 'Sale',
            'currency'       => 'USD',
            'notify_url'     => '',
            'locale'         => 'en_US',
            'validate_ssl'   => true,
        ];
    }


    /**
     * process transaction.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paypal_pay(Request $request)
    {
        // $settings = Settings::findOrFail('1');
        // $admin_commission = $settings->admin_commission;
        // $amount_paid_to_author = 100 - (100 * $admin_commission / 100);
        // dd($amount_paid_to_author);
        // $settings = Settings::findOrFail('1');
        // $admin_commission = $settings->admin_commission;

        $currency_code = getcong('currency_code') ? getcong('currency_code') : 'USD';

        $video_id = $request->video_id;

        $video_info = Movies::where('id', $video_id)->where('status', 1)->first();
        session(['video_info' => $video_info]); // Store it in the session

        $video_title = $video_info->title;
        $license_price = $video_info->license_price;

        $success_url = \URL::to('paypal/success/');
        $fail_url = \URL::to('paypal/fail/');

        $provider = new PayPalClient;
        $provider->setApiCredentials($this->config);
        $paypalToken = $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => $success_url,
                "cancel_url" => $fail_url,
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => $currency_code,
                        "value" => $license_price
                    ],
                    "description" => $video_title,
                ]
            ]
        ]);


        if (isset($response['id']) && $response['id'] != null) {

            // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }

            \Session::flash('error', 'PayPal order creation failed. Please try again later.');
            \Log::error('PayPal Order Creation Error: ' . json_encode($response));
            return redirect('dashboard');
        } else {
            $error_message = isset($response['error']) ? $response['error']['message'] :
                           (isset($response['message']) ? $response['message'] : 'Unable to process PayPal payment.');
            \Session::flash('error', $error_message);
            \Log::error('PayPal Error Response: ' . json_encode($response));
            return redirect('dashboard');
        }
    }

    /**
     * success transaction.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paypal_success(Request $request)
    {

        $settings = Settings::findOrFail('1');
        $admin_commission = $settings->admin_commission;
        $video_info = session('video_info'); // Retrieve from session
        $provider = new PayPalClient;
        $provider->setApiCredentials($this->config);
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {

            $payment_id = $response['purchase_units'][0]['payments']['captures'][0]['id'];
            $user_id = Auth::user()->id;
            $user_email = Auth::user()->email;
            $user = User::findOrFail($user_id);
            $video_id = $video_info->id;
            $license_price = $video_info->license_price;

            $user_license = new UserLicense;
            $author = User::find($video_info->added_by);

            $author_paypal_email = $author->paypal_email;
            // $author_paypal_email = "sb-bajni30868213@business.example.com";
            $admin_commission = $settings->admin_commission;
            $amount_paid_to_author = $license_price - ($license_price * $admin_commission / 100);
            $author_payment_id = $payment_id;

            $user_license->user_id = $user_id;
            $user_license->author = $video_info->added_by;

            $user_license->buyer_email = $user_email;
            $user_license->buyer_name = $user->name;
            $user_license->video_id = $video_id;
            $user_license->gateway = 'Paypal';
            $user_license->license_price = $license_price;
            $user_license->author_paypal_email = $author_paypal_email;
            $user_license->amount_paid_to_author = $amount_paid_to_author;
            $user_license->admin_commission = $license_price - $amount_paid_to_author;
            $user_license->author_payment_id = $author_payment_id;
            // Generate unique license key
            $license_key = $this->generateUniqueLicenseKey();
            $user_license->license_key = $license_key;
            $user_license->payment_id = $payment_id;
            $payout = $this->paypal_payout($amount_paid_to_author, $author_paypal_email);
            if ($payout) {
                $user_license->save();
                $this->generateLicenseFile($license_key, $user_license);
            }


            // Generate license text file


            // Send subscription email
            $user_full_name = $user->name;
            $data_email = array(
                'name' => $user_full_name
            );
            try {
                \Mail::send('emails.subscription_created', $data_email, function ($message) use ($user, $user_full_name) {
                    $message->to($user->email, $user_full_name)
                        ->from(getcong('site_email'), getcong('site_name'))
                        ->subject('Subscription Created');
                });
            } catch (\Throwable $e) {
                \Log::info($e->getMessage());
            }

            \Session::flash('flash_message', trans('words.payment_success'));

            return redirect()->to(url('movies/watch/' . $video_info->video_slug . '/' . $video_info->id));
        } else {
            \Session::flash('error', trans('words.payment_failed'));
            return redirect('/');
        }
    }

    protected function generateLicenseFile($license_key, $user_license)
    {
        // Retrieve author information
        $author = User::find($user_license->author);
        $author_name = $author ? $author->name : 'Unknown';

        // Set currency code, default to 'USD' if not set
        $currency_code = getcong('currency_code') ?: 'USD';

        // Retrieve movie title using video_id
        $movie = Movies::find($user_license->video_id);
        $movie_title = $movie ? $movie->video_title : 'Unknown Title';

        // Build the license information content
        $license_info = "License Key: {$license_key}\n";
        $license_info .= "Name: {$user_license->name}\n";
        $license_info .= "Email: {$user_license->email}\n";
        $license_info .= "Movie Title: {$movie_title}\n";
        $license_info .= "Author: {$author_name}\n";
        $license_info .= "License Type: Single\n";
        $license_info .= "License Price: {$user_license->license_price} {$currency_code}\n";
        $license_info .= "Payment Gateway: {$user_license->gateway}\n";
        $license_info .= "Payment ID: {$user_license->payment_id}\n";
        $license_info .= "Date: " . date('F d, Y', strtotime($user_license->created_at)) . "\n";

        // Add terms and conditions
        $license_info .= "\nTerms and Conditions:\n";
        $license_info .= "1. This license is non-transferable.\n";
        $license_info .= "2. This license is valid for the lifetime of the product.\n";
        $license_info .= "3. No refunds or exchanges are allowed.\n";

        // Set file path and ensure the directory exists
        $directory_path = storage_path('licenses');
        $file_path = $directory_path . '/' . $license_key . '.txt';

        if (!file_exists($directory_path)) {
            mkdir($directory_path, 0755, true);
        }

        // Write the license information to the file
        file_put_contents($file_path, $license_info);
    }




    /**
     * cancel transaction.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paypal_fail()
    {
        \Session::flash('error', trans('words.payment_failed'));
        return redirect('/');
    }
    private function generateUniqueLicenseKey()
    {
        do {
            // Generate a license key in the format XXXX-XXXX-XXXX-XXXX
            $license_key = strtoupper(Str::random(4)) . '-' .
                strtoupper(Str::random(4)) . '-' .
                strtoupper(Str::random(4)) . '-' .
                strtoupper(Str::random(4));
        } while (UserLicense::where('license_key', $license_key)->exists());

        return $license_key;
    }
    public function paypal_payout($amount, $recipient_email)
    {
        // Step 1: Retrieve the amount and recipient email from the request
        // $amount = $request->amount;
        // $recipient_email = $request->email;

        try {
            // Step 2: Initialize the PayPal client
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));

            // Step 3: Get the access token
            $accessToken = $provider->getAccessToken();
            if (!$accessToken) {
                \Session::flash('error', 'Unable to get PayPal access token.');
                return redirect()->back();
            }

            // Step 4: Create a unique batch ID for the payout
            $batch_id = uniqid();

            // Step 5: Make the payout request to PayPal
            $response = $provider->createBatchPayout([
                'sender_batch_header' => [
                    'sender_batch_id' => $batch_id,
                    'email_subject' => 'You have a payout!',
                    'email_message' => 'You have received a payout from our platform.',
                ],
                'items' => [
                    [
                        'recipient_type' => 'EMAIL',
                        'receiver' => $recipient_email,
                        'amount' => [
                            'value' => number_format($amount, 2),
                            'currency' => 'USD',
                        ],
                        'note' => 'Payout for your services.',
                        'sender_item_id' => uniqid(),
                    ]
                ]
            ]);

            // Step 6: Check the payout status
            $batch_status = $response['batch_header']['batch_status'] ?? '';
            if ($batch_status === 'SUCCESS') {
                \Session::flash('flash_message', 'Payout successful!');
            } elseif ($batch_status === 'PENDING') {
                \Session::flash('flash_message', 'Payout is pending. It will be processed shortly.');
            } else {
                // Log the response for debugging
                // \Log::error('Payout failed: ', $response);
                \Session::flash('error', $response['message'] ?? 'Payout failed.');
            }
        } catch (\Exception $e) {
            // Step 7: Catch any exceptions and log the error
            \Log::error('PayPal Payout Exception: ' . $e->getMessage());
            \Session::flash('error', 'An error occurred: ' . $e->getMessage());
        }

        // Step 8: Redirect back to the previous page
        return redirect()->back();
    }
}
