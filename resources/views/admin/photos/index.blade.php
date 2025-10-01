@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="{{ route('admin.photos.create') }}" class="btn btn-success btn-md waves-effect waves-light m-b-20" data-toggle="tooltip" title="Add Photo">
                                    <i class="fa fa-plus"></i> Add Photo
                                </a>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('admin.photos.index') }}" class="app-search" role="search">
                                    <input type="text" name="search" placeholder="Search photos..." class="form-control" value="{{ request('search') }}">
                                    <button type="submit"><i class="fa fa-search"></i></button>
                                </form>
                            </div>
                            <div class="col-md-2">
                                <form method="GET" action="{{ route('admin.photos.index') }}" role="search">
                                    <select name="category" class="form-control" onchange="this.form.submit();">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                            <div class="col-md-2">
                                <form method="GET" action="{{ route('admin.photos.index') }}" role="search">
                                    <select name="status" class="form-control" onchange="this.form.submit();">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </form>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.photos.index') }}" class="btn btn-info waves-effect waves-light">Reset</a>
                            </div>
                        </div>

                        <br>

                        <div class="row">
                            @foreach($photos as $i => $photo)
                                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6" id="card_box_id_{{ $photo->id }}">
                                    <!-- Simple card -->
                                    <div class="card m-b-20">
                                        <div class="wall-list-item">
                                            @if($photo->status == 'active')
                                                <span class="badge badge-success wall_sub_text">Active</span>
                                            @else
                                                <span class="badge badge-danger wall_sub_text">Inactive</span>
                                            @endif

                                            @if($photo->image_path)
                                                <img class="card-img-top thumb-xs img-fluid"
                                                     src="{{ $photo->image_url }}"
                                                     alt="{{ $photo->title }}"
                                                     style="width: 100%; height: 200px; object-fit: cover;">
                                            @else
                                                <img class="card-img-top thumb-xs img-fluid"
                                                     src="{{ URL::to('public/admin_assets/images/users/no-image.jpg') }}"
                                                     alt="No Image"
                                                     style="width: 100%; height: 200px; object-fit: cover;">
                                            @endif
                                        </div>

                                        <div class="card-body p-3">
                                            <h4 class="card-title book_title mb-3 d-flex align-items-center">
                                                {{ Str::limit($photo->title, 25) }}
                                            </h4>

                                            <div class="mb-2">
                                                @if($photo->category)
                                                    <small class="text-muted"><i class="fa fa-tag"></i> {{ $photo->category }}</small><br>
                                                @endif
                                                @if($photo->width && $photo->height)
                                                    <small class="text-muted"><i class="fa fa-image"></i> {{ $photo->width }}x{{ $photo->height }}</small><br>
                                                @endif
                                                <small class="text-muted"><i class="fa fa-download"></i> {{ $photo->download_count }} | <i class="fa fa-eye"></i> {{ $photo->view_count }}</small>
                                            </div>

                                            <div class="btn-group">
                                                <a href="{{ route('admin.photos.show', $photo->id) }}" class="btn btn-icon waves-effect waves-light btn-info m-r-5" data-toggle="tooltip" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.photos.edit', $photo->id) }}" class="btn btn-icon waves-effect waves-light btn-success m-r-5" data-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="javascript:void(0)" onclick="confirmDelete('{{ route('admin.photos.destroy', $photo->id) }}')" class="btn btn-icon waves-effect waves-light btn-danger" data-toggle="tooltip" title="Remove">
                                                    <i class="fa fa-remove"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($photos->hasPages())
                            <nav>
                                {{ $photos->appends(request()->query())->links() }}
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section("javascript")
<script>
function confirmDelete(deleteUrl) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        background: "#1a2234",
        color: "#fff"
    }).then((result) => {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;

            var csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            var methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
