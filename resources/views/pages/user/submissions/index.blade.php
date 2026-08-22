@extends('site_app')

@section('head_title', 'My Stock Media Submissions | ' . getcong('site_name'))

@section('content')

    <!-- Start Breadcrumb -->
    <div class="breadcrumb-section bg-xs"
        style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <h2>My Stock Media Submissions</h2>
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="{{ URL::to('/') }}" title="{{ trans('words.home') }}">{{ trans('words.home') }}</a></li>
                            <li><a href="{{ URL::to('dashboard') }}" title="Dashboard">Dashboard</a></li>
                            <li>Submissions</li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="vfx-item-ptb vfx-item-info">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <!-- Top Action Bar & Stats -->
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap: 15px;">
                        <div>
                            <h3 class="text-white mb-1" style="font-weight: 600;">Media Submissions Hub</h3>
                            <p class="text-muted mb-0">Track and manage your submitted Audio, Film Stock, Effects, and Photos.</p>
                        </div>
                        <a href="{{ route('user.submissions.create') }}" class="vfx-item-btn-danger text-uppercase" style="padding: 12px 24px; font-weight: 600; border-radius: 4px; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fa fa-plus-circle"></i> Submit New Media
                        </a>
                    </div>

                    <!-- Summary Stat Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                            <div class="p-3" style="background: #1a2234; border: 1px solid #2a3447; border-radius: 8px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <small class="text-muted text-uppercase">Total Submitted</small>
                                        <h2 class="text-white mb-0" style="font-weight: 700;">{{ $totalSubmissions }}</h2>
                                    </div>
                                    <div style="width: 48px; height: 48px; background: rgba(53, 184, 224, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa fa-cloud-upload text-info" style="font-size: 22px;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                            <div class="p-3" style="background: #1a2234; border: 1px solid #2a3447; border-radius: 8px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <small class="text-muted text-uppercase">Pending Review</small>
                                        <h2 class="text-warning mb-0" style="font-weight: 700;">{{ $pendingCount }}</h2>
                                    </div>
                                    <div style="width: 48px; height: 48px; background: rgba(255, 184, 34, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa fa-clock-o text-warning" style="font-size: 22px;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                            <div class="p-3" style="background: #1a2234; border: 1px solid #2a3447; border-radius: 8px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <small class="text-muted text-uppercase">Approved & Active</small>
                                        <h2 class="text-success mb-0" style="font-weight: 700;">{{ $approvedCount }}</h2>
                                    </div>
                                    <div style="width: 48px; height: 48px; background: rgba(16, 196, 105, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa fa-check-circle text-success" style="font-size: 22px;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                        }
                        .submission-custom-tabs .nav-link {
                            color: #94a3b8 !important;
                            background: transparent !important;
                            border: none !important;
                            padding: 14px 22px !important;
                            font-weight: 600 !important;
                            font-size: 15px !important;
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
                    </style>

                    <!-- Media Categories Tabs -->
                    <div class="card" style="background: #1a2234; border: 1px solid #2a3447; border-radius: 8px;">
                        <div class="card-header p-0" style="background: #121824; border-bottom: 1px solid #2a3447; border-radius: 8px 8px 0 0;">
                            <ul class="nav nav-tabs border-0 submission-custom-tabs" id="submissionTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="audio-tab" data-toggle="tab" href="#audio" role="tab">
                                        <i class="fa fa-music mr-2"></i> Audio Tracks ({{ $audios->count() }})
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="film-stock-tab" data-toggle="tab" href="#film-stock" role="tab">
                                        <i class="fa fa-film mr-2"></i> Film Stock ({{ $filmStocks->count() }})
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="effects-tab" data-toggle="tab" href="#effects" role="tab">
                                        <i class="fa fa-magic mr-2"></i> Effects ({{ $effects->count() }})
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="photos-tab" data-toggle="tab" href="#photos" role="tab">
                                        <i class="fa fa-camera mr-2"></i> Photos ({{ $photos->count() }})
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-4">
                            <div class="tab-content" id="submissionTabsContent">

                                <!-- Audio Tab -->
                                <div class="tab-pane fade show active" id="audio" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-dark table-hover mb-0" style="background: transparent;">
                                            <thead>
                                                <tr style="border-bottom: 1px solid #2a3447; color: #aaa;">
                                                    <th>Title</th>
                                                    <th>Genre</th>
                                                    <th>License Price</th>
                                                    <th>Submitted Date</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($audios as $audio)
                                                    <tr>
                                                        <td>
                                                            <strong class="text-white">{{ $audio->title }}</strong>
                                                        </td>
                                                        <td>{{ $audio->genre ?: 'General' }}</td>
                                                        <td>{{ $audio->license_price > 0 ? '$' . number_format($audio->license_price, 2) : 'Free' }}</td>
                                                        <td>{{ $audio->created_at->format('M d, Y') }}</td>
                                                        <td class="text-center">
                                                            @if($audio->status === 'pending')
                                                                <span class="badge badge-warning" style="padding: 6px 12px; font-size: 12px; background-color: #ffaa00; color: #000;"><i class="fa fa-clock-o"></i> Pending Approval</span>
                                                            @elseif($audio->status === 'rejected')
                                                                <span class="badge badge-danger" style="padding: 6px 12px; font-size: 12px; background-color: #f34943;"><i class="fa fa-times-circle"></i> Rejected</span>
                                                            @else
                                                                <span class="badge badge-success" style="padding: 6px 12px; font-size: 12px; background-color: #10c469;"><i class="fa fa-check-circle"></i> Approved & Active</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4 text-muted">You have not submitted any Audio tracks yet.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Film Stock Tab -->
                                <div class="tab-pane fade" id="film-stock" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-dark table-hover mb-0" style="background: transparent;">
                                            <thead>
                                                <tr style="border-bottom: 1px solid #2a3447; color: #aaa;">
                                                    <th>Video Name</th>
                                                    <th>Format</th>
                                                    <th>Submitted Date</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($filmStocks as $film)
                                                    <tr>
                                                        <td><strong class="text-white">{{ $film->name }}</strong></td>
                                                        <td>{{ strtoupper($film->mime_type ?: 'MP4') }}</td>
                                                        <td>{{ $film->created_at->format('M d, Y') }}</td>
                                                        <td class="text-center">
                                                            @if($film->status === 'pending')
                                                                <span class="badge badge-warning" style="padding: 6px 12px; font-size: 12px; background-color: #ffaa00; color: #000;"><i class="fa fa-clock-o"></i> Pending Approval</span>
                                                            @elseif($film->status === 'blocked' || $film->status === 'rejected')
                                                                <span class="badge badge-danger" style="padding: 6px 12px; font-size: 12px; background-color: #f34943;"><i class="fa fa-times-circle"></i> Rejected</span>
                                                            @else
                                                                <span class="badge badge-success" style="padding: 6px 12px; font-size: 12px; background-color: #10c469;"><i class="fa fa-check-circle"></i> Approved & Active</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted">You have not submitted any Film Stock videos yet.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Effects Tab -->
                                <div class="tab-pane fade" id="effects" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-dark table-hover mb-0" style="background: transparent;">
                                            <thead>
                                                <tr style="border-bottom: 1px solid #2a3447; color: #aaa;">
                                                    <th>Effect Title</th>
                                                    <th>Category</th>
                                                    <th>License Price</th>
                                                    <th>Submitted Date</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($effects as $effect)
                                                    <tr>
                                                        <td><strong class="text-white">{{ $effect->title }}</strong></td>
                                                        <td>{{ $effect->category ?: 'General' }}</td>
                                                        <td>{{ $effect->license_price > 0 ? '$' . number_format($effect->license_price, 2) : 'Free' }}</td>
                                                        <td>{{ $effect->created_at->format('M d, Y') }}</td>
                                                        <td class="text-center">
                                                            @if($effect->status === 'pending')
                                                                <span class="badge badge-warning" style="padding: 6px 12px; font-size: 12px; background-color: #ffaa00; color: #000;"><i class="fa fa-clock-o"></i> Pending Approval</span>
                                                            @elseif($effect->status === 'rejected')
                                                                <span class="badge badge-danger" style="padding: 6px 12px; font-size: 12px; background-color: #f34943;"><i class="fa fa-times-circle"></i> Rejected</span>
                                                            @else
                                                                <span class="badge badge-success" style="padding: 6px 12px; font-size: 12px; background-color: #10c469;"><i class="fa fa-check-circle"></i> Approved & Active</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4 text-muted">You have not submitted any Effects yet.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Photos Tab -->
                                <div class="tab-pane fade" id="photos" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-dark table-hover mb-0" style="background: transparent;">
                                            <thead>
                                                <tr style="border-bottom: 1px solid #2a3447; color: #aaa;">
                                                    <th>Photo Title</th>
                                                    <th>Category</th>
                                                    <th>License Price</th>
                                                    <th>Submitted Date</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($photos as $photo)
                                                    <tr>
                                                        <td><strong class="text-white">{{ $photo->title }}</strong></td>
                                                        <td>{{ $photo->category ?: 'General' }}</td>
                                                        <td>{{ $photo->license_price > 0 ? '$' . number_format($photo->license_price, 2) : 'Free' }}</td>
                                                        <td>{{ $photo->created_at->format('M d, Y') }}</td>
                                                        <td class="text-center">
                                                            @if($photo->status === 'pending')
                                                                <span class="badge badge-warning" style="padding: 6px 12px; font-size: 12px; background-color: #ffaa00; color: #000;"><i class="fa fa-clock-o"></i> Pending Approval</span>
                                                            @elseif($photo->status === 'rejected')
                                                                <span class="badge badge-danger" style="padding: 6px 12px; font-size: 12px; background-color: #f34943;"><i class="fa fa-times-circle"></i> Rejected</span>
                                                            @else
                                                                <span class="badge badge-success" style="padding: 6px 12px; font-size: 12px; background-color: #10c469;"><i class="fa fa-check-circle"></i> Approved & Active</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4 text-muted">You have not submitted any Photos yet.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
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

@endsection
