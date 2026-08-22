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
                                <strong style="color: #fff;">Simple 1-Click Submission:</strong><br>
                                Paste your Google Drive <strong>file link</strong> below and click <strong>"Scan File"</strong>. The title, preview, and format will be auto-filled for you.<br>
                                <span class="text-warning"><i class="fa fa-warning"></i> Bulk folder URLs are not permitted. Please provide single file links.</span>
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

                    <style>
                        .submission-custom-tabs {
                            border-bottom: 2px solid #2a3447 !important;
                            background: #121824;
                            padding: 8px 8px 0 8px;
                            border-radius: 10px 10px 0 0;
                            display: flex;
                        }
                        .submission-custom-tabs .nav-item {
                            margin-bottom: -2px;
                            flex: 1;
                        }
                        .submission-custom-tabs .nav-link {
                            color: #94a3b8 !important;
                            background: transparent !important;
                            border: none !important;
                            padding: 14px 16px !important;
                            font-weight: 600 !important;
                            font-size: 15px !important;
                            text-align: center;
                            transition: all 0.2s ease-in-out;
                            border-radius: 8px 8px 0 0 !important;
                            display: block;
                        }
                        .submission-custom-tabs .nav-link:hover {
                            color: #ffffff !important;
                            background: rgba(255, 255, 255, 0.06) !important;
                        }
                        .submission-custom-tabs .nav-link.active {
                            color: #ffffff !important;
                            background: linear-gradient(135deg, #ff3366, #e6004c) !important;
                            font-weight: 700 !important;
                            box-shadow: 0 4px 15px rgba(255, 51, 102, 0.35) !important;
                        }
                        .submission-custom-tabs .nav-link.active i {
                            color: #ffffff !important;
                        }

                        /* Unified Dark Inputs */
                        .submission-form-card .form-control {
                            background-color: #121824 !important;
                            border: 1px solid #2a3447 !important;
                            color: #ffffff !important;
                            border-radius: 6px !important;
                            height: 44px;
                            font-size: 14px;
                        }
                        .submission-form-card textarea.form-control {
                            height: auto !important;
                        }
                        .submission-form-card .form-control:focus {
                            border-color: #ff3366 !important;
                            box-shadow: 0 0 0 2px rgba(255, 51, 102, 0.2) !important;
                        }

                        /* Auto Preview Container */
                        .scan-preview-box {
                            background: #121824;
                            border: 1px dashed #35b8e0;
                            border-radius: 8px;
                            padding: 15px;
                            margin-bottom: 20px;
                            display: none;
                        }

                        /* Hide conflicting nice-select */
                        .submission-form-card .nice-select {
                            display: none !important;
                        }
                    </style>

                    <!-- Submission Box with Tabs -->
                    <div class="card submission-form-card" style="background: #1a2234; border: 1px solid #2a3447; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
                        <div class="card-header p-0" style="background: #121824; border-bottom: 1px solid #2a3447; border-radius: 10px 10px 0 0;">
                            <ul class="nav nav-tabs border-0 nav-justified submission-custom-tabs" id="submitFormTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="tab-audio" data-toggle="tab" href="#form-audio" role="tab">
                                        <i class="fa fa-music mr-2"></i> Audio Track
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-film" data-toggle="tab" href="#form-film" role="tab">
                                        <i class="fa fa-film mr-2"></i> Film Stock
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-effect" data-toggle="tab" href="#form-effect" role="tab">
                                        <i class="fa fa-magic mr-2"></i> Effect
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-photo" data-toggle="tab" href="#form-photo" role="tab">
                                        <i class="fa fa-camera mr-2"></i> Photo
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-4">
                            <div class="tab-content" id="submitFormTabsContent">

                                <!-- ========================================================
                                     1. AUDIO SUBMISSION TAB
                                     ======================================================== -->
                                <div class="tab-pane fade show active" id="form-audio" role="tabpanel">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h4 class="text-white mb-0" style="font-weight: 600;"><i class="fa fa-music text-danger mr-2"></i> Submit Audio Track</h4>
                                        <span class="badge badge-info" style="padding: 6px 10px;">GDrive Audio (.mp3, .wav, .ogg, .aac)</span>
                                    </div>

                                    <form action="{{ route('user.submissions.audio.store') }}" method="POST" id="audioSubmissionForm">
                                        @csrf

                                        <!-- Step 1: Link & Scan Button -->
                                        <div class="form-group mb-3">
                                            <label class="text-white">Google Drive Audio Link <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="drive_url" id="audio_drive_url" class="form-control" placeholder="Paste link (e.g. https://drive.google.com/file/d/1XyZ.../view)" value="{{ old('drive_url') }}" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-info font-weight-bold" type="button" onclick="scanGoogleDriveLink('audio')" id="btn_scan_audio">
                                                        <i class="fa fa-search mr-1"></i> Scan File
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Direct Google Drive file link. Make sure link sharing is set to "Anyone with the link".</small>
                                        </div>

                                        <!-- Live Preview Container (Auto-populated) -->
                                        <div id="audio_preview_box" class="scan-preview-box">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="text-info font-weight-bold"><i class="fa fa-play-circle mr-1"></i> Audio Preview</span>
                                                <span id="audio_badge_info" class="badge badge-success font-12"></span>
                                            </div>
                                            <div id="audio_player_container" class="mb-2"></div>
                                        </div>

                                        <!-- Step 2: Auto-Filled Fields -->
                                        <div class="form-group mb-3">
                                            <label class="text-white">Track Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" id="audio_title" class="form-control" placeholder="e.g. Cinematic Ambient Melody" value="{{ old('title') }}" required>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">Genre / Category</label>
                                                <input type="text" name="genre" id="audio_genre" class="form-control" placeholder="e.g. Cinematic, Ambient, Electronic" value="{{ old('genre') }}">
                                            </div>
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">License Price ($ USD)</label>
                                                <input type="number" step="0.01" min="0" name="license_price" class="form-control" placeholder="0.00 (Leave 0 for Free)" value="{{ old('license_price', '0.00') }}">
                                            </div>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label class="text-white">Description (Optional)</label>
                                            <textarea name="description" id="audio_description" class="form-control" rows="2" placeholder="Brief details about mood, instruments, or license usage...">{{ old('description') }}</textarea>
                                        </div>

                                        <button type="submit" class="vfx-item-btn-danger text-uppercase w-100" style="padding: 13px; font-weight: 700; border-radius: 6px;">
                                            <i class="fa fa-paper-plane mr-2"></i> Submit Audio for Approval
                                        </button>
                                    </form>
                                </div>


                                <!-- ========================================================
                                     2. FILM STOCK SUBMISSION TAB
                                     ======================================================== -->
                                <div class="tab-pane fade" id="form-film" role="tabpanel">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h4 class="text-white mb-0" style="font-weight: 600;"><i class="fa fa-film text-danger mr-2"></i> Submit Film Stock Video</h4>
                                        <span class="badge badge-info" style="padding: 6px 10px;">GDrive Video (.mp4, .mov, .webm)</span>
                                    </div>

                                    <form action="{{ route('user.submissions.film-stock.store') }}" method="POST" id="filmSubmissionForm">
                                        @csrf

                                        <!-- Step 1: Link & Scan Button -->
                                        <div class="form-group mb-3">
                                            <label class="text-white">Google Drive Video Link <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="drive_url" id="film_drive_url" class="form-control" placeholder="Paste link (e.g. https://drive.google.com/file/d/1XyZ.../view)" value="{{ old('drive_url') }}" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-info font-weight-bold" type="button" onclick="scanGoogleDriveLink('film')" id="btn_scan_film">
                                                        <i class="fa fa-search mr-1"></i> Scan File
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Direct Google Drive video link. Make sure link sharing is set to "Anyone with the link".</small>
                                        </div>

                                        <!-- Live Preview Container (Auto-populated) -->
                                        <div id="film_preview_box" class="scan-preview-box">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="text-info font-weight-bold"><i class="fa fa-play-circle mr-1"></i> Video Preview</span>
                                                <span id="film_badge_info" class="badge badge-success font-12"></span>
                                            </div>
                                            <div id="film_player_container" class="mb-2"></div>
                                        </div>

                                        <!-- Step 2: Auto-Filled Fields -->
                                        <div class="form-group mb-3">
                                            <label class="text-white">Film Stock Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" id="film_title" class="form-control" placeholder="e.g. 35mm Vintage Grain 4K" value="{{ old('title') }}" required>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">Category / Tag</label>
                                                <input type="text" name="category" id="film_category" class="form-control" placeholder="e.g. Vintage, Overlays, Grain" value="{{ old('category', 'Film Stock') }}">
                                            </div>
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">License Price ($ USD)</label>
                                                <input type="number" step="0.01" min="0" name="license_price" class="form-control" placeholder="0.00 (Leave 0 for Free)" value="{{ old('license_price', '0.00') }}">
                                            </div>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label class="text-white">Description (Optional)</label>
                                            <textarea name="description" id="film_description" class="form-control" rows="2" placeholder="Brief details about resolution, frame rate, or blend mode...">{{ old('description') }}</textarea>
                                        </div>

                                        <button type="submit" class="vfx-item-btn-danger text-uppercase w-100" style="padding: 13px; font-weight: 700; border-radius: 6px;">
                                            <i class="fa fa-paper-plane mr-2"></i> Submit Film Stock for Approval
                                        </button>
                                    </form>
                                </div>


                                <!-- ========================================================
                                     3. EFFECT SUBMISSION TAB
                                     ======================================================== -->
                                <div class="tab-pane fade" id="form-effect" role="tabpanel">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h4 class="text-white mb-0" style="font-weight: 600;"><i class="fa fa-magic text-danger mr-2"></i> Submit Video Effect</h4>
                                        <span class="badge badge-info" style="padding: 6px 10px;">GDrive Video / FX (.mp4, .mov, .webm)</span>
                                    </div>

                                    <form action="{{ route('user.submissions.effect.store') }}" method="POST" id="effectSubmissionForm">
                                        @csrf

                                        <!-- Step 1: Link & Scan Button -->
                                        <div class="form-group mb-3">
                                            <label class="text-white">Google Drive Effect Link <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="drive_url" id="effect_drive_url" class="form-control" placeholder="Paste link (e.g. https://drive.google.com/file/d/1XyZ.../view)" value="{{ old('drive_url') }}" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-info font-weight-bold" type="button" onclick="scanGoogleDriveLink('effect')" id="btn_scan_effect">
                                                        <i class="fa fa-search mr-1"></i> Scan File
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Direct Google Drive file link. Make sure link sharing is set to "Anyone with the link".</small>
                                        </div>

                                        <!-- Live Preview Container (Auto-populated) -->
                                        <div id="effect_preview_box" class="scan-preview-box">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="text-info font-weight-bold"><i class="fa fa-play-circle mr-1"></i> Effect Preview</span>
                                                <span id="effect_badge_info" class="badge badge-success font-12"></span>
                                            </div>
                                            <div id="effect_player_container" class="mb-2"></div>
                                        </div>

                                        <!-- Step 2: Auto-Filled Fields -->
                                        <div class="form-group mb-3">
                                            <label class="text-white">Effect Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" id="effect_title" class="form-control" placeholder="e.g. Glitch Transition Flash FX" value="{{ old('title') }}" required>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">Category</label>
                                                <input type="text" name="category" id="effect_category" class="form-control" placeholder="e.g. Transitions, Glitch, VFX, Lights" value="{{ old('category', 'Transitions') }}">
                                            </div>
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">License Price ($ USD)</label>
                                                <input type="number" step="0.01" min="0" name="license_price" class="form-control" placeholder="0.00 (Leave 0 for Free)" value="{{ old('license_price', '0.00') }}">
                                            </div>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label class="text-white">Description (Optional)</label>
                                            <textarea name="description" id="effect_description" class="form-control" rows="2" placeholder="Brief instructions, frame rate, or blend mode...">{{ old('description') }}</textarea>
                                        </div>

                                        <button type="submit" class="vfx-item-btn-danger text-uppercase w-100" style="padding: 13px; font-weight: 700; border-radius: 6px;">
                                            <i class="fa fa-paper-plane mr-2"></i> Submit Effect for Approval
                                        </button>
                                    </form>
                                </div>


                                <!-- ========================================================
                                     4. PHOTO SUBMISSION TAB
                                     ======================================================== -->
                                <div class="tab-pane fade" id="form-photo" role="tabpanel">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h4 class="text-white mb-0" style="font-weight: 600;"><i class="fa fa-camera text-danger mr-2"></i> Submit Stock Photo</h4>
                                        <span class="badge badge-info" style="padding: 6px 10px;">GDrive Image (.jpg, .png, .webp)</span>
                                    </div>

                                    <form action="{{ route('user.submissions.photo.store') }}" method="POST" id="photoSubmissionForm">
                                        @csrf

                                        <!-- Step 1: Link & Scan Button -->
                                        <div class="form-group mb-3">
                                            <label class="text-white">Google Drive Photo Link <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="drive_url" id="photo_drive_url" class="form-control" placeholder="Paste link (e.g. https://drive.google.com/file/d/1XyZ.../view)" value="{{ old('drive_url') }}" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-info font-weight-bold" type="button" onclick="scanGoogleDriveLink('photo')" id="btn_scan_photo">
                                                        <i class="fa fa-search mr-1"></i> Scan File
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Direct Google Drive image link. Make sure link sharing is set to "Anyone with the link".</small>
                                        </div>

                                        <!-- Live Preview Container (Auto-populated) -->
                                        <div id="photo_preview_box" class="scan-preview-box text-center">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="text-info font-weight-bold"><i class="fa fa-image mr-1"></i> Photo Preview</span>
                                                <span id="photo_badge_info" class="badge badge-success font-12"></span>
                                            </div>
                                            <div id="photo_player_container" class="mb-2"></div>
                                        </div>

                                        <!-- Step 2: Auto-Filled Fields -->
                                        <div class="form-group mb-3">
                                            <label class="text-white">Photo Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" id="photo_title" class="form-control" placeholder="e.g. Sunset Mountain Landscape" value="{{ old('title') }}" required>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">Category</label>
                                                <input type="text" name="category" id="photo_category" class="form-control" placeholder="e.g. Nature, Travel, Animals, City" value="{{ old('category', 'Nature') }}">
                                            </div>
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="text-white">License Price ($ USD)</label>
                                                <input type="number" step="0.01" min="0" name="license_price" class="form-control" placeholder="0.00 (Leave 0 for Free)" value="{{ old('license_price', '0.00') }}">
                                            </div>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label class="text-white">Description (Optional)</label>
                                            <textarea name="description" id="photo_description" class="form-control" rows="2" placeholder="Brief details about location, camera, or composition...">{{ old('description') }}</textarea>
                                        </div>

                                        <button type="submit" class="vfx-item-btn-danger text-uppercase w-100" style="padding: 13px; font-weight: 700; border-radius: 6px;">
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

<script>
/**
 * AJAX Google Drive Link Scanner
 * Automatically fetches title, file size, format and displays live preview.
 */
function scanGoogleDriveLink(type) {
    const urlInput = document.getElementById(type + '_drive_url');
    const btnScan = document.getElementById('btn_scan_' + type);
    const previewBox = document.getElementById(type + '_preview_box');
    const playerContainer = document.getElementById(type + '_player_container');
    const badgeInfo = document.getElementById(type + '_badge_info');
    const titleInput = document.getElementById(type + '_title');

    const driveUrl = urlInput.value.trim();
    if (!driveUrl) {
        alert('Please enter a Google Drive link first.');
        urlInput.focus();
        return;
    }

    // Show loading state on button
    const origBtnHtml = btnScan.innerHTML;
    btnScan.disabled = true;
    btnScan.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Scanning...';

    fetch("{{ route('user.submissions.scan-link') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({ drive_url: driveUrl })
    })
    .then(response => response.json())
    .then(data => {
        btnScan.disabled = false;
        btnScan.innerHTML = origBtnHtml;

        if (!data.success) {
            alert(data.message || 'Could not scan Google Drive link. Please verify the URL.');
            return;
        }

        // 1. Auto-fill title
        if (titleInput && (!titleInput.value || titleInput.value === '')) {
            titleInput.value = data.title || data.name;
        } else if (titleInput) {
            titleInput.value = data.title || data.name;
        }

        // 2. Show file info badge
        badgeInfo.innerText = (data.formatted_size !== 'Unknown' ? data.formatted_size + ' • ' : '') + (data.mime_type ? data.mime_type.split('/')[1].toUpperCase() : 'Ready');

        // 3. Render type-specific preview player
        playerContainer.innerHTML = '';
        if (type === 'audio') {
            playerContainer.innerHTML = `
                <iframe src="${data.preview_url}" style="width: 100%; height: 90px; border: 0; border-radius: 6px; background: #000;" allow="autoplay"></iframe>
            `;
        } else if (type === 'film' || type === 'effect') {
            playerContainer.innerHTML = `
                <iframe src="${data.preview_url}" style="width: 100%; height: 260px; border: 0; border-radius: 6px; background: #000;" allow="autoplay" allowfullscreen></iframe>
            `;
        } else if (type === 'photo') {
            playerContainer.innerHTML = `
                <img src="${data.thumbnail_url}" style="max-height: 240px; max-width: 100%; border-radius: 6px; object-fit: contain; box-shadow: 0 4px 12px rgba(0,0,0,0.5);" alt="Preview">
            `;
        }

        // 4. Reveal preview box with smooth animation
        previewBox.style.display = 'block';
    })
    .catch(err => {
        btnScan.disabled = false;
        btnScan.innerHTML = origBtnHtml;
        alert('Failed to connect to scanner service. Please check your link.');
    });
}

// Auto-scan on paste or Enter key
['audio', 'film', 'effect', 'photo'].forEach(type => {
    const input = document.getElementById(type + '_drive_url');
    if (input) {
        input.addEventListener('paste', () => {
            setTimeout(() => scanGoogleDriveLink(type), 250);
        });
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                scanGoogleDriveLink(type);
            }
        });
    }
});

// Tab navigation handler
document.addEventListener('DOMContentLoaded', function() {
    const tabLinks = document.querySelectorAll('#submitFormTabs .nav-link');
    const tabPanes = document.querySelectorAll('#submitFormTabsContent .tab-pane');

    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            tabLinks.forEach(t => t.classList.remove('active'));
            tabPanes.forEach(p => {
                p.classList.remove('show', 'active');
                p.style.display = 'none';
            });

            this.classList.add('active');
            const targetId = this.getAttribute('href');
            const targetPane = document.querySelector(targetId);
            if (targetPane) {
                targetPane.classList.add('show', 'active');
                targetPane.style.display = 'block';
            }
        });
    });

    if (window.location.hash) {
        const hashLink = document.querySelector('#submitFormTabs .nav-link[href="' + window.location.hash + '"]');
        if (hashLink) {
            hashLink.click();
            return;
        }
    }

    const activeTab = document.querySelector('#submitFormTabs .nav-link.active');
    if (activeTab) {
        const initialTarget = document.querySelector(activeTab.getAttribute('href'));
        if (initialTarget) {
            initialTarget.classList.add('show', 'active');
            initialTarget.style.display = 'block';
        }
    }
});
</script>

@endsection
