<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\Photos;
use App\Models\PhotoCategory;
use App\Slider;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PhotosController extends Controller
{
    /**
     * Display a listing of photos for frontend
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

        $slider = Slider::where('status',1)->whereRaw("find_in_set('Photos',slider_display_on)")->orderby('id','DESC')->get();

        $pagination_limit = 12;

        if(isset($_GET['category']))
        {
            $photo_category = $_GET['category'];
            $photos_list = Photos::where('status','active')
                ->where('category', $photo_category)
                ->orderBy('created_at','DESC')
                ->paginate($pagination_limit);
            $photos_list->appends(request()->only('category'))->links();
        }
        else if(isset($_GET['filter']))
        {
            $keyword = $_GET['filter'];

            if($keyword=='old')
            {
                $photos_list = Photos::where('status','active')->orderBy('created_at','ASC')->paginate($pagination_limit);
                $photos_list->appends(request()->only('filter'))->links();
            }
            else if($keyword=='alpha')
            {
                $photos_list = Photos::where('status','active')->orderBy('title','ASC')->paginate($pagination_limit);
                $photos_list->appends(request()->only('filter'))->links();
            }
            else if($keyword=='rand')
            {
                $photos_list = Photos::where('status','active')->inRandomOrder()->paginate($pagination_limit);
                $photos_list->appends(request()->only('filter'))->links();
            }
            else
            {
                $photos_list = Photos::where('status','active')->orderBy('created_at','DESC')->paginate($pagination_limit);
                $photos_list->appends(request()->only('filter'))->links();
            }
        }
        else
        {
            $photos_list = Photos::where('status','active')->orderBy('created_at','DESC')->paginate($pagination_limit);
        }

        $categories = Photos::where('status','active')->distinct()->pluck('category')->filter();

        return view('pages.shows.list', compact('slider', 'photos_list', 'categories'));
    }

    /**
     * Display the specified photo
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

        $photo = Photos::where('status','active')->where('id',$id)->first();

        if($photo == '')
        {
            abort(404, 'Photo not found.');
        }

        // Increment view count
        $photo->increment('view_count');

        // Get related photos (same category)
        $related_photos = Photos::where('status','active')
            ->where('category', $photo->category)
            ->where('id', '!=', $photo->id)
            ->take(6)
            ->get();

        return view('pages.photos.details', compact('photo', 'related_photos'));
    }

    /**
     * Download photo
     */
    public function download($id)
    {
        $photo = Photos::where('status','active')->where('id',$id)->first();

        if($photo == '')
        {
            abort(404, 'Photo not found.');
        }

        // Check if photo is premium and user is not authenticated or doesn't have access
        if($photo->license_price && $photo->license_price > 0) {
            if(!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login to download premium photos.');
            }

            // Check if user has purchased this photo (you can implement this logic)
            // For now, we'll show a message
            return redirect()->back()->with('error', 'This is a premium photo. Please contact us to purchase the license.');
        }

        // Increment download count
        $photo->increment('download_count');

        $file_path = public_path('upload/photos/' . $photo->image_path);

        if(file_exists($file_path)) {
            // For free photos, add watermark
            return $this->downloadWithWatermark($file_path, $photo);
        } else {
            abort(404, 'File not found.');
        }
    }

    /**
     * Download photo with watermark
     */
    private function downloadWithWatermark($file_path, $photo)
    {
        try {
            // Get site name from settings
            $site_name = getcong('site_name') ?: 'Photo Gallery';

            // Create watermarked version
            $watermarked_path = $this->addWatermarkToImage($file_path, $site_name);

            return response()->download($watermarked_path, 'watermarked_' . $photo->file_name);
        } catch (\Exception $e) {
            // If watermarking fails, return original file
            return response()->download($file_path, $photo->file_name);
        }
    }

    /**
     * Add watermark to image
     */
    private function addWatermarkToImage($image_path, $site_name)
    {
        $image_info = getimagesize($image_path);
        $mime_type = $image_info['mime'];

        // Create image resource based on type
        switch($mime_type) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($image_path);
                break;
            case 'image/png':
                $image = imagecreatefrompng($image_path);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($image_path);
                break;
            default:
                throw new \Exception('Unsupported image type');
        }

        if(!$image) {
            throw new \Exception('Could not create image resource');
        }

        // Get image dimensions
        $width = imagesx($image);
        $height = imagesy($image);

        // Create watermark text
        $text = $site_name;
        $font_size = max(12, min($width, $height) / 20); // Responsive font size
        $font_path = public_path('fonts/arial.ttf'); // You may need to add a font file

        // If font file doesn't exist, use built-in font
        if(file_exists($font_path)) {
            $text_box = imagettfbbox($font_size, 0, $font_path, $text);
            $text_width = $text_box[4] - $text_box[0];
            $text_height = $text_box[1] - $text_box[5];
        } else {
            // Use built-in font (5 is largest)
            $text_width = strlen($text) * imagefontwidth(5);
            $text_height = imagefontheight(5);
        }

        // Calculate position (bottom center)
        $x = ($width - $text_width) / 2;
        $y = $height - 20;

        // Create semi-transparent background for text
        $bg_color = imagecolorallocatealpha($image, 0, 0, 0, 50);
        $text_color = imagecolorallocate($image, 255, 255, 255);
        $stroke_color = imagecolorallocate($image, 0, 0, 0);

        // Draw background rectangle
        imagefilledrectangle($image, $x - 10, $y - $text_height - 5, $x + $text_width + 10, $y + 5, $bg_color);

        // Draw text with stroke effect
        if(file_exists($font_path)) {
            // Draw stroke
            imagettftext($image, $font_size, 0, $x-1, $y-1, $stroke_color, $font_path, $text);
            imagettftext($image, $font_size, 0, $x+1, $y-1, $stroke_color, $font_path, $text);
            imagettftext($image, $font_size, 0, $x-1, $y+1, $stroke_color, $font_path, $text);
            imagettftext($image, $font_size, 0, $x+1, $y+1, $stroke_color, $font_path, $text);

            // Draw main text
            imagettftext($image, $font_size, 0, $x, $y, $text_color, $font_path, $text);
        } else {
            // Use built-in font
            imagestring($image, 5, $x, $y - $text_height, $text, $text_color);
        }

        // Save watermarked image
        $watermarked_path = public_path('upload/photos/watermarked_' . time() . '_' . basename($image_path));

        switch($mime_type) {
            case 'image/jpeg':
                imagejpeg($image, $watermarked_path, 90);
                break;
            case 'image/png':
                imagepng($image, $watermarked_path, 9);
                break;
            case 'image/gif':
                imagegif($image, $watermarked_path);
                break;
        }

        // Clean up
        imagedestroy($image);

        return $watermarked_path;
    }
}
