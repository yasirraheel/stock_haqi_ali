@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="col-sm-6">
                                <h4 class="header-title m-t-0 m-b-30">{{ $page_title }}</h4>
                            </div>
                            <div class="col-sm-6 text-right">
                                <a href="{{ route('admin.photos.index') }}" class="btn btn-secondary btn-md waves-effect waves-light m-b-20">
                                    <i class="fa fa-arrow-left"></i> Back to Photos
                                </a>
                            </div>
                        </div>

                        @if(Session::has('flash_message'))
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                {{ Session::get('flash_message') }}
                            </div>
                        @endif

                        @if(Session::has('error'))
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                {{ Session::get('error') }}
                            </div>
                        @endif

                        <!-- Create New Category Form -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card-box">
                                    <h5 class="header-title">Add New Category</h5>
                                    <form method="POST" action="{{ route('admin.photo-categories.store') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <input type="text" name="name" placeholder="Category Name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <input type="text" name="description" placeholder="Description (optional)" value="{{ old('description') }}" class="form-control @error('description') is-invalid @enderror">
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="submit" class="btn btn-success btn-md waves-effect waves-light">
                                                    <i class="fa fa-plus"></i> Add
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Categories List -->
                        <div class="row">
                            <div class="col-12">
                                @if($categories->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Slug</th>
                                                    <th>Description</th>
                                                    <th>Photos Count</th>
                                                    <th>Status</th>
                                                    <th>Created</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($categories as $i => $category)
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td><strong>{{ $category->name }}</strong></td>
                                                        <td>{{ $category->slug }}</td>
                                                        <td>{{ $category->description ?: 'N/A' }}</td>
                                                        <td>
                                                            <span class="badge badge-info">{{ $category->photos()->count() }} photos</span>
                                                        </td>
                                                        <td>
                                                            @if($category->status == 'active')
                                                                <span class="badge badge-success">Active</span>
                                                            @else
                                                                <span class="badge badge-danger">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $category->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <button onclick="confirmDelete('{{ route('admin.photo-categories.destroy', $category->id) }}')" class="btn btn-icon waves-effect waves-light btn-danger btn-sm" data-toggle="tooltip" title="Delete">
                                                                <i class="fa fa-remove"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <div class="card-box">
                                            <div class="p-4">
                                                <i class="fa fa-tags fa-3x text-muted mb-3"></i>
                                                <h5>No Categories Found</h5>
                                                <p class="text-muted">Start by creating your first photo category using the form above.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal/Script -->
<script>
function confirmDelete(deleteUrl) {
    if (confirm('Are you sure you want to delete this category?')) {
        // Create a form and submit it
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
}
</script>

@endsection
