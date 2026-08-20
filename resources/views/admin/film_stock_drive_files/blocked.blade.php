@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-12">
                        <div class="card-box table-responsive">
                            <h4 class="m-t-0 header-title"><b>Blocked Film Stock Files</b></h4>

                            @if (Session::has('flash_message'))
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    {{ Session::get('flash_message') }}
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Video Name</th>
                                            <th>File ID</th>
                                            <th>Size</th>
                                            <th>Folder ID</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($files as $i => $file)
                                            <tr>
                                                <td>{{ $files->firstItem() + $i }}</td>
                                                <td><strong>{{ $file->name }}</strong></td>
                                                <td><code>{{ $file->file_id }}</code></td>
                                                <td><span class="badge badge-info">{{ $file->formatted_size }}</span></td>
                                                <td><code>{{ $file->folder_id }}</code></td>
                                                <td>
                                                    {!! Form::open(['route' => ['admin.film-stock-drive-files.unblock', $file->id], 'method' => 'POST', 'style' => 'display:inline-block;']) !!}
                                                    <button type="submit" class="btn btn-xs btn-success" title="Unblock File"><i class="fa fa-check"></i> Unblock</button>
                                                    {!! Form::close() !!}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">No blocked Film Stock video files.</td>
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
@endsection
