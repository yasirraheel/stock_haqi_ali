@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-12">
                        <!-- Top Stat Widgets -->
                        <div class="row mb-4">
                            <div class="col-xl-6 col-md-6">
                                <a href="{{ route('admin.film-stock-drive-files.index') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-primary" data-plugin="counterup">{{ number_format($totalFiles) }}</h2>
                                            <h5 style="color: #f9f9f9;">Scanned Film Stock Videos</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-6 col-md-6">
                                <a href="{{ route('admin.film-stock-drive-files.blocked') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-danger" data-plugin="counterup">{{ number_format($blockedCount) }}</h2>
                                            <h5 style="color: #f9f9f9;">Blocked Film Stock Files</h5>
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

                            <!-- Scan Film Stock Folder Form -->
                            <div class="p-3 mb-4" style="background-color: #212529; border-radius: 5px; border: 1px solid #32383e;">
                                {!! Form::open(['route' => 'admin.film-stock-drive-files.scan', 'class' => 'form-inline', 'role' => 'form', 'method' => 'post']) !!}
                                <div class="input-group w-100">
                                    <input type="text" name="folder_input" id="folder_input" class="form-control"
                                        placeholder="Paste Google Drive Folder URL or Folder ID to fetch Film Stock video clips..."
                                        value="{{ request('folder_id') }}" required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-purple waves-effect waves-light"><i class="fa fa-google"></i> Scan & Fetch GDrive Film Stock Videos</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>

                            <!-- Search Form & Clear Controls -->
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6">
                                    {!! Form::open(['route' => 'admin.film-stock-drive-files.index', 'class' => 'app-search', 'id' => 'search', 'role' => 'form', 'method' => 'get']) !!}
                                    <input type="text" name="s" placeholder="Search Film Stock videos by name, file ID, or folder ID..." value="{{ request('s') }}" class="form-control">
                                    <button type="submit"><i class="fa fa-search"></i></button>
                                    {!! Form::close() !!}
                                </div>
                                <div class="col-md-6 text-right">
                                    @if ($totalFiles > 0)
                                        {!! Form::open(['route' => 'admin.film-stock-drive-files.clear-all', 'method' => 'POST', 'id' => 'clear-all-scanned-form', 'style' => 'display:inline-block;']) !!}
                                        <button type="button" class="btn btn-danger waves-effect waves-light" onclick="confirmClearAllScanned()"><i class="fa fa-trash"></i> Remove All Scanned Film Stock Records</button>
                                        {!! Form::close() !!}
                                    @endif
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Video Name</th>
                                            <th>Preview</th>
                                            <th>File ID</th>
                                            <th>Size</th>
                                            <th>Folder ID</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($files as $i => $file)
                                            <tr>
                                                <td>{{ $files->firstItem() + $i }}</td>
                                                <td>
                                                    <strong>{{ $file->name }}</strong>
                                                </td>
                                                <td>
                                                    <video src="{{ $file->stream_url }}" controls style="max-width: 140px; max-height: 80px; border-radius: 4px; background: #000;"></video>
                                                </td>
                                                <td><code>{{ $file->file_id }}</code></td>
                                                <td><span class="badge badge-info">{{ $file->formatted_size }}</span></td>
                                                <td><code>{{ $file->folder_id }}</code></td>
                                                <td>
                                                    <span class="badge badge-primary">Scanned</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ $file->url }}" target="_blank" class="btn btn-xs btn-secondary" title="Open GDrive Direct Link"><i class="fa fa-external-link"></i></a>
                                                        
                                                        {!! Form::open(['route' => ['admin.film-stock-drive-files.block', $file->id], 'method' => 'POST', 'style' => 'display:inline-block;']) !!}
                                                        <button type="submit" class="btn btn-xs btn-warning" title="Block File"><i class="fa fa-ban"></i></button>
                                                        {!! Form::close() !!}

                                                        {!! Form::open(['route' => ['admin.film-stock-drive-files.delete', $file->id], 'method' => 'POST', 'style' => 'display:inline-block;']) !!}
                                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Delete this Film Stock record?')" title="Remove Record"><i class="fa fa-remove"></i></button>
                                                        {!! Form::close() !!}
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">No Film Stock video files scanned yet. Enter a Google Drive folder URL or ID above and click <strong>Scan & Fetch GDrive Film Stock Videos</strong>.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $files->links() }}
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmClearAllScanned() {
            Swal.fire({
                title: 'Remove All Scanned Film Stock Files?',
                text: "This will delete all scanned non-imported Film Stock records.",
                icon: 'warning',
                showCancelButton: true,
                confirmColor: '#d33',
                cancelColor: '#3085d6',
                confirmButtonText: 'Yes, Remove All!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('clear-all-scanned-form').submit();
                }
            });
        }
    </script>
@endsection
