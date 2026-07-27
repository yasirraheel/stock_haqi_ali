@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-12">
                        <!-- Top Stat Widgets -->
                        <div class="row mb-4">
                            <div class="col-xl-3 col-md-6">
                                <a href="{{ route('admin.drive-files.index') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-primary" data-plugin="counterup">{{ number_format($totalFiles) }}</h2>
                                            <h5 style="color: #f9f9f9;">Pending Scanned Files</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="{{ route('admin.effects.index') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-success" data-plugin="counterup">{{ number_format($importedCount ?? 0) }}</h2>
                                            <h5 style="color: #f9f9f9;">Imported Effects</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="{{ route('admin.drive-files.blocked') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-danger" data-plugin="counterup">{{ number_format($blockedCount) }}</h2>
                                            <h5 style="color: #f9f9f9;">Blocked Files</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-warning" data-plugin="counterup">{{ number_format($foldersCount) }}</h2>
                                            <h5 style="color: #f9f9f9;">Scanned Folders</h5>
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

                            <!-- Scan Folder Form -->
                            <div class="p-3 mb-4" style="background-color: #212529; border-radius: 5px; border: 1px solid #32383e;">
                                {!! Form::open(['route' => 'admin.drive-files.scan', 'class' => 'form-inline', 'role' => 'form', 'method' => 'post']) !!}
                                <div class="input-group w-100">
                                    <input type="text" name="folder_input" id="folder_input" class="form-control"
                                        placeholder="Paste Google Drive Folder URL or Folder ID (e.g. 1Q7N29v4hu63jsk0_5GhjuMpOykBw6akf)..."
                                        value="{{ request('folder_id', '1Q7N29v4hu63jsk0_5GhjuMpOykBw6akf') }}" required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-danger waves-effect waves-light"><i class="fa fa-refresh"></i> Scan & Sync Folder Files</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>

                            <!-- Search Form -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    {!! Form::open(['route' => 'admin.drive-files.index', 'class' => 'app-search', 'id' => 'search', 'role' => 'form', 'method' => 'get']) !!}
                                    <input type="text" name="s" placeholder="Search scanned files by name, file ID, or folder ID..." value="{{ request('s') }}" class="form-control">
                                    <button type="submit"><i class="fa fa-search"></i></button>
                                    {!! Form::close() !!}
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th style="min-width: 200px;">File Name</th>
                                            <th style="white-space: nowrap;">File Size</th>
                                            <th>Direct Download URL</th>
                                            <th style="min-width: 160px;">Import Status</th>
                                            <th class="text-center" width="120">Action</th>
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
                                                    <div class="input-group input-group-sm" style="max-width: 450px;">
                                                        <input type="text" class="form-control form-control-sm" id="url_{{ $file->id }}" value="{{ $file->url }}" readonly>
                                                        <div class="input-group-append" id="action_append_{{ $file->id }}">
                                                            <button class="btn btn-sm btn-info" type="button" onclick="previewDriveFile('{{ $file->file_id }}', '{{ addslashes($file->name) }}')" data-toggle="tooltip" title="Preview File"><i class="fa fa-play-circle"></i> Preview</button>
                                                            <button class="btn btn-sm btn-secondary" type="button" onclick="copyUrl('url_{{ $file->id }}')" data-toggle="tooltip" title="Copy Direct URL"><i class="fa fa-copy"></i> Copy</button>
                                                            <a href="{{ $file->url }}" target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Open Link"><i class="fa fa-external-link"></i> Open</a>
                                                            @if ($file->status === 'imported' || $file->effect_id)
                                                                @if ($file->effect_id)
                                                                    <a href="{{ route('admin.effects.edit', $file->effect_id) }}" class="btn btn-sm btn-success" data-toggle="tooltip" title="View Imported Effect"><i class="fa fa-eye"></i> View</a>
                                                                @endif
                                                            @else
                                                                <button type="button" class="btn btn-sm btn-success import-btn-{{ $file->id }}" onclick="importDriveFile({{ $file->id }}, this)" data-toggle="tooltip" title="Import as Effect and start background processing"><i class="fa fa-download"></i> Import</button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="status-cell" id="status_cell_{{ $file->id }}" data-file-id="{{ $file->id }}" data-effect-id="{{ $file->effect_id ?? '' }}">
                                                    @if ($file->status === 'imported' || $file->effect_id)
                                                        <span class="badge badge-info" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-clock-o"></i> Pending</span>
                                                    @else
                                                        <span class="badge badge-secondary" style="padding: 6px 10px; font-size: 11px;">Scanned</span>
                                                    @endif
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;">
                                                    {!! Form::open(['route' => ['admin.drive-files.block', $file->id], 'method' => 'POST', 'style' => 'display:inline-block;']) !!}
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning btn-xs m-r-5" data-toggle="tooltip" title="Block file from import"><i class="fa fa-ban"></i></button>
                                                    {!! Form::close() !!}

                                                    {!! Form::open(['route' => ['admin.drive-files.destroy', $file->id], 'method' => 'DELETE', 'id' => 'delete-form-'.$file->id, 'style' => 'display:inline-block;']) !!}
                                                    <button type="button" class="btn btn-icon waves-effect waves-light btn-danger btn-xs" onclick="confirmDelete({{ $file->id }})" data-toggle="tooltip" title="Remove Record"><i class="fa fa-remove"></i></button>
                                                    {!! Form::close() !!}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center p-4">No scanned files found. Enter a Google Drive Folder ID above and click "Scan & Sync Folder Files".</td>
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

    <!-- Google Drive File Preview Modal -->
    <div id="driveFilePreviewModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 900px;">
            <div class="modal-content" style="background: #1a2234; border: 1px solid #32383e; color: #fff; border-radius: 8px;">
                <div class="modal-header" style="border-bottom: 1px solid #32383e; padding: 15px 20px;">
                    <h5 class="modal-title mt-0" id="previewModalTitle"><i class="fa fa-play-circle text-info"></i> File Preview</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="closeDrivePreview()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0 text-center" style="background: #000; min-height: 500px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                    <iframe id="previewIframe" src="" style="width: 100%; height: 500px; border: 0;" allow="autoplay"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewDriveFile(fileId, fileName) {
            document.getElementById('previewModalTitle').innerHTML = '<i class="fa fa-play-circle text-info"></i> Preview: ' + fileName;
            var iframe = document.getElementById('previewIframe');
            iframe.src = 'https://drive.google.com/file/d/' + fileId + '/preview';
            $('#driveFilePreviewModal').modal('show');
        }

        function closeDrivePreview() {
            var iframe = document.getElementById('previewIframe');
            iframe.src = '';
        }

        $('#driveFilePreviewModal').on('hidden.bs.modal', function () {
            closeDrivePreview();
        });

        function copyUrl(elementId) {
            var copyText = document.getElementById(elementId);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Direct download URL copied to clipboard.',
                    showConfirmButton: false,
                    timer: 2000,
                    background: "#1a2234",
                    color: "#fff"
                });
            } else {
                alert("Direct URL copied to clipboard!");
            }
        }

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

        function importDriveFile(fileId, btn) {
            var $btn = $(btn);
            $btn.prop('disabled', true).html('<i class="fa fa-spin fa-circle-o-notch"></i> Importing...');

            $.ajax({
                url: '{{ url("admin/drive-files") }}/' + fileId + '/import',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (res.success) {
                        var $statusCell = $('#status_cell_' + fileId);
                        $statusCell.attr('data-effect-id', res.effect_id);
                        $statusCell.html('<span class="badge badge-info" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-clock-o"></i> Pending</span>');

                        $btn.replaceWith('<a href="' + res.effect_url + '" class="btn btn-sm btn-success" data-toggle="tooltip" title="View Imported Effect"><i class="fa fa-eye"></i> View</a>');

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Import Queued!',
                                text: res.message,
                                showConfirmButton: false,
                                timer: 2000,
                                toast: true,
                                background: "#1a2234",
                                color: "#fff"
                            });
                        }

                        pollDriveFileStatuses();
                    } else {
                        $btn.prop('disabled', false).html('<i class="fa fa-download"></i> Import');
                        alert(res.message || 'Import failed.');
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fa fa-download"></i> Import');
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error importing file.';
                    alert(msg);
                }
            });
        }

        function pollDriveFileStatuses() {
            var pendingEffectIds = [];
            $('.status-cell').each(function() {
                var effectId = $(this).attr('data-effect-id');
                if (effectId && effectId !== '') {
                    var currentText = $(this).text().toLowerCase();
                    if (!currentText.includes('ready') && !currentText.includes('failed')) {
                        pendingEffectIds.push(effectId);
                    }
                }
            });

            if (pendingEffectIds.length === 0) return;

            $.ajax({
                url: '{{ route("admin.effects.check-status") }}',
                type: 'GET',
                data: {
                    ids: pendingEffectIds.join(',')
                },
                success: function(data) {
                    $.each(data, function(effectId, effect) {
                        var $cell = $('.status-cell[data-effect-id="' + effectId + '"]');
                        if ($cell.length) {
                            var currentStatus = effect.status;
                            var stepText = effect.process_step || '';
                            var html = '';

                            if (currentStatus === 'ready') {
                                html = '<span class="badge badge-success" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-check-circle"></i> Ready' + (effect.converted_mb ? ' (' + effect.converted_mb + ')' : '') + '</span>';
                            } else if (currentStatus === 'downloading') {
                                html = '<span class="badge badge-info" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-cloud-download fa-spin"></i> ' + (stepText || 'Downloading...') + '</span>';
                            } else if (currentStatus === 'processing') {
                                html = '<span class="badge badge-warning" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-cogs fa-spin"></i> ' + (stepText || 'Compressing MP4...') + '</span>';
                            } else if (currentStatus === 'failed' || currentStatus === 'error') {
                                html = '<span class="badge badge-danger" style="padding: 6px 10px; font-size: 11px;" title="' + (stepText || 'Failed') + '"><i class="fa fa-exclamation-triangle"></i> ' + (stepText || 'Failed') + '</span>';
                            } else {
                                html = '<span class="badge badge-secondary" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-clock-o"></i> Pending</span>';
                            }

                            $cell.html(html);
                        }
                    });
                }
            });
        }

        (function() {
            function initDrivePoller() {
                if (typeof jQuery === 'undefined') {
                    setTimeout(initDrivePoller, 100);
                    return;
                }
                pollDriveFileStatuses();
                setInterval(pollDriveFileStatuses, 3000);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDrivePoller);
            } else {
                initDrivePoller();
            }
        })();
    </script>
@endsection
