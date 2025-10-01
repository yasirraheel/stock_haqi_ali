<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\User;
use App\Movies;
use App\Genres;
use App\Language;
use App\RecentlyWatched;
use App\ActorDirector;

use App\Http\Requests;
use App\Models\GoogleDriveApi;
use App\Models\Thumbnail;
use App\Services\GeminiImageService;
use App\Services\GeminiTextService;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MoviesController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');

        parent::__construct();
        check_verify_purchase();
    }
    public function movies_list()
    {
        if (Auth::User()->usertype != "Admin" && Auth::User()->usertype != "Sub_Admin") {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = trans('words.movies_text');
        $language_list = Language::orderBy('language_name')->get();
        $genres_list = Genres::orderBy('genre_name')->get();

        $query = Movies::query();

        if (Auth::User()->usertype == "Sub_Admin") {
            $query->where('added_by', Auth::id());
        }

        if (isset($_GET['s'])) {
            $keyword = $_GET['s'];
            $query->where("video_title", "LIKE", "%$keyword%");
            $movies_list = $query->orderBy('video_title')->paginate(12);
            $movies_list->appends(request()->only('s'));
        } else if (isset($_GET['language_id'])) {
            $language_id = $_GET['language_id'];
            $query->where("movie_lang_id", "=", $language_id);
            $movies_list = $query->orderBy('id', 'DESC')->paginate(12);
            $movies_list->appends(request()->only('language_id'));
        } else if (isset($_GET['genres_id'])) {
            $genres_id = $_GET['genres_id'];
            $query->whereRaw("find_in_set('$genres_id',movie_genre_id)");
            $movies_list = $query->orderBy('id', 'DESC')->paginate(12);
            $movies_list->appends(request()->only('genres_id'));
        } else {
            $movies_list = $query->orderBy('id', 'DESC')->paginate(12);
        }

        return view('admin.pages.movies.list', compact('page_title', 'movies_list', 'language_list', 'genres_list'));
    }

    public function addMovie()
    {
        // Check if screen width indicates mobile device
        // $screenWidth = request()->input('screen_width');

        // if ($screenWidth && $screenWidth <= 768) { // Assuming 768px or less is mobile/tablet
        //     \Session::flash('flash_message', 'Access denied. For better experience, please use a desktop device.');

        //     return redirect()->back();
        // }

        // Check user type
        if (Auth::User()->usertype != "Admin" and Auth::User()->usertype != "Sub_Admin") {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = trans('words.add_movie');
        $language_list = Language::orderBy('language_name')->get();
        $genre_list = Genres::orderBy('genre_name')->get();
        $actor_list = ActorDirector::where('ad_type', 'actor')->orderBy('ad_name')->get();
        $director_list = ActorDirector::where('ad_type', 'director')->orderBy('ad_name')->get();

        return view('admin.pages.movies.addedit', compact('page_title', 'language_list', 'genre_list', 'actor_list', 'director_list'));
    }

    public function addnew(Request $request)
    {
        // Fetch all Google Drive API key from db
        $google_drive_api  = $this->getRandomApiKey();

        GoogleDriveApi::where('api_key', $google_drive_api)->increment('calls');

        $googleDriveUrl = $request->video_url;

        // Check if URL is already a Google Drive streaming URL
        if (strpos($googleDriveUrl, 'https://www.googleapis.com/drive/v3/files/') !== false) {
            $video_url = $googleDriveUrl;
            // Extract the file ID directly from the URL
            preg_match("/files\/(.*?)\?/", $googleDriveUrl, $matches);
            $fileId = $matches[1];
        } else {
            // Extract the file ID from the Google Drive URL
            preg_match("/\/d\/(.*?)\//", $googleDriveUrl, $matches);

            if (isset($matches[1])) {
                $fileId = $matches[1];
                // Construct the Google Drive streaming URL
                $video_url = "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&key={$google_drive_api}";
            } else {
                session()->flash('error', 'Invalid Google Drive URL');
                return back();
            }
        }

        $data = request()->except(['_token']);
        $inputs = $request->all();

        // Set validation rules
        $rule = [
            'genres' => 'required',
            'video_title' => 'required'
        ];

        $validator = Validator::make($data, $rule);

        // Redirect back with errors if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }
        // If updating an existing movie, retrieve it. Otherwise, create a new movie object.
        $movie_obj = !empty($inputs['id']) ? Movies::findOrFail($inputs['id']) : new Movies;

        // Store original values for comparison (for updates)
        $original_title = $movie_obj->video_title ?? '';
        $original_description = $movie_obj->video_description ?? '';
        $original_file_id = $movie_obj->file_id ?? '';

        session()->put('movie_obj', $movie_obj);

        $video_slug = Str::slug($inputs['video_title'], '-', null);

        // Fill in movie object data
        $movie_obj->funding_url = $inputs['funding_url'];
        $movie_obj->movie_lang_id = 0;
        $movie_obj->movie_genre_id = implode(',', $inputs['genres']);
        $movie_obj->video_title = addslashes($inputs['video_title']);
        $movie_obj->video_slug = $video_slug;
        $movie_obj->video_description = addslashes($inputs['video_description']);
        $movie_obj->actor_id = isset($inputs['actors_id']) ? implode(',', $inputs['actors_id']) : null;
        $movie_obj->director_id = isset($inputs['director_id']) ? implode(',', $inputs['director_id']) : null;

        // Handle poster link if provided
        if (isset($inputs['poster_link']) && $inputs['poster_link'] != '') {
            $image_source = $inputs['poster_link'];
            $save_to = public_path('/upload/images/' . $inputs['video_image']);
            grab_image($image_source, $save_to);
            $movie_obj->video_image = 'upload/images/' . $inputs['video_image'];
        }

        // Other fields
        $movie_obj->added_by = Auth::User()->id;
        $movie_obj->license_price = $inputs['license_price'];
        $movie_obj->file_id = $fileId;
        $movie_obj->webpage_url = $inputs['webpage_url'];
        $movie_obj->status = auth()->user()->usertype == 'Admin' ? $inputs['status'] : 0;
        $movie_obj->video_url = $video_url;
        $movie_obj->video_type = "URL";

        // Optional fields for video quality, downloads, and subtitles
        if (isset($inputs['video_quality'])) {
            $movie_obj->video_quality = $inputs['video_quality'];
        }

        if (isset($inputs['download_enable'])) {
            $movie_obj->download_enable = $inputs['download_enable'];
            $movie_obj->download_url = $inputs['download_url'];
        }

        if (isset($inputs['subtitle_on_off'])) {
            $movie_obj->subtitle_on_off = $inputs['subtitle_on_off'];
        }

        // Remove from recently watched if status is 0 (inactive)
        if (!empty($inputs['id']) && $inputs['status'] == 0) {
            DB::table("recently_watched")
                ->where("video_type", "=", "Movies")
                ->where("video_id", "=", $inputs['id'])
                ->delete();
        }

        // Save the movie object
        $movie_obj->save();

        // Determine if we need to generate a new screenshot
        $needsNewScreenshot = false;

        if (empty($inputs['id'])) {
            // New movie - always generate screenshot
            $needsNewScreenshot = true;
        } else {
            // Existing movie - check if key fields changed
            $titleChanged = $original_title !== $movie_obj->video_title;
            $descriptionChanged = $original_description !== $movie_obj->video_description;
            $fileIdChanged = $original_file_id !== $fileId;

            // Also check if no existing thumbnail exists
            $hasExistingThumbnail = !empty($movie_obj->video_image_thumb) && file_exists(public_path($movie_obj->video_image_thumb));

            $needsNewScreenshot = $titleChanged || $descriptionChanged || $fileIdChanged || !$hasExistingThumbnail;
        }

        // Generate screenshot only if needed
        if ($needsNewScreenshot) {
            $screenshotResult = $this->generateAIScreenshot($movie_obj->video_title, $movie_obj->video_description, $fileId);

            // Handle error if screenshot generation fails
            if (isset($screenshotResult['error'])) {
                return redirect()->back()->with('error', $screenshotResult['error']);
            }
        }
        // Flash success message and redirect back
        Session::flash('flash_message', !empty($inputs['id']) ? trans('words.successfully_updated') : trans('words.added'));
        return Redirect::back();
    }


    public function editMovie($movie_id)
    {

        if (Auth::User()->usertype != "Admin" and Auth::User()->usertype != "Sub_Admin") {

            Session::flash('flash_message', trans('words.access_denied'));

            return redirect('dashboard');
        }

        $page_title = trans('words.edit_movie');

        $language_list = Language::orderBy('language_name')->get();
        $genre_list = Genres::orderBy('genre_name')->get();

        $actor_list = ActorDirector::where('ad_type', 'actor')->orderBy('ad_name')->get();
        $director_list = ActorDirector::where('ad_type', 'director')->orderBy('ad_name')->get();

        $movie = Movies::findOrFail($movie_id);

        return view('admin.pages.movies.addedit', compact('page_title', 'movie', 'language_list', 'genre_list', 'actor_list', 'director_list'));
    }

    public function delete($movie_id)
    {
        if (Auth::User()->usertype == "Admin" or Auth::User()->usertype == "Sub_Admin") {

            $recently = RecentlyWatched::where('video_type', 'Movies')->where('video_id', $movie_id)->delete();

            $movie = Movies::findOrFail($movie_id);
            $movie->delete();

            Session::flash('flash_message', trans('words.deleted'));

            return redirect()->back();
        } else {
            Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
        }
    }
    /**
     * Generate AI-powered screenshot using Gemini AI
     *
     * @param string $videoTitle
     * @param string $videoDescription
     * @param string $fileId
     * @return array
     */
    public function generateAIScreenshot($videoTitle, $videoDescription, $fileId)
    {
        try {
            $geminiService = new GeminiImageService();

            Log::info("Starting AI screenshot generation for: {$videoTitle} (File ID: {$fileId})");

            // Try to generate image using Gemini AI
            $result = $geminiService->generateImage($videoTitle, $videoDescription, $fileId);

            // If Gemini fails, use fallback method
            if (isset($result['error'])) {
                Log::warning('Gemini AI failed, using fallback: ' . $result['error']);
                
                $fallbackPath = $geminiService->generateFallbackImage($videoTitle, $fileId);

                if (!$fallbackPath) {
                    Log::error('Both Gemini AI and fallback method failed');
                    return ['error' => 'Failed to generate image using both Gemini AI and fallback method'];
                }

                // Update result format for fallback
                $result = [
                    'success' => 'Fallback image generated successfully', 
                    'image_path' => $fallbackPath,
                    'is_fallback' => true
                ];
                
                Log::info("Fallback image generated: {$fallbackPath}");
            } else {
                Log::info("AI image generated successfully: " . $result['image_path']);
                $result['is_fallback'] = false;
            }

            // Verify the generated image actually exists
            $imagePath = $result['image_path'];
            $fullPath = public_path($imagePath);
            
            if (!file_exists($fullPath)) {
                Log::error("Generated image file does not exist: {$fullPath}");
                return ['error' => 'Generated image file not found'];
            }

            // Save or update the screenshot in the Thumbnail model
            Thumbnail::updateOrCreate(
                ['file_id' => $fileId],
                ['video_image_thumb' => $imagePath]
            );
            
            Log::info("Thumbnail record updated for file ID: {$fileId}");

            // Update the movie object with the generated image
            $movie_obj = session()->get('movie_obj');
            if ($movie_obj) {
                $movie_obj->video_image_thumb = $imagePath;
                $movie_obj->video_image = $imagePath;
                $movie_obj->save();
                session()->forget('movie_obj');
                
                Log::info("Movie object updated with new image path: {$imagePath}");
            } else {
                // If no session object, try to find and update the movie directly
                $movie = Movies::where('file_id', $fileId)->first();
                if ($movie) {
                    $movie->video_image_thumb = $imagePath;
                    $movie->video_image = $imagePath;
                    $movie->save();
                    
                    Log::info("Movie record updated directly with new image path: {$imagePath}");
                }
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('AI Screenshot Generation Error: ' . $e->getMessage());
            return ['error' => 'Error generating AI screenshot: ' . $e->getMessage()];
        }
    }

    /**
     * Manually regenerate screenshot for a specific movie
     *
     * @param int $movie_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function regenerateScreenshot($movie_id)
    {
        if (Auth::User()->usertype != "Admin" and Auth::User()->usertype != "Sub_Admin") {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        try {
            $movie = Movies::findOrFail($movie_id);

            // Generate new screenshot
            $screenshotResult = $this->generateAIScreenshot($movie->video_title, $movie->video_description, $movie->file_id);

            if (isset($screenshotResult['error'])) {
                Session::flash('flash_message', 'Error regenerating screenshot: ' . $screenshotResult['error']);
                return redirect()->back();
            }

            Session::flash('flash_message', 'Screenshot regenerated successfully!');
            return redirect()->back();

        } catch (\Exception $e) {
            Log::error('Screenshot regeneration error: ' . $e->getMessage());
            Session::flash('flash_message', 'Error regenerating screenshot: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Generate movie description using Gemini AI
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDescription(Request $request)
    {
        try {
            $movieTitle = $request->input('title', '');
            $genres = $request->input('genres', '');
            $actors = $request->input('actors', '');
            $directors = $request->input('directors', '');

            if (empty($movieTitle)) {
                return response()->json(['error' => 'Movie title is required'], 400);
            }

            $geminiService = new GeminiTextService();
            $result = $geminiService->generateMovieDescription($movieTitle, $genres, $actors, $directors);

            if (isset($result['error'])) {
                return response()->json(['error' => $result['error']], 500);
            }

            return response()->json([
                'success' => true,
                'description' => $result['description']
            ]);

        } catch (\Exception $e) {
            Log::error('Description Generation Error: ' . $e->getMessage());
            return response()->json(['error' => 'Error generating description: ' . $e->getMessage()], 500);
        }
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
