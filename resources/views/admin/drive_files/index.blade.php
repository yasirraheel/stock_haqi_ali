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
                            <div class="p-3 mb-3" style="background-color: #212529; border-radius: 6px; border: 1px solid #32383e;">
                                {!! Form::open(['route' => 'admin.drive-files.scan', 'class' => 'form-inline', 'role' => 'form', 'method' => 'post']) !!}
                                <div class="input-group w-100">
                                    <input type="text" name="folder_input" id="folder_input" class="form-control"
                                        placeholder="Paste Google Drive Folder URL or Folder ID (e.g. 1Q7N29v4hu63jsk0_5GhjuMpOykBw6akf)..."
                                        value="{{ request('folder_id', '') }}" required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-danger waves-effect waves-light"><i class="fa fa-refresh"></i> Scan & Sync Folder Files</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>

                            <!-- Scanned Folders List (Saved in Database) -->
                            @if(isset($scannedFolders) && $scannedFolders->count() > 0)
                                <div class="p-3 mb-4" style="background: #1a2234; border: 1px solid #2e384d; border-radius: 6px;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="m-0 text-white font-14"><i class="fa fa-folder-open text-warning mr-1"></i> Tracked Scanned Folders ({{ $scannedFolders->count() }})</h5>
                                        <a href="{{ route('admin.drive-files.index', ['tab' => $activeTab ?? 'all']) }}" class="btn btn-xs {{ empty($activeFolder) ? 'btn-primary' : 'btn-secondary' }}">
                                            <i class="fa fa-th-list"></i> View All Folders ({{ number_format($totalFiles) }})
                                        </a>
                                    </div>
                                    <div class="row">
                                        @foreach($scannedFolders as $folder)
                                            @php $isSelected = ($activeFolder === $folder->folder_id); @endphp
                                            <div class="col-xl-4 col-md-6 mb-2">
                                                <div class="p-2 d-flex flex-column justify-content-between" style="background: {{ $isSelected ? '#203254' : '#141a29' }}; border: 1px solid {{ $isSelected ? '#3b82f6' : '#2b354d' }}; border-radius: 6px; height: 100%;">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <div class="text-truncate mr-2" style="max-width: 80%;">
                                                            <strong class="text-white font-13" title="{{ $folder->folder_id }}"><i class="fa fa-google text-danger"></i> {{ $folder->folder_id }}</strong>
                                                            @if($folder->last_scanned_at)
                                                                <small class="text-muted d-block font-11"><i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($folder->last_scanned_at)->diffForHumans() }}</small>
                                                            @endif
                                                        </div>
                                                        <span class="badge {{ $isSelected ? 'badge-primary' : 'badge-dark' }} font-11">{{ number_format($folder->total_files) }} files</span>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top border-dark">
                                                        <div class="font-11">
                                                            <span class="text-success mr-2" title="Imported"><i class="fa fa-check"></i> {{ $folder->imported_files }}</span>
                                                            <span class="text-info mr-2" title="Pending"><i class="fa fa-clock-o"></i> {{ $folder->pending_files }}</span>
                                                            @if($folder->blocked_files > 0)
                                                                <span class="text-danger" title="Blocked"><i class="fa fa-ban"></i> {{ $folder->blocked_files }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('admin.drive-files.index', ['folder_id' => $folder->folder_id, 'tab' => $activeTab ?? 'all']) }}" class="btn btn-xs {{ $isSelected ? 'btn-success' : 'btn-outline-info' }}" title="Filter files by this folder">
                                                                <i class="fa fa-filter"></i> {{ $isSelected ? 'Active' : 'Filter' }}
                                                            </a>
                                                            {!! Form::open(['route' => 'admin.drive-files.scan', 'method' => 'post', 'style' => 'display:inline;']) !!}
                                                            <input type="hidden" name="folder_input" value="{{ $folder->folder_id }}">
                                                            <button type="submit" class="btn btn-xs btn-outline-warning" title="Re-Scan & Sync this folder"><i class="fa fa-refresh"></i></button>
                                                            {!! Form::close() !!}
                                                            {!! Form::open(['route' => ['admin.drive-files.folder.delete', $folder->folder_id], 'method' => 'post', 'style' => 'display:inline;']) !!}
                                                            <button type="submit" class="btn btn-xs btn-outline-danger" onclick="return confirm('Remove folder {{ $folder->folder_id }} from history?');" title="Remove from list"><i class="fa fa-trash"></i></button>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Status Filter Tabs & Search Bar -->
                            <div class="row align-items-center mb-3">
                                <div class="col-lg-8 col-md-12 mb-2 mb-lg-0">
                                    <div class="btn-group flex-wrap">
                                        <a href="{{ route('admin.drive-files.index', ['folder_id' => $activeFolder, 'tab' => 'all', 's' => request('s')]) }}" class="btn btn-sm {{ ($activeTab ?? 'all') === 'all' ? 'btn-primary' : 'btn-dark' }} font-12">
                                            <i class="fa fa-list"></i> All Files <span class="badge badge-light ml-1">{{ number_format($totalFiles) }}</span>
                                        </a>
                                        <a href="{{ route('admin.drive-files.index', ['folder_id' => $activeFolder, 'tab' => 'pending', 's' => request('s')]) }}" class="btn btn-sm {{ ($activeTab ?? '') === 'pending' ? 'btn-info' : 'btn-dark' }} font-12">
                                            <i class="fa fa-clock-o"></i> Pending <span class="badge badge-info ml-1">{{ number_format($pendingCount) }}</span>
                                        </a>
                                        <a href="{{ route('admin.drive-files.index', ['folder_id' => $activeFolder, 'tab' => 'imported', 's' => request('s')]) }}" class="btn btn-sm {{ ($activeTab ?? '') === 'imported' ? 'btn-success' : 'btn-dark' }} font-12">
                                            <i class="fa fa-check-circle"></i> Imported <span class="badge badge-success ml-1">{{ number_format($importedCount) }}</span>
                                        </a>
                                        <a href="{{ route('admin.drive-files.index', ['folder_id' => $activeFolder, 'tab' => 'blocked', 's' => request('s')]) }}" class="btn btn-sm {{ ($activeTab ?? '') === 'blocked' ? 'btn-danger' : 'btn-dark' }} font-12">
                                            <i class="fa fa-ban"></i> Blocked <span class="badge badge-danger ml-1">{{ number_format($blockedCount) }}</span>
                                        </a>
                                    </div>

                                    @if(!empty($activeFolder))
                                        <span class="badge badge-warning ml-2 font-12 p-1">
                                            Folder: {{ substr($activeFolder, 0, 12) }}...
                                            <a href="{{ route('admin.drive-files.index', ['tab' => $activeTab ?? 'all', 's' => request('s')]) }}" class="text-white ml-1" title="Clear folder filter">&times;</a>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-lg-4 col-md-12">
                                    {!! Form::open(['route' => 'admin.drive-files.index', 'class' => 'app-search', 'id' => 'search', 'role' => 'form', 'method' => 'get']) !!}
                                    <input type="hidden" name="tab" value="{{ $activeTab ?? 'all' }}">
                                    @if(!empty($activeFolder))
                                        <input type="hidden" name="folder_id" value="{{ $activeFolder }}">
                                    @endif
                                    <input type="text" name="s" placeholder="Search files by name or ID..." value="{{ request('s') }}" class="form-control">
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
                                                    @if ($file->folder_id)
                                                        <small class="text-muted font-10">Folder: {{ substr($file->folder_id, 0, 10) }}...</small>
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
                                                        <span class="badge badge-success" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-check-circle"></i> Imported</span>
                                                    @elseif ($file->status === 'blocked')
                                                        <span class="badge badge-danger" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-ban"></i> Blocked</span>
                                                    @else
                                                        <span class="badge badge-info" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-clock-o"></i> Pending</span>
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
                                                <td colspan="6" class="text-center p-4">
                                                    <div class="p-3 text-muted">
                                                        <i class="fa fa-folder-open-o fa-3x mb-2 d-block text-secondary"></i>
                                                        <strong>No files found matching the current filter.</strong>
                                                        <p class="font-12 mt-1">Try switching tabs above (e.g. "All Files" or "Imported") or scan a new Google Drive Folder ID.</p>
                                                    </div>
                                                </td>
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
