<?php $__env->startSection("content"); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="<?php echo e(route('admin.photos.create')); ?>" class="btn btn-success btn-md waves-effect waves-light m-b-20" data-toggle="tooltip" title="Add Photo">
                                    <i class="fa fa-plus"></i> Add Photo
                                </a>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="<?php echo e(route('admin.photos.index')); ?>" class="app-search" role="search">
                                    <input type="text" name="search" placeholder="Search photos..." class="form-control" value="<?php echo e(request('search')); ?>">
                                    <button type="submit"><i class="fa fa-search"></i></button>
                                </form>
                            </div>
                            <div class="col-md-2">
                                <form method="GET" action="<?php echo e(route('admin.photos.index')); ?>" role="search">
                                    <select name="category" class="form-control" onchange="this.form.submit();">
                                        <option value="">All Categories</option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($cat); ?>" <?php echo e(request('category') == $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </form>
                            </div>
                            <div class="col-md-2">
                                <form method="GET" action="<?php echo e(route('admin.photos.index')); ?>" role="search">
                                    <select name="status" class="form-control" onchange="this.form.submit();">
                                        <option value="">All Status</option>
                                        <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                                        <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                    </select>
                                </form>
                            </div>
                            <div class="col-md-2">
                                <a href="<?php echo e(route('admin.photos.index')); ?>" class="btn btn-info waves-effect waves-light">Reset</a>
                            </div>
                        </div>

                        <br>

                        <div class="row">
                            <?php $__currentLoopData = $photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6" id="card_box_id_<?php echo e($photo->id); ?>">
                                    <!-- Simple card -->
                                    <div class="card m-b-20">
                                        <div class="wall-list-item">
                                            <?php if($photo->status == 'active'): ?>
                                                <span class="badge badge-success wall_sub_text">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger wall_sub_text">Inactive</span>
                                            <?php endif; ?>

                                            <?php if($photo->image_path): ?>
                                                <img class="card-img-top thumb-xs img-fluid"
                                                     src="<?php echo e($photo->image_url); ?>"
                                                     alt="<?php echo e($photo->title); ?>"
                                                     style="width: 100%; height: 200px; object-fit: cover;">
                                            <?php else: ?>
                                                <img class="card-img-top thumb-xs img-fluid"
                                                     src="<?php echo e(URL::to('public/admin_assets/images/users/no-image.jpg')); ?>"
                                                     alt="No Image"
                                                     style="width: 100%; height: 200px; object-fit: cover;">
                                            <?php endif; ?>
                                        </div>

                                        <div class="card-body p-3">
                                            <h4 class="card-title book_title mb-3 d-flex align-items-center">
                                                <?php echo e(Str::limit($photo->title, 25)); ?>

                                            </h4>

                                            <div class="mb-2">
                                                <?php if($photo->category): ?>
                                                    <small class="text-muted"><i class="fa fa-tag"></i> <?php echo e($photo->category); ?></small><br>
                                                <?php endif; ?>
                                                <?php if($photo->width && $photo->height): ?>
                                                    <small class="text-muted"><i class="fa fa-image"></i> <?php echo e($photo->width); ?>x<?php echo e($photo->height); ?></small><br>
                                                <?php endif; ?>
                                                <?php if($photo->license_price && $photo->license_price > 0): ?>
                                                    <small class="text-warning"><i class="fa fa-crown"></i> Premium: <?php echo e(getcong('currency_symbol')); ?><?php echo e(number_format($photo->license_price, 2)); ?></small><br>
                                                <?php else: ?>
                                                    <small class="text-success"><i class="fa fa-gift"></i> Free</small><br>
                                                <?php endif; ?>
                                                <small class="text-muted"><i class="fa fa-download"></i> <?php echo e($photo->download_count); ?> | <i class="fa fa-eye"></i> <?php echo e($photo->view_count); ?></small>
                                            </div>

                                            <div class="btn-group">
                                                <a href="<?php echo e(route('admin.photos.show', $photo->id)); ?>" class="btn btn-icon waves-effect waves-light btn-info m-r-5" data-toggle="tooltip" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.photos.edit', $photo->id)); ?>" class="btn btn-icon waves-effect waves-light btn-success m-r-5" data-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="javascript:void(0)" onclick="confirmDelete('<?php echo e(route('admin.photos.destroy', $photo->id)); ?>')" class="btn btn-icon waves-effect waves-light btn-danger" data-toggle="tooltip" title="Remove">
                                                    <i class="fa fa-remove"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <?php if($photos->hasPages()): ?>
                            <nav>
                                <?php echo e($photos->appends(request()->query())->links()); ?>

                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection("javascript"); ?>
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
            csrfToken.value = '<?php echo e(csrf_token()); ?>';
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make("admin.admin_app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/admin/photos/index.blade.php ENDPATH**/ ?>