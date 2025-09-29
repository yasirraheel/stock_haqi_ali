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

                        <form action="<?php echo e(route('admin.photos.update', $photo->id)); ?>" method="post" enctype="multipart/form-data" id="photo_form" class="form-horizontal" role="form">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Title <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" name="title" id="title" value="<?php echo e(old('title', $photo->title)); ?>" placeholder="Enter photo title" required class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Category</label>
                                <div class="col-sm-8">
                                    <select name="category" id="category" class="form-control">
                                        <option value="">Select a category</option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value); ?>" <?php echo e(old('category', $photo->category) == $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <textarea name="description" id="description" rows="3" placeholder="Enter photo description" class="form-control"><?php echo e(old('description', $photo->description)); ?></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Status <span class="text-danger">*</span></label>
                                <div class="col-sm-4">
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="active" <?php echo e(old('status', $photo->status) == 'active' ? 'selected' : ''); ?>>Active</option>
                                        <option value="inactive" <?php echo e(old('status', $photo->status) == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tags</label>
                                <div class="col-sm-8">
                                    <input type="text" name="tags" id="tags" value="<?php echo e(old('tags', $photo->tags)); ?>" placeholder="nature, landscape, sunset (comma separated)" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Keywords</label>
                                <div class="col-sm-8">
                                    <input type="text" name="keywords" id="keywords" value="<?php echo e(old('keywords', $photo->keywords)); ?>" placeholder="Additional keywords for SEO" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">License Price</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo e(getcong('currency_symbol')); ?></span>
                                        </div>
                                        <input type="number" name="license_price" id="license_price" value="<?php echo e(old('license_price', $photo->license_price)); ?>" placeholder="0.00" step="0.01" min="0" class="form-control" pattern="[0-9]+([.][0-9]{1,2})?">
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
                                            <?php if($photo->image_path): ?>
                                                <img src="<?php echo e($photo->image_url); ?>" alt="<?php echo e($photo->title); ?>" class="img-fluid img-thumbnail">
                                            <?php else: ?>
                                                <img src="<?php echo e(URL::to('public/admin_assets/images/users/no-image.jpg')); ?>" alt="No Image" class="img-fluid img-thumbnail">
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-8">
                                            <small><strong>Dimensions:</strong> <?php echo e($photo->dimensions ?: 'N/A'); ?></small><br>
                                            <small><strong>File Size:</strong> <?php echo e($photo->formatted_file_size ?: 'N/A'); ?></small><br>
                                            <small><strong>File Type:</strong> <?php echo e($photo->file_type ? strtoupper($photo->file_type) : 'N/A'); ?></small><br>
                                            <?php if($photo->camera_make || $photo->camera_model): ?>
                                                <small><strong>Camera:</strong> <?php echo e($photo->camera_make ?: ''); ?> <?php echo e($photo->camera_model ?: ''); ?></small><br>
                                            <?php endif; ?>
                                            <?php if($photo->date_taken): ?>
                                                <small><strong>Date Taken:</strong> <?php echo e($photo->date_taken->format('M d, Y H:i')); ?></small>
                                            <?php endif; ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make("admin.admin_app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/admin/photos/edit.blade.php ENDPATH**/ ?>