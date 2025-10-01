<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photos;
use App\Models\PhotoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;

class PhotosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of photos
     */
    public function index(Request $request)
    {
        $page_title = 'Photos';

        $photos = Photos::with('user')
            ->when($request->search, function($query, $search) {
                return $query->search($search);
            })
            ->when($request->category, function($query, $category) {
                return $query->category($category);
            })
            ->when($request->status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $categories = Photos::distinct()->pluck('category')->filter();

        return view('admin.photos.index', compact('page_title', 'photos', 'categories'));
    }

    /**
     * Show the form for creating a new photo
     */
    public function create()
    {
        $page_title = 'Add Photo';
        $categories = PhotoCategory::where('status', 'active')->orderBy('name')->pluck('name', 'name');
        return view('admin.photos.create', compact('page_title', 'categories'));
    }

    /**
     * Store a newly created photo
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,bmp,tiff,webp|max:20480', // 20MB max
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $photo = new Photos();

            // Handle image upload and metadata extraction
            if ($request->hasFile('image')) {
                $uploadResult = $this->handleImageUpload($request->file('image'));

                if ($uploadResult['success']) {
                    // Fill basic data
                    $photo->fill($request->except('image'));

                    // Fill image data
                    $photo->image_name = $uploadResult['image_name'];
                    $photo->image_path = $uploadResult['image_path'];
                    $photo->added_by = Auth::id();

                    // Fill metadata
                    foreach ($uploadResult['metadata'] as $key => $value) {
                        $photo->$key = $value;
                    }

                    $photo->save();

                    return redirect()->route('admin.photos.index')
                        ->with('flash_message', trans('words.added_text'));
                } else {
                    return redirect()->back()
                        ->with('error', 'Failed to upload image: ' . $uploadResult['message'])
                        ->withInput();
                }
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating photo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified photo
     */
    public function show(Photos $photo)
    {
        $page_title = 'View Photo';
        return view('admin.photos.show', compact('page_title', 'photo'));
    }

    /**
     * Show the form for editing the specified photo
     */
    public function edit(Photos $photo)
    {
        $page_title = 'Edit Photo';
        $categories = PhotoCategory::where('status', 'active')->orderBy('name')->pluck('name', 'name');
        return view('admin.photos.edit', compact('page_title', 'photo', 'categories'));
    }

    /**
     * Update the specified photo
     */
    public function update(Request $request, Photos $photo)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,bmp,tiff,webp|max:20480',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Handle new image upload if provided
            if ($request->hasFile('image')) {
                // Delete old image
                $this->deleteImage($photo->image_path);

                $uploadResult = $this->handleImageUpload($request->file('image'));

                if ($uploadResult['success']) {
                    $photo->image_name = $uploadResult['image_name'];
                    $photo->image_path = $uploadResult['image_path'];

                    // Update metadata
                    foreach ($uploadResult['metadata'] as $key => $value) {
                        $photo->$key = $value;
                    }
                } else {
                    return redirect()->back()
                        ->with('error', 'Failed to upload image: ' . $uploadResult['message'])
                        ->withInput();
                }
            }

            // Update other fields
            $photo->fill($request->except('image'));
            $photo->save();

            return redirect()->route('admin.photos.index')
                ->with('flash_message', trans('words.updated_text'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating photo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified photo
     */
    public function destroy(Photos $photo)
    {
        try {
            // Delete image file
            $this->deleteImage($photo->image_path);

            // Delete record
            $photo->delete();

            return redirect()->route('admin.photos.index')
                ->with('flash_message', trans('words.deleted_text'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting photo: ' . $e->getMessage());
        }
    }

    /**
     * Get image metadata via AJAX
     */
    public function getImageMetadata(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'No image provided'], 400);
        }

        try {
            $file = $request->file('image');
            $metadata = $this->extractImageMetadata($file->getPathname(), $file);

            return response()->json(['success' => true, 'metadata' => $metadata]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle image upload and metadata extraction
     */
    private function handleImageUpload($file)
    {
        try {
            // Generate unique filename
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $uploadPath = 'public/upload/photos/';

            // Create directory if it doesn't exist
            if (!file_exists(public_path('upload/photos'))) {
                mkdir(public_path('upload/photos'), 0755, true);
            }

            // Move file
            $file->move(public_path('upload/photos'), $fileName);
            $fullPath = public_path('upload/photos/' . $fileName);

            // Extract metadata
            $metadata = $this->extractImageMetadata($fullPath, $file);

            return [
                'success' => true,
                'image_name' => $file->getClientOriginalName(),
                'image_path' => $fileName,
                'metadata' => $metadata
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Extract image metadata including EXIF data
     */
    private function extractImageMetadata($imagePath, $uploadedFile = null)
    {
        $metadata = [];

        try {
            // Basic file information
            if ($uploadedFile) {
                $metadata['file_name'] = $uploadedFile->getClientOriginalName();
                $metadata['file_size'] = $uploadedFile->getSize();
                $metadata['file_type'] = $uploadedFile->getClientOriginalExtension();
                $metadata['mime_type'] = $uploadedFile->getMimeType();
            }

            // Get image dimensions and other info
            if (function_exists('getimagesize')) {
                $imageInfo = getimagesize($imagePath);
                if ($imageInfo) {
                    $metadata['width'] = $imageInfo[0];
                    $metadata['height'] = $imageInfo[1];
                    $metadata['mime_type'] = $metadata['mime_type'] ?? $imageInfo['mime'];

                    // Calculate resolution (assuming 72 DPI default)
                    $metadata['resolution'] = '72 DPI';

                    // Determine if image has transparency
                    if (isset($imageInfo['channels'])) {
                        $metadata['has_transparency'] = $imageInfo['channels'] == 4;
                    }
                }
            }

            // Extract EXIF data
            if (function_exists('exif_read_data') && in_array(strtolower($metadata['file_type'] ?? ''), ['jpg', 'jpeg', 'tiff'])) {
                $exif = @exif_read_data($imagePath);

                if ($exif !== false) {
                    // Camera information
                    $metadata['camera_make'] = $exif['Make'] ?? null;
                    $metadata['camera_model'] = $exif['Model'] ?? null;
                    $metadata['lens_model'] = $exif['UndefinedTag:0xA434'] ?? null;

                    // Photo settings
                    if (isset($exif['FocalLength'])) {
                        $metadata['focal_length'] = $this->formatExifFraction($exif['FocalLength']) . 'mm';
                    }

                    if (isset($exif['FNumber'])) {
                        $metadata['aperture'] = 'f/' . $this->formatExifFraction($exif['FNumber']);
                    }

                    if (isset($exif['ExposureTime'])) {
                        $metadata['shutter_speed'] = $this->formatExifFraction($exif['ExposureTime']) . 's';
                    }

                    $metadata['iso'] = $exif['ISOSpeedRatings'] ?? null;
                    $metadata['flash'] = isset($exif['Flash']) ? ($exif['Flash'] ? 'Yes' : 'No') : null;
                    $metadata['white_balance'] = $exif['WhiteBalance'] ?? null;

                    // Date taken
                    if (isset($exif['DateTimeOriginal'])) {
                        $metadata['date_taken'] = date('Y-m-d H:i:s', strtotime($exif['DateTimeOriginal']));
                    } elseif (isset($exif['DateTime'])) {
                        $metadata['date_taken'] = date('Y-m-d H:i:s', strtotime($exif['DateTime']));
                    }

                    // GPS information
                    if (isset($exif['GPSLatitude'], $exif['GPSLongitude'])) {
                        $metadata['gps_latitude'] = $this->formatGPS($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
                        $metadata['gps_longitude'] = $this->formatGPS($exif['GPSLongitude'], $exif['GPSLongitudeRef']);
                    }

                    // Orientation
                    if (isset($exif['Orientation'])) {
                        $orientations = [
                            1 => 'Normal',
                            2 => 'Mirrored',
                            3 => 'Rotated 180°',
                            4 => 'Mirrored and rotated 180°',
                            5 => 'Mirrored and rotated 90° CCW',
                            6 => 'Rotated 90° CW',
                            7 => 'Mirrored and rotated 90° CW',
                            8 => 'Rotated 90° CCW'
                        ];
                        $metadata['orientation'] = $orientations[$exif['Orientation']] ?? 'Unknown';
                    }

                    // Copyright and artist
                    $metadata['copyright'] = $exif['Copyright'] ?? null;
                    $metadata['artist'] = $exif['Artist'] ?? null;
                }
            }

            // Color space detection (basic)
            $metadata['color_space'] = 'RGB'; // Default assumption
            $metadata['bit_depth'] = 8; // Default assumption

        } catch (\Exception $e) {
            // Log error but don't fail
            \Log::warning('Failed to extract image metadata: ' . $e->getMessage());
        }

        return $metadata;
    }

    /**
     * Format EXIF fraction values
     */
    private function formatExifFraction($fraction)
    {
        if (strpos($fraction, '/') !== false) {
            $parts = explode('/', $fraction);
            if (count($parts) == 2 && $parts[1] != 0) {
                return round($parts[0] / $parts[1], 2);
            }
        }
        return $fraction;
    }

    /**
     * Format GPS coordinates
     */
    private function formatGPS($coordinate, $hemisphere)
    {
        if (!is_array($coordinate) || count($coordinate) < 3) {
            return null;
        }

        $degrees = $this->formatExifFraction($coordinate[0]);
        $minutes = $this->formatExifFraction($coordinate[1]);
        $seconds = $this->formatExifFraction($coordinate[2]);

        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

        if ($hemisphere == 'S' || $hemisphere == 'W') {
            $decimal = -$decimal;
        }

        return $decimal;
    }

    /**
     * Delete image file
     */
    private function deleteImage($imagePath)
    {
        if ($imagePath && file_exists(public_path('upload/photos/' . $imagePath))) {
            unlink(public_path('upload/photos/' . $imagePath));
        }
    }
}
