@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-sm-6">
                                <a href="{{ route('admin.photos.index') }}">
                                    <h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;">
                                        <i class="fa fa-arrow-left"></i> {{ $page_title }}
                                    </h4>
                                </a>
                            </div>
                            <div class="col-sm-6">
                                &nbsp;
                            </div>
                        </div>

                        <form action="{{ route('admin.photos.update', $photo->id) }}" method="post" enctype="multipart/form-data" id="photo_form" class="form-horizontal" role="form">
                            @csrf
                            @method('PUT')

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Title <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" name="title" id="title" value="{{ old('title', $photo->title) }}" placeholder="Enter photo title" required class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Category</label>
                                <div class="col-sm-8">
                                    <select name="category" id="category" class="form-control">
                                        <option value="">Select a category</option>
                                        @foreach($categories as $value => $label)
                                            <option value="{{ $value }}" {{ old('category', $photo->category) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <textarea name="description" id="description" rows="3" placeholder="Enter photo description" class="form-control">{{ old('description', $photo->description) }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Status <span class="text-danger">*</span></label>
                                <div class="col-sm-4">
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="active" {{ old('status', $photo->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $photo->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tags</label>
                                <div class="col-sm-8">
                                    <input type="text" name="tags" id="tags" value="{{ old('tags', $photo->tags) }}" placeholder="nature, landscape, sunset (comma separated)" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Keywords</label>
                                <div class="col-sm-8">
                                    <input type="text" name="keywords" id="keywords" value="{{ old('keywords', $photo->keywords) }}" placeholder="Additional keywords for SEO" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">License Price</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{getcong('currency_symbol')}}</span>
                                        </div>
                                        <input type="number" name="license_price" id="license_price" value="{{ old('license_price', $photo->license_price) }}" placeholder="0.00" step="0.01" min="0" class="form-control" pattern="[0-9]+([.][0-9]{1,2})?">
                                    </div>
                                    <small class="form-text text-muted">License price for commercial use (leave empty for free photos)</small>
                                </div>
                            </div>

                            <!-- Current Image -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Current Image</label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-md-4">
                                            @if($photo->image_path)
                                                <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" class="img-fluid img-thumbnail">
                                            @else
                                                <img src="{{ URL::to('public/admin_assets/images/users/no-image.jpg') }}" alt="No Image" class="img-fluid img-thumbnail">
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <small><strong>Dimensions:</strong> {{ $photo->dimensions ?: 'N/A' }}</small><br>
                                            <small><strong>File Size:</strong> {{ $photo->formatted_file_size ?: 'N/A' }}</small><br>
                                            <small><strong>File Type:</strong> {{ $photo->file_type ? strtoupper($photo->file_type) : 'N/A' }}</small><br>
                                            @if($photo->camera_make || $photo->camera_model)
                                                <small><strong>Camera:</strong> {{ $photo->camera_make ?: '' }} {{ $photo->camera_model ?: '' }}</small><br>
                                            @endif
                                            @if($photo->date_taken)
                                                <small><strong>Date Taken:</strong> {{ $photo->date_taken->format('M d, Y H:i') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Change Image</label>
                                <div class="col-sm-8">
                                    <input type="file" name="image" id="image" accept="image/*" class="form-control" onchange="handleImageUpload(this)">
                                    <small class="form-text text-muted">Leave empty to keep current image. Supported formats: JPEG, PNG, GIF, BMP, TIFF, WebP (Max: 20MB)</small>
                                </div>
                            </div>

                            <div id="image_preview" style="display: none;" class="form-group row">
                                <label class="col-sm-3 col-form-label">New Preview</label>
                                <div class="col-sm-8">
                                    <img id="preview_img" src="" alt="Preview" style="max-width: 300px; max-height: 200px;" class="img-thumbnail">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-3">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <i class="fa fa-save"></i> Update Photo
                                    </button>
                                    <a href="{{ route('admin.photos.index') }}" class="btn btn-secondary waves-effect waves-light">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section("javascript")
<script type="text/javascript">
function handleImageUpload(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $('#preview_img').attr('src', e.target.result);
            $('#image_preview').show();
        }

        reader.readAsDataURL(input.files[0]);
    }
}

// Ensure license price is properly formatted
$(document).ready(function() {
    $('#license_price').on('blur', function() {
        var value = $(this).val();
        if (value && !isNaN(value)) {
            $(this).val(parseFloat(value).toFixed(2));
        }
    });
});
</script>
@endsection
