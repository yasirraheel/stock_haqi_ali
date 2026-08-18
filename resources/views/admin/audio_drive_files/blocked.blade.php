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
                                            <h5 style="color: #f9f9f9;">Active Scanned Audio Files</h5>
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

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    {!! Form::open(['route' => 'admin.audio-drive-files.blocked', 'class' => 'app-search', 'id' => 'search', 'role' => 'form', 'method' => 'get']) !!}
                                    <input type="text" name="s" placeholder="Search blocked audio by name, file ID..." value="{{ request('s') }}" class="form-control">
                                    <button type="submit"><i class="fa fa-search"></i></button>
                                    {!! Form::close() !!}
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Audio Track Name</th>
                                            <th>File Size</th>
                                            <th>Status</th>
                                            <th class="text-center" width="120">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($files as $i => $file)
                                            <tr>
                                                <td>{{ $files->firstItem() + $i }}</td>
                                                <td><strong>{{ $file->name }}</strong></td>
                                                <td>{{ $file->formatted_size }}</td>
                                                <td><span class="badge badge-danger">Blocked</span></td>
                                                <td class="text-center">
                                                    {!! Form::open(['route' => ['admin.audio-drive-files.unblock', $file->id], 'method' => 'POST', 'style' => 'display:inline-block;']) !!}
                                                    <button type="submit" class="btn btn-sm btn-success" data-toggle="tooltip" title="Unblock Audio Track"><i class="fa fa-check"></i> Unblock</button>
                                                    {!! Form::close() !!}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center p-4">No blocked audio files found.</td>
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
@endsection
