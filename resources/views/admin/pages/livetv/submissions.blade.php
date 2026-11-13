@extends('admin.admin_app')

@section('content')
<div class="content-page"><div class="content"><div class="container-fluid"><div class="row"><div class="col-12">
    <div class="card-box table-responsive">
        <div class="row align-items-center mb-3">
            <div class="col-md-5"><h4 class="header-title m-0"><i class="fa fa-television"></i> Live TV User Submissions</h4></div>
            <div class="col-md-7 text-right"><a href="{{ URL::to('admin/live_tv') }}" class="btn btn-secondary mr-2"><i class="fa fa-list"></i> All Live TV</a><a href="{{ URL::to('admin/live_tv/add_live_tv') }}" class="btn btn-success"><i class="fa fa-plus"></i> Add Manually</a></div>
        </div>
        <div class="row mb-3"><div class="col-md-8"><form class="form-inline" method="GET"><input class="form-control mr-2" name="s" value="{{ request('s') }}" placeholder="Search channel or user"><select class="form-control mr-2" name="status"><option value="all">All statuses</option><option value="pending" @selected(request('status') === 'pending')>Pending ({{ $pendingCount }})</option><option value="approved" @selected(request('status') === 'approved')>Approved ({{ $approvedCount }})</option><option value="rejected" @selected(request('status') === 'rejected')>Rejected ({{ $rejectedCount }})</option></select><button class="btn btn-info" type="submit"><i class="fa fa-search"></i> Filter</button></form></div></div>
        <div class="row">
        @forelse($submissions as $submission)
            <div class="col-lg-6 col-xl-4 mb-4"><div class="submission-card">
                <div class="submission-card-head"><div><h5 class="mb-1">{{ $submission->channel_name }}</h5><small>{{ optional($submission->user)->name ?: 'Unknown user' }} · {{ optional($submission->user)->email }}</small></div><span class="badge badge-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($submission->status) }}</span></div>
                @if($submission->channel_thumb)<img class="submission-thumb" src="{{ live_tv_thumb_url($submission->channel_thumb) }}" alt="{{ $submission->channel_name }}" onerror="this.style.display='none'">@endif
                <div class="submission-card-body"><div class="small text-muted mb-2">{{ $submission->channel_url_type }} · {{ $submission->channel_access }} · {{ optional($submission->created_at)->format('M d, Y H:i') }}</div><p class="mb-2">{{ Illuminate\Support\Str::limit($submission->channel_description ?: 'No description provided.', 180) }}</p><div class="stream-url"><strong>Stream:</strong> {{ Illuminate\Support\Str::limit($submission->channel_url, 95) }}</div>
                @if($submission->status === 'pending')<div class="mt-3 d-flex" style="gap:8px"><form method="POST" action="{{ route('admin.live-tv.submissions.approve', $submission->id) }}" class="flex-fill">@csrf<button class="btn btn-success btn-block" onclick="return confirm('Approve and publish this Live TV channel?')"><i class="fa fa-check"></i> Approve & Publish</button></form><form method="POST" action="{{ route('admin.live-tv.submissions.reject', $submission->id) }}" class="flex-fill">@csrf<button class="btn btn-danger btn-block" onclick="return confirm('Reject this Live TV submission?')"><i class="fa fa-times"></i> Reject</button></form></div>@elseif($submission->status === 'approved' && $submission->live_tv_id)<a href="{{ URL::to('admin/live_tv/edit_live_tv/'.$submission->live_tv_id) }}" class="btn btn-outline-info btn-sm mt-2"><i class="fa fa-edit"></i> Edit published channel</a>@endif
                </div>
            </div></div>
        @empty
            <div class="col-12"><div class="alert alert-info">No Live TV submissions found.</div></div>
        @endforelse
        </div>
    </div>
    <nav class="paging_simple_numbers">@include('admin.pagination', ['paginator' => $submissions])</nav>
</div></div></div></div>
<style>
.submission-card { background:#1a2234; border:1px solid #2a3447; border-radius:8px; overflow:hidden; height:100%; color:#fff; }
.submission-card-head { background:#121824; border-bottom:1px solid #2a3447; padding:14px; display:flex; justify-content:space-between; gap:10px; }
.submission-card-head h5 { color:#fff; font-weight:600; }
.submission-card-head small { color:#94a3b8; }
.submission-thumb { width:100%; height:150px; object-fit:cover; background:#0b0f17; }
.submission-card-body { padding:14px; }
.submission-card-body p { color:#d1d5db; min-height:42px; }
.stream-url { padding:8px; background:#121824; border:1px solid #2a3447; border-radius:4px; color:#b8c3d1; font-size:12px; word-break:break-all; }
</style>
@endsection
