@extends("admin.admin_app")

@section("content")

<style>
    .custom-dark-table {
        background-color: #1a2234 !important;
        border: 1px solid #2a3447 !important;
        color: #ffffff !important;
    }
    .custom-dark-table thead th {
        background-color: #121824 !important;
        border-color: #2a3447 !important;
        color: #94a3b8 !important;
        font-weight: 600 !important;
    }
    .custom-dark-table tbody tr {
        background-color: #1a2234 !important;
        border-color: #2a3447 !important;
        color: #ffffff !important;
    }
    .custom-dark-table tbody tr:hover {
        background-color: #222c42 !important;
    }
    .custom-dark-table tbody tr td {
        background-color: transparent !important;
        border-color: #2a3447 !important;
        color: #ffffff !important;
        vertical-align: middle !important;
    }
    .card-box {
        background-color: #1a2234 !important;
        border: 1px solid #2a3447 !important;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">

                        <div class="row mb-3 align-items-center">
                            <div class="col-md-6">
                                <h4 class="m-t-0 header-title"><i class="fa fa-film text-info"></i> <b>Film Stock Submitted by Users</b></h4>
                                <p class="text-muted font-13 m-b-10">Review and approve user-submitted Google Drive film stock videos.</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <span class="badge badge-warning p-2 font-13 mr-2"><i class="fa fa-clock-o"></i> Pending: {{ $pendingCount }}</span>
                                <span class="badge badge-success p-2 font-13"><i class="fa fa-check"></i> Approved: {{ $approvedCount }}</span>
                            </div>
                        </div>

                        <!-- Filter and Search Bar -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.film-stock.user-submissions') }}" class="form-inline">
                                    <div class="form-group mr-2">
                                        <label class="mr-2 text-white">Status:</label>
                                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()" style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                                            <option value="pending" {{ request('status') == 'pending' || !request()->has('status') ? 'selected' : '' }}>Pending Review</option>
                                            <option value="imported" {{ request('status') == 'imported' ? 'selected' : '' }}>Approved & Active</option>
                                            <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Rejected / Blocked</option>
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <input type="text" name="s" class="form-control form-control-sm" placeholder="Search title or user..." value="{{ request('s') }}" style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered custom-dark-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Video Name</th>
                                        <th>Submitted By</th>
                                        <th class="text-center" style="width: 100px;">Preview</th>
                                        <th>File ID</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-center" style="min-width: 150px;">Approval Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($files as $i => $file)
                                        <tr>
                                            <td>{{ $files->firstItem() + $i }}</td>
                                            <td>
                                                <strong class="text-white">{{ $file->name }}</strong>
                                            </td>
                                            <td>
                                                @if($file->user)
                                                    <strong class="text-info">{{ $file->user->name }}</strong>
                                                    <small class="text-muted d-block">{{ $file->user->email }}</small>
                                                @else
                                                    <span class="text-muted">User Submission</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-info waves-effect waves-light"
                                                    onclick="previewFilmStock('{{ $file->file_id }}', '{{ $file->stream_url }}', '{{ addslashes($file->name) }}')"
                                                    data-toggle="tooltip" title="Open Video Preview Popup">
                                                    <i class="fa fa-play-circle"></i> Preview
                                                </button>
                                            </td>
                                            <td><code style="background: #121824; color: #35b8e0; padding: 3px 8px; border-radius: 4px; border: 1px solid #2a3447;">{{ $file->file_id }}</code></td>
                                            <td>
                                                @if($file->status === 'pending')
                                                    <span class="badge badge-warning" style="padding: 5px 10px; font-size: 11px;"><i class="fa fa-clock-o"></i> Pending</span>
                                                @elseif($file->status === 'blocked')
                                                    <span class="badge badge-danger" style="padding: 5px 10px; font-size: 11px;"><i class="fa fa-ban"></i> Rejected</span>
                                                @else
                                                    <span class="badge badge-success" style="padding: 5px 10px; font-size: 11px;"><i class="fa fa-check-circle"></i> Approved</span>
                                                @endif
                                            </td>
                                            <td>{{ $file->created_at->format('M d, Y') }}</td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                @if($file->status !== 'imported')
                                                    <button type="button" class="btn btn-sm btn-success m-r-5" data-toggle="tooltip" title="Approve Video"
                                                        onclick="confirmApprove('{{ route('admin.film-stock.user-submissions.approve', $file->id) }}', '{{ addslashes($file->name) }}')">
                                                        <i class="fa fa-check"></i> Approve
                                                    </button>
                                                @endif

                                                @if($file->status !== 'blocked')
                                                    <button type="button" class="btn btn-sm btn-warning m-r-5" data-toggle="tooltip" title="Reject Video"
                                                        onclick="confirmReject('{{ route('admin.film-stock.user-submissions.reject', $file->id) }}', '{{ addslashes($file->name) }}')">
                                                        <i class="fa fa-ban"></i> Reject
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">No user-submitted Film Stock videos found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <nav class="paging_simple_numbers">
                            @include('admin.pagination', ['paginator' => $files])
                        </nav>

                    </div>
                </div>
            </div>
        </div>
        @include("admin.copyright")
    </div>
</div>

<!-- Video Preview Popup Modal -->
<div id="filmStockPreviewModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: #1a2234; border: 1px solid #32383e; color: #fff; border-radius: 8px;">
            <div class="modal-header" style="border-bottom: 1px solid #32383e;">
                <h5 class="modal-title mt-0" id="previewModalTitle"><i class="fa fa-film text-info mr-1"></i> Film Stock Video Preview</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="closeFilmStockPreview()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3 text-center" style="background: #0d1117;">
                <div id="videoPlayerWrapper" style="width: 100%; height: 420px; display: none;">
                    <video id="modalVideoPlayer" controls autoplay style="width: 100%; height: 100%; border-radius: 6px; background: #000; outline: none;"></video>
                </div>
                <div id="videoIframeWrapper" style="width: 100%; height: 420px; display: none;">
                    <iframe id="modalVideoIframe" src="" style="width: 100%; height: 100%; border: 0; border-radius: 6px;" allow="autoplay" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
    function previewFilmStock(fileId, streamUrl, fileName) {
        document.getElementById('previewModalTitle').innerText = fileName ? fileName : 'Film Stock Video Preview';
        var videoPlayer = document.getElementById('modalVideoPlayer');
        var playerWrapper = document.getElementById('videoPlayerWrapper');
        var iframeWrapper = document.getElementById('videoIframeWrapper');
        var iframe = document.getElementById('modalVideoIframe');

        if (fileId && fileId !== '') {
            if (videoPlayer) {
                videoPlayer.pause();
                videoPlayer.src = '';
            }
            playerWrapper.style.display = 'none';
            iframe.src = 'https://drive.google.com/file/d/' + fileId + '/preview';
            iframeWrapper.style.display = 'block';
        } else if (streamUrl && streamUrl !== '') {
            iframe.src = '';
            iframeWrapper.style.display = 'none';
            videoPlayer.src = streamUrl;
            playerWrapper.style.display = 'block';
            videoPlayer.play().catch(function() {});
        }

        $('#filmStockPreviewModal').modal('show');
    }

    function closeFilmStockPreview() {
        var videoPlayer = document.getElementById('modalVideoPlayer');
        var iframe = document.getElementById('modalVideoIframe');
        if (videoPlayer) {
            videoPlayer.pause();
            videoPlayer.src = '';
        }
        if (iframe) {
            iframe.src = '';
        }
    }

    $('#filmStockPreviewModal').on('hidden.bs.modal', function () {
        closeFilmStockPreview();
    });

    function confirmApprove(url, title) {
        Swal.fire({
            title: 'Approve Submission?',
            text: 'Are you sure you want to approve "' + title + '" and add it to the library?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10c469',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-check"></i> Yes, Approve',
            cancelButtonText: 'Cancel',
            background: '#1a2234',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    function confirmReject(url, title) {
        Swal.fire({
            title: 'Reject Submission?',
            text: 'Are you sure you want to reject "' + title + '"?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f34943',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-ban"></i> Yes, Reject',
            cancelButtonText: 'Cancel',
            background: '#1a2234',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    @if(Session::has('flash_message'))
        Swal.fire({
            position: 'center',
            icon: 'success',
            title: "{{ Session::get('flash_message') }}",
            showConfirmButton: true,
            confirmButtonColor: '#10c469',
            background: '#1a2234',
            color: '#fff'
        });
    @endif
</script>
@endsection
