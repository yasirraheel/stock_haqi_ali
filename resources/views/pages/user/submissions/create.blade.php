@extends('site_app')

@section('head_title', 'Submit Stock Media | ' . getcong('site_name'))

@section('content')

    <!-- Start Breadcrumb -->
    <div class="breadcrumb-section bg-xs"
        style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <h2>Submit Stock Media</h2>
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="{{ URL::to('/') }}" title="{{ trans('words.home') }}">{{ trans('words.home') }}</a></li>
                            <li><a href="{{ route('user.submissions.index') }}" title="Submissions">Submissions</a></li>
                            <li>Submit</li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="vfx-item-ptb vfx-item-info">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-md-11 col-sm-12">

                    <!-- Info Alert Regarding Single Google Drive Link Policy -->
                    <div class="alert alert-info mb-4" style="background: rgba(53, 184, 224, 0.15); border: 1px solid #35b8e0; color: #e0f7fa; border-radius: 8px;">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-info-circle mr-3" style="font-size: 24px; color: #35b8e0;"></i>
                            <div>
                                <strong style="color: #fff;">Single Item Submission Policy:</strong><br>
                                Please provide a direct Google Drive <strong>file link</strong> (e.g. <code>https://drive.google.com/file/d/FILE_ID/view</code>).<br>
                                <span class="text-warning"><i class="fa fa-warning"></i> Bulk folder URLs are not permitted. Each item must be submitted individually for admin review.</span>
                            </div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4" style="background: rgba(243, 73, 67, 0.15); border: 1px solid #f34943; color: #ffcdd2; border-radius: 8px;">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Submission Box with Tabs -->
                    <div class="card" style="background: #1a2234; border: 1px solid #2a3447; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
                        <div class="card-header p-0" style="background: #151c2b; border-bottom: 1px solid #2a3447;">
                            <ul class="nav nav-tabs border-0 nav-justified" id="submitFormTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active text-white" id="tab-audio" data-toggle="tab" href="#form-audio" role="tab" style="padding: 16px; border: none; font-weight: 600;">
                                        <i class="fa fa-music mr-2"></i> Audio Track
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white" id="tab-film" data-toggle="tab" href="#form-film" role="tab" style="padding: 16px; border: none; font-weight: 600;">
                                        <i class="fa fa-film mr-2"></i> Film Stock
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white" id="tab-effect" data-toggle="tab" href="#form-effect" role="tab" style="padding: 16px; border: none; font-weight: 600;">
                                        <i class="fa fa-magic mr-2"></i> Effect
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white" id="tab-photo" data-toggle="tab" href="#form-photo" role="tab" style="padding: 16px; border: none; font-weight: 600;">
                                        <i class="fa fa-camera mr-2"></i> Photo
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-4">
                            <div class="tab-content" id="submitFormTabsContent">

                                <!-- 1. Audio Submission Form -->
                                <div class="tab-pane fade show active" id="form-audio" role="tabpanel">
                                    <h4 class="text-white mb-3" style="font-weight: 600;"><i class="fa fa-music text-danger mr-2"></i> Submit Audio Track</h4>
                                    <form action="{{ route('user.submissions.audio.store') }}" method="POST">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label class="text-white">Audio Track Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" placeholder="e.g. Cinematic Ambient Soundscape" value="{{ old('title') }}" required style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="text-white">Google Drive Audio File Link <span class="text-danger">*</span></label>
                                            <input type="text" name="drive_url" class="form-control" placeholder="https://drive.google.com/file/d/1XyZ.../view" value="{{ old('drive_url') }}" required style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                            <small class="text-muted">Direct Google Drive file link (.mp3, .wav, .ogg, .aac). Ensure sharing permissions are set to "Anyone with the link".</small>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">Genre / Category</label>
                                                <input type="text" name="genre" class="form-control" placeholder="e.g. Cinematic, Electronic, Pop" value="{{ old('genre') }}" style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                            </div>
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">License Price ($ USD)</label>
                                                <input type="number" step="0.01" min="0" name="license_price" class="form-control" placeholder="0.00 (Leave 0 for Free)" value="{{ old('license_price', '0.00') }}" style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                            </div>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label class="text-white">Description</label>
                                            <textarea name="description" class="form-control" rows="3" placeholder="Describe the mood, tempo, instruments, and usage..." style="background: #121824; border: 1px solid #2a3447; color: #fff;">{{ old('description') }}</textarea>
                                        </div>

                                        <button type="submit" class="vfx-item-btn-danger text-uppercase w-100" style="padding: 14px; font-weight: 700; border-radius: 6px;">
                                            <i class="fa fa-paper-plane mr-2"></i> Submit Audio for Approval
                                        </button>
                                    </form>
                                </div>

                                <!-- 2. Film Stock Submission Form -->
                                <div class="tab-pane fade" id="form-film" role="tabpanel">
                                    <h4 class="text-white mb-3" style="font-weight: 600;"><i class="fa fa-film text-danger mr-2"></i> Submit Film Stock Video</h4>
                                    <form action="{{ route('user.submissions.film-stock.store') }}" method="POST">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label class="text-white">Film Stock Name / Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" placeholder="e.g. Vintage 35mm Grain Overlay 4K" value="{{ old('title') }}" required style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="text-white">Google Drive Video File Link <span class="text-danger">*</span></label>
                                            <input type="text" name="drive_url" class="form-control" placeholder="https://drive.google.com/file/d/1XyZ.../view" value="{{ old('drive_url') }}" required style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                            <small class="text-muted">Direct Google Drive video file link (.mp4, .mov, .webm). Ensure link sharing is enabled.</small>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label class="text-white">Description</label>
                                            <textarea name="description" class="form-control" rows="3" placeholder="Describe resolution, frame rate, lighting, or style..." style="background: #121824; border: 1px solid #2a3447; color: #fff;">{{ old('description') }}</textarea>
                                        </div>

                                        <button type="submit" class="vfx-item-btn-danger text-uppercase w-100" style="padding: 14px; font-weight: 700; border-radius: 6px;">
                                            <i class="fa fa-paper-plane mr-2"></i> Submit Film Stock for Approval
                                        </button>
                                    </form>
                                </div>

                                <!-- 3. Effect Submission Form -->
                                <div class="tab-pane fade" id="form-effect" role="tabpanel">
                                    <h4 class="text-white mb-3" style="font-weight: 600;"><i class="fa fa-magic text-danger mr-2"></i> Submit Video Effect</h4>
                                    <form action="{{ route('user.submissions.effect.store') }}" method="POST">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label class="text-white">Effect Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" placeholder="e.g. Glitch Transition FX" value="{{ old('title') }}" required style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="text-white">Google Drive Effect File Link <span class="text-danger">*</span></label>
                                            <input type="text" name="drive_url" class="form-control" placeholder="https://drive.google.com/file/d/1XyZ.../view" value="{{ old('drive_url') }}" required style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                            <small class="text-muted">Direct Google Drive file link (.mp4, .mov, .zip). Ensure link sharing is enabled.</small>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">Category</label>
                                                <input type="text" name="category" class="form-control" placeholder="e.g. Transitions, Glitch, VFX" value="{{ old('category') }}" style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                            </div>
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">License Price ($ USD)</label>
                                                <input type="number" step="0.01" min="0" name="license_price" class="form-control" placeholder="0.00 (Leave 0 for Free)" value="{{ old('license_price', '0.00') }}" style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                            </div>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label class="text-white">Description</label>
                                            <textarea name="description" class="form-control" rows="3" placeholder="Describe the effect, blend mode, and instructions..." style="background: #121824; border: 1px solid #2a3447; color: #fff;">{{ old('description') }}</textarea>
                                        </div>

                                        <button type="submit" class="vfx-item-btn-danger text-uppercase w-100" style="padding: 14px; font-weight: 700; border-radius: 6px;">
                                            <i class="fa fa-paper-plane mr-2"></i> Submit Effect for Approval
                                        </button>
                                    </form>
                                </div>

                                <!-- 4. Photo Submission Form -->
                                <div class="tab-pane fade" id="form-photo" role="tabpanel">
                                    <h4 class="text-white mb-3" style="font-weight: 600;"><i class="fa fa-camera text-danger mr-2"></i> Submit Stock Photo</h4>
                                    <form action="{{ route('user.submissions.photo.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label class="text-white">Photo Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" placeholder="e.g. Sunset Mountain Panorama" value="{{ old('title') }}" required style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="text-white">Google Drive Photo Link <span class="text-muted">(Option 1)</span></label>
                                            <input type="text" name="drive_url" class="form-control" placeholder="https://drive.google.com/file/d/1XyZ.../view" value="{{ old('drive_url') }}" style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                            <small class="text-muted">Direct Google Drive link to image file (.jpg, .png, .webp).</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="text-white">Or Upload Direct Image File <span class="text-muted">(Option 2)</span></label>
                                            <input type="file" name="image_file" class="form-control-file text-white">
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">Category</label>
                                                <select name="category" class="form-control" style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                                    <option value="General">General</option>
                                                    @foreach($photoCategories as $pcat)
                                                        <option value="{{ $pcat->category_name }}">{{ $pcat->category_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">License Price ($ USD)</label>
                                                <input type="number" step="0.01" min="0" name="license_price" class="form-control" placeholder="0.00 (Leave 0 for Free)" value="{{ old('license_price', '0.00') }}" style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                            </div>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label class="text-white">Description</label>
                                            <textarea name="description" class="form-control" rows="3" placeholder="Describe camera settings, location, or subject..." style="background: #121824; border: 1px solid #2a3447; color: #fff;">{{ old('description') }}</textarea>
                                        </div>

                                        <button type="submit" class="vfx-item-btn-danger text-uppercase w-100" style="padding: 14px; font-weight: 700; border-radius: 6px;">
                                            <i class="fa fa-paper-plane mr-2"></i> Submit Photo for Approval
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
