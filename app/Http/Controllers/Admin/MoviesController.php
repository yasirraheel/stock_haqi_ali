<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Movies;
use App\Genres;
use App\Language;
use App\RecentlyWatched;
use App\ActorDirector;
use App\Http\Requests;
use App\Models\GoogleDriveApi;
use App\Models\Thumbnail;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
            \Session::flash('flash_message', trans('words.access_denied'));
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
            $movies_list->appends(\Request::only('s'))->links();
        } else if (isset($_GET['language_id'])) {
            $language_id = $_GET['language_id'];
            $query->where("movie_lang_id", "=", $language_id);
            $movies_list = $query->orderBy('id', 'DESC')->paginate(12);
            $movies_list->appends(\Request::only('language_id'))->links();
        } else if (isset($_GET['genres_id'])) {
            $genres_id = $_GET['genres_id'];
            $query->whereRaw("find_in_set('$genres_id',movie_genre_id)");
            $movies_list = $query->orderBy('id', 'DESC')->paginate(12);
            $movies_list->appends(\Request::only('genres_id'))->links();
        } else {
            $movies_list = $query->orderBy('id', 'DESC')->paginate(12);
        }

        return view('admin.pages.movies.list', compact('page_title', 'movies_list', 'language_list', 'genres_list'));
    }

    public function addMovie()
    {
        if (Auth::User()->usertype != "Admin" and Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
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
        $google_drive_api  = $this->getRandomApiKey();
        GoogleDriveApi::where('api_key', $google_drive_api)->increment('calls');
        $googleDriveUrl = $request->video_url;

        if (strpos($googleDriveUrl, 'https://www.googleapis.com/drive/v3/files/') !== false) {
            $video_url = $googleDriveUrl;
            preg_match("/files\/(.*?)\?/", $googleDriveUrl, $matches);
            $fileId = $matches[1];
        } else {
            preg_match("/\/d\/(.*?)\//", $googleDriveUrl, $matches);
            if (isset($matches[1])) {
                $fileId = $matches[1];
                $video_url = "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&key={$google_drive_api}";
            } else {
                session()->flash('error', 'Invalid Google Drive URL');
                return back();
            }
        }

        $data = \Request::except(['_token']);
        $inputs = $request->all();

        $rule = [
            'genres' => 'required',
            'video_title' => 'required'
        ];
        $validator = \Validator::make($data, $rule);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        $movie_obj = !empty($inputs['id']) ? Movies::findOrFail($inputs['id']) : new Movies;
        session()->put('movie_obj', $movie_obj);

        $video_slug = Str::slug($inputs['video_title'], '-', null);

        $movie_obj->funding_url = $inputs['funding_url'];
        $movie_obj->movie_lang_id = 0;
        $movie_obj->movie_genre_id = implode(',', $inputs['genres']);
        $movie_obj->video_title = addslashes($inputs['video_title']);
        $movie_obj->video_slug = $video_slug;
        $movie_obj->video_description = addslashes($inputs['video_description']);

        $title = addslashes($inputs['video_title']);
        $description = addslashes($inputs['video_description']);

        $movie_obj->actor_id = isset($inputs['actors_id']) ? implode(',', $inputs['actors_id']) : null;
        $movie_obj->director_id = isset($inputs['director_id']) ? implode(',', $inputs['director_id']) : null;

        if (isset($inputs['poster_link']) && $inputs['poster_link'] != '') {
            $image_source = $inputs['poster_link'];
            $save_to = public_path('/upload/images/' . $inputs['video_image']);
            grab_image($image_source, $save_to);
            $movie_obj->video_image = 'upload/images/' . $inputs['video_image'];
        }

        $movie_obj->added_by = Auth::User()->id;
        $movie_obj->license_price = $inputs['license_price'];
        $movie_obj->file_id = $fileId;
        $movie_obj->webpage_url = $inputs['webpage_url'];
        $movie_obj->status = auth()->user()->usertype == 'Admin' ? $inputs['status'] : 0;
        $movie_obj->video_url = $video_url;
        $movie_obj->video_type = "URL";

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

        if (!empty($inputs['id']) && $inputs['status'] == 0) {
            DB::table("recently_watched")
                ->where("video_type", "=", "Movies")
                ->where("video_id", "=", $inputs['id'])
                ->delete();
        }

        $movie_obj->save();
        $screenshotResult = $this->store_generateScreenshot($fileId, $title, $description);

        if (isset($screenshotResult['error'])) {
            return redirect()->back()->with('error', $screenshotResult['error']);
        }

        \Session::flash('flash_message', !empty($inputs['id']) ? trans('words.successfully_updated') : trans('words.added'));
        return \Redirect::back();
    }

    public function editMovie($movie_id)
    {
        if (Auth::User()->usertype != "Admin" and Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
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
            \Session::flash('flash_message', trans('words.deleted'));
            return redirect()->back();
        } else {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }
    }

    /**
     * Generates a beautiful thumbnail with random colors, large text, and optional effects.
     *
     * @param string $fileId The unique ID for the file.
     * @param string $title The video title from the form.
     * @param string $description The video description from the form.
     * @return array Result of the operation.
     */
    public function store_generateScreenshot($fileId, $title, $description)
    {
        try {
            // Force a new seed for the random number generator to ensure unique colors
            mt_srand((float)microtime() * 1000000);

            // Step 1: Define storage paths
            $publicImagePath = public_path('screenshots/' . $fileId . '.jpg');
            $publicScreenshotsDir = public_path('screenshots');

            // Step 2: Ensure screenshot directory exists
            if (!file_exists($publicScreenshotsDir)) {
                mkdir($publicScreenshotsDir, 0777, true);
            }

            // Step 3: Delete existing file if present
            if (file_exists($publicImagePath)) {
                unlink($publicImagePath);
            }

            // Step 4: Canvas and Color Setup (improved)
            $width = 1280;
            $height = 720;

            // Generate a pleasing color palette: choose a base color and a slightly shifted second color
            $r1 = mt_rand(20, 235);
            $g1 = mt_rand(20, 235);
            $b1 = mt_rand(20, 235);
            $bgColor1 = "rgb($r1, $g1, $b1)";

            // create canvas
            $img = Image::canvas($width, $height, $bgColor1);

            // Add a subtle dark overlay in the center area to improve text contrast
            $overlay = Image::canvas($width, $height, 'rgba(0,0,0,0)');
            $overlayColor = 'rgba(0,0,0,0.18)';
            $overlay->rectangle(40, $height * 0.25, $width - 40, $height - ($height * 0.18), function ($draw) use ($overlayColor) {
                $draw->background($overlayColor);
            });
            $img->insert($overlay);

            // Determine contrasting text color based on the initial background luminance
            $luminance = (0.299 * $r1 + 0.587 * $g1 + 0.114 * $b1) / 255;
            $textColor = $luminance > 0.55 ? '#000000' : '#FFFFFF';

            // Step 5: Font and Text Setup
            $fontPathBold = public_path('fonts/Roboto-Bold.ttf');
            $fontPathRegular = public_path('fonts/Roboto-Regular.ttf');

            if (!file_exists($fontPathBold) || !file_exists($fontPathRegular)) {
                 return ['error' => 'Font files not found in public/fonts/. Please add Roboto-Bold.ttf and Roboto-Regular.ttf.'];
            }

            // Compute dynamic font sizes so long titles don't overflow
            $maxTitleWidth = $width - 160; // leave padding
            $maxTitleHeight = ($height * 0.5) - 40;
            $maxDescWidth = $width - 200;

            // Start with recommended sizes and downscale if needed
            $titleSize = 72;
            $descSize = 36;

            // Try to wrap with current sizes and measure total height
            $wrappedTitle = $this->wordwrapText($title, $titleSize, $fontPathBold, $maxTitleWidth);
            $titleLines = substr_count($wrappedTitle, "\n") + 1;
            $titleBBox = imagettfbbox($titleSize, 0, $fontPathBold, str_replace("\n", ' ', $wrappedTitle));
            $titleLineHeight = abs($titleBBox[1] - $titleBBox[7]) + 8;
            $titleHeight = $titleLines * $titleLineHeight;

            // If title height exceeds allowed, reduce font size proportionally
            if ($titleHeight > $maxTitleHeight) {
                $ratio = $maxTitleHeight / $titleHeight;
                $titleSize = max(32, floor($titleSize * $ratio));
                $wrappedTitle = $this->wordwrapText($title, $titleSize, $fontPathBold, $maxTitleWidth);
                $titleLines = substr_count($wrappedTitle, "\n") + 1;
                $titleBBox = imagettfbbox($titleSize, 0, $fontPathBold, str_replace("\n", ' ', $wrappedTitle));
                $titleLineHeight = abs($titleBBox[1] - $titleBBox[7]) + 6;
                $titleHeight = $titleLines * $titleLineHeight;
            }

            // Wrap description with computed desc size and limit to 3 lines
            $wrappedDesc = $this->wordwrapText($description, $descSize, $fontPathRegular, $maxDescWidth);
            $descLines = explode("\n", $wrappedDesc);
            if (count($descLines) > 3) {
                $descLines = array_slice($descLines, 0, 3);
                $wrappedDesc = implode("\n", $descLines) . '...';
            }

            // Vertical placement: place title a bit above center and description below
            $centerY = $height / 2;
            $titleY = $centerY - ($titleHeight / 2) - 20;
            $descY = $centerY + ($titleHeight / 2) + 10;

            // Draw subtle shadow by drawing the text slightly offset in semi-transparent black
            $shadowOffset = 2;

            // Draw Title shadow
            $img->text($wrappedTitle, $width / 2 + $shadowOffset, $titleY + $shadowOffset, function($font) use ($fontPathBold) {
                $font->file($fontPathBold);
                $font->size($titleSize);
                $font->color('rgba(0,0,0,0.45)');
                $font->align('center');
                $font->valign('top');
            });

            // Draw Title main
            $img->text($wrappedTitle, $width / 2, $titleY, function($font) use ($fontPathBold, $textColor, $titleSize) {
                $font->file($fontPathBold);
                $font->size($titleSize);
                $font->color($textColor);
                $font->align('center');
                $font->valign('top');
            });

            // Draw Description shadow
            $img->text($wrappedDesc, $width / 2 + $shadowOffset, $descY + $shadowOffset, function($font) use ($fontPathRegular, $descSize) {
                $font->file($fontPathRegular);
                $font->size($descSize);
                $font->color('rgba(0,0,0,0.35)');
                $font->align('center');
                $font->valign('top');
            });

            // Draw Description main
            $img->text($wrappedDesc, $width / 2, $descY, function($font) use ($fontPathRegular, $textColor, $descSize) {
                $font->file($fontPathRegular);
                $font->size($descSize);
                $font->color($textColor);
                $font->align('center');
                $font->valign('top');
            });

            // Step 6: Save the generated image
            $img->save($publicImagePath, 90, 'jpg');

            // Step 7: Update the database
            Thumbnail::updateOrCreate(
                ['file_id' => $fileId],
                ['video_image_thumb' => 'screenshots/' . $fileId . '.jpg']
            );

            // Step 8: Update the movie object from the session and then forget it
            $movies = session()->get('movie_obj');
            if ($movies) {
                $movies->video_image_thumb = 'screenshots/' . $fileId . '.jpg';
                $movies->video_image = 'screenshots/' . $fileId . '.jpg';
                $movies->save();
                session()->forget('movie_obj');
            }

            return ['success' => 'Screenshot generated successfully.'];

        } catch (\Exception $e) {
            return ['error' => 'Exception while generating image: ' . $e->getMessage()];
        }
    }

    public function extractAudio($id)
    {
        $movie = Movies::find($id);
        if (!$movie) {
            return response()->json(['error' => 'Movie not found'], 404);
        }
        $videoUrl = $movie->video_url;
        return $this->downloadVideoAndExtractAudio($videoUrl, $id);
    }

    public function downloadVideoAndExtractAudio($videoUrl, $id)
    {
        if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'Invalid URL format'], 400);
        }
        $videoDir = public_path('videos');
        if (!file_exists($videoDir)) {
            mkdir($videoDir, 0777, true);
        }
        $videoPath = $videoDir . '/temp_video.mp4';
        try {
            $videoContent = Http::get($videoUrl)->body();
            if (empty($videoContent)) {
                return response()->json(['error' => 'Failed to download video'], 500);
            }
            file_put_contents($videoPath, $videoContent);
            return $this->extractAudioFromVideo($videoPath, $id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while downloading the video: ' . $e->getMessage()], 500);
        }
    }

    public function extractAudioFromVideo($videoPath, $id)
    {
        $audioOutputPath = public_path('extracted_audios/') . $id . '.mp3';
        $audioDir = public_path('extracted_audios');
        if (!file_exists($audioDir)) {
            mkdir($audioDir, 0777, true);
        }
        $operatingSystem = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $ffmpegPath = $operatingSystem ? storage_path('ffmpeg_win/bin/ffmpeg.exe') : storage_path('ffmpeg_linux/ffmpeg');
        $command = "\"$ffmpegPath\" -i \"$videoPath\" -vn -acodec libmp3lame -ar 44100 -ac 2 -ab 192k -f mp3 \"$audioOutputPath\" 2>&1";
        \Log::debug("FFmpeg Command: " . $command);
        $descriptors = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];
        $process = proc_open($command, $descriptors, $pipes);
        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $errorOutput = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            \Log::debug("FFmpeg Output: " . $output);
            \Log::debug("FFmpeg Error: " . $errorOutput);
            $returnVar = proc_close($process);
            if ($returnVar === 0 && file_exists($audioOutputPath)) {
                unlink($videoPath);
                session()->flash('flash_message', 'Audio extracted successfully!');
                return redirect()->back();
            } else {
                session()->flash('error', 'Error in generating audio from Url.');
                return redirect()->back();
            }
        } else {
            return response()->json(['error' => 'Failed to start FFmpeg process.'], 500);
        }
    }

    public function getRandomApiKey()
    {
        $google_drive_apis = GoogleDriveApi::all();
        if ($google_drive_apis->isEmpty()) {
            session()->flash('error', 'No API keys available.');
        }
        $lastUsedApiKey = session()->get('last_used_api_key', null);
        $availableApiKeys = $google_drive_apis->filter(function ($api) use ($lastUsedApiKey) {
            return $api->api_key !== $lastUsedApiKey;
        });
        if ($availableApiKeys->isEmpty()) {
            session()->flash('error', 'Only one API key available, cannot alternate.');
        }
        $newApiKey = $availableApiKeys->random()->api_key;
        session()->put('last_used_api_key', $newApiKey);
        return $newApiKey;
    }

    /**
     * Helper function to wrap text based on a max width for a given font and size.
     *
     * @param string $text The text to wrap.
     * @param int $fontSize The font size.
     * @param string $fontFile The path to the TTF font file.
     * @param int $maxWidth The maximum width in pixels.
     * @return string The wrapped text with newlines.
     */
    private function wordwrapText($text, $fontSize, $fontFile, $maxWidth)
    {
        if (!function_exists('imagettfbbox')) {
            return wordwrap($text, 50);
        }

        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine . ($currentLine ? ' ' : '') . $word;
            $dimensions = imagettfbbox($fontSize, 0, $fontFile, $testLine);
            $lineWidth = $dimensions[2] - $dimensions[0];

            if ($lineWidth > $maxWidth && $currentLine !== '') {
                $lines[] = $currentLine;
                $currentLine = $word;
            } else {
                $currentLine = $testLine;
            }
        }
        $lines[] = $currentLine;

        return implode("\n", $lines);
    }
}
