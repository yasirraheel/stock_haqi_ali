<?php $__env->startSection("content"); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="m-t-0 header-title">Audio Details</h4>
                            </div>
                            <div class="col-md-6">
                                <a href="<?php echo e(route('admin.audio.index')); ?>" class="btn btn-secondary btn-sm pull-right">
                                    <i class="fa fa-arrow-left"></i> Back to List
                                </a>
                                <a href="<?php echo e(route('admin.audio.edit', $audio->id)); ?>" class="btn btn-warning btn-sm pull-right" style="margin-right: 10px;">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Title:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <?php echo e($audio->title); ?>

                                </div>
                            </div>
                            <hr>

                            <?php if($audio->description): ?>
                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Description:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <?php echo e($audio->description); ?>

                                </div>
                            </div>
                            <hr>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Audio File:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <?php if($audio->audio_path): ?>
                                        <audio controls class="w-100">
                                            <source src="<?php echo e(asset('storage/' . $audio->audio_path)); ?>" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                        <br>
                                        <a href="<?php echo e(asset('storage/' . $audio->audio_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="fa fa-download"></i> Download Audio
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No audio file</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>License Price:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <?php if($audio->license_price && $audio->license_price > 0): ?>
                                        <span class="badge badge-warning">$<?php echo e(number_format($audio->license_price, 2)); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Free</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Status:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <?php if($audio->is_active): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Downloads:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <?php echo e($audio->downloads_count); ?>

                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Views:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <?php echo e($audio->views_count); ?>

                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Created:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <?php echo e($audio->created_at->format('M d, Y H:i:s')); ?>

                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Updated:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <?php echo e($audio->updated_at->format('M d, Y H:i:s')); ?>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Audio Information</h5>
                                </div>
                                <div class="card-body">
                                    <?php if($audio->duration): ?>
                                    <div class="mb-3">
                                        <strong>Duration:</strong><br>
                                        <?php echo e($audio->duration); ?>

                                    </div>
                                    <?php endif; ?>

                                    <?php if($audio->file_size): ?>
                                    <div class="mb-3">
                                        <strong>File Size:</strong><br>
                                        <?php echo e($audio->file_size); ?>

                                    </div>
                                    <?php endif; ?>

                                    <?php if($audio->format): ?>
                                    <div class="mb-3">
                                        <strong>Format:</strong><br>
                                        <span class="badge badge-info"><?php echo e($audio->format); ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <?php if($audio->bitrate): ?>
                                    <div class="mb-3">
                                        <strong>Bitrate:</strong><br>
                                        <?php echo e($audio->bitrate); ?> kbps
                                    </div>
                                    <?php endif; ?>

                                    <?php if($audio->sample_rate): ?>
                                    <div class="mb-3">
                                        <strong>Sample Rate:</strong><br>
                                        <?php echo e(number_format($audio->sample_rate)); ?> Hz
                                    </div>
                                    <?php endif; ?>

                                    <?php if($audio->genre): ?>
                                    <div class="mb-3">
                                        <strong>Genre:</strong><br>
                                        <span class="badge badge-secondary"><?php echo e($audio->genre); ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <?php if($audio->mood): ?>
                                    <div class="mb-3">
                                        <strong>Mood:</strong><br>
                                        <span class="badge badge-primary"><?php echo e($audio->mood); ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <?php if($audio->tags): ?>
                                    <div class="mb-3">
                                        <strong>Tags:</strong><br>
                                        <?php $__currentLoopData = explode(',', $audio->tags); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge badge-light mr-1"><?php echo e(trim($tag)); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group-vertical w-100" role="group">
                                        <a href="<?php echo e(route('admin.audio.edit', $audio->id)); ?>" class="btn btn-warning mb-2">
                                            <i class="fas fa-edit"></i> Edit Audio
                                        </a>
                                        <form action="<?php echo e(route('admin.audio.destroy', $audio->id)); ?>" method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this audio?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fas fa-trash"></i> Delete Audio
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("admin.admin_app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/admin/audio/show.blade.php ENDPATH**/ ?>