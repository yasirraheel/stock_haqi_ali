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
                                <h4 class="m-t-0 header-title"><i class="fa fa-music text-info"></i> <b>Audio Submitted by Users</b></h4>
                                <p class="text-muted font-13 m-b-10">Review and approve user-submitted Google Drive audio tracks.</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <span class="badge badge-warning p-2 font-13 mr-2"><i class="fa fa-clock-o"></i> Pending: {{ $pendingCount }}</span>
                                <span class="badge badge-success p-2 font-13"><i class="fa fa-check"></i> Approved: {{ $approvedCount }}</span>
                            </div>
                        </div>

                        <!-- Filter and Search Bar -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.audio.user-submissions') }}" class="form-inline">
                                    <div class="form-group mr-2">
                                        <label class="mr-2 text-white">Status:</label>
                                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()" style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                                            <option value="pending" {{ request('status') == 'pending' || !request()->has('status') ? 'selected' : '' }}>Pending Review</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Approved & Active</option>
                                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <input type="text" name="s" class="form-control form-control-sm" placeholder="Search track or user..." value="{{ request('s') }}" style="background: #121824; border: 1px solid #2a3447; color: #fff;">
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
                                        <th>Track Title</th>
                                        <th>Submitted By</th>
                                        <th class="text-center" style="width: 100px;">Preview</th>
                                        <th>Genre</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-center" style="min-width: 150px;">Approval Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($audios as $i => $audio)
                                        <tr>
                                            <td>{{ $audios->firstItem() + $i }}</td>
                                            <td>
                                                <strong class="text-white">{{ $audio->title }}</strong>
                                                @if($audio->description)
                                                    <small class="text-muted d-block" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $audio->description }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($audio->user)
                                                    <strong class="text-info">{{ $audio->user->name }}</strong>
                                                    <small class="text-muted d-block">{{ $audio->user->email }}</small>
                                                @else
                                                    <span class="text-muted">User #{{ $audio->added_by }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger waves-effect waves-light"
                                                    onclick="openAudioPreview('{{ addslashes($audio->title) }}', '{{ $audio->audio_url }}', '{{ $audio->drive_file_id ?? '' }}')"
                                                    data-toggle="tooltip" title="Listen in Popup Player">
                                                    <i class="fa fa-play-circle"></i> Preview
                                                </button>
                                            </td>
                                            <td>{{ $audio->genre ?: 'N/A' }}</td>
                                            <td>
                                                @if($audio->license_price > 0)
                                                    <span class="badge badge-warning">${{ number_format($audio->license_price, 2) }}</span>
                                                @else
                                                    <span class="badge badge-success">Free</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($audio->status === 'pending')
                                                    <span class="badge badge-warning" style="padding: 5px 10px; font-size: 11px;"><i class="fa fa-clock-o"></i> Pending</span>
                                                @elseif($audio->status === 'rejected')
                                                    <span class="badge badge-danger" style="padding: 5px 10px; font-size: 11px;"><i class="fa fa-times-circle"></i> Rejected</span>
                                                @else
                                                    <span class="badge badge-success" style="padding: 5px 10px; font-size: 11px;"><i class="fa fa-check-circle"></i> Approved</span>
                                                @endif
                                            </td>
                                            <td>{{ $audio->created_at->format('M d, Y') }}</td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                @if($audio->status !== 'active')
                                                    <button type="button" class="btn btn-sm btn-success m-r-5" data-toggle="tooltip" title="Approve & Publish Live"
                                                        onclick="confirmApprove('{{ route('admin.audio.user-submissions.approve', $audio->id) }}', '{{ addslashes($audio->title) }}')">
                                                        <i class="fa fa-check"></i> Approve
                                                    </button>
                                                @endif

                                                @if($audio->status !== 'rejected')
                                                    <button type="button" class="btn btn-sm btn-warning m-r-5" data-toggle="tooltip" title="Reject Submission"
                                                        onclick="confirmReject('{{ route('admin.audio.user-submissions.reject', $audio->id) }}', '{{ addslashes($audio->title) }}')">
                                                        <i class="fa fa-ban"></i> Reject
                                                    </button>
                                                @endif

                                                <a href="{{ route('admin.audio.edit', $audio->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Edit Audio Details">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4 text-muted">No user-submitted audio tracks found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <nav class="paging_simple_numbers">
                            @include('admin.pagination', ['paginator' => $audios])
                        </nav>

                    </div>
                </div>
            </div>
        </div>
        @include("admin.copyright")
    </div>
</div>

<!-- Audio Preview Popup Modal -->
<div id="audioPreviewModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
        <div class="modal-content" style="background: #1a2234; border: 1px solid #32383e; color: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div class="modal-header" style="border-bottom: 1px solid #2a3447; padding: 14px 20px; background: #151c2b;">
                <h5 class="modal-title mt-0" id="audioPreviewModalTitle" style="font-size: 15px; font-weight: 600; overflow-wrap: anywhere; word-break: break-word;">
                    <i class="fa fa-music text-info mr-1"></i> Audio Track Preview
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="closeAudioPreview()" style="outline: none; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center" style="background: #0d1117;">
                <div id="modalGDriveAudioContainer" style="display: none;">
                    <iframe id="modalGDriveAudioIframe" src="" style="width: 100%; height: 90px; border: 0; border-radius: 6px; background: #000;" allow="autoplay"></iframe>
                </div>
                <div id="modalHtmlAudioContainer" style="display: none;">
                    <audio id="modalHtmlAudioPlayer" controls style="width: 100%; outline: none; border-radius: 6px;"></audio>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
    function openAudioPreview(title, audioUrl, driveFileId) {
        document.getElementById('audioPreviewModalTitle').innerHTML = '<i class="fa fa-music text-info mr-1"></i> ' + title;
        var gdriveContainer = document.getElementById('modalGDriveAudioContainer');
        var gdriveIframe = document.getElementById('modalGDriveAudioIframe');
        var htmlContainer = document.getElementById('modalHtmlAudioContainer');
        var htmlPlayer = document.getElementById('modalHtmlAudioPlayer');

        if (driveFileId && driveFileId !== '') {
            htmlContainer.style.display = 'none';
            htmlPlayer.pause();
            htmlPlayer.src = '';
            gdriveIframe.src = 'https://drive.google.com/file/d/' + driveFileId + '/preview';
            gdriveContainer.style.display = 'block';
        } else if (audioUrl && audioUrl !== '') {
            gdriveContainer.style.display = 'none';
            gdriveIframe.src = '';
            htmlPlayer.src = audioUrl;
            htmlContainer.style.display = 'block';
            htmlPlayer.play().catch(function() {});
        }

        $('#audioPreviewModal').modal('show');
    }

    function closeAudioPreview() {
        var gdriveIframe = document.getElementById('modalGDriveAudioIframe');
        var htmlPlayer = document.getElementById('modalHtmlAudioPlayer');
        if (gdriveIframe) gdriveIframe.src = '';
        if (htmlPlayer) {
            htmlPlayer.pause();
            htmlPlayer.src = '';
        }
    }

    $('#audioPreviewModal').on('hidden.bs.modal', function () {
        closeAudioPreview();
    });

    function confirmApprove(url, title) {
        Swal.fire({
            title: 'Approve Audio Track?',
            text: 'Are you sure you want to approve "' + title + '" and publish it to the library?',
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
