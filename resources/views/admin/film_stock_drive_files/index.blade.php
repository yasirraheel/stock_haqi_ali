@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card-box table-responsive">

                            <div class="row mb-3 align-items-center">
                                <div class="col-md-3">
                                    <h4 class="m-t-0 header-title"><b>FILM STOCK MANAGEMENT</b></h4>
                                </div>
                                <div class="col-md-6">
                                    {!! Form::open(['route' => 'admin.film-stock-drive-files.index', 'class' => 'app-search', 'id' => 'search', 'role' => 'form', 'method' => 'get']) !!}
                                    <input type="text" name="s" placeholder="Search by title..." value="{{ request('s') }}" class="form-control">
                                    <button type="submit"><i class="fa fa-search"></i></button>
                                    {!! Form::close() !!}
                                </div>
                                <div class="col-md-3 text-right">
                                    @if ($totalFiles > 0)
                                        {!! Form::open(['route' => 'admin.film-stock-drive-files.clear-all', 'method' => 'POST', 'id' => 'clear-all-scanned-form', 'style' => 'display:inline-block;']) !!}
                                        <button type="button" class="btn btn-danger btn-md waves-effect waves-light" onclick="confirmClearAllScanned()"><i class="fa fa-trash"></i> Remove All</button>
                                        {!! Form::close() !!}
                                    @endif
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

                            <!-- Scan Film Stock Folder Form -->
                            <div class="p-3 mb-4" style="background-color: #212529; border-radius: 5px; border: 1px solid #32383e;">
                                {!! Form::open(['route' => 'admin.film-stock-drive-files.scan', 'class' => 'form-inline', 'role' => 'form', 'method' => 'post']) !!}
                                <div class="input-group w-100">
                                    <input type="text" name="folder_input" id="folder_input" class="form-control"
                                        placeholder="Paste Google Drive Folder URL or Folder ID to fetch Film Stock videos..."
                                        value="{{ request('folder_id') }}" required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-success waves-effect waves-light"><i class="fa fa-google"></i> Scan & Fetch GDrive Film Stock</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Preview</th>
                                            <th>Format</th>
                                            <th>File Size</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($files as $i => $file)
                                            <tr>
                                                <td><strong>{{ $file->name }}</strong></td>
                                                <td>
                                                    <video src="{{ $file->stream_url }}" controls style="max-width: 140px; max-height: 80px; border-radius: 4px; background: #000;"></video>
                                                </td>
                                                <td>{{ strtoupper(pathinfo($file->name, PATHINFO_EXTENSION) ?: 'mp4') }}</td>
                                                <td><span class="badge badge-info">{{ $file->formatted_size }}</span></td>
                                                <td><span class="badge badge-success">Active</span></td>
                                                <td>
                                                    <a href="{{ $file->url }}" target="_blank" class="btn btn-icon waves-effect waves-light btn-primary m-b-5 m-r-5" data-toggle="tooltip" title="View GDrive Link"> <i class="fa fa-eye"></i> </a>
                                                    {!! Form::open(['route' => ['admin.film-stock-drive-files.delete', $file->id], 'method' => 'POST', 'style' => 'display:inline-block;']) !!}
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger m-b-5" onclick="return confirm('Remove this Film Stock record?')" data-toggle="tooltip" title="Remove"> <i class="fa fa-remove"></i> </button>
                                                    {!! Form::close() !!}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">No Film Stock video files found. Enter a Google Drive folder URL or ID above to scan.</td>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmClearAllScanned() {
            Swal.fire({
                title: 'Remove All Scanned Film Stock Files?',
                text: "This will delete all scanned Film Stock records.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Remove All!',
                background: '#1a2234',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('clear-all-scanned-form').submit();
                }
            });
        }
    </script>
@endsection
