<?php $__env->startSection("content"); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-sm-6">
                                <a href="<?php echo e(route('admin.photos.index')); ?>">
                                    <h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;">
                                        <i class="fa fa-arrow-left"></i> <?php echo e($page_title); ?>

                                    </h4>
                                </a>
                            </div>
                            <div class="col-sm-6">
                                &nbsp;
                            </div>
                        </div>

                        <form action="<?php echo e(route('admin.photos.store')); ?>" method="post" enctype="multipart/form-data" id="photo_form" class="form-horizontal" role="form">
                            <?php echo csrf_field(); ?>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Title <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" name="title" id="title" value="<?php echo e(old('title')); ?>" placeholder="Enter photo title" required class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Category</label>
                                <div class="col-sm-8">
                                    <select name="category" id="category" class="form-control">
                                        <option value="">Select a category</option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value); ?>" <?php echo e(old('category') == $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <textarea name="description" id="description" rows="3" placeholder="Enter photo description" class="form-control"><?php echo e(old('description')); ?></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Status <span class="text-danger">*</span></label>
                                <div class="col-sm-4">
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="active" <?php echo e(old('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                                        <option value="inactive" <?php echo e(old('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tags</label>
                                <div class="col-sm-8">
                                    <input type="text" name="tags" id="tags" value="<?php echo e(old('tags')); ?>" placeholder="nature, landscape, sunset (comma separated)" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Keywords</label>
                                <div class="col-sm-8">
                                    <input type="text" name="keywords" id="keywords" value="<?php echo e(old('keywords')); ?>" placeholder="Additional keywords for SEO" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">License Price</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo e(getcong('currency_symbol')); ?></span>
                                        </div>
                                        <input type="number" name="license_price" id="license_price" value="<?php echo e(old('license_price')); ?>" placeholder="0.00" step="0.01" min="0" class="form-control">
                                    </div>
                                    <small class="form-text text-muted">License price for commercial use (leave empty for free photos)</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Image <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="file" name="image" id="image" accept="image/*" required class="form-control" onchange="handleImageUpload(this)">
                                    <small class="form-text text-muted">Supported formats: JPEG, PNG, GIF, BMP, TIFF, WebP (Max: 20MB)</small>
                                </div>
                            </div>

                            <div id="image_preview" style="display: none;" class="form-group row">
                                <label class="col-sm-3 col-form-label">Preview</label>
                                <div class="col-sm-8">
                                    <img id="preview_img" src="" alt="Preview" style="max-width: 300px; max-height: 200px;" class="img-thumbnail">
                                </div>
                            </div>

                            <!-- Metadata Section -->
                            <div id="metadata_section" style="display: none;" class="form-group row">
                                <label class="col-sm-3 col-form-label">Image Metadata</label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card border-secondary">
                                                <div class="card-body py-2 text-white">
                                                    <small><strong>Dimensions:</strong> <span id="meta_dimensions">-</span></small><br>
                                                    <small><strong>File Size:</strong> <span id="meta_file_size">-</span></small><br>
                                                    <small><strong>File Type:</strong> <span id="meta_file_type">-</span></small><br>
                                                    <small><strong>Color Space:</strong> <span id="meta_color_space">-</span></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-secondary">
                                                <div class="card-body py-2 text-white">
                                                    <small><strong>Camera:</strong> <span id="meta_camera_make">-</span> <span id="meta_camera_model"></span></small><br>
                                                    <small><strong>Lens:</strong> <span id="meta_lens_model">-</span></small><br>
                                                    <small><strong>Settings:</strong> <span id="meta_focal_length">-</span> <span id="meta_aperture">-</span></small><br>
                                                    <small><strong>ISO:</strong> <span id="meta_iso">-</span></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-3">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <i class="fa fa-save"></i> Save Photo
                                    </button>
                                    <a href="<?php echo e(route('admin.photos.index')); ?>" class="btn btn-secondary waves-effect waves-light">
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection("javascript"); ?>
<script type="text/javascript">
function handleImageUpload(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $('#preview_img').attr('src', e.target.result);
            $('#image_preview').show();
        }

        reader.readAsDataURL(input.files[0]);

        // Extract metadata
        extractMetadata(input.files[0]);
    }
}

function extractMetadata(file) {
    var formData = new FormData();
    formData.append('image', file);
    formData.append('_token', '<?php echo e(csrf_token()); ?>');

    $.ajax({
        url: '<?php echo e(route("admin.photos.metadata")); ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success && response.metadata) {
                var meta = response.metadata;

                // Update metadata display
                $('#meta_dimensions').text((meta.width && meta.height) ? meta.width + ' x ' + meta.height + ' px' : '-');
                $('#meta_file_size').text(formatFileSize(meta.file_size) || '-');
                $('#meta_file_type').text(meta.file_type ? meta.file_type.toUpperCase() : '-');
                $('#meta_color_space').text(meta.color_space || '-');
                $('#meta_camera_make').text(meta.camera_make || '');
                $('#meta_camera_model').text(meta.camera_model || '');
                $('#meta_lens_model').text(meta.lens_model || '-');
                $('#meta_focal_length').text(meta.focal_length || '');
                $('#meta_aperture').text(meta.aperture || '');
                $('#meta_iso').text(meta.iso || '-');

                $('#metadata_section').show();

                // Auto-fill some form fields if they're empty
                if (!$('#title').val() && meta.file_name) {
                    var titleFromFile = meta.file_name.replace(/\.[^/.]+$/, "").replace(/[_-]/g, ' ');
                    titleFromFile = titleFromFile.charAt(0).toUpperCase() + titleFromFile.slice(1);
                    $('#title').val(titleFromFile);
                }

                if (!$('#keywords').val() && meta.artist) {
                    $('#keywords').val('photography, ' + meta.artist);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error extracting metadata:', error);
            $('#metadata_section').show();
            $('.metadata-value').text('-');
        }
    });
}

function formatFileSize(bytes) {
    if (!bytes) return '';

    var sizes = ['B', 'KB', 'MB', 'GB'];
    var i = 0;
    while (bytes >= 1024 && i < sizes.length - 1) {
        bytes /= 1024;
        i++;
    }
    return Math.round(bytes * 100) / 100 + ' ' + sizes[i];
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("admin.admin_app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/admin/photos/create.blade.php ENDPATH**/ ?>