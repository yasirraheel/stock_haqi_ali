<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Effect;
use App\Models\FilmStockDriveFile;
use App\Models\PhotoCategory;
use App\Models\Photos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class UserSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all media submissions made by the logged-in user.
     */
    public function index()
    {
        $userId = Auth::id();

        $audios = Audio::where('added_by', $userId)->orderBy('id', 'DESC')->get();
        $effects = Effect::where('added_by', $userId)->orderBy('id', 'DESC')->get();
        $filmStocks = FilmStockDriveFile::where('added_by', $userId)->orderBy('id', 'DESC')->get();
        $photos = Photos::where('added_by', $userId)->orderBy('id', 'DESC')->get();

        $totalSubmissions = $audios->count() + $effects->count() + $filmStocks->count() + $photos->count();
        $pendingCount = $audios->where('status', 'pending')->count()
            + $effects->where('status', 'pending')->count()
            + $filmStocks->where('status', 'pending')->count()
            + $photos->where('status', 'pending')->count();
        $approvedCount = $totalSubmissions - $pendingCount;

        return view('pages.user.submissions.index', compact(
            'audios',
            'effects',
            'filmStocks',
            'photos',
            'totalSubmissions',
            'pendingCount',
            'approvedCount'
        ));
    }

    /**
     * Show the media submission form.
     */
    public function create()
    {
        $photoCategories = PhotoCategory::where('status', '1')->orderBy('category_name')->get();
        return view('pages.user.submissions.create', compact('photoCategories'));
    }

    /**
     * Store a single user-submitted Audio track.
     */
    public function storeAudio(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'drive_url' => 'required|string',
            'genre' => 'nullable|string|max:100',
            'license_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000'
        ]);

        $fileId = $this->extractSingleDriveFileId($request->input('drive_url'));
        if (!$fileId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['drive_url' => 'Invalid Google Drive link. Please provide a single file link (folder links are not allowed).']);
        }

        $audio = new Audio();
        $audio->title = trim($request->input('title'));
        $audio->description = $request->input('description');
        $audio->audio_path = "https://drive.google.com/uc?export=download&id={$fileId}";
        $audio->genre = $request->input('genre') ?: 'User Audio';
        $audio->format = 'mp3';
        $audio->license_price = $request->input('license_price') ?: 0.00;
        $audio->is_active = false;
        $audio->status = 'pending';
        $audio->added_by = Auth::id();
        $audio->save();

        Session::flash('flash_message', "Audio track '{$audio->title}' submitted successfully! It is currently pending admin approval.");
        return redirect()->route('user.submissions.index');
    }

    /**
     * Store a single user-submitted Effect.
     */
    public function storeEffect(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'drive_url' => 'required|string',
            'category' => 'nullable|string|max:100',
            'license_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000'
        ]);

        $fileId = $this->extractSingleDriveFileId($request->input('drive_url'));
        if (!$fileId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['drive_url' => 'Invalid Google Drive link. Please provide a single file link (folder links are not allowed).']);
        }

        $price = $request->input('license_price') ? (float)$request->input('license_price') : 0.00;

        $effect = new Effect();
        $effect->title = trim($request->input('title'));
        $effect->description = $request->input('description');
        $effect->effect_url = "https://drive.google.com/uc?export=download&id={$fileId}";
        $effect->category = $request->input('category') ?: 'General';
        $effect->license_price = $price;
        $effect->is_premium = ($price > 0);
        $effect->is_active = false;
        $effect->status = 'pending';
        $effect->added_by = Auth::id();
        $effect->save();

        Session::flash('flash_message', "Effect '{$effect->title}' submitted successfully! It is currently pending admin approval.");
        return redirect()->route('user.submissions.index');
    }

    /**
     * Store a single user-submitted Film Stock video.
     */
    public function storeFilmStock(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'drive_url' => 'required|string',
            'description' => 'nullable|string|max:1000'
        ]);

        $fileId = $this->extractSingleDriveFileId($request->input('drive_url'));
        if (!$fileId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['drive_url' => 'Invalid Google Drive link. Please provide a single file link (folder links are not allowed).']);
        }

        // Check if file ID already submitted
        $existing = FilmStockDriveFile::where('file_id', $fileId)->first();
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['drive_url' => 'This Google Drive video file has already been submitted or exists in the library.']);
        }

        $filmStock = new FilmStockDriveFile();
        $filmStock->folder_id = 'user_submission';
        $filmStock->file_id = $fileId;
        $filmStock->name = trim($request->input('title'));
        $filmStock->url = "https://drive.google.com/uc?export=download&id={$fileId}";
        $filmStock->mime_type = 'video/mp4';
        $filmStock->size = 0;
        $filmStock->status = 'pending';
        $filmStock->added_by = Auth::id();
        $filmStock->save();

        Session::flash('flash_message', "Film Stock '{$filmStock->name}' submitted successfully! It is currently pending admin approval.");
        return redirect()->route('user.submissions.index');
    }

    /**
     * Store a single user-submitted Photo.
     */
    public function storePhoto(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'license_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'drive_url' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240'
        ]);

        $driveUrl = trim($request->input('drive_url', ''));
        $imagePath = null;

        if (!empty($driveUrl)) {
            $fileId = $this->extractSingleDriveFileId($driveUrl);
            if (!$fileId) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['drive_url' => 'Invalid Google Drive link. Please provide a single file link (folder links are not allowed).']);
            }
            $imagePath = "https://drive.google.com/uc?export=download&id={$fileId}";
        } elseif ($request->hasFile('image_file')) {
            $image = $request->file('image_file');
            $imageName = 'user_' . Auth::id() . '_' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('upload/photos');
            if (!file_exists($destinationPath)) {
                @mkdir($destinationPath, 0755, true);
            }
            $image->move($destinationPath, $imageName);
            $imagePath = $imageName;
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors(['drive_url' => 'Please provide either a Google Drive image link or upload an image file.']);
        }

        $photo = new Photos();
        $photo->title = trim($request->input('title'));
        $photo->description = $request->input('description');
        $photo->image_path = $imagePath;
        $photo->category = $request->input('category') ?: 'General';
        $photo->license_price = $request->input('license_price') ?: 0.00;
        $photo->status = 'pending';
        $photo->added_by = Auth::id();
        $photo->save();

        Session::flash('flash_message', "Photo '{$photo->title}' submitted successfully! It is currently pending admin approval.");
        return redirect()->route('user.submissions.index');
    }

    /**
     * Extract a SINGLE Google Drive File ID from input string or URL.
     * Strictly blocks folder links/IDs.
     *
     * @param string $input
     * @return string|null
     */
    private function extractSingleDriveFileId($input)
    {
        $input = trim($input);
        if (empty($input)) {
            return null;
        }

        // Strictly reject folder URLs
        if (stripos($input, 'folders/') !== false || stripos($input, 'folder') !== false) {
            return null;
        }

        // Match /file/d/{fileId}
        if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $input, $matches)) {
            return $matches[1];
        }

        // Match id={fileId}
        if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $input, $matches)) {
            return $matches[1];
        }

        // Match /open?id={fileId} or /uc?id={fileId}
        if (preg_match('/\/uc\?.*id=([a-zA-Z0-9_-]+)/', $input, $matches)) {
            return $matches[1];
        }

        // Match direct 25+ char alphanumeric File ID (not folder)
        if (preg_match('/^[a-zA-Z0-9_-]{20,}$/', $input)) {
            return $input;
        }

        return null;
    }
}
