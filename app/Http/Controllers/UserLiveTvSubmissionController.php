<?php

namespace App\Http\Controllers;

use App\LiveTVSubmission;
use App\TvCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserLiveTvSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $submissions = LiveTVSubmission::where('user_id', Auth::id())
            ->with('liveTv')
            ->latest()
            ->get();

        return view('pages.user.live_tv_submissions.index', compact('submissions'));
    }

    public function create()
    {
        $cat_list = TvCategory::where('status', '1')->orderBy('category_name')->get();

        return view('pages.user.live_tv_submissions.create', compact('cat_list'));
    }

    public function store(Request $request)
    {
        if ($request->input('channel_url_type') === 'Embed' && !$request->filled('channel_url')) {
            $request->merge(['channel_url' => $request->input('channel_url_embed')]);
        }

        $request->validate([
            'channel_name' => 'required|string|max:255',
            'channel_description' => 'nullable|string|max:5000',
            'channel_access' => 'required|in:Free,Paid',
            'tv_category' => 'required|integer|exists:channel_category,id',
            'channel_url_type' => 'required|in:HLS,Embed',
            'channel_url' => 'required|string|max:500',
            'channel_url2' => 'nullable|string|max:500',
            'channel_url3' => 'nullable|string|max:500',
            'channel_thumb' => 'required|string|max:500',
        ]);

        $urlType = $request->input('channel_url_type');
        $streamUrl = trim($request->input('channel_url'));

        if ($urlType === 'Embed') {
            $streamUrl = trim($request->input('channel_url_embed', $streamUrl));
        } elseif ($urlType === 'youtube') {
            $streamUrl = trim($request->input('channel_url_youtube', $streamUrl));
        } elseif ($urlType === 'dash') {
            $streamUrl = trim($request->input('channel_url_dash', $streamUrl));
        }

        LiveTVSubmission::create([
            'user_id' => Auth::id(),
            'channel_name' => trim($request->input('channel_name')),
            'channel_description' => $request->input('channel_description'),
            'channel_access' => $request->input('channel_access'),
            'channel_cat_id' => $request->input('tv_category'),
            'channel_url_type' => $urlType,
            'channel_url' => $streamUrl,
            'channel_url2' => $request->input('channel_url2'),
            'channel_url3' => $request->input('channel_url3'),
            'channel_thumb' => trim($request->input('channel_thumb')),
            'status' => 'pending',
        ]);

        Session::flash('flash_message', 'Live TV submitted successfully. It will be published after admin approval.');

        return redirect()->route('user.live-tv.index');
    }
}
