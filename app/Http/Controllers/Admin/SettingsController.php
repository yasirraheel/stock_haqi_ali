<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Settings;
use App\WebAds;

use App\Http\Requests;
use App\Models\GoogleDriveApi;
use App\Models\Thumbnail;
use App\Models\UserLicense;
use Illuminate\Http\Request;
use League\Flysystem\UrlGeneration\PublicUrlGenerator;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingsController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
        check_verify_purchase();
    }
    public function general_settings()
    {
        if (Auth::User()->usertype != "Admin") {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
        }

        $page_title = trans('words.general');

        $settings = Settings::findOrFail('1');


        return view('admin.pages.settings.general', compact('page_title', 'settings'));
    }

    public function update_general_settings(Request $request)
    {

        $settings = Settings::findOrFail('1');


        $data =  \Request::except(array('_token'));

        $rule = array(
            'site_name' => 'required',
            'site_logo' => 'required',
            'site_favicon' => 'required',
            'site_email' => 'required'
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }


        $inputs = $request->all();

        putPermanentEnv('APP_TIMEZONE', $inputs['time_zone']);
        putPermanentEnv('APP_LANG', $inputs['default_language']);

        $settings->time_zone = $inputs['time_zone'];
        $settings->admin_commission = $inputs['admin_commission'];
        $settings->default_language = $inputs['default_language'];
        $settings->styling = $inputs['styling'];
        $settings->currency_code = $inputs['currency_code'];

        $settings->site_name = addslashes($inputs['site_name']);
        $settings->site_logo = $inputs['site_logo'];
        $settings->site_favicon = $inputs['site_favicon'];
        $settings->site_email = $inputs['site_email'];
        $settings->site_description = addslashes($inputs['site_description']);
        $settings->site_keywords = addslashes($inputs['site_keywords']);

        $settings->site_header_code = addslashes($inputs['site_header_code']);
        $settings->site_footer_code = addslashes($inputs['site_footer_code']);

        $settings->site_copyright = addslashes($inputs['site_copyright']);


        $settings->footer_fb_link = addslashes($inputs['footer_fb_link']);
        $settings->footer_twitter_link = addslashes($inputs['footer_twitter_link']);
        $settings->footer_instagram_link = addslashes($inputs['footer_instagram_link']);

        $settings->footer_google_play_link = addslashes($inputs['footer_google_play_link']);
        $settings->footer_apple_store_link = addslashes($inputs['footer_apple_store_link']);

        $settings->gdpr_cookie_on_off = $inputs['gdpr_cookie_on_off'];
        $settings->gdpr_cookie_title = addslashes($inputs['gdpr_cookie_title']);
        $settings->gdpr_cookie_text = addslashes($inputs['gdpr_cookie_text']);
        $settings->gdpr_cookie_url = addslashes($inputs['gdpr_cookie_url']);

        $settings->tmdb_api_key = trim($inputs['tmdb_api_key']);

        $settings->save();


        Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }

    public function email_settings()
    {
        if (Auth::User()->usertype != "Admin") {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
        }

        $page_title = trans('words.smtp_email');

        $settings = Settings::findOrFail('1');


        return view('admin.pages.settings.smtp_email', compact('page_title', 'settings'));
    }

    public function update_email_settings(Request $request)
    {

        $settings = Settings::findOrFail('1');


        $data =  \Request::except(array('_token'));

        $rule = array(
            'smtp_host' => 'required',
            'smtp_port' => 'required',
            'smtp_email' => 'required',
            'smtp_password' => 'required'
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }


        $inputs = $request->all();

        putPermanentEnv('MAIL_HOST', $inputs['smtp_host']);
        putPermanentEnv('MAIL_PORT', $inputs['smtp_port']);
        putPermanentEnv('MAIL_USERNAME', $inputs['smtp_email']);
        putPermanentEnv('MAIL_PASSWORD', $inputs['smtp_password']);
        putPermanentEnv('MAIL_ENCRYPTION', $inputs['smtp_encryption']);

        putPermanentEnv('MAIL_FROM_ADDRESS', $inputs['smtp_email']);

        $settings->smtp_host = $inputs['smtp_host'];
        $settings->smtp_port = $inputs['smtp_port'];
        $settings->smtp_email = $inputs['smtp_email'];
        $settings->smtp_password = $inputs['smtp_password'];
        $settings->smtp_encryption = $inputs['smtp_encryption'];

        $settings->save();

        Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }

    public function test_smtp_settings()
    {
        if (Auth::User()->usertype != "Admin") {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
        }

        $test_email = $_GET['test_email'];

        $user_name = 'John Deo';

        $data_email = array(
            'name' => $user_name
        );

        try {

            \Mail::send('emails.test_smtp', $data_email, function ($message) use ($test_email, $user_name) {
                $message->to($test_email, $user_name)
                    ->from(getcong('site_email'), getcong('site_name'))
                    ->subject('Test SMTP');
            });

            $response['resp_status']    = 'success';
            $response['resp_msg']    = 'Email sent successfully.';
        } catch (\Throwable $e) {

            $response['resp_status']    = 'failed';
            $response['resp_msg']    = $e->getMessage();
        }

        echo json_encode($response);
        exit;
    }

    public function social_login_settings()
    {
        if (Auth::User()->usertype != "Admin") {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
        }

        $page_title = trans('words.social_login');

        $settings = Settings::findOrFail('1');


        return view('admin.pages.settings.social_login', compact('page_title', 'settings'));
    }

    public function update_social_login_settings(Request $request)
    {

        $settings = Settings::findOrFail('1');

        $data =  \Request::except(array('_token'));

        $inputs = $request->all();

        $google_redirect = \URL::to('auth/google/callback');
        $facebook_redirect = \URL::to('auth/facebook/callback');

        putPermanentEnv('GOOGLE_CLIENT_DI', $inputs['google_client_id']);
        putPermanentEnv('GOOGLE_SECRET', $inputs['google_client_secret']);
        putPermanentEnv('GOOGLE_REDIRECT', $google_redirect);

        putPermanentEnv('FB_APP_ID', $inputs['facebook_app_id']);
        putPermanentEnv('FB_SECRET', $inputs['facebook_client_secret']);
        putPermanentEnv('FB_REDIRECT', $facebook_redirect);

        $settings->google_login = $inputs['google_login'];
        $settings->google_client_id = $inputs['google_client_id'];
        $settings->google_client_secret = $inputs['google_client_secret'];
        $settings->google_redirect = $google_redirect;

        $settings->facebook_login = $inputs['facebook_login'];
        $settings->facebook_app_id = $inputs['facebook_app_id'];
        $settings->facebook_client_secret = $inputs['facebook_client_secret'];
        $settings->facebook_redirect = $facebook_redirect;

        $settings->save();

        Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }


    public function menu_settings()
    {
        if (Auth::User()->usertype != "Admin") {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
        }

        $page_title = trans('words.menu');

        $settings = Settings::findOrFail('1');


        return view('admin.pages.settings.menu', compact('page_title', 'settings'));
    }

    public function update_menu_settings(Request $request)
    {

        $settings = Settings::findOrFail('1');

        $data =  \Request::except(array('_token'));

        $inputs = $request->all();


        $settings->menu_movies = $inputs['menu_movies'];
        $settings->menu_livetv = $inputs['menu_livetv'];
        $settings->menu_photos = $inputs['menu_photos'];
        $settings->menu_audio = $inputs['menu_audio'];


        $settings->save();

        Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }

    public function recaptcha_settings()
    {
        if (Auth::User()->usertype != "Admin") {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
        }

        $page_title = 'reCAPTCHA';

        $settings = Settings::findOrFail('1');


        return view('admin.pages.settings.recaptcha', compact('page_title', 'settings'));
    }

    public function update_recaptcha_settings(Request $request)
    {

        $settings = Settings::findOrFail('1');

        $data =  \Request::except(array('_token'));

        $inputs = $request->all();

        $settings->recaptcha_site_key = $inputs['recaptcha_site_key'];
        $settings->recaptcha_secret_key = $inputs['recaptcha_secret_key'];

        $settings->recaptcha_on_login = $inputs['recaptcha_on_login'];
        $settings->recaptcha_on_signup = $inputs['recaptcha_on_signup'];
        $settings->recaptcha_on_forgot_pass = $inputs['recaptcha_on_forgot_pass'];
        $settings->recaptcha_on_contact_us = $inputs['recaptcha_on_contact_us'];

        $settings->save();

        Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }


    public function web_ads_settings()
    {
        // session()->forget('flash_message');
        if (Auth::User()->usertype != "Admin") {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
        }

        $page_title = 'Banner Ads';

        $settings = WebAds::findOrFail('1');

        return view('admin.pages.settings.web_ads', compact('page_title', 'settings'));
    }

    public function update_web_ads_settings(Request $request)
    {


        $web_ads_obj = WebAds::findOrFail('1');

        $data =  \Request::except(array('_token'));

        $inputs = $request->all();
        // dd($inputs);

        $web_ads_obj->home_top = addslashes($inputs['home_top_text']);
        $web_ads_obj->home_bottom = addslashes($inputs['home_top_text']);

        $web_ads_obj->list_top = addslashes($inputs['home_bottom_text']);
        $web_ads_obj->list_bottom = addslashes($inputs['home_bottom_text']);

        $web_ads_obj->details_top = addslashes($inputs['list_top_text']);
        $web_ads_obj->details_bottom = addslashes($inputs['list_top_text']);

        // $web_ads_obj->other_page_top = addslashes($inputs['other_page_top']);
        // $web_ads_obj->other_page_bottom = addslashes($inputs['other_page_bottom']);

        $web_ads_obj->save();

        Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }

    public function site_maintenance(Request $request)
    {

        $page_title = trans('words.site_maintenance');

        $settings = Settings::findOrFail('1');

        return view('admin.pages.settings.site_maintenance', compact('page_title', 'settings'));
    }

    public function update_site_maintenance(Request $request)
    {

        $settings_obj = Settings::findOrFail('1');

        $data =  \Request::except(array('_token'));

        $rule = array(
            'maintenance_title' => 'required',
            'maintenance_secret' => 'required'
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        $inputs = $request->all();

        //$maintenance_secret = $inputs['maintenance_secret'];
        //echo 'down --secret='.$maintenance_secret.'';exit;

        $settings_obj->maintenance_title = addslashes($inputs['maintenance_title']);
        $settings_obj->maintenance_description = addslashes($inputs['maintenance_description']);

        $settings_obj->maintenance_secret = $inputs['maintenance_secret'];

        $settings_obj->save();

        Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }

    public function site_maintenance_on_off(Request $request)
    {
        $inputs = $request->all();

        $mode = $inputs['mode'];

        $settings_obj = Settings::findOrFail('1');

        if ($mode == "up") {
            $maintenance_secret = $settings_obj->maintenance_secret;

            $settings_obj->maintenance_mode = "down";

            $exitCode = \Artisan::call('down --secret=' . $maintenance_secret . '');

            $response['status'] = "down";
        } else {
            $settings_obj->maintenance_mode = "up";

            $exitCode = \Artisan::call('up');

            $response['status'] = "up";
        }

        $settings_obj->save();

        echo json_encode($response);
        exit;
    }


    /**
     * Put & Back to Maintenance Mode
     *
     * @param $mode ('down' or 'up')
     * @return \Illuminate\Http\RedirectResponse
     */
    public function maintenance($mode): \Illuminate\Http\RedirectResponse
    {
        $errorFound = true;

        // Go to maintenance with DOWN status
        try {
            echo $mode;
            exit;
            Artisan::call($mode);
        } catch (\Throwable $e) {
            //Alert::error($e->getMessage())->flash();
            $e->getMessage();
            $errorFound = true;
        }

        // Check if error occurred
        if (!$errorFound) {
            if ($mode == 'down') {
                $message = trans('admin.The website has been putted in maintenance mode');
            } else {
                $message = trans('admin.The website has left the maintenance mode');
            }
            echo $message;
            exit;
            Session::flash('flash_message', $message);
            //Alert::success($message)->flash();
        }

        return redirect()->back();
    }
    public function paypal_payout()
    {
        $page_title = 'Paypal Payout';

        return view('admin.paypal.payout_paypal', compact('page_title'));
    }
    public function sold_licenses()
    {
        $page_title = 'Sold License';

        $currency_code = getcong('currency_code') ?: 'USD';
        $allTransactions = UserLicense::where('author', auth()->id())->paginate(10);
        return view('admin.paypal.sold_licenses', compact('page_title', 'allTransactions', 'currency_code'));
    }
    public function paypal_dashboard()
    {
        $page_title = 'Paypal Dashboard';
        $currency_code = getcong('currency_code') ?: 'USD';
        $allTransactions = UserLicense::where('payment_id', '!=', null)->paginate(10);
        return view('admin.paypal.paypal_dashboard', compact('page_title', 'allTransactions', 'currency_code'));
    }



    public function store_generateScreenshot(Request $request)
    {
        $google_drive_api  = $this->getRandomApiKey();
        GoogleDriveApi::where('api_key', $google_drive_api)->increment('calls');

        if ($request->has('file_id')) {
            // Logic if file_id is passed in the URL
            $fileId = $request->file_id;
            $videoUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&key={$google_drive_api}";
        }

        if ($request->has('video_url')) {
            // Logic if video_url is passed in the request
            $googleDriveUrl = $request->video_url;

            // Extract the file ID from the Google Drive URL
            preg_match("/\/d\/(.*?)\//", $googleDriveUrl, $matches);
            if (isset($matches[1])) {
                $fileId = $matches[1];
                $videoUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&key={$google_drive_api}";
            } else {
                return redirect()->back()->with('error', 'Invalid Google Drive URL');
            }
        }

        // Define paths for the screenshot
        $tempImagePath = storage_path('app/public/screenshots/' . $fileId . '.jpg');
        $publicImagePath = public_path('screenshots/' . $fileId . '.jpg');

        // Ensure the public screenshots directory exists
        $publicScreenshotsDir = public_path('screenshots');
        if (!file_exists($publicScreenshotsDir)) {
            mkdir($publicScreenshotsDir, 0777, true);
        }

        // Check if the screenshot already exists in the temporary location
        if (file_exists($tempImagePath)) {
            // Delete the existing screenshot to avoid conflict
            unlink($tempImagePath);
            session()->forget($tempImagePath);
        }

        // FFmpeg executable path
        $opratingSystem = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $ffmpegPath = $opratingSystem ? storage_path('ffmpeg_win/bin/ffmpeg.exe') : storage_path('ffmpeg_linux/ffmpeg');

        // Generate a random timestamp within the first 15 seconds
        $randomTimestamp = rand(1, 15);

        // FFmpeg command to generate the screenshot
        $command = "\"$ffmpegPath\" -ss $randomTimestamp -i \"$videoUrl\" -t 00:00:15 -vframes 1 \"$tempImagePath\" 2>&1";

        // Execute the command using proc_open
        $descriptors = [
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w']   // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes);
        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $errorOutput = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $returnVar = proc_close($process);

            // Check if the FFmpeg command was successful
            if ($returnVar === 0) {
                // Move the screenshot to the public directory
                if (file_exists($tempImagePath)) {
                    rename($tempImagePath, $publicImagePath);
                }

                // Save or update the screenshot in the Thumbnail model
                Thumbnail::updateOrCreate(
                    ['file_id' => $fileId],
                    ['video_image_thumb' => 'screenshots/' . $fileId . '.jpg']
                );

                return redirect()->back()->with('flash_message', 'Screenshot generated successfully!');
            } else {
                return redirect()->back()->with('error', 'Error generating screenshot: ' . $errorOutput);
            }
        } else {
            return redirect()->back()->with('error', 'Failed to start FFmpeg process.');
        }
    }
    public function google_drive_api()
    {
        $page_title = 'Google Drive APIs';
        $APIs = GoogleDriveApi::paginate(5);

        return view('admin.google_drive_api', compact('page_title','APIs'));
    }
    public function google_drive_api_store(Request $request)
    {
        $data = $request->all();
        // check if the API key already exists
        $exists = GoogleDriveApi::where('api_key', $data['api_key'])->first();
        if ($exists) {
            return redirect()->back()->with('error', 'API key already exists!');
        }
        $googleDriveApi = new GoogleDriveApi();
        $googleDriveApi->api_key = $data['api_key'];
        $googleDriveApi->email = $data['email'];
        $googleDriveApi->save();

        return redirect()->back()->with('flash_message', 'Google Drive API key saved successfully!');
    }
    public function google_drive_api_delete($id)
    {
        GoogleDriveApi::destroy($id);

        return redirect()->back()->with('flash_message', 'Google Drive API key deleted successfully!');
    }
    public function getRandomApiKey()
    {
        // Get all available Google Drive API keys
        $google_drive_apis = GoogleDriveApi::all();

        if ($google_drive_apis->isEmpty()) {
            session()->flash('error', 'No API keys available.');

        }

        // Retrieve the last used API key (from session or cache)
        $lastUsedApiKey = session()->get('last_used_api_key', null);

        // Filter out the last used API key from the list
        $availableApiKeys = $google_drive_apis->filter(function ($api) use ($lastUsedApiKey) {
            return $api->api_key !== $lastUsedApiKey;
        });

        // If only one key is available, we can't alternate
        if ($availableApiKeys->isEmpty()) {
            session()->flash('error', 'Only one API key available, cannot alternate.');

        }

        // Randomly select a new API key that hasn't been used last
        $newApiKey = $availableApiKeys->random()->api_key;

        // Store the new API key in session to prevent it from being reused next time
        session()->put('last_used_api_key', $newApiKey);

        return $newApiKey;
    }
}
