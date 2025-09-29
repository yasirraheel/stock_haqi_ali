@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-sm-8">
                                <a href="{{ route('admin.photos.index') }}">
                                    <h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;">
                                        <i class="fa fa-arrow-left"></i> {{ $page_title }}: {{ $photo->title }}
                                    </h4>
                                </a>
                            </div>
                            <div class="col-sm-4 text-right">
                                <a href="{{ route('admin.photos.edit', $photo->id) }}" class="btn btn-success btn-sm waves-effect waves-light">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Image Display -->
                            <div class="col-md-6">
                                <div class="card-box">
                                    <h5 class="header-title">Image Preview</h5>
                                    <div class="text-center">
                                        @if($photo->image_path)
                                            <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" class="img-fluid" style="max-height: 400px; border: 1px solid #ddd; border-radius: 4px;">
                                            <div class="mt-3">
                                                <a href="{{ $photo->image_url }}" target="_blank" class="btn btn-info btn-sm">
                                                    <i class="fa fa-external-link"></i> View Full Size
                                                </a>
                                            </div>
                                        @else
                                            <img src="{{ URL::to('public/admin_assets/images/users/no-image.jpg') }}" alt="No Image" class="img-fluid">
                                            <p class="text-muted mt-2">No image available</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card-box">
                                    <h5 class="header-title">Basic Information</h5>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-borderless">
                                            <tr>
                                                <td><strong>Title:</strong></td>
                                                <td>{{ $photo->title }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Description:</strong></td>
                                                <td>{{ $photo->description ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Category:</strong></td>
                                                <td>{{ $photo->category ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tags:</strong></td>
                                                <td>{{ $photo->tags ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Keywords:</strong></td>
                                                <td>{{ $photo->keywords ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    @if($photo->status == 'active')
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Downloads:</strong></td>
                                                <td>{{ number_format($photo->download_count) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Views:</strong></td>
                                                <td>{{ number_format($photo->view_count) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Technical Details -->
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="card-box">
                                    <h6 class="header-title">File Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>Original Name:</strong></td>
                                                <td>{{ $photo->file_name ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>File Size:</strong></td>
                                                <td>{{ $photo->formatted_file_size ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>File Type:</strong></td>
                                                <td>{{ $photo->file_type ? strtoupper($photo->file_type) : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>MIME Type:</strong></td>
                                                <td>{{ $photo->mime_type ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Dimensions:</strong></td>
                                                <td>{{ $photo->dimensions ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Resolution:</strong></td>
                                                <td>{{ $photo->resolution ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Color Space:</strong></td>
                                                <td>{{ $photo->color_space ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Bit Depth:</strong></td>
                                                <td>{{ $photo->bit_depth ? $photo->bit_depth . ' bit' : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Transparency:</strong></td>
                                                <td>{{ $photo->has_transparency ? 'Yes' : 'No' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Software:</strong></td>
                                                <td>{{ $photo->software ?: 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card-box">
                                    <h6 class="header-title">Camera & Lens</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>Camera Make:</strong></td>
                                                <td>{{ $photo->camera_make ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Camera Model:</strong></td>
                                                <td>{{ $photo->camera_model ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Lens Model:</strong></td>
                                                <td>{{ $photo->lens_model ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Lens Spec:</strong></td>
                                                <td>{{ $photo->lens_specification ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Lens Serial:</strong></td>
                                                <td>{{ $photo->lens_serial_number ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Focal Length:</strong></td>
                                                <td>{{ $photo->focal_length ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>35mm Equivalent:</strong></td>
                                                <td>{{ $photo->focal_length_35mm ? $photo->focal_length_35mm . 'mm' : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Digital Zoom:</strong></td>
                                                <td>{{ $photo->digital_zoom_ratio ?: 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card-box">
                                    <h6 class="header-title">Exposure Settings</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>Aperture:</strong></td>
                                                <td>{{ $photo->aperture ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Shutter Speed:</strong></td>
                                                <td>{{ $photo->shutter_speed ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>ISO:</strong></td>
                                                <td>{{ $photo->iso ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Exposure Mode:</strong></td>
                                                <td>{{ $photo->exposure_mode ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Exposure Program:</strong></td>
                                                <td>{{ $photo->exposure_program ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Metering Mode:</strong></td>
                                                <td>{{ $photo->metering_mode ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Flash:</strong></td>
                                                <td>{{ $photo->flash ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>White Balance:</strong></td>
                                                <td>{{ $photo->white_balance ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>WB Mode:</strong></td>
                                                <td>{{ $photo->white_balance_mode ?: 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card-box">
                                    <h6 class="header-title">Focus & Scene</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>Focus Mode:</strong></td>
                                                <td>{{ $photo->focus_mode ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Focus Distance:</strong></td>
                                                <td>{{ $photo->focus_distance ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Subject Distance:</strong></td>
                                                <td>{{ $photo->subject_distance_range ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Scene Type:</strong></td>
                                                <td>{{ $photo->scene_type ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Scene Capture:</strong></td>
                                                <td>{{ $photo->scene_capture_type ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Contrast:</strong></td>
                                                <td>{{ $photo->contrast ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Saturation:</strong></td>
                                                <td>{{ $photo->saturation ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Sharpness:</strong></td>
                                                <td>{{ $photo->sharpness ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Custom Rendered:</strong></td>
                                                <td>{{ $photo->custom_rendered ?: 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="card-box">
                                    <h6 class="header-title">GPS & Location</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>GPS Latitude:</strong></td>
                                                <td>{{ $photo->gps_latitude ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>GPS Longitude:</strong></td>
                                                <td>{{ $photo->gps_longitude ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>GPS Altitude:</strong></td>
                                                <td>{{ $photo->gps_altitude ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>GPS Location:</strong></td>
                                                <td>{{ $photo->gps_location ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Orientation:</strong></td>
                                                <td>{{ $photo->orientation ?: 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card-box">
                                    <h6 class="header-title">Image Quality</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>Image Quality:</strong></td>
                                                <td>{{ $photo->image_quality ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Light Source:</strong></td>
                                                <td>{{ $photo->light_source ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Gain Control:</strong></td>
                                                <td>{{ $photo->gain_control ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Date Taken:</strong></td>
                                                <td>{{ $photo->date_taken ? $photo->date_taken->format('M d, Y H:i') : 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card-box">
                                    <h6 class="header-title">Copyright & Info</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>Copyright:</strong></td>
                                                <td>{{ $photo->copyright ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Artist:</strong></td>
                                                <td>{{ $photo->artist ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Subject:</strong></td>
                                                <td>{{ $photo->subject ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Added By:</strong></td>
                                                <td>{{ $photo->user ? $photo->user->name : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Added Date:</strong></td>
                                                <td>{{ $photo->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Last Updated:</strong></td>
                                                <td>{{ $photo->updated_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>License Price:</strong></td>
                                                <td>{{ $photo->license_price ? getcong('currency_symbol') . number_format($photo->license_price, 2) : 'Free' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
