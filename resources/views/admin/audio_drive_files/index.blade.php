@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-12">
                        <!-- Top Stat Widgets -->
                        <div class="row mb-4">
                            <div class="col-xl-4 col-md-6">
                                <a href="{{ route('admin.audio-drive-files.index') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-primary" data-plugin="counterup">{{ number_format($totalFiles) }}</h2>
                                            <h5 style="color: #f9f9f9;">Pending Scanned Audio Files</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <a href="{{ route('admin.audio.index') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-success" data-plugin="counterup">{{ number_format($importedCount ?? 0) }}</h2>
                                            <h5 style="color: #f9f9f9;">Imported Audio Tracks</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <a href="{{ route('admin.audio-drive-files.blocked') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-danger" data-plugin="counterup">{{ number_format($blockedCount) }}</h2>
                                            <h5 style="color: #f9f9f9;">Blocked Audio Files</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Main Table Card Box -->
                        <div class="card-box table-responsive">

                            @if (Session::has('flash_message'))
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    {{ Session::get('flash_message') }}
                                </div>
                            @endif

                            @if (Session::has('error_message'))
                                <div class="alert alert-danger">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    {{ Session::get('error_message') }}
                                </div>
                            @endif

                            <!-- Scan Audio Folder Form -->
                            <div class="p-3 mb-4" style="background-color: #212529; border-radius: 5px; border: 1px solid #32383e;">
                                {!! Form::open(['route' => 'admin.audio-drive-files.scan', 'class' => 'form-inline', 'role' => 'form', 'method' => 'post']) !!}
                                <div class="input-group w-100">
                                    <input type="text" name="folder_input" id="folder_input" class="form-control"
                                        placeholder="Paste Google Drive Folder URL or Folder ID to fetch audio tracks (e.g. 1Q7N29v4hu63jsk0_5GhjuMpOykBw6akf)..."
                                        value="{{ request('folder_id') }}" required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-danger waves-effect waves-light"><i class="fa fa-google"></i> Scan & Fetch GDrive Audio Files</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>

                            <!-- Search Form & Clear All Controls -->
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6">
                                    {!! Form::open(['route' => 'admin.audio-drive-files.index', 'class' => 'app-search', 'id' => 'search', 'role' => 'form', 'method' => 'get']) !!}
                                    <input type="text" name="s" placeholder="Search scanned audio tracks by name, file ID, or folder ID..." value="{{ request('s') }}" class="form-control">
                                    <button type="submit"><i class="fa fa-search"></i></button>
                                    {!! Form::close() !!}
                                </div>
                                <div class="col-md-6 text-right">
                                    @if ($totalFiles > 0)
                                        {!! Form::open(['route' => 'admin.audio-drive-files.clear-all', 'method' => 'POST', 'id' => 'clear-all-scanned-form', 'style' => 'display:inline-block;']) !!}
                                        <button type="button" class="btn btn-danger waves-effect waves-light" onclick="confirmClearAllScanned()"><i class="fa fa-trash"></i> Remove All Scanned Audio Records</button>
                                        {!! Form::close() !!}
                                    @endif
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th style="min-width: 220px;">Audio Track Name</th>
                                            <th style="white-space: nowrap;">File Size</th>
                                            <th class="text-center" style="width: 110px;">Preview</th>
                                            <th style="min-width: 130px;">Status</th>
                                            <th class="text-center" style="min-width: 160px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($files as $i => $file)
                                            <tr id="drive_file_id_{{ $file->id }}">
                                                <td>{{ $files->firstItem() + $i }}</td>
                                                <td style="max-width: 260px;">
                                                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $file->name }}">
                                                        <strong>{{ $file->name }}</strong>
                                                    </div>
                                                    @if ($file->mime_type)
                                                        <small style="color: #aaa; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $file->mime_type }}">{{ $file->mime_type }}</small>
                                                    @endif
                                                </td>
                                                <td style="white-space: nowrap;">{{ $file->formatted_size }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-danger waves-effect waves-light" onclick="openAudioDrivePreview('{{ addslashes($file->name) }}', '{{ $file->stream_url }}', '{{ $file->file_id }}')" data-toggle="tooltip" title="Open Audio Preview Popup">
                                                        <i class="fa fa-play-circle"></i> Preview
                                                    </button>
                                                </td>
                                                <td class="status-cell" id="status_cell_{{ $file->id }}" data-file-id="{{ $file->id }}" data-audio-id="{{ $file->audio_id ?? '' }}">
                                                    @if ($file->audio_id)
                                                        <span class="badge badge-success" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-check-circle"></i> Audio Imported</span>
                                                    @elseif ($file->status === 'imported')
                                                        <span class="badge badge-info" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-check"></i> Imported</span>
                                                    @else
                                                        <span class="badge badge-secondary" style="padding: 6px 10px; font-size: 11px;">Scanned</span>
                                                    @endif
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;">
                                                    <div id="action_append_{{ $file->id }}" style="display: inline-block; margin-right: 5px;">
                                                        @if ($file->status === 'imported' || $file->audio_id)
                                                            <a href="{{ route('admin.audio.edit', $file->audio_id ?: $file->id) }}" class="btn btn-xs btn-info" data-toggle="tooltip" title="View Imported Audio Track"><i class="fa fa-music"></i> Track</a>
                                                        @else
                                                            <button type="button" class="btn btn-xs btn-success import-audio-btn-{{ $file->id }}" onclick="importDriveAudioFile({{ $file->id }}, this)" data-toggle="tooltip" title="Import directly into Audio library"><i class="fa fa-download"></i> Import</button>
                                                        @endif
                                                    </div>

                                                    {!! Form::open(['route' => ['admin.audio-drive-files.block', $file->id], 'method' => 'POST', 'style' => 'display:inline-block;']) !!}
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning btn-xs m-r-5" data-toggle="tooltip" title="Block file from import"><i class="fa fa-ban"></i></button>
                                                    {!! Form::close() !!}

                                                    {!! Form::open(['route' => ['admin.audio-drive-files.destroy', $file->id], 'method' => 'DELETE', 'id' => 'delete-form-'.$file->id, 'style' => 'display:inline-block;']) !!}
                                                    <button type="button" class="btn btn-icon waves-effect waves-light btn-danger btn-xs" onclick="confirmDelete({{ $file->id }})" data-toggle="tooltip" title="Remove Record"><i class="fa fa-remove"></i></button>
                                                    {!! Form::close() !!}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center p-4">No scanned audio files found. Paste a Google Drive Audio Folder ID above and click "Scan & Fetch GDrive Audio Files".</td>
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
        </div>
    </div>

    <!-- Audio Drive Preview Popup Modal -->
    <div id="audioDrivePreviewModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
            <div class="modal-content" style="background: #1a2234; border: 1px solid #32383e; color: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div class="modal-header" style="border-bottom: 1px solid #2a3447; padding: 14px 20px; background: #151c2b;">
                    <h5 class="modal-title mt-0" id="audioDriveModalTitle" style="font-size: 15px; font-weight: 600;">
                        <i class="fa fa-music text-info mr-1"></i> Audio File Preview
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="closeAudioDrivePreview()" style="outline: none; opacity: 0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 text-center" style="background: #121824;">
                    <div class="m-b-20">
                        <div style="width: 70px; height: 70px; margin: 0 auto; background: linear-gradient(135deg, #10c469, #35b8e0); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(16, 196, 105, 0.3);">
                            <i class="fa fa-headphones" style="font-size: 32px; color: #fff;"></i>
                        </div>
                        <h4 id="audioDriveTrackTitle" class="text-white mt-3 mb-2" style="font-size: 15px; font-weight: 600; word-break: break-word; overflow-wrap: anywhere; white-space: normal; line-height: 1.4; padding: 0 10px;">Track Name</h4>
                        <p class="text-muted mb-0" style="font-size: 13px;"><i class="fa fa-play-circle-o"></i> GDrive Audio Stream & Preview</p>
                    </div>

                    <!-- Single Player Container (Only ONE player shown, never dual) -->
                    <div id="driveAudioPlayerWrapper" class="p-2" style="background: #1e2838; border-radius: 8px; border: 1px solid #32383e; display: none;">
                        <audio id="modalDriveAudioPlayer" controls style="width: 100%; outline: none;"></audio>
                    </div>

                    <div id="driveAudioIframeWrapper" style="display: none;">
                        <iframe id="modalDriveAudioIframe" src="" style="width: 100%; height: 110px; border: 0; border-radius: 8px; background: #1e2838;" allow="autoplay"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAudioDrivePreview(fileName, streamUrl, fileId) {
            document.getElementById('audioDriveTrackTitle').innerText = fileName;
            var audioPlayer = document.getElementById('modalDriveAudioPlayer');
            var audioPlayerWrapper = document.getElementById('driveAudioPlayerWrapper');
            var iframeWrapper = document.getElementById('driveAudioIframeWrapper');
            var iframe = document.getElementById('modalDriveAudioIframe');

            // Immediately stop & clear any previous playback
            if (audioPlayer) {
                audioPlayer.pause();
                audioPlayer.currentTime = 0;
                audioPlayer.src = '';
            }
            if (iframe) {
                iframe.src = '';
            }

            if (fileId && fileId !== '') {
                // For Google Drive audio, show ONLY the GDrive player
                audioPlayerWrapper.style.display = 'none';
                iframe.src = 'https://drive.google.com/file/d/' + fileId + '/preview';
                iframeWrapper.style.display = 'block';
            } else if (streamUrl && streamUrl !== '') {
                // For local/direct stream, show ONLY the HTML5 player
                iframeWrapper.style.display = 'none';
                audioPlayer.src = streamUrl;
                audioPlayerWrapper.style.display = 'block';
                audioPlayer.play().catch(function() {});
            }

            $('#audioDrivePreviewModal').modal('show');
        }

        function closeAudioDrivePreview() {
            var audioPlayer = document.getElementById('modalDriveAudioPlayer');
            var iframe = document.getElementById('modalDriveAudioIframe');
            if (audioPlayer) {
                audioPlayer.pause();
                audioPlayer.currentTime = 0;
                audioPlayer.src = '';
            }
            if (iframe) {
                iframe.src = '';
            }
        }

        $('#audioDrivePreviewModal').on('hidden.bs.modal', function () {
            closeAudioDrivePreview();
        });

        // Pause any currently playing audio when another audio starts playing
        document.addEventListener('play', function (e) {
            var audios = document.getElementsByTagName('audio');
            for (var i = 0; i < audios.length; i++) {
                if (audios[i] !== e.target) {
                    audios[i].pause();
                }
            }
        }, true);

        function confirmDelete(fileId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '{{ trans("words.dlt_warning") }}',
                    text: '{{ trans("words.dlt_warning_text") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ trans("words.dlt_confirm") }}',
                    cancelButtonText: '{{ trans("words.btn_cancel") }}',
                    background: '#1a2234',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + fileId).submit();
                    }
                });
            } else {
                if (confirm("Are you sure you want to remove this record?")) {
                    document.getElementById('delete-form-' + fileId).submit();
                }
            }
        }

        function confirmClearAllScanned() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Remove All Scanned Audios?',
                    text: 'Are you sure you want to remove all pending scanned audio records from the database?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Remove All',
                    cancelButtonText: 'Cancel',
                    background: '#1a2234',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('clear-all-scanned-form').submit();
                    }
                });
            } else {
                if (confirm('Are you sure you want to remove all pending scanned audio records from the database?')) {
                    document.getElementById('clear-all-scanned-form').submit();
                }
            }
        }

        function importDriveAudioFile(fileId, btn) {
            var $btn = $(btn);
            $btn.prop('disabled', true).html('<i class="fa fa-spin fa-circle-o-notch"></i> Importing...');

            $.ajax({
                url: '{{ url("admin/audio-drive-files") }}/' + fileId + '/import',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (res.success) {
                        var $statusCell = $('#status_cell_' + fileId);
                        $statusCell.attr('data-audio-id', res.audio_id);
                        $statusCell.html('<span class="badge badge-success" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-check-circle"></i> Audio Imported</span>');

                        $btn.replaceWith('<a href="' + res.audio_url + '" class="btn btn-sm btn-info" data-toggle="tooltip" title="View Imported Audio Track"><i class="fa fa-music"></i> Audio Track</a>');

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Audio Imported!',
                                text: res.message,
                                showConfirmButton: false,
                                timer: 2000,
                                toast: true,
                                background: "#1a2234",
                                color: "#fff"
                            });
                        }
                    } else {
                        $btn.prop('disabled', false).html('<i class="fa fa-music"></i> Import Audio');
                        alert(res.message || 'Import failed.');
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fa fa-music"></i> Import Audio');
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error importing audio.';
                    alert(msg);
                }
            });
        }
    </script>
@endsection
