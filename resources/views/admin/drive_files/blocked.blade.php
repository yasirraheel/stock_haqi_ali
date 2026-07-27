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
                                            <h5 style="color: #f9f9f9;">Active Scanned Files</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="{{ route('admin.drive-files.blocked') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-danger" data-plugin="counterup">{{ number_format($blockedCount) }}</h2>
                                            <h5 style="color: #f9f9f9;">Total Blocked Files</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-warning" data-plugin="counterup">{{ number_format($foldersCount) }}</h2>
                                            <h5 style="color: #f9f9f9;">Total Folders</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-success" data-plugin="counterup">Protected</h2>
                                            <h5 style="color: #f9f9f9;">Folder Scan Shield</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Main Table Card Box -->
                        <div class="card-box table-responsive">

                            <div class="row mb-2">
                                <div class="col-md-8">
                                    <h4 class="m-t-0 header-title text-danger"><b><i class="fa fa-ban"></i> Blocked Files List</b></h4>
                                    <p class="text-muted font-13 m-b-15">
                                        Files in this list are blocked from import jobs. Even if you re-scan a Google Drive folder in the future, these files will remain blocked automatically.
                                    </p>
                                </div>
                                <div class="col-md-6 text-right">
                                    {!! Form::open(['route' => 'admin.drive-files.clear-blocked', 'method' => 'POST', 'id' => 'clear-blocked-form', 'style' => 'display:inline-block;']) !!}
                                        <button type="button" class="btn btn-danger btn-sm waves-effect waves-light m-r-5" onclick="confirmClearBlocked()"><i class="fa fa-trash"></i> Delete All Blocked Files</button>
                                    {!! Form::close() !!}
                                    <a href="{{ route('admin.drive-files.index') }}" class="btn btn-primary btn-sm waves-effect waves-light"><i class="fa fa-arrow-left"></i> Back to Scanned Files</a>
                                </div>
                            </div>

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

                            <!-- Search Form -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    {!! Form::open(['route' => 'admin.drive-files.blocked', 'class' => 'app-search', 'id' => 'search', 'role' => 'form', 'method' => 'get']) !!}
                                    <input type="text" name="s" placeholder="Search blocked files by name, file ID, or folder ID..." value="{{ request('s') }}" class="form-control">
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
                                            <th>Status</th>
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
                                                    <div class="input-group input-group-sm" style="min-width: 320px;">
                                                        <input type="text" class="form-control form-control-sm" id="url_{{ $file->id }}" value="{{ $file->url }}" readonly>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-sm btn-info" type="button" onclick="previewDriveFile('{{ $file->file_id }}', '{{ addslashes($file->name) }}')" data-toggle="tooltip" title="Preview File"><i class="fa fa-play-circle"></i> Preview</button>
                                                            <button class="btn btn-sm btn-secondary" type="button" onclick="copyUrl('url_{{ $file->id }}')" data-toggle="tooltip" title="Copy Direct URL"><i class="fa fa-copy"></i> Copy</button>
                                                            <a href="{{ $file->url }}" target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Open Link"><i class="fa fa-external-link"></i> Open</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-danger" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-ban"></i> Blocked</span>
                                                </td>
                                                <td class="text-center">
                                                    {!! Form::open(['route' => ['admin.drive-files.unblock', $file->id], 'method' => 'POST', 'style' => 'display:inline-block;']) !!}
                                                    <button type="submit" class="btn btn-xs btn-success waves-effect waves-light m-r-5" data-toggle="tooltip" title="Unblock File"><i class="fa fa-unlock"></i> Unblock</button>
                                                    {!! Form::close() !!}

                                                    {!! Form::open(['route' => ['admin.drive-files.destroy', $file->id], 'method' => 'DELETE', 'id' => 'delete-form-'.$file->id, 'style' => 'display:inline-block;']) !!}
                                                    <button type="button" class="btn btn-icon waves-effect waves-light btn-danger btn-xs" onclick="confirmDelete({{ $file->id }})" data-toggle="tooltip" title="Remove Record"><i class="fa fa-remove"></i></button>
                                                    {!! Form::close() !!}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center p-4">No blocked files found.</td>
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
            }
        }

        window.confirmClearBlocked = function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete All Blocked Files?',
                    text: 'This will permanently remove all blocked file records from your database.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Delete All Blocked',
                    cancelButtonText: 'Cancel',
                    background: '#1a2234',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('clear-blocked-form').submit();
                    }
                });
            } else {
                if (confirm("Are you sure you want to permanently delete all blocked file records?")) {
                    document.getElementById('clear-blocked-form').submit();
                }
            }
        };
    </script>
@endsection
