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
                                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                                            <option value="pending" {{ request('status') == 'pending' || !request()->has('status') ? 'selected' : '' }}>Pending Review</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Approved & Active</option>
                                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <input type="text" name="s" class="form-control form-control-sm" placeholder="Search track or user..." value="{{ request('s') }}">
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
                                                <strong style="color: #fff;">{{ $audio->title }}</strong>
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
                                                    <span class="badge badge-warning"><i class="fa fa-clock-o"></i> Pending</span>
                                                @elseif($audio->status === 'rejected')
                                                    <span class="badge badge-danger"><i class="fa fa-times-circle"></i> Rejected</span>
                                                @else
                                                    <span class="badge badge-success"><i class="fa fa-check-circle"></i> Approved</span>
                                                @endif
                                            </td>
                                            <td>{{ $audio->created_at->format('M d, Y') }}</td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                @if($audio->status !== 'active')
                                                    <a href="{{ route('admin.audio.user-submissions.approve', $audio->id) }}" class="btn btn-sm btn-success m-r-5" data-toggle="tooltip" title="Approve & Publish Live" onclick="return confirm('Approve this audio track and publish to stock library?')">
                                                        <i class="fa fa-check"></i> Approve
                                                    </a>
                                                @endif

                                                @if($audio->status !== 'rejected')
                                                    <a href="{{ route('admin.audio.user-submissions.reject', $audio->id) }}" class="btn btn-sm btn-warning m-r-5" data-toggle="tooltip" title="Reject Submission" onclick="return confirm('Reject this user submission?')">
                                                        <i class="fa fa-ban"></i> Reject
                                                    </a>
                                                @endif

                                                <a href="{{ route('admin.audio.edit', $audio->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Edit Audio Details">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">No user-submitted audio tracks found.</td>
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
                <h5 class="modal-title mt-0" id="audioPreviewModalTitle" style="font-size: 15px; font-weight: 600;">
                    <i class="fa fa-music text-info mr-1"></i> Audio Track Preview
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="closeAudioPreview()" style="outline: none; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center" style="background: #121824;">
                <div class="m-b-20">
                    <div style="width: 70px; height: 70px; margin: 0 auto; background: linear-gradient(135deg, #10c469, #35b8e0); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(16, 196, 105, 0.3);">
                        <i class="fa fa-headphones" style="font-size: 32px; color: #fff;"></i>
                    </div>
                    <h4 id="audioModalTrackTitle" class="text-white mt-3 mb-2" style="font-size: 15px; font-weight: 600; word-break: break-word; overflow-wrap: anywhere; white-space: normal; line-height: 1.4; padding: 0 10px;">Track Title</h4>
                    <p class="text-muted mb-0" style="font-size: 13px;"><i class="fa fa-play-circle-o"></i> User Audio Preview</p>
                </div>

                <!-- Single Player Container -->
                <div id="audioPlayerWrapper" class="p-2" style="background: #1e2838; border-radius: 8px; border: 1px solid #32383e; display: none;">
                    <audio id="modalAudioPlayer" controls style="width: 100%; outline: none;"></audio>
                </div>

                <div id="audioIframeWrapper" style="display: none;">
                    <iframe id="modalAudioIframe" src="" style="width: 100%; height: 110px; border: 0; border-radius: 8px; background: #1e2838;" allow="autoplay"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>

<script type="text/javascript">
  function openAudioPreview(title, audioUrl, driveFileId) {
    document.getElementById('audioModalTrackTitle').innerText = title;
    var audioPlayer = document.getElementById('modalAudioPlayer');
    var audioPlayerWrapper = document.getElementById('audioPlayerWrapper');
    var iframeWrapper = document.getElementById('audioIframeWrapper');
    var iframe = document.getElementById('modalAudioIframe');

    // Reset previous audio
    if (audioPlayer) {
        audioPlayer.pause();
        audioPlayer.currentTime = 0;
        audioPlayer.src = '';
    }
    if (iframe) {
        iframe.src = '';
    }

    if (driveFileId && driveFileId !== '') {
        audioPlayerWrapper.style.display = 'none';
        iframe.src = 'https://drive.google.com/file/d/' + driveFileId + '/preview';
        iframeWrapper.style.display = 'block';
    } else if (audioUrl && audioUrl !== '') {
        iframeWrapper.style.display = 'none';
        audioPlayer.src = audioUrl;
        audioPlayerWrapper.style.display = 'block';
        audioPlayer.play().catch(function() {});
    }

    $('#audioPreviewModal').modal('show');
  }

  function closeAudioPreview() {
    var audioPlayer = document.getElementById('modalAudioPlayer');
    var iframe = document.getElementById('modalAudioIframe');
    if (audioPlayer) {
        audioPlayer.pause();
        audioPlayer.currentTime = 0;
        audioPlayer.src = '';
    }
    if (iframe) {
        iframe.src = '';
    }
  }

  $('#audioPreviewModal').on('hidden.bs.modal', function () {
    closeAudioPreview();
  });
</script>

@endsection
