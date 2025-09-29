<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Audio;
use App\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AudioController extends Controller
{
    /**
     * Display a listing of audios for frontend
     */
    public function index()
    {
        if(Auth::check())
        {
            if(Auth::user()->usertype!="Admin" AND Auth::user()->usertype!="Sub_Admin")
           {
              if(user_device_limit_reached(Auth::user()->id,Auth::user()->plan_id))
              {
                  return redirect('dashboard');
              }
           }
        }

        $slider = Slider::where('status',1)->whereRaw("find_in_set('Audio',slider_display_on)")->orderby('id','DESC')->get();

        $pagination_limit = 12;

        if(isset($_GET['genre']))
        {
            $audio_genre = $_GET['genre'];
            $audios_list = Audio::where('is_active', true)
                ->where('genre', $audio_genre)
                ->orderBy('created_at','DESC')
                ->paginate($pagination_limit);
            $audios_list->appends(request()->only('genre'))->links();
        }
        else if(isset($_GET['filter']))
        {
            $keyword = $_GET['filter'];

            if($keyword=='old')
            {
                $audios_list = Audio::where('is_active', true)->orderBy('created_at','ASC')->paginate($pagination_limit);
                $audios_list->appends(request()->only('filter'))->links();
            }
            else if($keyword=='alpha')
            {
                $audios_list = Audio::where('is_active', true)->orderBy('title','ASC')->paginate($pagination_limit);
                $audios_list->appends(request()->only('filter'))->links();
            }
            else if($keyword=='rand')
            {
                $audios_list = Audio::where('is_active', true)->inRandomOrder()->paginate($pagination_limit);
                $audios_list->appends(request()->only('filter'))->links();
            }
            else
            {
                $audios_list = Audio::where('is_active', true)->orderBy('created_at','DESC')->paginate($pagination_limit);
                $audios_list->appends(request()->only('filter'))->links();
            }
        }
        else
        {
            $audios_list = Audio::where('is_active', true)->orderBy('created_at','DESC')->paginate($pagination_limit);
        }

        $genres = Audio::where('is_active', true)->distinct()->pluck('genre')->filter();

        return view('pages.sports.list', compact('slider', 'audios_list', 'genres'));
    }

    /**
     * Display the specified audio
     */
    public function show($id)
    {
        if(Auth::check())
        {
            if(Auth::user()->usertype!="Admin" AND Auth::user()->usertype!="Sub_Admin")
           {
              if(user_device_limit_reached(Auth::user()->id,Auth::user()->plan_id))
              {
                  return redirect('dashboard');
              }
           }
        }

        $audio = Audio::where('is_active', true)->where('id',$id)->first();

        if($audio == '')
        {
            abort(404, 'Audio not found.');
        }

        // Increment view count
        $audio->increment('views_count');

        // Get related audios (same genre)
        $related_audios = Audio::where('is_active', true)
            ->where('genre', $audio->genre)
            ->where('id', '!=', $audio->id)
            ->take(6)
            ->get();

        return view('pages.sports.details', compact('audio', 'related_audios'));
    }

    /**
     * Download audio
     */
    public function download($id)
    {
        $audio = Audio::where('is_active', true)->where('id',$id)->first();

        if($audio == '')
        {
            abort(404, 'Audio not found.');
        }

        // Increment download count
        $audio->increment('downloads_count');

        // Check if audio is premium
        if($audio->license_price && $audio->license_price > 0)
        {
            // For premium audios, you might want to check user permissions
            // For now, we'll just redirect back with a message
            return redirect()->back()->with('error', 'This is a premium audio. Please contact us to purchase the license.');
        }

        $file_path = public_path('storage/' . $audio->audio_path);

        if(!file_exists($file_path))
        {
            abort(404, 'Audio file not found.');
        }

        return response()->download($file_path, $audio->title . '.' . $audio->format);
    }
}
