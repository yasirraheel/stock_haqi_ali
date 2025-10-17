<?php

namespace App\Http\Controllers\API;

use Auth;
use App\User;
use App\Slider;
use App\Series;
use App\Season;
use App\Episodes;
use App\Movies;
use App\HomeSections;
use App\Sports;
use App\Pages;
use App\RecentlyWatched;
use App\Language;
use App\Genres;
use App\Settings;
use App\SportsCategory;
use App\SubscriptionPlan;
use App\Transactions;
use App\SettingsAndroidApp;
use App\TvCategory;
use App\LiveTV;
use App\Player;
use App\Watchlist;
use App\Coupons;
use App\ActorDirector;
use App\PaymentGateway;
use App\AppAds;
use App\PasswordReset;
use App\UsersDeviceHistory;
use App\Models\Audio;
use App\Models\Photos;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\GoogleDriveApi;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Session;
use URL;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Mail;

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class AndroidApiController extends MainAPIController
{

    public function index()
    {
          $response[] = array('msg' => "API",'success'=>'1');

        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'status_code' => 200
        ));

    }
    public function app_details()
    {

        $get_data=checkSignSalt($_POST['data']);

        if(isset($get_data['user_id']) && $get_data['user_id']!='')
        {
            $user_id=$get_data['user_id'];
            $user_info = User::getUserInfo($user_id);

           if(!empty($user_info))
           {
                if($user_info!='' AND $user_info->status==1)
                {
                    $user_status=true;
                }
                else
                {
                    $user_status=false;
                }
            }
            else
            {
               $user_status=false;
            }
        }
        else
        {
            $user_status=false;
        }


        $settings = SettingsAndroidApp::findOrFail('1');

        $app_name=$settings->app_name;
        $app_logo=\URL::to('/'.$settings->app_logo);
        $app_version=$settings->app_version?$settings->app_version:'';
        $app_company=$settings->app_company?$settings->app_company:'';
        $app_email=$settings->app_email;
        $app_website=$settings->app_website?$settings->app_website:'';
        $app_contact=$settings->app_contact?$settings->app_contact:'';
        $app_about=$settings->app_about?stripslashes($settings->app_about):'';
        $app_privacy=$settings->app_privacy?stripslashes($settings->app_privacy):'';
        $app_terms=$settings->app_terms?stripslashes($settings->app_terms):'';


        //Ad List
        $ads_list = AppAds::where('status','1')->orderby('id')->get();

        if(count($ads_list) > 0)
        {
            foreach($ads_list as $ad_data)
            {
                    $ad_id= $ad_data->id;
                    $ads_name= $ad_data->ads_name;
                    $ads_info= json_decode($ad_data->ads_info);
                    $status= $ad_data->status?"true":"false";;

                    $ads_obj[]=array("ad_id"=>$ad_id,"ads_name"=>$ads_name,"ads_info"=>$ads_info,"status"=>$status);
            }
        }
        else
        {
            $ads_obj= array();
        }


        $app_update_hide_show=$settings->app_update_hide_show;
        $app_update_version_code=$settings->app_update_version_code?$settings->app_update_version_code:'1';
        $app_update_desc=stripslashes($settings->app_update_desc);
        $app_update_link=$settings->app_update_link;
        $app_update_cancel_option=$settings->app_update_cancel_option;

        $web_settings = Settings::findOrFail('1');

        $menu_shows=$web_settings->menu_shows?"true":"false";
        $menu_movies=$web_settings->menu_movies?"true":"false";
        $menu_sports=$web_settings->menu_sports?"true":"false";
        $menu_livetv=$web_settings->menu_livetv?"true":"false";


        $app_package_name=env('BUYER_APP_PACKAGE_NAME')?env('BUYER_APP_PACKAGE_NAME'):"";

        $response[] = array('app_package_name'=>$app_package_name,'app_name' => $app_name,'app_logo' => $app_logo,'app_version' => $app_version,'app_company' => $app_company,'app_email' => $app_email,'app_website' => $app_website,'app_contact' => $app_contact,'app_about' => $app_about,'app_privacy' => $app_privacy,'app_terms'=>$app_terms,'ads_list'=>$ads_obj,'app_update_hide_show' => $app_update_hide_show,'app_update_version_code' => $app_update_version_code,'app_update_desc' => $app_update_desc,'app_update_link' => $app_update_link,'app_update_cancel_option' => $app_update_cancel_option,'menu_shows'=>$menu_shows,'menu_movies'=>$menu_movies,'menu_sports'=>$menu_sports,'menu_livetv'=>$menu_livetv,'success'=>'1');

        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'user_status' => $user_status,
            'status_code' => 200
        ));

    }

    public function postLogin()
    {
        // Add logging to see what's being received
        \Log::info('Login attempt - POST data:', ['data' => $_POST]);
        \Log::info('Login attempt - Raw input:', ['input' => file_get_contents('php://input')]);

        if (!isset($_POST['data'])) {
            \Log::error('Login failed - No data parameter in POST');
            return \Response::json(array(
                'VIDEO_STREAMING_APP' => [array('msg' => "No data parameter",'success'=>'0')],
                'status_code' => 400
            ));
        }

        $get_data=checkSignSalt($_POST['data']);


        $email=isset($get_data['email'])?$get_data['email']:'';
        $password=isset($get_data['password'])?$get_data['password']:'';

        if ($email=='' AND $password=='')
        {

               $response[] = array('msg' => "All field required",'success'=>'0');

                return \Response::json(array(
                    'VIDEO_STREAMING_APP' => $response,
                    'status_code' => 200
                ));
        }

        $user_info = User::where('email',$email)->first();


        if (!empty($user_info) AND Hash::check($password, $user_info['password']))
        {
            if($user_info->status=='0' AND $user_info->deleted_at!=NULL)
            {

                $response[] = array('msg' => trans('words.account_delete_msg'),'success'=>'0');
            }
            else if($user_info->status==0){
                //\Auth::logout();

                  $response[] = array('msg' => trans('words.account_banned'),'success'=>'0');
            }
            else
            {
                $user_id=$user_info->id;
                $user = User::findOrFail($user_id);

                \Auth::attempt(['email' => $user->email, 'password' => null]);

                $brand=isset($get_data['brand'])?$get_data['brand']:'';
                $model=isset($get_data['model'])?$get_data['model']:'';
                $platform=isset($get_data['platform'])?$get_data['platform']:'';

                $user_device_name= $brand.' '.$model.' '.$platform;

                 //Save History
                $user_session_name=Session::getId();

                $ck_user_device_obj = UsersDeviceHistory::where('user_id',$user_id)->where('user_session_name',$user_session_name)->first();

                if($ck_user_device_obj)
                {
                    $device_h_id=$ck_user_device_obj->id;

                    $user_device_obj = UsersDeviceHistory::findOrFail($device_h_id);

                    $user_device_obj->user_id = $user_id;
                    $user_device_obj->user_device_name=$user_device_name;
                    $user_device_obj->user_session_name=Session::getId();
                    $user_device_obj->save();
                }
                else
                {
                    $user_device_obj = new UsersDeviceHistory;

                    $user_device_obj->user_id = $user_id;
                    $user_device_obj->user_device_name=$user_device_name;
                    $user_device_obj->user_session_name=Session::getId();
                    $user_device_obj->save();
                }

                /***Save Device End***/
                //Device limit check
                $plan_id=$user->plan_id;
                if(user_device_limit_reached($user_id,$plan_id))
                {
                    $device_limit_reached = true;
                }
                else
                {
                    $device_limit_reached = false;
                }

                if($user->user_image!='')
                {
                    $user_image=\URL::asset('upload/'.$user->user_image);
                }
                else
                {
                    $user_image=\URL::asset('upload/profile.png');
                }
                $phone= $user->phone?$user->phone:'';
                $response[] = array('user_session_name' => $user_session_name,'device_limit_reached' => $device_limit_reached,'user_id' => $user_id,'name' => $user->name,'email' => $user->email,'phone' => $phone,'user_image' => $user_image,'msg' => 'Login successfully...','success'=>'1');
            }

        }
        else
        {
            $response[] = array('msg' => trans('words.email_password_invalid'),'success'=>'0');
        }


        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'status_code' => 200
        ));
    }

    public function postSignup()
    {


        $get_data=checkSignSalt($_POST['data']);

        $name=isset($get_data['name'])?$get_data['name']:'';
        $email=isset($get_data['email'])?$get_data['email']:'';
        $password=isset($get_data['password'])?$get_data['password']:'';


        // Validation rules - same as web signup
        $rule = array(
            'name' => 'required',
            'email' => 'required|email|max:200|unique:users',
            'password' => 'required|min:8'
        );

        $data = array(
            'name' => $name,
            'email' => $email,
            'password' => $password
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            $response[] = array('msg' => "Validation failed: " . implode(', ', $validator->errors()->all()),'success'=>'0');
            return \Response::json(array(
                'VIDEO_STREAMING_APP' => $response,
                'status_code' => 200
            ));
        }

        $user = new User;

        //$confirmation_code = str_random(30);


        $user->usertype = 'Sub_Admin';
        $user->name = $name;
        $user->email = $email;
        $user->password= bcrypt($password);
        $user->save();

        //Welcome Email

        try{
            $user_name=$name;
            $user_email=$email;

            $data_email = array(
                'name' => $user_name,
                'email' => $user_email
                );

            \Mail::send('emails.welcome', $data_email, function($message) use ($user_name,$user_email){
                $message->to($user_email, $user_name)
                ->from(getcong('site_email'), getcong('site_name'))
                ->subject('Welcome to '.getcong('site_name'));
            });
        }catch (\Throwable $e) {

                    \Log::info($e->getMessage());
                }

        $response[] = array('msg' => trans('words.account_created_successfully'),'success'=>'1');
        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'status_code' => 200
        ));


    }
    public function logout()
    {
        $get_data=checkSignSalt($_POST['data']);

        $user_id=$get_data['user_id'];

        //Delete session file
        $user_session_name=$get_data['user_session_name'];
        \Session::getHandler()->destroy($user_session_name);

        $user_device_obj = UsersDeviceHistory::where('user_id',$user_id)->where('user_session_name',$user_session_name);
        $user_device_obj->delete();

        $response[] = array('msg' => trans('words.logout'),'success'=>'1');

        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'status_code' => 200
        ));
    }
    public function forgot_password()
    {
        $get_data=checkSignSalt($_POST['data']);

        $email=isset($get_data['email'])?$get_data['email']:'';

        $user = User::where('email', $email)->first();

        if(!$user)
        {
            $response[] = array('msg' => 'We can\'t find a user with that e-mail address.','success'=>'1');
        }
        else
        {

            $token = Str::random(64);

            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now()
              ]);

            Mail::send('emails.forget_password', ['token' => $token], function($message) use($email){
                $message->to($email);
                $message->subject('Reset Password');
            });


           $response[] = array('msg' => 'We have e-mailed your password reset link!','success'=>'1');

        }

        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'status_code' => 200
        ));
    }

    public function profile()
    {
        $get_data=checkSignSalt($_POST['data']);

        $user_id=$get_data['user_id'];

        $user = User::findOrFail($user_id);

        if($user->user_image!='')
        {
            $user_image=\URL::asset('upload/'.$user->user_image);
        }
        else
        {
            $user_image=\URL::asset('upload/profile.jpg');
        }

        $phone=$user->phone?$user->phone:'';
        $user_address=$user->user_address?$user->user_address:'';
        $paypal_email=$user->paypal_email?$user->paypal_email:'';

        $response = array(
            'user_id' => $user_id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $phone,
            'paypal_email' => $paypal_email,
            'user_address' => $user_address,
            'user_image' => $user_image
        );

        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'status_code' => 200
        ));
    }

    public function profile_update(Request $request)
    {
        // Use EXACT same method as web UserController@editprofile
        $get_data=checkSignSalt($request->input('data'));

        // Debug logging
        \Log::info('Profile Update - Received data:', ['data' => $get_data]);

        $user_id = $get_data['user_id'];
        $user = User::findOrFail($user_id);

        // Debug logging - show current user data
        \Log::info('Profile Update - Current user data:', [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'paypal_email' => $user->paypal_email,
            'user_address' => $user->user_address
        ]);

        // Same validation as web but handle image validation properly
        $rule = array(
            'name' => 'required',
            'email' => 'required|email|max:255|unique:users,email,'.$user_id
        );

        $data = array(
            'name' => $get_data['name'],
            'email' => $get_data['email']
        );

        // Only validate image if it's provided
        if ($request->hasFile('user_image')) {
            $rule['user_image'] = 'mimes:jpg,jpeg,gif,png';
            $data['user_image'] = $request->file('user_image');
        }

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            \Log::error('Profile Update - Validation failed:', ['errors' => $validator->errors()->all()]);
            $response = array('msg' => 'Validation failed: ' . implode(', ', $validator->errors()->all()), 'success'=>'0');
            return \Response::json(array(
                'VIDEO_STREAMING_APP' => $response,
                'status_code' => 200
            ));
        }

        // EXACT same image handling as web
        $icon = $request->file('user_image');

        if($icon){
            \File::delete(public_path('/upload/').$user->user_image);
            $tmpFilePath = public_path('/upload/');
            $hardPath = Str::slug($get_data['name'], '-').'-'.md5(time());
            $img = Image::make($icon);
            $img->fit(250, 250)->save($tmpFilePath.$hardPath.'-b.jpg');
            $user->user_image = $hardPath.'-b.jpg';
        }

        // EXACT same field updates as web
        \Log::info('Profile Update - About to update fields:', [
            'new_name' => $get_data['name'],
            'new_email' => $get_data['email'],
            'new_phone' => $get_data['phone'],
            'new_paypal_email' => $get_data['paypal_email'],
            'new_user_address' => $get_data['user_address']
        ]);

        $user->name = $get_data['name'];
        $user->email = $get_data['email'];
        $user->phone = $get_data['phone'];
        $user->paypal_email = $get_data['paypal_email'];
        $user->user_address = $get_data['user_address'];

        if($get_data['password'])
        {
            $user->password = bcrypt($get_data['password']);
        }

        $saveResult = $user->save();
        \Log::info('Profile Update - Save result:', ['saved' => $saveResult]);

        // Debug logging - show updated user data
        \Log::info('Profile Update - Updated user data:', [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'paypal_email' => $user->paypal_email,
            'user_address' => $user->user_address
        ]);

        // Return updated profile data
        $user_image = '';
        if($user->user_image != '') {
            $user_image = \URL::asset('upload/'.$user->user_image);
        } else {
            $user_image = \URL::asset('upload/profile.jpg');
        }

        $phone = $user->phone ? $user->phone : '';
        $user_address = $user->user_address ? $user->user_address : '';
        $paypal_email = $user->paypal_email ? $user->paypal_email : '';

        $profile_data = array(
            'user_id' => $user_id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $phone,
            'paypal_email' => $paypal_email,
            'user_address' => $user_address,
            'user_image' => $user_image
        );

        $response = array('msg' => trans('words.successfully_updated'),'success'=>'1');
        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'profile_data' => $profile_data,
            'status_code' => 200
        ));
    }

    public function check_user_plan()
    {
        $get_data=checkSignSalt($_POST['data']);

        $user_id=$get_data['user_id'];

        $user_info = User::findOrFail($user_id);
        $user_plan_id=$user_info->plan_id;
        $user_plan_exp_date=$user_info->exp_date;


        if($user_plan_id==0)
        {
             $response = array('msg' => 'Please select subscription plan','success'=>'0');

            return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'status_code' => 200
            ));
        }
        else if(strtotime(date('m/d/Y'))>$user_plan_exp_date)
        {

                $current_plan=SubscriptionPlan::getSubscriptionPlanInfo($user_plan_id,'plan_name');

                $expired_on=$user_plan_exp_date;

                $response = array('current_plan'=>$current_plan,'expired_on'=>$expired_on,'msg' => 'Renew subscription plan','success'=>'0');

                return \Response::json(array(
                'VIDEO_STREAMING_APP' => $response,
                'status_code' => 200
                ));
        }
        else
        {
                $current_plan=SubscriptionPlan::getSubscriptionPlanInfo($user_plan_id,'plan_name');

                $expired_on=$user_plan_exp_date;

                $response = array('current_plan'=>$current_plan,'expired_on'=>$expired_on,'msg' => 'My Subscription','success'=>'1');

                return \Response::json(array(
                'VIDEO_STREAMING_APP' => $response,
                'status_code' => 200
                ));
        }


    }

    public function genres()
    {
        // $get_data=checkSignSalt($_POST['data']);

        $genres_list = Genres::where('status',1)->orderby('id')->get();

        foreach($genres_list as $genres_data)
        {
                $genre_id= $genres_data->id;
                $genre_name= stripslashes($genres_data->genre_name);
                $response[]=array("genre_id"=>$genre_id,"genre_name"=>$genre_name);
        }

        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'status_code' => 200
        ));
    }

    public function seasons()
    {
        $get_data=checkSignSalt($_POST['data']);

        $series_id=$get_data['show_id'];

        $season_list = Season::where('status',1)->where('series_id',$series_id)->get();

        if($season_list->count())
       {
            foreach($season_list as $season_data)
            {
                    $season_id= $season_data->id;
                    $season_name=  stripslashes($season_data->season_name);
                    $season_poster=  URL::to('/'.$season_data->season_poster);

                    $response[]=array("season_id"=>$season_id,"season_name"=>$season_name,"season_poster"=>$season_poster);
            }
       }
       else
       {
            $response=array();
       }

        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'status_code' => 200
        ));
    }

    public function movies()
    {
        $get_data=checkSignSalt($_POST['data']);

        $slider= Slider::where('status',1)->whereRaw("find_in_set('Movies',slider_display_on)")->orderby('id','DESC')->get();

        foreach($slider as $slider_data)
        {
            $response['slider'][]=array("slider_title"=>stripslashes($slider_data->slider_title),"slider_type"=>$slider_data->slider_type,"slider_post_id"=>$slider_data->slider_post_id,"slider_image"=>\URL::to('/'.$slider_data->slider_image));
        }

        if(isset($get_data['filter']))
        {
            $keyword = $get_data['filter'];

            if($keyword=='old')
            {
                $movies_list = Movies::where('status',1)->where('upcoming',0)->orderBy('id','ASC')->paginate(12);
                $movies_list->appends(\Request::only('filter'))->links();
            }
            else if($keyword=='alpha')
            {
                $movies_list = Movies::where('status',1)->where('upcoming',0)->orderBy('video_title','ASC')->paginate(12);
                $movies_list->appends(\Request::only('filter'))->links();
            }
            else if($keyword=='rand')
            {
                $movies_list = Movies::where('status',1)->where('upcoming',0)->inRandomOrder()->paginate(12);
                $movies_list->appends(\Request::only('filter'))->links();
            }
            else
            {
                $movies_list = Movies::where('status',1)->where('upcoming',0)->orderBy('id','DESC')->paginate(12);
                $movies_list->appends(\Request::only('filter'))->links();
            }

        }
        else
        {
            $movies_list = Movies::where('status',1)->where('upcoming',0)->orderBy('id','DESC')->paginate(12);
        }

      $total_records=Movies::where('status','1')->where('upcoming',0)->count();

      if($movies_list->count())
       {
            foreach($movies_list  as $movie_data)
            {

                    $movie_id= $movie_data->id;
                    $movie_title= stripslashes($movie_data->video_title);
                    $movie_poster= URL::to('/'.$movie_data->video_image_thumb);
                    $movie_duration= $movie_data->duration;
                    $movie_access= $movie_data->video_access;

                    $content_rating= $movie_data->content_rating?$movie_data->content_rating:'';


                    $response['movies'][]=array("movie_id"=>$movie_id,"content_rating"=>$content_rating,"movie_title"=>$movie_title,"movie_poster"=>$movie_poster,"movie_duration"=>$movie_duration,"movie_access"=>$movie_access);

            }
        }
        else
        {
            $response=array();
        }

        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'total_records' => $total_records,
            'status_code' => 200
        ));
    }

    public function random_videos()
    {
        // API Key validation for public access
        $api_key = request()->header('X-API-KEY') ?: request()->input('api_key');
        $valid_api_key = 'sk_cineworm_2024_random_video_api_key_secure';

        if (!$api_key || $api_key !== $valid_api_key) {
            return \Response::json(array(
                'error' => 'Invalid or missing API key',
                'message' => 'Please provide a valid API key in X-API-KEY header or api_key parameter',
                'status_code' => 401
            ), 401);
        }

        // Check if data parameter exists, if not use default values
        if (!isset($_POST['data']) || empty($_POST['data'])) {
            $get_data = array(); // Default empty array
        } else {
            $get_data = checkSignSalt($_POST['data']);
        }

        // Smart randomization - avoid repeating videos
        $total_records = Movies::where('status', 1)->where('upcoming', 0)->count();

        // Get or create session key for tracking shown videos
        $session_key = 'random_videos_shown_' . $api_key;
        $shown_videos = session($session_key, []);

        // If all videos have been shown, reset the tracking
        if (count($shown_videos) >= $total_records) {
            $shown_videos = [];
            session([$session_key => []]);
        }

        // Get a random video that hasn't been shown yet
        $movie_data = Movies::where('status', 1)
                            ->where('upcoming', 0)
                            ->whereNotIn('id', $shown_videos)
                            ->inRandomOrder()
                            ->first();

        // If somehow no video is found (edge case), get any random video
        if (!$movie_data) {
            $movie_data = Movies::where('status', 1)
                                ->where('upcoming', 0)
                                ->inRandomOrder()
                                ->first();
            // Reset the session if this happens
            session([$session_key => []]);
            $shown_videos = [];
        }

        // Add current video to shown list
        if ($movie_data) {
            $shown_videos[] = $movie_data->id;
            session([$session_key => $shown_videos]);
        }

        $response = array();

        if($movie_data)
        {
            $movie_id = $movie_data->id;
            $movie_title = stripslashes($movie_data->video_title);
            $movie_poster = URL::to('/'.$movie_data->video_image_thumb);
            $movie_duration = $movie_data->duration;
            $movie_access = $movie_data->video_access;
            $content_rating = $movie_data->content_rating ? $movie_data->content_rating : '';
            $video_description = stripslashes($movie_data->video_description);
            $release_date = $movie_data->release_date;
            $imdb_rating = $movie_data->imdb_rating;
            $views = $movie_data->views;

            // Video URLs
            $video_url = $movie_data->video_url ? $movie_data->video_url : '';
            $video_url_480 = $movie_data->video_url_480 ? $movie_data->video_url_480 : '';
            $video_url_720 = $movie_data->video_url_720 ? $movie_data->video_url_720 : '';
            $video_url_1080 = $movie_data->video_url_1080 ? $movie_data->video_url_1080 : '';

            // Subtitle information
            $subtitle_language1 = $movie_data->subtitle_language1 ? $movie_data->subtitle_language1 : '';
            $subtitle_url1 = $movie_data->subtitle_url1 ? $movie_data->subtitle_url1 : '';
            $subtitle_language2 = $movie_data->subtitle_language2 ? $movie_data->subtitle_language2 : '';
            $subtitle_url2 = $movie_data->subtitle_url2 ? $movie_data->subtitle_url2 : '';
            $subtitle_language3 = $movie_data->subtitle_language3 ? $movie_data->subtitle_language3 : '';
            $subtitle_url3 = $movie_data->subtitle_url3 ? $movie_data->subtitle_url3 : '';

            // Download information
            $download_enable = $movie_data->download_enable ? 'true' : 'false';
            $download_url = $movie_data->download_url ? $movie_data->download_url : '';

            // Additional fields
            $video_image = $movie_data->video_image ? URL::to('/'.$movie_data->video_image) : '';
            $video_slug = $movie_data->video_slug ? $movie_data->video_slug : '';
            $added_by = $movie_data->added_by ? $movie_data->added_by : '';
            $is_upcoming = $movie_data->upcoming ? 'true' : 'false';
            $seo_title = $movie_data->seo_title ? $movie_data->seo_title : '';
            $seo_description = $movie_data->seo_description ? $movie_data->seo_description : '';
            $seo_keyword = $movie_data->seo_keyword ? $movie_data->seo_keyword : '';

            // Get genre information
            $genres = array();
            if($movie_data->movie_genre_id) {
                foreach(explode(',', $movie_data->movie_genre_id) as $genre_id) {
                    $genre_name = Genres::getGenresInfo($genre_id, 'genre_name');
                    if($genre_name) {
                        $genres[] = array('genre_id' => $genre_id, 'genre_name' => $genre_name);
                    }
                }
            }

            // Get language information
            $language_name = '';
            if($movie_data->movie_lang_id) {
                $language_name = Language::getLanguageInfo($movie_data->movie_lang_id, 'language_name');
            }

            // Get actor information
            $actors = array();
            if($movie_data->actor_id) {
                foreach(explode(',', $movie_data->actor_id) as $actor_id) {
                    $ad_info = ActorDirector::where('id', $actor_id)->first();
                    if($ad_info) {
                        $ad_name = isset($ad_info->ad_name) ? $ad_info->ad_name : '';
                        $ad_image = isset($ad_info->ad_image) ? $ad_info->ad_image : '';
                        $ad_image_url = $ad_image ? URL::to('/'.$ad_image) : URL::to('images/user_icon.png');

                        $actors[] = array(
                            'actor_id' => $actor_id,
                            'actor_name' => $ad_name,
                            'actor_image' => $ad_image_url
                        );
                    }
                }
            }

            // Get director information
            $directors = array();
            if($movie_data->director_id) {
                foreach(explode(',', $movie_data->director_id) as $director_id) {
                    $ad_info = ActorDirector::where('id', $director_id)->first();
                    if($ad_info) {
                        $ad_name = isset($ad_info->ad_name) ? $ad_info->ad_name : '';
                        $ad_image = isset($ad_info->ad_image) ? $ad_info->ad_image : '';
                        $ad_image_url = $ad_image ? URL::to('/'.$ad_image) : URL::to('images/user_icon.png');

                        $directors[] = array(
                            'director_id' => $director_id,
                            'director_name' => $ad_name,
                            'director_image' => $ad_image_url
                        );
                    }
                }
            }

            $response['random_video'] = array(
                "movie_id" => $movie_id,
                "movie_title" => $movie_title,
                "video_slug" => $video_slug,
                "movie_poster" => $movie_poster,
                "video_image" => $video_image,
                "movie_duration" => $movie_duration,
                "movie_access" => $movie_access,
                "content_rating" => $content_rating,
                "video_description" => $video_description,
                "release_date" => $release_date,
                "imdb_rating" => $imdb_rating,
                "views" => $views,
                "video_url" => $video_url,
                "video_url_480" => $video_url_480,
                "video_url_720" => $video_url_720,
                "video_url_1080" => $video_url_1080,
                "subtitle_language1" => $subtitle_language1,
                "subtitle_url1" => $subtitle_url1,
                "subtitle_language2" => $subtitle_language2,
                "subtitle_url2" => $subtitle_url2,
                "subtitle_language3" => $subtitle_language3,
                "subtitle_url3" => $subtitle_url3,
                "download_enable" => $download_enable,
                "download_url" => $download_url,
                "added_by" => $added_by,
                "is_upcoming" => $is_upcoming,
                "seo_title" => $seo_title,
                "seo_description" => $seo_description,
                "seo_keyword" => $seo_keyword,
                "genres" => $genres,
                "language_name" => $language_name,
                "actors" => $actors,
                "directors" => $directors,
                "license_price" => $movie_data->license_price ? $movie_data->license_price : null,
                "is_premium" => $movie_data->license_price && $movie_data->license_price > 0 ? true : false
            );
        }

        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'total_records' => $total_records,
            'returned_records' => $movie_data ? 1 : 0,
            'randomization_info' => array(
                'videos_shown_in_cycle' => count($shown_videos),
                'total_available_videos' => $total_records,
                'cycle_progress' => round((count($shown_videos) / max($total_records, 1)) * 100, 2) . '%'
            ),
            'status_code' => 200
        ));
    }

    public function search()
    {
        $get_data=checkSignSalt($_POST['data']);

        $keyword = $get_data['search_text'];

        //Movie Data
        $movies_list = Movies::where('status',1)->where('upcoming',0)->where("video_title", "LIKE","%$keyword%")->orderBy('video_title')->get();

           if($movies_list->count())
           {
                if(getcong('menu_movies'))
                {
                    foreach($movies_list  as $movie_data)
                    {

                            $movie_id= $movie_data->id;
                            $movie_title= stripslashes($movie_data->video_title);
                            $movie_poster= URL::to('/'.$movie_data->video_image_thumb);
                            $movie_duration= $movie_data->duration;
                            $movie_access= $movie_data->video_access;

                            $response['movies'][]=array("movie_id"=>$movie_id,"movie_title"=>$movie_title,"movie_poster"=>$movie_poster,"movie_duration"=>$movie_duration,"movie_access"=>$movie_access);

                    }
                }
                else
                {
                    $response['movies']=array();
                }
            }
            else
            {
                $response['movies']=array();
            }

        //Movie End

        //Show Start
        $series_list = Series::where('status',1)->where('upcoming',0)->where("series_name", "LIKE","%$keyword%")->orderBy('series_name')->get();

           if($series_list->count())
           {
                if(getcong('menu_shows'))
                {

                    foreach($series_list as $series_data)
                    {
                            $show_id= $series_data->id;
                            $show_title=  stripslashes($series_data->series_name);
                            $show_poster=  URL::to('/'.$series_data->series_poster);
                            $show_access= $series_data->series_access;

                            $response['shows'][]=array("show_id"=>$show_id,"show_title"=>$show_title,"show_poster"=>$show_poster,"show_access"=>$show_access);
                    }
                }
                else
                {
                    $response['shows']=array();
                }
           }
           else
           {
                $response['shows']=array();
           }
        //Show End

        //Sports Start
        $sports_video_list = Sports::where('status',1)->where("video_title", "LIKE","%$keyword%")->orderBy('video_title')->get();

          if($sports_video_list->count())
           {
                if(getcong('menu_sports'))
                {
                    foreach($sports_video_list  as $sports_data)
                    {

                            $sport_id= $sports_data->id;
                            $sport_title= stripslashes($sports_data->video_title);
                            $sport_poster= URL::to('/'.$sports_data->video_image);
                            $sport_duration= $sports_data->duration;
                            $sport_access= $sports_data->video_access;

                            $response['sports'][]=array("sport_id"=>$sport_id,"sport_title"=>$sport_title,"sport_image"=>$sport_poster,"sport_duration"=>$sport_duration,"sport_access"=>$sport_access);

                    }
                }
                else
                {
                    $response['sports']=array();
                }
            }
            else
            {
                $response['sports']=array();
            }
        //Sports End

        //Live TV Start
        $live_tv_list = LiveTV::where('status',1)->where("channel_name", "LIKE","%$keyword%")->orderBy('channel_name')->get();

          if($live_tv_list->count())
           {
                if(getcong('menu_livetv'))
                {
                    foreach($live_tv_list  as $live_tv_data)
                    {

                            $tv_id= $live_tv_data->id;
                            $tv_title= stripslashes($live_tv_data->channel_name);
                            $tv_logo= URL::to('/'.$live_tv_data->channel_thumb);
                            $tv_access= $live_tv_data->channel_access;

                            $response['live_tv'][]=array("tv_id"=>$tv_id,"tv_title"=>$tv_title,"tv_logo"=>$tv_logo,"tv_access"=>$tv_access);

                    }
                }
                else
                {
                    $response['live_tv']=array();
                }
            }
            else
            {
                $response['live_tv']=array();
            }
        //Live TV End

        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
             'status_code' => 200
        ));
    }
    public function addnew(Request $request)
    {
        try {
            // Log all incoming request data
            \Log::info('=== ADD NEW MOVIE API CALL ===');
            \Log::info('Request Method: ' . $request->method());
            \Log::info('Request URL: ' . $request->fullUrl());
            \Log::info('Request Headers: ' . json_encode($request->headers->all()));
            \Log::info('Request Data: ' . json_encode($request->all()));
            \Log::info('User ID: ' . (Auth::id() ?? 'Not authenticated'));
            \Log::info('IP Address: ' . $request->ip());
            \Log::info('User Agent: ' . $request->userAgent());
            \Log::info('Content Type: ' . $request->header('Content-Type'));
            \Log::info('Authorization: ' . $request->header('Authorization'));
            
            // Use checkSignSalt to validate and decode data (same as profile update)
            $dataSource = checkSignSalt($request->input('data'));
            
            // Log specific fields that are important for movie creation
            \Log::info('=== MOVIE DATA BREAKDOWN ===');
            \Log::info('Video Title: ' . ($dataSource['video_title'] ?? 'Not provided'));
            \Log::info('Video URL: ' . ($dataSource['video_url'] ?? 'Not provided'));
            \Log::info('Video Description: ' . ($dataSource['video_description'] ?? 'Not provided'));
            \Log::info('Genres: ' . json_encode($dataSource['genres'] ?? []));
            \Log::info('Actors ID: ' . json_encode($dataSource['actors_id'] ?? []));
            \Log::info('Director ID: ' . json_encode($dataSource['director_id'] ?? []));
            \Log::info('License Price: ' . ($dataSource['license_price'] ?? 'Not provided'));
            \Log::info('Video Quality: ' . ($dataSource['video_quality'] ?? 'Not provided'));
            \Log::info('Download Enable: ' . ($dataSource['download_enable'] ?? 'Not provided'));
            \Log::info('Download URL: ' . ($dataSource['download_url'] ?? 'Not provided'));
            \Log::info('Subtitle On/Off: ' . ($dataSource['subtitle_on_off'] ?? 'Not provided'));
            \Log::info('Poster Link: ' . ($dataSource['poster_link'] ?? 'Not provided'));
            \Log::info('Video Image: ' . ($dataSource['video_image'] ?? 'Not provided'));
            \Log::info('Webpage URL: ' . ($dataSource['webpage_url'] ?? 'Not provided'));
            \Log::info('Status: ' . ($dataSource['status'] ?? 'Not provided'));
            \Log::info('Movie ID (for update): ' . ($dataSource['id'] ?? 'New movie'));
            \Log::info('==========================');
            
            $google_drive_api = $this->getRandomApiKey();
            GoogleDriveApi::where('api_key', $google_drive_api)->increment('calls');
    
            $googleDriveUrl = $dataSource['video_url'] ?? '';
    
            // Extract file ID safely
            $googleDriveUrl = trim(urldecode($googleDriveUrl));
            
            // Debug: Log the URL being processed
            \Log::info('Processing Google Drive URL: ' . $googleDriveUrl);

            // Improved regex to handle various Google Drive URL formats
            $patterns = [
                '/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/',
                '/drive\.google\.com\/d\/([a-zA-Z0-9_-]+)/',
                '/id=([a-zA-Z0-9_-]+)/',
                '/\/files\/([a-zA-Z0-9_-]+)/'
            ];
            
            $fileId = null;
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $googleDriveUrl, $matches)) {
                    $fileId = $matches[1];
                    break;
                }
            }
            
            if (!$fileId) {
                \Log::error('Failed to extract file ID from URL: ' . $googleDriveUrl);
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Google Drive URL format. Please provide a valid file share link. URL received: ' . $googleDriveUrl
                ], 400);
            }
            
            \Log::info('Extracted file ID: ' . $fileId);
            
    
            $video_url = "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&key={$google_drive_api}";
    
            // Validate input
            $validator = \Validator::make($dataSource, [
                'genres' => 'required|array',
                'video_title' => 'required|string|max:255',
            ]);
            
            if ($validator->fails()) {
                \Log::error('Validation failed: ' . json_encode($validator->errors()));
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }
    
            $inputs = $dataSource;
    
            // Create or update movie
            $movie_obj = !empty($inputs['id']) ? Movies::findOrFail($inputs['id']) : new Movies;
            $original_file_id = $movie_obj->file_id ?? '';
    
            $movie_obj->fill([
                'funding_url' => $inputs['funding_url'] ?? null,
                'movie_lang_id' => 0,
                'movie_genre_id' => implode(',', $inputs['genres']),
                'video_title' => addslashes($inputs['video_title']),
                'video_slug' => Str::slug($inputs['video_title']),
                'video_description' => addslashes($inputs['video_description'] ?? ''),
                'actor_id' => isset($inputs['actors_id']) ? implode(',', $inputs['actors_id']) : null,
                'director_id' => isset($inputs['director_id']) ? implode(',', $inputs['director_id']) : null,
                'added_by' => Auth::id(),
                'license_price' => $inputs['license_price'] ?? 0,
                'file_id' => $fileId,
                'webpage_url' => $inputs['webpage_url'] ?? null,
                'status' => auth()->user()->usertype == 'Admin' ? ($inputs['status'] ?? 0) : 0,
                'video_url' => $video_url,
                'video_type' => 'URL'
            ]);
    
            // Optional fields
            $movie_obj->video_quality = $inputs['video_quality'] ?? $movie_obj->video_quality;
            $movie_obj->download_enable = $inputs['download_enable'] ?? $movie_obj->download_enable;
            $movie_obj->download_url = $inputs['download_url'] ?? $movie_obj->download_url;
            $movie_obj->subtitle_on_off = $inputs['subtitle_on_off'] ?? $movie_obj->subtitle_on_off;
    
            // Poster download
            if (!empty($inputs['poster_link'])) {
                try {
                    $save_to = public_path('/upload/images/' . $inputs['video_image']);
                    grab_image($inputs['poster_link'], $save_to);
                    $movie_obj->video_image = 'upload/images/' . $inputs['video_image'];
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Poster download failed'], 500);
                }
            }
    
            $movie_obj->save();
    
            // Generate screenshot if needed
            if (empty($inputs['id']) || $original_file_id !== $fileId) {
                $screenshotResult = $this->generateAIScreenshot($movie_obj->video_title, $movie_obj->video_description, $fileId);
                if (isset($screenshotResult['error'])) {
                    return response()->json(['status' => false, 'message' => $screenshotResult['error']], 500);
                }
            }
    
            $response_data = [
                'status' => true,
                'message' => !empty($inputs['id']) ? 'Movie successfully updated.' : 'Movie successfully added.',
                'data' => $movie_obj
            ];
            
            // Log successful response
            \Log::info('=== SUCCESSFUL RESPONSE ===');
            \Log::info('Response Data: ' . json_encode($response_data));
            \Log::info('Movie ID: ' . $movie_obj->id);
            \Log::info('Movie Title: ' . $movie_obj->video_title);
            \Log::info('Video URL: ' . $movie_obj->video_url);
            \Log::info('========================');
            
            return response()->json($response_data, 200);
    
        } catch (\Exception $e) {
            // Log error details
            \Log::error('=== ERROR IN ADDNEW METHOD ===');
            \Log::error('Error Message: ' . $e->getMessage());
            \Log::error('Error File: ' . $e->getFile());
            \Log::error('Error Line: ' . $e->getLine());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());
            \Log::error('Request Data: ' . json_encode($request->all()));
            \Log::error('============================');
            
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getRandomApiKey()
    {
        // Get all available Google Drive API keys
        $google_drive_apis = GoogleDriveApi::pluck('api_key');
    
        if ($google_drive_apis->isEmpty()) {
            throw new \Exception('No Google Drive API keys available.');
        }
    
        // Retrieve the last used key (from cache)
        $lastUsedApiKey = Cache::get('last_used_api_key');
    
        // Exclude the last used one, if possible
        $availableApiKeys = $google_drive_apis->reject(function ($key) use ($lastUsedApiKey) {
            return $key === $lastUsedApiKey;
        });
    
        // If no alternate available (e.g. only one key), fallback to full list
        if ($availableApiKeys->isEmpty()) {
            $availableApiKeys = $google_drive_apis;
        }
    
        // Randomly select a new key
        $newApiKey = $availableApiKeys->random();
    
        // Cache the new key for next rotation (e.g., 10 minutes)
        Cache::put('last_used_api_key', $newApiKey, now()->addMinutes(10));
    
        return $newApiKey;
    }

  





 

  



    

 


    public function user_device_limit_reached()
    {
       $user_id=$_GET['user_id'];
       $plan_id=$_GET['plan_id'];

        if(user_device_limit_reached($user_id,$plan_id))
        {
            $response[] = array('msg' => trans('words.user_device_limit_reached'),'success'=>'1');
        }
        else
        {
            $response[] = array('msg' => trans('words.no_limit_reached'),'success'=>'0');
        }

        return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response,
            'status_code' => 200
             ));
    }

  



    public function get_genres()
    {
        $api_key = request()->header('X-API-KEY') ?: request()->input('api_key');
        $valid_api_key = 'sk_cineworm_2024_random_video_api_key_secure';

        try {
            // Get all active genres
            $genres = \App\Genres::where('status', 1)
                ->orderBy('genre_name', 'asc')
                ->get(['id', 'genre_name']);
            
            $response = [];
            foreach ($genres as $genre) {
                $response[] = [
                    'id' => $genre->id,
                    'name' => $genre->genre_name
                ];
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Genres fetched successfully',
                'data' => $response
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch genres: ' . $e->getMessage()
            ], 500);
        }
    }

    public function account_delete()
    {

        $get_data=checkSignSalt($_POST['data']);

        $user_id=$get_data['user_id'];

        //Change Status
        $user_obj = User::findOrFail($user_id);
        $user_obj->status=0;
        $user_obj->save();

        $user = User::findOrFail($user_id);

          //Delete session file
        $user_session_name=$get_data['user_session_name'];
        \Session::getHandler()->destroy($user_session_name);

        $user_device_obj = UsersDeviceHistory::where('user_id',$user_id)->where('user_session_name',$user_session_name);
        $user_device_obj->delete();

        $user->delete();

         //Account Delete Email
         if(getenv("MAIL_USERNAME"))
         {
             $user_name=$user->name;
             $user_email=$user->email;

             $data_email = array(
                 'name' => $user_name,
                 'email' => $user_email
                 );

             \Mail::send('emails.account_delete', $data_email, function($message) use ($user_name,$user_email){
                 $message->to($user_email, $user_name)
                 ->from(getcong('site_email'), getcong('site_name'))
                 ->subject(trans('words.user_dlt_email_subject'));
             });
         }

        $response_arr[] = array('msg' => trans('words.user_dlt_success'),'success'=>'1');

         return \Response::json(array(
            'VIDEO_STREAMING_APP' => $response_arr,
             'status_code' => 200
        ));

    }



  
 
    public function random_audios()
    {
        // Simple test response first
        return \Response::json(array(
            'AUDIO_STREAMING_APP' => array(
                'random_audio' => array(
                    "audio_id" => 1,
                    "title" => "Test Audio",
                    "description" => "Test Description",
                    "audio_url" => "https://stock.cineworm.org/storage/audio/test.mp3",
                    "duration" => "3:30",
                    "file_size" => "5MB",
                    "format" => "mp3",
                    "bitrate" => 320,
                    "sample_rate" => 44100,
                    "genre" => "Test",
                    "mood" => "Happy",
                    "tags" => "test,audio",
                    "license_price" => 0,
                    "downloads_count" => 0,
                    "views_count" => 0,
                    "is_premium" => "false"
                )
            ),
            'total_records' => 1,
            'returned_records' => 1,
            'randomization_info' => array(
                'audios_shown_in_cycle' => 1,
                'total_available_audios' => 1,
                'cycle_progress' => '100%'
            ),
            'status_code' => 200
        ));
    }

    public function random_photos()
    {
        // API Key validation for public access
        $api_key = request()->header('X-API-KEY') ?: request()->input('api_key');
        $valid_api_key = 'sk_cineworm_2024_random_video_api_key_secure';

        if (!$api_key || $api_key !== $valid_api_key) {
            return \Response::json(array(
                'error' => 'Invalid or missing API key',
                'message' => 'Please provide a valid API key in X-API-KEY header or api_key parameter',
                'status_code' => 401
            ), 401);
        }

        // Check if data parameter exists, if not use default values
        if (!isset($_POST['data']) || empty($_POST['data'])) {
            $get_data = array(); // Default empty array
        } else {
            $get_data = checkSignSalt($_POST['data']);
        }

        // Smart randomization - avoid repeating photos
        $total_records = Photos::where('status', 'active')->count();

        // Get or create session key for tracking shown photos
        $session_key = 'random_photos_shown_' . $api_key;
        $shown_photos = session($session_key, []);

        // If all photos have been shown, reset the tracking
        if (count($shown_photos) >= $total_records) {
            $shown_photos = [];
            session([$session_key => []]);
        }

        // Get a random photo that hasn't been shown yet
        $photo_data = Photos::where('status', 'active')
                            ->whereNotIn('id', $shown_photos)
                            ->inRandomOrder()
                            ->first();

        // If somehow no photo is found (edge case), get any random photo
        if (!$photo_data) {
            $photo_data = Photos::where('status', 'active')
                                ->inRandomOrder()
                                ->first();
            // Reset the session if this happens
            session([$session_key => []]);
            $shown_photos = [];
        }

        // Add current photo to shown list
        if ($photo_data) {
            $shown_photos[] = $photo_data->id;
            session([$session_key => $shown_photos]);
        }

        $response = array();

        if($photo_data)
        {
            $response['random_photo'] = array(
                "photo_id" => $photo_data->id,
                "title" => $photo_data->title,
                "description" => $photo_data->description,
                "image_url" => $photo_data->image_url,
                "image_name" => $photo_data->image_name,
                "category" => $photo_data->category,
                "tags" => $photo_data->tags,
                "keywords" => $photo_data->keywords,
                "width" => $photo_data->width,
                "height" => $photo_data->height,
                "resolution" => $photo_data->resolution,
                "file_size" => $photo_data->file_size,
                "formatted_file_size" => $photo_data->formatted_file_size,
                "dimensions" => $photo_data->dimensions,
                "file_type" => $photo_data->file_type,
                "mime_type" => $photo_data->mime_type,
                "license_price" => $photo_data->license_price,
                "download_count" => $photo_data->download_count,
                "view_count" => $photo_data->view_count,
                "is_premium" => $photo_data->license_price > 0 ? 'true' : 'false',
                "camera_info" => array(
                    "camera_make" => $photo_data->camera_make,
                    "camera_model" => $photo_data->camera_model,
                    "lens_model" => $photo_data->lens_model,
                    "focal_length" => $photo_data->focal_length,
                    "aperture" => $photo_data->aperture,
                    "shutter_speed" => $photo_data->shutter_speed,
                    "iso" => $photo_data->iso,
                    "date_taken" => $photo_data->date_taken
                )
            );
        }

        return \Response::json(array(
            'PHOTO_STREAMING_APP' => $response,
            'total_records' => $total_records,
            'returned_records' => $photo_data ? 1 : 0,
            'randomization_info' => array(
                'photos_shown_in_cycle' => count($shown_photos),
                'total_available_photos' => $total_records,
                'cycle_progress' => round((count($shown_photos) / max($total_records, 1)) * 100, 2) . '%'
            ),
            'status_code' => 200
        ));
    }

    public function all_content()
    {
        // API Key validation for public access
        $api_key = request()->header('X-API-KEY') ?: request()->input('api_key');
        $valid_api_key = 'sk_cineworm_2024_random_video_api_key_secure';

        if (!$api_key || $api_key !== $valid_api_key) {
            return \Response::json(array(
                'error' => 'Invalid or missing API key',
                'message' => 'Please provide a valid API key in X-API-KEY header or api_key parameter',
                'status_code' => 401
            ), 401);
        }

        // Check if data parameter exists, if not use default values
        if (!isset($_POST['data']) || empty($_POST['data'])) {
            $get_data = array(); // Default empty array
        } else {
            $get_data = checkSignSalt($_POST['data']);
        }

        $response = array();

        // Get random video
        $movie_data = Movies::where('status', 1)
                            ->where('upcoming', 0)
                            ->inRandomOrder()
                            ->first();

        if($movie_data) {
            $movie_id = $movie_data->id;
            $movie_title = stripslashes($movie_data->video_title);
            $movie_poster = URL::to('/'.$movie_data->video_image_thumb);
            $movie_duration = $movie_data->duration;
            $movie_access = $movie_data->video_access;
            $content_rating = $movie_data->content_rating ? $movie_data->content_rating : '';
            $video_description = stripslashes($movie_data->video_description);
            $release_date = $movie_data->release_date;
            $imdb_rating = $movie_data->imdb_rating;
            $views = $movie_data->views;
            $video_url = $movie_data->video_url ? $movie_data->video_url : '';
            $video_image = $movie_data->video_image ? URL::to('/'.$movie_data->video_image) : '';
            $video_slug = $movie_data->video_slug ? $movie_data->video_slug : '';

            // Get genre information
            $genres = array();
            if($movie_data->movie_genre_id) {
                foreach(explode(',', $movie_data->movie_genre_id) as $genre_id) {
                    $genre_name = Genres::getGenresInfo($genre_id, 'genre_name');
                    if($genre_name) {
                        $genres[] = array('genre_id' => $genre_id, 'genre_name' => $genre_name);
                    }
                }
            }

            // Get language information
            $language_name = '';
            if($movie_data->movie_lang_id) {
                $language_name = Language::getLanguageInfo($movie_data->movie_lang_id, 'language_name');
            }

            $response['random_video'] = array(
                "movie_id" => $movie_id,
                "movie_title" => $movie_title,
                "video_slug" => $video_slug,
                "movie_poster" => $movie_poster,
                "video_image" => $video_image,
                "movie_duration" => $movie_duration,
                "movie_access" => $movie_access,
                "content_rating" => $content_rating,
                "video_description" => $video_description,
                "release_date" => $release_date,
                "imdb_rating" => $imdb_rating,
                "views" => $views,
                "video_url" => $video_url,
                "genres" => $genres,
                "language_name" => $language_name
            );
        }

        // Get random audio
        $audio_data = Audio::where('is_active', true)
                            ->inRandomOrder()
                            ->first();

        if($audio_data) {
            $response['random_audio'] = array(
                "audio_id" => $audio_data->id,
                "title" => $audio_data->title,
                "description" => $audio_data->description,
                "audio_url" => 'https://stock.cineworm.org/storage/' . $audio_data->audio_path,
                "duration" => $audio_data->duration,
                "file_size" => $audio_data->file_size,
                "format" => $audio_data->format,
                "bitrate" => $audio_data->bitrate,
                "sample_rate" => $audio_data->sample_rate,
                "genre" => $audio_data->genre,
                "mood" => $audio_data->mood,
                "tags" => $audio_data->tags,
                "license_price" => $audio_data->license_price,
                "downloads_count" => $audio_data->downloads_count,
                "views_count" => $audio_data->views_count,
                "is_premium" => $audio_data->license_price > 0 ? 'true' : 'false'
            );
        }

        // Get random photo
        $photo_data = Photos::where('status', 'active')
                            ->inRandomOrder()
                            ->first();

        if($photo_data) {
            $response['random_photo'] = array(
                "photo_id" => $photo_data->id,
                "title" => $photo_data->title,
                "description" => $photo_data->description,
                "image_url" => $photo_data->image_url,
                "image_name" => $photo_data->image_name,
                "category" => $photo_data->category,
                "tags" => $photo_data->tags,
                "keywords" => $photo_data->keywords,
                "width" => $photo_data->width,
                "height" => $photo_data->height,
                "resolution" => $photo_data->resolution,
                "file_size" => $photo_data->file_size,
                "formatted_file_size" => $photo_data->formatted_file_size,
                "dimensions" => $photo_data->dimensions,
                "file_type" => $photo_data->file_type,
                "mime_type" => $photo_data->mime_type,
                "license_price" => $photo_data->license_price,
                "download_count" => $photo_data->download_count,
                "view_count" => $photo_data->view_count,
                "is_premium" => $photo_data->license_price > 0 ? 'true' : 'false'
            );
        }

        return \Response::json(array(
            'ALL_CONTENT_APP' => $response,
            'content_summary' => array(
                'videos_available' => Movies::where('status', 1)->where('upcoming', 0)->count(),
                'audios_available' => Audio::where('is_active', true)->count(),
                'photos_available' => Photos::where('status', 'active')->count()
            ),
            'status_code' => 200
        ));
    }

    public function videos_list()
    {
        // API Key validation for public access
        $api_key = request()->header('X-API-KEY') ?: request()->input('api_key');
        $valid_api_key = 'sk_cineworm_2024_random_video_api_key_secure';

        if (!$api_key || $api_key !== $valid_api_key) {
            return \Response::json(array(
                'error' => 'Invalid or missing API key',
                'message' => 'Please provide a valid API key in X-API-KEY header or api_key parameter',
                'status_code' => 401
            ), 401);
        }

        // Check if data parameter exists, if not use default values
        if (!isset($_POST['data']) || empty($_POST['data'])) {
            $get_data = array(); // Default empty array
        } else {
            $get_data = checkSignSalt($_POST['data']);
        }

        // Get pagination parameters
        $page = isset($get_data['page']) ? (int)$get_data['page'] : 1;
        $per_page = isset($get_data['per_page']) ? (int)$get_data['per_page'] : 10;
        $offset = ($page - 1) * $per_page;

        // Get videos with pagination
        $videos = Movies::where('status', 1)
                        ->where('upcoming', 0)
                        ->orderBy('id', 'desc')
                        ->offset($offset)
                        ->limit($per_page)
                        ->get();

        $total_videos = Movies::where('status', 1)->where('upcoming', 0)->count();
        $total_pages = ceil($total_videos / $per_page);

        $response = array();
        foreach($videos as $movie_data) {
            $movie_id = $movie_data->id;
            $movie_title = stripslashes($movie_data->video_title);
            $movie_poster = URL::to('/'.$movie_data->video_image_thumb);
            $movie_duration = $movie_data->duration;
            $movie_access = $movie_data->video_access;
            $content_rating = $movie_data->content_rating ? $movie_data->content_rating : '';
            $video_description = stripslashes($movie_data->video_description);
            $release_date = $movie_data->release_date;
            $imdb_rating = $movie_data->imdb_rating;
            $views = $movie_data->views;
            $video_url = $movie_data->video_url ? $movie_data->video_url : '';
            $video_image = $movie_data->video_image ? URL::to('/'.$movie_data->video_image) : '';
            $video_slug = $movie_data->video_slug ? $movie_data->video_slug : '';

            // Get genre information
            $genres = array();
            if($movie_data->movie_genre_id) {
                foreach(explode(',', $movie_data->movie_genre_id) as $genre_id) {
                    $genre_name = Genres::getGenresInfo($genre_id, 'genre_name');
                    if($genre_name) {
                        $genres[] = array('genre_id' => $genre_id, 'genre_name' => $genre_name);
                    }
                }
            }

            // Get language information
            $language_name = '';
            if($movie_data->movie_lang_id) {
                $language_name = Language::getLanguageInfo($movie_data->movie_lang_id, 'language_name');
            }

            $response[] = array(
                "movie_id" => $movie_id,
                "movie_title" => $movie_title,
                "video_slug" => $video_slug,
                "movie_poster" => $movie_poster,
                "video_image" => $video_image,
                "movie_duration" => $movie_duration,
                "movie_access" => $movie_access,
                "content_rating" => $content_rating,
                "video_description" => $video_description,
                "release_date" => $release_date,
                "imdb_rating" => $imdb_rating,
                "views" => $views,
                "video_url" => $video_url,
                "genres" => $genres,
                "language_name" => $language_name,
                "license_price" => $movie_data->license_price ? $movie_data->license_price : null,
                "is_premium" => $movie_data->license_price && $movie_data->license_price > 0 ? true : false
            );
        }

        return \Response::json(array(
            'VIDEOS_LIST' => $response,
            'pagination' => array(
                'current_page' => $page,
                'per_page' => $per_page,
                'total_videos' => $total_videos,
                'total_pages' => $total_pages,
                'has_next_page' => $page < $total_pages,
                'has_prev_page' => $page > 1
            ),
            'status_code' => 200
        ));
    }

    public function audios_list()
    {
        // API Key validation for public access
        $api_key = request()->header('X-API-KEY') ?: request()->input('api_key');
        $valid_api_key = 'sk_cineworm_2024_random_video_api_key_secure';

        if (!$api_key || $api_key !== $valid_api_key) {
            return \Response::json(array(
                'error' => 'Invalid or missing API key',
                'message' => 'Please provide a valid API key in X-API-KEY header or api_key parameter',
                'status_code' => 401
            ), 401);
        }

        // Check if data parameter exists, if not use default values
        if (!isset($_POST['data']) || empty($_POST['data'])) {
            $get_data = array(); // Default empty array
        } else {
            $get_data = checkSignSalt($_POST['data']);
        }

        // Get pagination parameters
        $page = isset($get_data['page']) ? (int)$get_data['page'] : 1;
        $per_page = isset($get_data['per_page']) ? (int)$get_data['per_page'] : 10;
        $offset = ($page - 1) * $per_page;

        // Get audios with pagination
        $audios = Audio::where('is_active', true)
                       ->orderBy('id', 'desc')
                       ->offset($offset)
                       ->limit($per_page)
                       ->get();

        $total_audios = Audio::where('is_active', true)->count();
        $total_pages = ceil($total_audios / $per_page);

        $response = array();
        foreach($audios as $audio_data) {
            $response[] = array(
                "audio_id" => $audio_data->id,
                "title" => $audio_data->title,
                "description" => $audio_data->description,
                "audio_url" => 'https://stock.cineworm.org/storage/' . $audio_data->audio_path,
                "duration" => $audio_data->duration,
                "file_size" => $audio_data->file_size,
                "format" => $audio_data->format,
                "bitrate" => $audio_data->bitrate,
                "sample_rate" => $audio_data->sample_rate,
                "genre" => $audio_data->genre,
                "mood" => $audio_data->mood,
                "tags" => $audio_data->tags,
                "license_price" => $audio_data->license_price,
                "downloads_count" => $audio_data->downloads_count,
                "views_count" => $audio_data->views_count,
                "is_premium" => $audio_data->license_price > 0 ? 'true' : 'false'
            );
        }

        return \Response::json(array(
            'AUDIOS_LIST' => $response,
            'pagination' => array(
                'current_page' => $page,
                'per_page' => $per_page,
                'total_audios' => $total_audios,
                'total_pages' => $total_pages,
                'has_next_page' => $page < $total_pages,
                'has_prev_page' => $page > 1
            ),
            'status_code' => 200
        ));
    }

    public function photos_list()
    {
        // API Key validation for public access
        $api_key = request()->header('X-API-KEY') ?: request()->input('api_key');
        $valid_api_key = 'sk_cineworm_2024_random_video_api_key_secure';

        if (!$api_key || $api_key !== $valid_api_key) {
            return \Response::json(array(
                'error' => 'Invalid or missing API key',
                'message' => 'Please provide a valid API key in X-API-KEY header or api_key parameter',
                'status_code' => 401
            ), 401);
        }

        // Check if data parameter exists, if not use default values
        if (!isset($_POST['data']) || empty($_POST['data'])) {
            $get_data = array(); // Default empty array
        } else {
            $get_data = checkSignSalt($_POST['data']);
        }

        // Get pagination parameters
        $page = isset($get_data['page']) ? (int)$get_data['page'] : 1;
        $per_page = isset($get_data['per_page']) ? (int)$get_data['per_page'] : 10;
        $offset = ($page - 1) * $per_page;

        // Get photos with pagination
        $photos = Photos::where('status', 'active')
                        ->orderBy('id', 'desc')
                        ->offset($offset)
                        ->limit($per_page)
                        ->get();

        $total_photos = Photos::where('status', 'active')->count();
        $total_pages = ceil($total_photos / $per_page);

        $response = array();
        foreach($photos as $photo_data) {
            $response[] = array(
                "photo_id" => $photo_data->id,
                "title" => $photo_data->title,
                "description" => $photo_data->description,
                "image_url" => $photo_data->image_url,
                "image_name" => $photo_data->image_name,
                "category" => $photo_data->category,
                "tags" => $photo_data->tags,
                "keywords" => $photo_data->keywords,
                "width" => $photo_data->width,
                "height" => $photo_data->height,
                "resolution" => $photo_data->resolution,
                "file_size" => $photo_data->file_size,
                "formatted_file_size" => $photo_data->formatted_file_size,
                "dimensions" => $photo_data->dimensions,
                "file_type" => $photo_data->file_type,
                "mime_type" => $photo_data->mime_type,
                "license_price" => $photo_data->license_price,
                "download_count" => $photo_data->download_count,
                "view_count" => $photo_data->view_count,
                "is_premium" => $photo_data->license_price > 0 ? 'true' : 'false'
            );
        }

        return \Response::json(array(
            'PHOTOS_LIST' => $response,
            'pagination' => array(
                'current_page' => $page,
                'per_page' => $per_page,
                'total_photos' => $total_photos,
                'total_pages' => $total_pages,
                'has_next_page' => $page < $total_pages,
                'has_prev_page' => $page > 1
            ),
            'status_code' => 200
        ));
    }


}
