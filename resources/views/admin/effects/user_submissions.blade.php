@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">

                        <div class="row mb-3 align-items-center">
                            <div class="col-md-6">
                                <h4 class="m-t-0 header-title"><i class="fa fa-magic text-info"></i> <b>Effects Submitted by Users</b></h4>
                                <p class="text-muted font-13 m-b-10">Review and approve user-submitted Google Drive video effects.</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <span class="badge badge-warning p-2 font-13 mr-2"><i class="fa fa-clock-o"></i> Pending: {{ $pendingCount }}</span>
                                <span class="badge badge-success p-2 font-13"><i class="fa fa-check"></i> Approved: {{ $approvedCount }}</span>
                            </div>
                        </div>

                        <!-- Filter and Search Bar -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.effects.user-submissions') }}" class="form-inline">
                                    <div class="form-group mr-2">
                                        <label class="mr-2 text-white">Status:</label>
                                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                                            <option value="pending" {{ request('status') == 'pending' || !request()->has('status') ? 'selected' : '' }}>Pending Review</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Approved & Active</option>
                                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <input type="text" name="s" class="form-control form-control-sm" placeholder="Search effect or user..." value="{{ request('s') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @if(Session::has('flash_message'))
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {{ Session::get('flash_message') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Effect Title</th>
                                        <th>Submitted By</th>
                                        <th class="text-center" style="width: 100px;">Preview</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-center" style="min-width: 150px;">Approval Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($effects as $i => $effect)
                                        <tr>
                                            <td>{{ $effects->firstItem() + $i }}</td>
                                            <td>
                                                <strong style="color: #fff;">{{ $effect->title }}</strong>
                                                @if($effect->description)
                                                    <small class="text-muted d-block" style="max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $effect->description }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($effect->user)
                                                    <strong class="text-info">{{ $effect->user->name }}</strong>
                                                    <small class="text-muted d-block">{{ $effect->user->email }}</small>
                                                @else
                                                    <span class="text-muted">User #{{ $effect->added_by }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger waves-effect waves-light"
                                                    onclick="previewEffect('{{ $effect->drive_file_id }}', '{{ $effect->effect_url }}', '{{ addslashes($effect->title) }}')"
                                                    data-toggle="tooltip" title="Open Video Preview Popup">
                                                    <i class="fa fa-play-circle"></i> Preview
                                                </button>
                                            </td>
                                            <td>{{ $effect->category ?: 'General' }}</td>
                                            <td>
                                                @if($effect->license_price > 0)
                                                    <span class="badge badge-warning">${{ number_format($effect->license_price, 2) }}</span>
                                                @else
                                                    <span class="badge badge-success">Free</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($effect->status === 'pending')
                                                    <span class="badge badge-warning"><i class="fa fa-clock-o"></i> Pending</span>
                                                @elseif($effect->status === 'rejected')
                                                    <span class="badge badge-danger"><i class="fa fa-times-circle"></i> Rejected</span>
                                                @else
                                                    <span class="badge badge-success"><i class="fa fa-check-circle"></i> Approved</span>
                                                @endif
                                            </td>
                                            <td>{{ $effect->created_at->format('M d, Y') }}</td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                @if($effect->status !== 'active')
                                                    <a href="{{ route('admin.effects.user-submissions.approve', $effect->id) }}" class="btn btn-sm btn-success m-r-5" data-toggle="tooltip" title="Approve & Publish Live" onclick="return confirm('Approve this effect and publish to library?')">
                                                        <i class="fa fa-check"></i> Approve
                                                    </a>
                                                @endif

                                                @if($effect->status !== 'rejected')
                                                    <a href="{{ route('admin.effects.user-submissions.reject', $effect->id) }}" class="btn btn-sm btn-warning m-r-5" data-toggle="tooltip" title="Reject Submission" onclick="return confirm('Reject this effect submission?')">
                                                        <i class="fa fa-ban"></i> Reject
                                                    </a>
                                                @endif

                                                <a href="{{ route('admin.effects.edit', $effect->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Edit Details">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">No user-submitted effects found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <nav class="paging_simple_numbers">
                            @include('admin.pagination', ['paginator' => $effects])
                        </nav>

                    </div>
                </div>
            </div>
        </div>
        @include("admin.copyright")
    </div>
</div>

<!-- Effect Preview Modal -->
<div id="effectPreviewModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: #1a2234; border: 1px solid #32383e; color: #fff; border-radius: 8px;">
            <div class="modal-header" style="border-bottom: 1px solid #32383e;">
                <h5 class="modal-title mt-0" id="effectModalTitle"><i class="fa fa-magic text-info mr-1"></i> Effect Preview</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="closeEffectPreview()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3 text-center" style="background: #0d1117;">
                <div id="effectPlayerWrapper" style="width: 100%; height: 420px; display: none;">
                    <video id="modalEffectVideoPlayer" controls autoplay style="width: 100%; height: 100%; border-radius: 6px; background: #000; outline: none;"></video>
                </div>
                <div id="effectIframeWrapper" style="width: 100%; height: 420px; display: none;">
                    <iframe id="modalEffectIframe" src="" style="width: 100%; height: 100%; border: 0; border-radius: 6px;" allow="autoplay" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>

<script type="text/javascript">
    function previewEffect(driveFileId, effectUrl, title) {
        document.getElementById('effectModalTitle').innerText = title ? title : 'Effect Preview';
        var videoPlayer = document.getElementById('modalEffectVideoPlayer');
        var playerWrapper = document.getElementById('effectPlayerWrapper');
        var iframeWrapper = document.getElementById('effectIframeWrapper');
        var iframe = document.getElementById('modalEffectIframe');

        if (driveFileId && driveFileId !== '') {
            if (videoPlayer) {
                videoPlayer.pause();
                videoPlayer.src = '';
            }
            playerWrapper.style.display = 'none';
            iframe.src = 'https://drive.google.com/file/d/' + driveFileId + '/preview';
            iframeWrapper.style.display = 'block';
        } else if (effectUrl && effectUrl !== '') {
            iframe.src = '';
            iframeWrapper.style.display = 'none';
            videoPlayer.src = effectUrl;
            playerWrapper.style.display = 'block';
            videoPlayer.play().catch(function() {});
        }

        $('#effectPreviewModal').modal('show');
    }

    function closeEffectPreview() {
        var videoPlayer = document.getElementById('modalEffectVideoPlayer');
        var iframe = document.getElementById('modalEffectIframe');
        if (videoPlayer) {
            videoPlayer.pause();
            videoPlayer.src = '';
        }
        if (iframe) {
            iframe.src = '';
        }
    }

    $('#effectPreviewModal').on('hidden.bs.modal', function () {
        closeEffectPreview();
    });
</script>

@endsection
