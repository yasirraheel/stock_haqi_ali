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

                            <!-- Search Form -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    {!! Form::open(['route' => 'admin.audio-drive-files.index', 'class' => 'app-search', 'id' => 'search', 'role' => 'form', 'method' => 'get']) !!}
                                    <input type="text" name="s" placeholder="Search scanned audio tracks by name, file ID, or folder ID..." value="{{ request('s') }}" class="form-control">
                                    <button type="submit"><i class="fa fa-search"></i></button>
                                    {!! Form::close() !!}
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th style="min-width: 220px;">Audio Track Name</th>
                                            <th style="white-space: nowrap;">File Size</th>
                                            <th style="min-width: 380px;">Audio Direct Preview</th>
                                            <th style="min-width: 140px;">Status</th>
                                            <th class="text-center" width="100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($files as $i => $file)
                                            <tr id="drive_file_id_{{ $file->id }}">
                                                <td>{{ $files->firstItem() + $i }}</td>
                                                <td style="max-width: 240px;">
                                                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $file->name }}">
                                                        <strong>{{ $file->name }}</strong>
                                                    </div>
                                                    @if ($file->mime_type)
                                                        <small style="color: #aaa; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $file->mime_type }}">{{ $file->mime_type }}</small>
                                                    @endif
                                                </td>
                                                <td style="white-space: nowrap;">{{ $file->formatted_size }}</td>
                                                <td>
                                                    <!-- Direct Inline Audio Player in Table Row -->
                                                    <div class="d-flex align-items-center" style="gap: 10px;">
                                                        <audio controls preload="none" style="height: 32px; width: 250px; outline: none;">
                                                            <source src="{{ $file->stream_url }}" type="audio/mpeg">
                                                            Your browser does not support HTML5 audio preview.
                                                        </audio>

                                                        <div id="action_append_{{ $file->id }}" style="display: inline-block;">
                                                            @if ($file->status === 'imported' || $file->audio_id)
                                                                <a href="{{ route('admin.audio.edit', $file->audio_id ?: $file->id) }}" class="btn btn-sm btn-info" data-toggle="tooltip" title="View Imported Audio Track"><i class="fa fa-music"></i> Audio Track</a>
                                                            @else
                                                                <button type="button" class="btn btn-sm btn-success import-audio-btn-{{ $file->id }}" onclick="importDriveAudioFile({{ $file->id }}, this)" data-toggle="tooltip" title="Import directly into Audio library"><i class="fa fa-download"></i> Import Audio</button>
                                                            @endif
                                                        </div>
                                                    </div>
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

    <script>
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
