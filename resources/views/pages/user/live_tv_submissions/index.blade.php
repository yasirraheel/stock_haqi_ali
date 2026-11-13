@extends('site_app')

@section('head_title', 'My Live TV Submissions | ' . getcong('site_name'))

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid"><div class="row"><div class="col-xl-12">
        <h2>My Live TV Submissions</h2>
        <nav id="breadcrumbs"><ul><li><a href="{{ URL::to('/') }}">{{ trans('words.home') }}</a></li><li><a href="{{ URL::to('dashboard') }}">Dashboard</a></li><li>My Live TV</li></ul></nav>
    </div></div></div>
</div>
<div class="vfx-item-ptb vfx-item-info"><div class="container"><div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap:15px;"><div><h3 class="text-white mb-1">My Live TV</h3><p class="text-muted mb-0">Your channels become public only after admin approval.</p></div><a href="{{ route('user.live-tv.create') }}" class="vfx-item-btn-danger text-uppercase" style="padding:12px 24px; border-radius:4px;"><i class="fa fa-plus-circle"></i> Add Live TV</a></div>
    <div class="card live-tv-submit-card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-dark table-hover mb-0"><thead><tr><th>Channel</th><th>Type</th><th>Submitted</th><th>Status</th></tr></thead><tbody>
    @forelse($submissions as $submission)
        <tr><td><strong>{{ $submission->channel_name }}</strong><br><small class="text-muted">{{ IlluminateSupportStr::limit($submission->channel_url, 70) }}</small></td><td>{{ $submission->channel_url_type }}</td><td>{{ $submission->created_at->format('M d, Y') }}</td><td>@if($submission->status === 'approved')<span class="badge badge-success">Approved & Published</span>@elseif($submission->status === 'rejected')<span class="badge badge-danger">Rejected</span>@else<span class="badge badge-warning">Pending Review</span>@endif</td></tr>
    @empty
        <tr><td colspan="4" class="text-center text-muted py-5">You have not submitted a Live TV channel yet.</td></tr>
    @endforelse
    </tbody></table></div></div></div>
</div></div></div></div>
<style>
.live-tv-submit-card { background:#1a2234; border:1px solid #2a3447; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,.35); }
.live-tv-submit-card th { background:#121824; color:#fff; border-color:#2a3447; }
.live-tv-submit-card td { border-color:#2a3447; color:#e5e7eb; vertical-align:middle; }
</style>
@endsection
