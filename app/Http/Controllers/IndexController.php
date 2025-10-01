<?php

namespace App\Http\Controllers;

use App\Genres;
use Auth;
use App\User;
use App\Slider;
use App\Series;
use App\Movies;
use App\HomeSections;
use App\Sports;
use App\Pages;
use App\RecentlyWatched;
use App\LiveTV;
use App\UsersDeviceHistory;
use App\Models\Audio;
use App\Models\Photos;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;



use ProtoneMedia\LaravelFFMpeg;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Support\ServiceProvider;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;
use FFMpeg\Filters\AdvancedMedia\ComplexFilters;
use FFMpeg\Filters\Video\WatermarkFilter;

require(base_path() . '/public/device-detector/vendor/autoload.php');

use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);

class IndexController extends Controller
{


    public function index()
    {

        if (!$this->alreadyInstalled()) {
            return redirect('public/install');
        }
        $random_movie = Movies::where('status', 1)->inRandomOrder()->first();
        $slider = Slider::where('status', 1)->whereRaw("find_in_set('Home',slider_display_on)")->orderby('id', 'DESC')->get();

        if (Auth::check()) {
            $current_user_id = Auth::User()->id;

            if (getcong('menu_movies') == 0 and getcong('menu_shows') == 0) {
                $recently_watched = RecentlyWatched::where('user_id', $current_user_id)->where('video_type', '!=', 'Movies')->where('video_type', '!=', 'Episodes')->orderby('id', 'DESC')->get();
            } else if (getcong('menu_sports') == 0 and getcong('menu_livetv') == 0) {
                $recently_watched = RecentlyWatched::where('user_id', $current_user_id)->where('video_type', '!=', 'Sports')->where('video_type', '!=', 'LiveTV')->orderby('id', 'DESC')->get();
            } else if (getcong('menu_livetv') == 0) {
                $recently_watched = RecentlyWatched::where('user_id', $current_user_id)->where('video_type', '!=', 'LiveTV')->orderby('id', 'DESC')->get();
            } else if (getcong('menu_sports') == 0) {
                $recently_watched = RecentlyWatched::where('user_id', $current_user_id)->where('video_type', '!=', 'Sports')->orderby('id', 'DESC')->get();
            } else if (getcong('menu_movies') == 0) {
                $recently_watched = RecentlyWatched::where('user_id', $current_user_id)->where('video_type', '!=', 'Movies')->orderby('id', 'DESC')->get();
            } else if (getcong('menu_shows') == 0) {
                $recently_watched = RecentlyWatched::where('user_id', $current_user_id)->where('video_type', '!=', 'Episodes')->orderby('id', 'DESC')->get();
            } else {
                $recently_watched = RecentlyWatched::where('user_id', $current_user_id)->orderby('id', 'DESC')->get();
            }
        } else {
            $recently_watched = array();
        }

        $upcoming_movies = Movies::where('upcoming', 1)->orderby('id', 'DESC')->get();
        $upcoming_series = Series::where('upcoming', 1)->orderby('id', 'DESC')->get();

        //get only one rando// Fetch the previously selected video IDs from the session
        $previousVideos = Session::get('previous_videos', []);

        // Fetch a random movie that hasn't been selected before
        $movies_info = Movies::where('status', 1)
            ->where('upcoming', 0)
            ->whereNotIn('id', $previousVideos)
            ->inRandomOrder()
            ->first();



        // If no more new movies are available, reset the session
        if (!$movies_info) {
            Session::forget('previous_videos');
            $previousVideos = [];
            $movie = Movies::where('status', 1)
                ->where('upcoming', 0)
                ->inRandomOrder()
                ->first();
        }

        if ($movies_info) {
            $previousVideos[] = $movies_info->id;
            Session::put('previous_videos', $previousVideos);
        }
        // $movies_info = Movies::where('status', 1)->where('upcoming', 0)->inRandomOrder()->first();
        // dd($movies_info);
        if (Auth::check()) {
            try {
                // Fetch the like record if the user is authenticated
                $user_has_liked = Like::where('movie_video_id', $movies_info->id)
                    ->where('user_id', Auth::id())
                    ->first();
            } catch (\Exception $e) {
                // Log the error or handle it as needed
                \Log::error('Error fetching like status: ' . $e->getMessage());
                $user_has_liked = null; // Set to null in case of an error
            }
        } else {
            $user_has_liked = null; // User is not authenticated
        }



        $pagination_limit = 18;

        if (isset($_GET['lang_id'])) {
            $movie_lang_id = $_GET['lang_id'];

            $movies_list = Movies::where('status', 1)->where('upcoming', 0)->where('movie_lang_id', $movie_lang_id)->orderBy('id', 'DESC')->paginate($pagination_limit);
            $movies_list->appends(\Request::only('lang_id'))->links();
        } else if (isset($_GET['genre_id'])) {
            $movie_genre_id = $_GET['genre_id'];

            $movies_list = Movies::where('status', 1)->where('upcoming', 0)->whereRaw("find_in_set('$movie_genre_id',movie_genre_id)")->orderBy('id', 'DESC')->paginate($pagination_limit);
            $movies_list->appends(\Request::only('genre_id'))->links();
        } else if (isset($_GET['filter'])) {
            $keyword = $_GET['filter'];

            if ($keyword == 'old') {
                $movies_list = Movies::where('status', 1)->where('upcoming', 0)->orderBy('id', 'ASC')->paginate($pagination_limit);
                $movies_list->appends(\Request::only('filter'))->links();
            } else if ($keyword == 'alpha') {
                $movies_list = Movies::where('status', 1)->where('upcoming', 0)->orderBy('video_title', 'ASC')->paginate($pagination_limit);
                $movies_list->appends(\Request::only('filter'))->links();
            } else if ($keyword == 'rand') {
                $movies_list = Movies::where('status', 1)->where('upcoming', 0)->inRandomOrder()->paginate($pagination_limit);
                $movies_list->appends(\Request::only('filter'))->links();
            } else {
                $movies_list = Movies::where('status', 1)->where('upcoming', 0)->orderBy('id', 'DESC')->paginate($pagination_limit);
                $movies_list->appends(\Request::only('filter'))->links();
            }
        } else {
            $movies_list = Movies::where('status', 1)->where('upcoming', 0)->orderBy('id', 'DESC')->paginate($pagination_limit);
        }

        $genres = Genres::where('status', 1)->orderBy('id', 'ASC')->get();
        // dd($Genres);
        $home_sections = HomeSections::where('status', 1)->orderby('id')->get();
        // user_has_liked


        // Fetch audio data for home page
        $audio_list = Audio::where('is_active', true)
            ->orderBy('created_at', 'DESC')
            ->take(12) // Limit to 12 audios for home page
            ->get();

        // Fetch photos data for home page
        $photos_list = Photos::where('status', 'active')
            ->orderBy('created_at', 'DESC')
            ->take(12) // Limit to 12 photos for home page
            ->get();

        return view('pages.index', compact('movies_info', 'genres', 'slider', 'recently_watched', 'upcoming_movies', 'upcoming_series', 'home_sections', 'movies_list', 'pagination_limit', 'random_movie', 'user_has_liked', 'audio_list', 'photos_list'));
    }

    public function home_collections($slug, $id)
    {
        $home_section = HomeSections::where('id', $id)->where('status', 1)->first();

        //echo $home_section->post_type;exit;

        if ($home_section->post_type == "Movie") {
            return view('pages.home.movies', compact('home_section'));
        } else if ($home_section->post_type == "Shows") {
            return view('pages.home.shows', compact('home_section'));
        } else if ($home_section->post_type == "LiveTV") {
            return view('pages.home.livetv', compact('home_section'));
        } else if ($home_section->post_type == "Sports") {
            return view('pages.home.sports', compact('home_section'));
        } else {
            return view('pages.home_section', compact('home_section'));
        }
    }


    public function alreadyInstalled()
    {

        return file_exists(base_path('/public/.lic'));
    }

    public function search_elastic()
    {
        $keyword = $_GET['s'];

        if (getcong('menu_movies')) {
            $s_movies_list = Movies::where('status', 1)->where("video_title", "LIKE", "%$keyword%")->orderBy('video_title')->get();
        } else {
            $s_movies_list = array();
        }

        if (getcong('menu_shows')) {
            $s_series_list = Series::where('status', 1)->where("series_name", "LIKE", "%$keyword%")->orderBy('series_name')->get();
        } else {
            $s_series_list = array();
        }

        if (getcong('menu_sports')) {
            $s_sports_list = Sports::where('status', 1)->where("video_title", "LIKE", "%$keyword%")->orderBy('video_title')->get();
        } else {
            $s_sports_list = array();
        }

        if (getcong('menu_livetv')) {
            $live_tv_list = LiveTV::where('status', 1)->where("channel_name", "LIKE", "%$keyword%")->orderBy('channel_name')->get();
        } else {
            $live_tv_list = array();
        }


        return view('_particles.search_elastic', compact('s_movies_list', 's_series_list', 's_sports_list', 'live_tv_list'));
    }

    public function search()
    {
        $keyword = $_GET['s'];

        $movies_list = Movies::where('status', 1)->where('upcoming', 0)->where("video_title", "LIKE", "%$keyword%")->orderBy('video_title')->get();

        $series_list = Series::where('status', 1)->where('upcoming', 0)->where("series_name", "LIKE", "%$keyword%")->orderBy('series_name')->get();

        $sports_video_list = Sports::where('status', 1)->where("video_title", "LIKE", "%$keyword%")->orderBy('video_title')->get();

        $live_tv_list = LiveTV::where('status', 1)->where("channel_name", "LIKE", "%$keyword%")->orderBy('channel_name')->get();

        return view('pages.search', compact('movies_list', 'series_list', 'sports_video_list', 'live_tv_list'));
    }

    public function sitemap()
    {
        return response()->view('pages.sitemap')->header('Content-Type', 'text/xml');
    }

    public function sitemap_misc()
    {
        $pages_list = Pages::where('status', 1)->orderBy('id')->get();

        return response()->view('pages.sitemap_misc', compact('pages_list'))->header('Content-Type', 'text/xml');
    }


    public function sitemap_movies()
    {
        $movies_list = Movies::where('status', 1)->orderBy('id', 'DESC')->get();

        return response()->view('pages.sitemap_movies', compact('movies_list'))->header('Content-Type', 'text/xml');
    }

    public function sitemap_show()
    {
        $series_list = Series::where('status', 1)->orderBy('id', 'DESC')->get();

        return response()->view('pages.sitemap_show', compact('series_list'))->header('Content-Type', 'text/xml');
    }

    public function sitemap_sports()
    {
        $sports_video_list = Sports::where('status', 1)->orderBy('id', 'DESC')->get();

        return response()->view('pages.sitemap_sports', compact('sports_video_list'))->header('Content-Type', 'text/xml');
    }

    public function sitemap_livetv()
    {
        $live_list = LiveTV::where('status', 1)->orderBy('id', 'DESC')->get();

        return response()->view('pages.sitemap_livetv', compact('live_list'))->header('Content-Type', 'text/xml');
    }

    public function login()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                if ($user->usertype != 'Admin') {
                    $changeUserType = User::where('id', $user->id)->update(['usertype' => 'Sub_Admin']);
                    if ($changeUserType) {
                        return redirect('/');
                    }
                }
            }
        }
        return view('pages.user.login');
    }

    public function postLogin(Request $request)
    {


        $data =  \Request::except(array('_token'));
        $inputs = $request->all();

        if (getcong('recaptcha_on_login')) {
            $rule = array(
                'email' => 'required|email',
                'password' => 'required',
                'g-recaptcha-response' => 'required'
            );
        } else {
            $rule = array(
                'email' => 'required|email',
                'password' => 'required'
            );
        }

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            Session::flash('login_flash_error', 'required');
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        //check reCaptcha
        if (getcong('recaptcha_on_login')) {

            $recaptcha_response = $inputs['g-recaptcha-response'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'secret' => getcong('recaptcha_secret_key'),
                'response' => $recaptcha_response,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ]);

            $resp = json_decode(curl_exec($ch));
            curl_close($ch);

            //dd($resp);exit;

            if ($resp->success != true) {

                \Session::flash('error_flash_message', 'Captcha timeout or duplicate');
                return \Redirect::back();
            }
        }

        $credentials = $request->only('email', 'password');

        $remember_me = $request->has('remember') ? true : false;

        if (Auth::attempt($credentials, $remember_me)) {

            if (Auth::user()->status == '0' and Auth::user()->deleted_at != NULL) {
                \Auth::logout();

                Session::flash('login_flash_error', 'required');
                return redirect('/login')->withInput()->withErrors(trans('words.account_delete_msg'));
            }

            if (Auth::user()->status == '0') {
                \Auth::logout();
                Session::flash('login_flash_error', 'required');
                return redirect('/login')->withInput()->withErrors(trans('words.account_banned'));
            }

            return $this->handleUserWasAuthenticated($request);
        }

        Session::flash('login_flash_error', 'required');
        return redirect('/login')->withInput()->withErrors(trans('words.email_password_invalid'));
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $throttles
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request)
    {

        if (method_exists($this, 'authenticated')) {
            return $this->authenticated($request, Auth::user());
        }

        /*$previous_session = Auth::User()->session_id;
        if ($previous_session) {
            Session::getHandler()->destroy($previous_session);
        }

        Auth::user()->session_id = Session::getId();
        Auth::user()->save();
        */

        if (Auth::user()->usertype == 'Admin' or Auth::user()->usertype == 'Sub_Admin') {
            return redirect('/');
        } else {

            $user_id = Auth::user()->id;
            /***Save Device***/
            $userAgent = $_SERVER['HTTP_USER_AGENT']; // change this to the useragent you want to parse

            $dd = new DeviceDetector($userAgent);

            $dd->parse();

            if ($dd->isBot()) {
                // handle bots,spiders,crawlers,...
                $botInfo = $dd->getBot();
            } else {
                $clientInfo = $dd->getClient(); // holds information about browser, feed reader, media player, ...
                $osInfo = $dd->getOs();
                $device = $dd->getDeviceName();
                $brand = $dd->getBrandName();
                $model = $dd->getModel();


                if ($brand) {
                    $user_device_name = $brand . ' ' . $model . ' ' . $osInfo['platform'] . ' ' . $device;
                } else {
                    $user_device_name = $osInfo['name'] . $osInfo['version'] . ' ' . $osInfo['platform'] . ' ' . $device;
                }

                //Save History
                $user_device_obj = new UsersDeviceHistory;

                $user_device_obj->user_id = $user_id;
                $user_device_obj->user_device_name = $user_device_name;
                $user_device_obj->user_session_name = Session::getId();
                $user_device_obj->save();
            }

            /***Save Device End***/

            return redirect('dashboard');
        }
    }


    public function signup()
    {
        return view('pages.user.signup');
    }

    public function postSignup(Request $request)
    {


        $data =  \Request::except(array('_token'));

        $inputs = $request->all();

        if (getcong('recaptcha_on_signup')) {
            $rule = array(
                'name' => 'required',
                'email' => 'required|email|max:200|unique:users',
                'password' => 'required|confirmed|min:8',
                'password_confirmation' => 'required',
                'g-recaptcha-response' => 'required'
            );
        } else {
            $rule = array(
                'name' => 'required',
                'email' => 'required|email|max:200|unique:users',
                'password' => 'required|confirmed|min:8',
                'password_confirmation' => 'required'
            );
        }


        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            Session::flash('signup_flash_error', 'required');
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        //check reCaptcha
        if (getcong('recaptcha_on_signup')) {

            $recaptcha_response = $inputs['g-recaptcha-response'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'secret' => getcong('recaptcha_secret_key'),
                'response' => $recaptcha_response,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ]);

            $resp = json_decode(curl_exec($ch));
            curl_close($ch);

            //dd($resp);exit;

            if ($resp->success != true) {

                \Session::flash('error_flash_message', 'Captcha timeout or duplicate');
                return \Redirect::back();
            }
        }

        $user = new User;

        //$confirmation_code = str_random(30);


        $user->usertype = 'Sub_Admin';
        $user->name = $inputs['name'];
        $user->email = $inputs['email'];
        $user->password = bcrypt($inputs['password']);

        $user->save();

        //Welcome Email

        try {
            $user_name = $inputs['name'];
            $user_email = $inputs['email'];

            $data_email = array(
                'name' => $user_name,
                'email' => $user_email
            );

            \Mail::send('emails.welcome', $data_email, function ($message) use ($user_name, $user_email) {
                $message->to($user_email, $user_name)
                    ->from(getcong('site_email'), getcong('site_name'))
                    ->subject('Welcome to ' . getcong('site_name'));
            });
        } catch (\Throwable $e) {

            \Log::info($e->getMessage());
        }


        Session::flash('signup_flash_message', trans('words.account_created_successfully'));

        return redirect('signup');
    }


    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $user_id = Auth::user()->id;

        //Delete session file
        $user_session_name = Session::getId();
        \Session::getHandler()->destroy($user_session_name);

        $user_device_obj = UsersDeviceHistory::where('user_id', $user_id)->where('user_session_name', $user_session_name);
        $user_device_obj->delete();

        Auth::logout();

        return redirect('/');
    }

    public function logout_user_remotely($user_session_name)
    {
        $user_id = Auth::user()->id;

        $user_obj = User::findOrFail($user_id);

        //Push Notification on Mobile
        $content = array("en" => "Logout device remotely");

        $fields = array(
            'app_id' => getcong_app('onesignal_app_id'),
            'included_segments' => array('all'),
            'data' => array("foo" => "bar", "logout_remote" => "1"),
            'filters' => array(array('field' => 'tag', 'key' => 'user_session', 'relation' => '=', 'value' => $user_session_name)),
            'headings' => array("en" => getcong_app('app_name')),
            'contents' => $content
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic ' . getcong_app('onesignal_rest_key')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $notify_res = curl_exec($ch);

        curl_close($ch);

        //dd($notify_res);
        //exit;


        //Delete session file
        \Session::getHandler()->destroy($user_session_name);

        $user_device_obj = UsersDeviceHistory::where('user_id', $user_id)->where('user_session_name', $user_session_name);
        $user_device_obj->delete();


        \Session::flash('success', 'Logout device remotely successfully...');

        return redirect('/dashboard');
    }

    public function check_user_remotely_logout_or_not($user_session_name)
    {


        $user_device_obj = UsersDeviceHistory::where('user_session_name', $user_session_name)->first();
        //dd($user_device_obj);
        //exit;

        if ($user_device_obj) {
            echo "true";
        } else {
            echo "false";
        }
    }
}
