<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audio;
use App\Models\Effect;
use App\Models\FilmStockDriveFile;
use App\Models\Photos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserSubmissionsAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* ==========================================================
     *  1. AUDIO BY USER
     * ========================================================== */

    public function audioSubmissions(Request $request)
    {
        $query = Audio::whereNotNull('added_by')->with('user');

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('s') && !empty($request->s)) {
            $search = trim($request->s);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('genre', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $audios = $query->orderBy('id', 'DESC')->paginate(20)->appends($request->query());
        $pendingCount = Audio::whereNotNull('added_by')->where('status', 'pending')->count();
        $approvedCount = Audio::whereNotNull('added_by')->where('status', 'active')->count();
        $page_title = 'User Submitted Audio Tracks';

        return view('admin.audio.user_submissions', compact('audios', 'pendingCount', 'approvedCount', 'page_title'));
    }

    public function approveAudio($id)
    {
        $audio = Audio::findOrFail($id);
        $audio->status = 'active';
        $audio->is_active = true;
        $audio->save();

        Session::flash('flash_message', "Audio track '{$audio->title}' approved successfully!");
        return redirect()->back();
    }

    public function rejectAudio($id)
    {
        $audio = Audio::findOrFail($id);
        $audio->status = 'rejected';
        $audio->is_active = false;
        $audio->save();

        Session::flash('flash_message', "Audio track '{$audio->title}' marked as rejected.");
        return redirect()->back();
    }

    /* ==========================================================
     *  2. FILM STOCK BY USER
     * ========================================================== */

    public function filmStockSubmissions(Request $request)
    {
        $query = FilmStockDriveFile::where(function ($q) {
            $q->whereNotNull('added_by')->orWhere('folder_id', 'user_submission');
        })->with('user');

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('s') && !empty($request->s)) {
            $search = trim($request->s);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('file_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $files = $query->orderBy('id', 'DESC')->paginate(20)->appends($request->query());
        $pendingCount = FilmStockDriveFile::where(function ($q) {
            $q->whereNotNull('added_by')->orWhere('folder_id', 'user_submission');
        })->where('status', 'pending')->count();
        $approvedCount = FilmStockDriveFile::where(function ($q) {
            $q->whereNotNull('added_by')->orWhere('folder_id', 'user_submission');
        })->where('status', '!=', 'pending')->where('status', '!=', 'blocked')->count();
        $page_title = 'User Submitted Film Stock';

        return view('admin.film_stock_drive_files.user_submissions', compact('files', 'pendingCount', 'approvedCount', 'page_title'));
    }

    public function approveFilmStock($id)
    {
        $filmStock = FilmStockDriveFile::findOrFail($id);
        $filmStock->status = 'imported';
        $filmStock->save();

        Session::flash('flash_message', "Film Stock video '{$filmStock->name}' approved successfully!");
        return redirect()->back();
    }

    public function rejectFilmStock($id)
    {
        $filmStock = FilmStockDriveFile::findOrFail($id);
        $filmStock->status = 'blocked';
        $filmStock->save();

        Session::flash('flash_message', "Film Stock video '{$filmStock->name}' rejected.");
        return redirect()->back();
    }

    /* ==========================================================
     *  3. EFFECTS BY USER
     * ========================================================== */

    public function effectsSubmissions(Request $request)
    {
        $query = Effect::whereNotNull('added_by')->with('user');

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('s') && !empty($request->s)) {
            $search = trim($request->s);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $effects = $query->orderBy('id', 'DESC')->paginate(20)->appends($request->query());
        $pendingCount = Effect::whereNotNull('added_by')->where('status', 'pending')->count();
        $approvedCount = Effect::whereNotNull('added_by')->where('status', 'active')->count();
        $page_title = 'User Submitted Video Effects';

        return view('admin.effects.user_submissions', compact('effects', 'pendingCount', 'approvedCount', 'page_title'));
    }

    public function approveEffect($id)
    {
        $effect = Effect::findOrFail($id);
        $effect->status = 'active';
        $effect->is_active = true;
        $effect->save();

        Session::flash('flash_message', "Effect '{$effect->title}' approved successfully!");
        return redirect()->back();
    }

    public function rejectEffect($id)
    {
        $effect = Effect::findOrFail($id);
        $effect->status = 'rejected';
        $effect->is_active = false;
        $effect->save();

        Session::flash('flash_message', "Effect '{$effect->title}' marked as rejected.");
        return redirect()->back();
    }

    /* ==========================================================
     *  4. PHOTOS BY USER
     * ========================================================== */

    public function photosSubmissions(Request $request)
    {
        $query = Photos::whereNotNull('added_by')->with('user');

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('s') && !empty($request->s)) {
            $search = trim($request->s);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $photos = $query->orderBy('id', 'DESC')->paginate(20)->appends($request->query());
        $pendingCount = Photos::whereNotNull('added_by')->where('status', 'pending')->count();
        $approvedCount = Photos::whereNotNull('added_by')->where('status', 'active')->count();
        $page_title = 'User Submitted Photos';

        return view('admin.photos.user_submissions', compact('photos', 'pendingCount', 'approvedCount', 'page_title'));
    }

    public function approvePhoto($id)
    {
        $photo = Photos::findOrFail($id);
        $photo->status = 'active';
        $photo->save();

        Session::flash('flash_message', "Photo '{$photo->title}' approved successfully!");
        return redirect()->back();
    }

    public function rejectPhoto($id)
    {
        $photo = Photos::findOrFail($id);
        $photo->status = 'rejected';
        $photo->save();

        Session::flash('flash_message', "Photo '{$photo->title}' marked as rejected.");
        return redirect()->back();
    }
}
