@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-12">
                        <!-- Top Stat Widgets matching Paypal Dashboard -->
                        <div class="row mb-4">
                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-primary" data-plugin="counterup">{{ number_format($totalFiles) }}</h2>
                                            <h5 style="color: #f9f9f9;">Total Synced Files</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-warning" data-plugin="counterup">{{ number_format($foldersCount) }}</h2>
                                            <h5 style="color: #f9f9f9;">Total Folders Scanned</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-success" data-plugin="counterup">Active</h2>
                                            <h5 style="color: #f9f9f9;">Google Drive API Status</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-info" data-plugin="counterup">Auto</h2>
                                            <h5 style="color: #f9f9f9;">Deduplication</h5>
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
                                            <th>File Name</th>
                                            <th>File Size</th>
                                            <th>Direct Download URL</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($files as $i => $file)
                                            <tr id="drive_file_id_{{ $file->id }}">
                                                <td>{{ $files->firstItem() + $i }}</td>
                                                <td>
                                                    <strong>{{ $file->name }}</strong>
                                                    @if ($file->mime_type)
                                                        <br><small style="color: #aaa;">{{ $file->mime_type }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $file->formatted_size }}</td>
                                                <td>
                                                    <div class="input-group input-group-sm" style="min-width: 250px;">
                                                        <input type="text" class="form-control form-control-sm" id="url_{{ $file->id }}" value="{{ $file->url }}" readonly>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-sm btn-secondary" type="button" onclick="copyUrl('url_{{ $file->id }}')"><i class="fa fa-copy"></i> Copy</button>
                                                            <a href="{{ $file->url }}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-external-link"></i> Open</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="badge badge-success">{{ ucfirst($file->status) }}</span></td>
                                                <td class="text-center">
                                                    {!! Form::open(['route' => ['admin.drive-files.destroy', $file->id], 'method' => 'DELETE', 'style' => 'display:inline-block;', 'onclick' => 'return confirm("Are you sure you want to remove this record?")']) !!}
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger m-b-5" data-toggle="tooltip" title="Remove"><i class="fa fa-remove"></i></button>
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

    <script>
        function copyUrl(elementId) {
            var copyText = document.getElementById(elementId);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("Direct URL copied to clipboard!");
        }
    </script>
@endsection
