<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photos;
use App\Models\PhotoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            'status' => 'required|in:active,inactive',
            'license_price' => 'nullable|numeric|min:0'
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

                    // Explicitly handle license_price to ensure proper decimal handling
                    $licensePrice = $request->license_price;
                    if ($licensePrice !== null && $licensePrice !== '') {
                        $photo->license_price = round((float)$licensePrice, 2);
                    } else {
                        $photo->license_price = null;
                    }

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
            'status' => 'required|in:active,inactive',
            'license_price' => 'nullable|numeric|min:0'
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

            // Explicitly handle license_price to ensure proper decimal handling
            $licensePrice = $request->license_price;
            if ($licensePrice !== null && $licensePrice !== '') {
                $photo->license_price = round((float)$licensePrice, 2);
            } else {
                $photo->license_price = null;
            }

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
                    $metadata['lens_model'] = $exif['UndefinedTag:0xA434'] ?? $exif['LensModel'] ?? null;

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

                    $metadata['iso'] = $exif['ISOSpeedRatings'] ?? $exif['ISO'] ?? null;

                    // Enhanced flash information
                    if (isset($exif['Flash'])) {
                        $flashValues = [
                            0 => 'No Flash',
                            1 => 'Fired',
                            5 => 'Fired, Return not detected',
                            7 => 'Fired, Return detected',
                            8 => 'On, Did not fire',
                            9 => 'On, Fired',
                            13 => 'On, Return not detected',
                            15 => 'On, Return detected',
                            16 => 'Off, Did not fire',
                            24 => 'Off, Did not fire, Return not detected',
                            25 => 'Auto, Did not fire',
                            29 => 'Auto, Fired',
                            31 => 'Auto, Fired, Return not detected',
                            32 => 'No flash function',
                            65 => 'On, Fired',
                            69 => 'On, Fired, Return not detected',
                            71 => 'On, Fired, Return detected',
                            73 => 'On, Red-eye reduction',
                            79 => 'On, Red-eye reduction, Return not detected',
                            89 => 'On, Red-eye reduction, Return detected',
                            93 => 'On, Fired, Red-eye reduction',
                            95 => 'On, Fired, Red-eye reduction, Return not detected'
                        ];
                        $metadata['flash'] = $flashValues[$exif['Flash']] ?? 'Unknown';
                    }

                    // Enhanced white balance
                    if (isset($exif['WhiteBalance'])) {
                        $wbValues = [
                            0 => 'Auto',
                            1 => 'Manual'
                        ];
                        $metadata['white_balance'] = $wbValues[$exif['WhiteBalance']] ?? 'Unknown';
                    }

                    // Date taken - try multiple fields
                    if (isset($exif['DateTimeOriginal'])) {
                        $metadata['date_taken'] = date('Y-m-d H:i:s', strtotime($exif['DateTimeOriginal']));
                    } elseif (isset($exif['DateTime'])) {
                        $metadata['date_taken'] = date('Y-m-d H:i:s', strtotime($exif['DateTime']));
                    } elseif (isset($exif['DateTimeDigitized'])) {
                        $metadata['date_taken'] = date('Y-m-d H:i:s', strtotime($exif['DateTimeDigitized']));
                    }

                    // GPS information
                    if (isset($exif['GPSLatitude'], $exif['GPSLongitude'])) {
                        $metadata['gps_latitude'] = $this->formatGPS($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
                        $metadata['gps_longitude'] = $this->formatGPS($exif['GPSLongitude'], $exif['GPSLongitudeRef']);

                        // Try to get GPS altitude
                        if (isset($exif['GPSAltitude'])) {
                            $metadata['gps_altitude'] = $this->formatExifFraction($exif['GPSAltitude']) . 'm';
                        }
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

                    // Additional EXIF data
                    $metadata['software'] = $exif['Software'] ?? null;
                    $metadata['exposure_mode'] = $this->getExposureMode($exif['ExposureMode'] ?? null);
                    $metadata['metering_mode'] = $this->getMeteringMode($exif['MeteringMode'] ?? null);
                    $metadata['scene_capture_type'] = $this->getSceneCaptureType($exif['SceneCaptureType'] ?? null);
                    $metadata['contrast'] = $this->getContrast($exif['Contrast'] ?? null);
                    $metadata['saturation'] = $this->getSaturation($exif['Saturation'] ?? null);
                    $metadata['sharpness'] = $this->getSharpness($exif['Sharpness'] ?? null);

                    // Lens information
                    $metadata['lens_specification'] = $exif['LensSpecification'] ?? null;
                    $metadata['lens_serial_number'] = $exif['LensSerialNumber'] ?? null;

                    // Focus information
                    $metadata['focus_distance'] = $exif['SubjectDistance'] ?? null;
                    $metadata['focus_mode'] = $this->getFocusMode($exif['FocusMode'] ?? null);

                    // Image quality settings
                    $metadata['image_quality'] = $exif['ImageQuality'] ?? null;
                    $metadata['white_balance_mode'] = $exif['WhiteBalanceMode'] ?? null;

                    // Color information
                    if (isset($exif['ColorSpace'])) {
                        $colorSpaces = [
                            1 => 'sRGB',
                            2 => 'Adobe RGB',
                            65535 => 'Uncalibrated'
                        ];
                        $metadata['color_space'] = $colorSpaces[$exif['ColorSpace']] ?? 'Unknown';
                    }

                    // Bit depth from EXIF
                    if (isset($exif['BitsPerSample'])) {
                        $metadata['bit_depth'] = is_array($exif['BitsPerSample']) ? $exif['BitsPerSample'][0] : $exif['BitsPerSample'];
                    }

                    // Keywords and subject
                    $metadata['keywords'] = $exif['Keywords'] ?? null;
                    $metadata['subject'] = $exif['Subject'] ?? null;
                    $metadata['subject_distance_range'] = $this->getSubjectDistanceRange($exif['SubjectDistanceRange'] ?? null);

                    // Digital zoom
                    $metadata['digital_zoom_ratio'] = $exif['DigitalZoomRatio'] ?? null;

                    // Focal length in 35mm film
                    $metadata['focal_length_35mm'] = $exif['FocalLengthIn35mmFilm'] ?? null;

                    // Scene type
                    $metadata['scene_type'] = $exif['SceneType'] ?? null;

                    // Custom rendered
                    $metadata['custom_rendered'] = $this->getCustomRendered($exif['CustomRendered'] ?? null);

                    // Exposure program
                    $metadata['exposure_program'] = $this->getExposureProgram($exif['ExposureProgram'] ?? null);

                    // Light source
                    $metadata['light_source'] = $this->getLightSource($exif['LightSource'] ?? null);

                    // Gain control
                    $metadata['gain_control'] = $this->getGainControl($exif['GainControl'] ?? null);

                    // Contrast, saturation, sharpness
                    $metadata['contrast'] = $this->getContrast($exif['Contrast'] ?? null);
                    $metadata['saturation'] = $this->getSaturation($exif['Saturation'] ?? null);
                    $metadata['sharpness'] = $this->getSharpness($exif['Sharpness'] ?? null);
                }
            }

            // Color space detection (basic) - only if not set from EXIF
            if (!isset($metadata['color_space'])) {
                $metadata['color_space'] = 'RGB'; // Default assumption
            }

            // Bit depth - only if not set from EXIF
            if (!isset($metadata['bit_depth'])) {
                $metadata['bit_depth'] = 8; // Default assumption
            }

        } catch (\Exception $e) {
            // Log error but don't fail
            Log::warning('Failed to extract image metadata: ' . $e->getMessage());
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

    /**
     * Get exposure mode description
     */
    private function getExposureMode($mode)
    {
        $modes = [
            0 => 'Auto',
            1 => 'Manual',
            2 => 'Auto bracket'
        ];
        return $modes[$mode] ?? null;
    }

    /**
     * Get metering mode description
     */
    private function getMeteringMode($mode)
    {
        $modes = [
            0 => 'Unknown',
            1 => 'Average',
            2 => 'Center-weighted average',
            3 => 'Spot',
            4 => 'Multi-spot',
            5 => 'Multi-segment',
            6 => 'Partial',
            255 => 'Other'
        ];
        return $modes[$mode] ?? null;
    }

    /**
     * Get scene capture type description
     */
    private function getSceneCaptureType($type)
    {
        $types = [
            0 => 'Standard',
            1 => 'Landscape',
            2 => 'Portrait',
            3 => 'Night scene'
        ];
        return $types[$type] ?? null;
    }

    /**
     * Get contrast description
     */
    private function getContrast($contrast)
    {
        $contrasts = [
            0 => 'Normal',
            1 => 'Soft',
            2 => 'Hard'
        ];
        return $contrasts[$contrast] ?? null;
    }

    /**
     * Get saturation description
     */
    private function getSaturation($saturation)
    {
        $saturations = [
            0 => 'Normal',
            1 => 'Low',
            2 => 'High'
        ];
        return $saturations[$saturation] ?? null;
    }

    /**
     * Get sharpness description
     */
    private function getSharpness($sharpness)
    {
        $sharpnesses = [
            0 => 'Normal',
            1 => 'Soft',
            2 => 'Hard'
        ];
        return $sharpnesses[$sharpness] ?? null;
    }

    /**
     * Get focus mode description
     */
    private function getFocusMode($mode)
    {
        $modes = [
            0 => 'Auto',
            1 => 'Manual',
            2 => 'Auto macro',
            3 => 'Manual macro'
        ];
        return $modes[$mode] ?? null;
    }

    /**
     * Get subject distance range description
     */
    private function getSubjectDistanceRange($range)
    {
        $ranges = [
            0 => 'Unknown',
            1 => 'Macro',
            2 => 'Close view',
            3 => 'Distant view'
        ];
        return $ranges[$range] ?? null;
    }

    /**
     * Get custom rendered description
     */
    private function getCustomRendered($rendered)
    {
        $renders = [
            0 => 'Normal process',
            1 => 'Custom process'
        ];
        return $renders[$rendered] ?? null;
    }

    /**
     * Get exposure program description
     */
    private function getExposureProgram($program)
    {
        $programs = [
            0 => 'Not defined',
            1 => 'Manual',
            2 => 'Program AE',
            3 => 'Aperture-priority AE',
            4 => 'Shutter-priority AE',
            5 => 'Creative program',
            6 => 'Action program',
            7 => 'Portrait mode',
            8 => 'Landscape mode'
        ];
        return $programs[$program] ?? null;
    }

    /**
     * Get light source description
     */
    private function getLightSource($source)
    {
        $sources = [
            0 => 'Unknown',
            1 => 'Daylight',
            2 => 'Fluorescent',
            3 => 'Tungsten',
            4 => 'Flash',
            9 => 'Fine weather',
            10 => 'Cloudy weather',
            11 => 'Shade',
            12 => 'Daylight fluorescent',
            13 => 'Day white fluorescent',
            14 => 'Cool white fluorescent',
            15 => 'White fluorescent',
            17 => 'Standard light A',
            18 => 'Standard light B',
            19 => 'Standard light C',
            20 => 'D55',
            21 => 'D65',
            22 => 'D75',
            23 => 'D50',
            24 => 'ISO studio tungsten',
            255 => 'Other'
        ];
        return $sources[$source] ?? null;
    }

    /**
     * Get gain control description
     */
    private function getGainControl($control)
    {
        $controls = [
            0 => 'None',
            1 => 'Low gain up',
            2 => 'High gain up',
            3 => 'Low gain down',
            4 => 'High gain down'
        ];
        return $controls[$control] ?? null;
    }
}
