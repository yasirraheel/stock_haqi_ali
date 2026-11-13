@extends('site_app')

@section('head_title', 'Add Live TV | ' . getcong('site_name'))

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid"><div class="row"><div class="col-xl-12">
        <h2>Add Live TV</h2>
        <nav id="breadcrumbs"><ul>
            <li><a href="{{ URL::to('/') }}">{{ trans('words.home') }}</a></li>
            <li><a href="{{ route('user.live-tv.index') }}">My Live TV</a></li>
            <li>Add</li>
        </ul></nav>
    </div></div></div>
</div>

<div class="vfx-item-ptb vfx-item-info">
    <div class="container"><div class="row justify-content-center"><div class="col-lg-9 col-md-11 col-sm-12">
        <div class="alert alert-info" style="background: rgba(53,184,224,.15); border:1px solid #35b8e0; color:#e0f7fa; border-radius:8px;">
            <i class="fa fa-info-circle mr-2"></i>
            Submit your stream details below. <strong class="text-white">Your Live TV will stay private until an admin reviews and approves it.</strong>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" style="background: rgba(243,73,67,.15); border:1px solid #f34943; color:#ffcdd2; border-radius:8px;">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="card live-tv-submit-card">
            <div class="card-header"><h4 class="text-white mb-0"><i class="fa fa-television text-danger mr-2"></i> Live TV Details</h4></div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('user.live-tv.store') }}">
                    @csrf
                    <div class="form-group"><label>Channel Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="channel_name" value="{{ old('channel_name') }}" placeholder="e.g. Cineworm News" required>
                    </div>
                    <div class="form-group"><label>Description</label>
                        <textarea class="form-control" name="channel_description" rows="3" placeholder="Tell us about this channel">{{ old('channel_description') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Access</label>
                            <select class="form-control" name="channel_access"><option value="Free" @selected(old('channel_access', 'Free') === 'Free')>Free</option><option value="Paid" @selected(old('channel_access') === 'Paid')>Paid</option></select>
                        </div>
                        <div class="col-md-6 form-group"><label>Category <span class="text-danger">*</span></label>
                            <select class="form-control" name="tv_category" required><option value="">Select category</option>@foreach($cat_list as $cat)<option value="{{ $cat->id }}" @selected(old('tv_category') == $cat->id)>{{ $cat->category_name }}</option>@endforeach</select>
                        </div>
                    </div>

                    <div class="form-group"><label>Stream Type <span class="text-danger">*</span></label>
                        <select class="form-control" name="channel_url_type" id="channel_url_type" required>
                            <option value="HLS" @selected(old('channel_url_type', 'HLS') === 'HLS')>HLS (.m3u8)</option>
                            <option value="Embed" @selected(old('channel_url_type') === 'Embed')>Embed Code</option>
                        </select>
                    </div>
                    <div id="hls-fields">
                        <div class="form-group"><label>Stream URL <span class="text-danger">*</span></label><input class="form-control" name="channel_url" value="{{ old('channel_url') }}" placeholder="https://example.com/live/stream.m3u8"></div>
                        <div class="row"><div class="col-md-6 form-group"><label>Backup URL 2</label><input class="form-control" name="channel_url2" value="{{ old('channel_url2') }}"></div><div class="col-md-6 form-group"><label>Backup URL 3</label><input class="form-control" name="channel_url3" value="{{ old('channel_url3') }}"></div></div>
                    </div>
                    <div id="embed-fields" style="display:none"><div class="form-group"><label>Embed Code / URL <span class="text-danger">*</span></label><textarea class="form-control" name="channel_url_embed" rows="4" placeholder="Paste the safe embed code or player URL">{{ old('channel_url_embed') }}</textarea></div></div>

                    <div class="form-group"><label>Thumbnail Path or Image URL <span class="text-danger">*</span></label>
                        <input class="form-control" name="channel_thumb" value="{{ old('channel_thumb') }}" placeholder="upload/live_tv/channel.jpg or https://..." required>
                        <small class="text-muted">Use an existing Cineworm upload path or a publicly accessible image URL.</small>
                    </div>
                    <button class="vfx-item-btn-danger text-uppercase w-100" style="padding:13px; font-weight:700; border-radius:6px;" type="submit"><i class="fa fa-paper-plane mr-2"></i> Submit for Admin Review</button>
                </form>
            </div>
        </div>
    </div></div></div>
</div>

<style>
.live-tv-submit-card { background:#1a2234; border:1px solid #2a3447; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,.4); }
.live-tv-submit-card .card-header { background:#121824; border-bottom:1px solid #2a3447; border-radius:10px 10px 0 0; padding:18px 22px; }
.live-tv-submit-card label { color:#fff; font-weight:600; }
.live-tv-submit-card .form-control { background:#121824!important; border:1px solid #2a3447!important; color:#fff!important; border-radius:6px!important; min-height:44px; }
.live-tv-submit-card .form-control:focus { border-color:#ff3366!important; box-shadow:0 0 0 2px rgba(255,51,102,.2)!important; }
.live-tv-submit-card option { background:#121824; color:#fff; }
</style>
<script>
function toggleLiveTvStreamFields() {
    const embed = document.getElementById('channel_url_type').value === 'Embed';
    document.getElementById('hls-fields').style.display = embed ? 'none' : 'block';
    document.getElementById('embed-fields').style.display = embed ? 'block' : 'none';
    document.querySelector('[name="channel_url"]').required = !embed;
    document.querySelector('[name="channel_url_embed"]').required = embed;
}
document.getElementById('channel_url_type').addEventListener('change', toggleLiveTvStreamFields);
toggleLiveTvStreamFields();
</script>
@endsection
