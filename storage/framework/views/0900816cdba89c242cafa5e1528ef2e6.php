<?php $__env->startSection("content"); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-sm-8">
                                <a href="<?php echo e(route('admin.photos.index')); ?>">
                                    <h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;">
                                        <i class="fa fa-arrow-left"></i> <?php echo e($page_title); ?>: <?php echo e($photo->title); ?>

                                    </h4>
                                </a>
                            </div>
                            <div class="col-sm-4 text-right">
                                <a href="<?php echo e(route('admin.photos.edit', $photo->id)); ?>" class="btn btn-success btn-sm waves-effect waves-light">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Image Display -->
                            <div class="col-md-6">
                                <div class="card-box">
                                    <h5 class="header-title">Image Preview</h5>
                                    <div class="text-center">
                                        <?php if($photo->image_path): ?>
                                            <img src="<?php echo e($photo->image_url); ?>" alt="<?php echo e($photo->title); ?>" class="img-fluid" style="max-height: 400px; border: 1px solid #ddd; border-radius: 4px;">
                                            <div class="mt-3">
                                                <a href="<?php echo e($photo->image_url); ?>" target="_blank" class="btn btn-info btn-sm">
                                                    <i class="fa fa-external-link"></i> View Full Size
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <img src="<?php echo e(URL::to('public/admin_assets/images/users/no-image.jpg')); ?>" alt="No Image" class="img-fluid">
                                            <p class="text-muted mt-2">No image available</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card-box">
                                    <h5 class="header-title">Basic Information</h5>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-borderless">
                                            <tr>
                                                <td><strong>Title:</strong></td>
                                                <td><?php echo e($photo->title); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Description:</strong></td>
                                                <td><?php echo e($photo->description ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Category:</strong></td>
                                                <td><?php echo e($photo->category ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tags:</strong></td>
                                                <td><?php echo e($photo->tags ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Keywords:</strong></td>
                                                <td><?php echo e($photo->keywords ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    <?php if($photo->status == 'active'): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Downloads:</strong></td>
                                                <td><?php echo e(number_format($photo->download_count)); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Views:</strong></td>
                                                <td><?php echo e(number_format($photo->view_count)); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Technical Details -->
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="card-box">
                                    <h6 class="header-title">File Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>Original Name:</strong></td>
                                                <td><?php echo e($photo->file_name ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>File Size:</strong></td>
                                                <td><?php echo e($photo->formatted_file_size ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>File Type:</strong></td>
                                                <td><?php echo e($photo->file_type ? strtoupper($photo->file_type) : 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>MIME Type:</strong></td>
                                                <td><?php echo e($photo->mime_type ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Dimensions:</strong></td>
                                                <td><?php echo e($photo->dimensions ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Resolution:</strong></td>
                                                <td><?php echo e($photo->resolution ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Color Space:</strong></td>
                                                <td><?php echo e($photo->color_space ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Bit Depth:</strong></td>
                                                <td><?php echo e($photo->bit_depth ? $photo->bit_depth . ' bit' : 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Transparency:</strong></td>
                                                <td><?php echo e($photo->has_transparency ? 'Yes' : 'No'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Software:</strong></td>
                                                <td><?php echo e($photo->software ?: 'N/A'); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card-box">
                                    <h6 class="header-title">Camera & Lens</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>Camera Make:</strong></td>
                                                <td><?php echo e($photo->camera_make ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Camera Model:</strong></td>
                                                <td><?php echo e($photo->camera_model ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Lens Model:</strong></td>
                                                <td><?php echo e($photo->lens_model ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Lens Spec:</strong></td>
                                                <td><?php echo e($photo->lens_specification ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Lens Serial:</strong></td>
                                                <td><?php echo e($photo->lens_serial_number ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Focal Length:</strong></td>
                                                <td><?php echo e($photo->focal_length ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>35mm Equivalent:</strong></td>
                                                <td><?php echo e($photo->focal_length_35mm ? $photo->focal_length_35mm . 'mm' : 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Digital Zoom:</strong></td>
                                                <td><?php echo e($photo->digital_zoom_ratio ?: 'N/A'); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card-box">
                                    <h6 class="header-title">Exposure Settings</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>Aperture:</strong></td>
                                                <td><?php echo e($photo->aperture ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Shutter Speed:</strong></td>
                                                <td><?php echo e($photo->shutter_speed ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>ISO:</strong></td>
                                                <td><?php echo e($photo->iso ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Exposure Mode:</strong></td>
                                                <td><?php echo e($photo->exposure_mode ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Exposure Program:</strong></td>
                                                <td><?php echo e($photo->exposure_program ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Metering Mode:</strong></td>
                                                <td><?php echo e($photo->metering_mode ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Flash:</strong></td>
                                                <td><?php echo e($photo->flash ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>White Balance:</strong></td>
                                                <td><?php echo e($photo->white_balance ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>WB Mode:</strong></td>
                                                <td><?php echo e($photo->white_balance_mode ?: 'N/A'); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card-box">
                                    <h6 class="header-title">Focus & Scene</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>Focus Mode:</strong></td>
                                                <td><?php echo e($photo->focus_mode ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Focus Distance:</strong></td>
                                                <td><?php echo e($photo->focus_distance ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Subject Distance:</strong></td>
                                                <td><?php echo e($photo->subject_distance_range ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Scene Type:</strong></td>
                                                <td><?php echo e($photo->scene_type ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Scene Capture:</strong></td>
                                                <td><?php echo e($photo->scene_capture_type ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Contrast:</strong></td>
                                                <td><?php echo e($photo->contrast ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Saturation:</strong></td>
                                                <td><?php echo e($photo->saturation ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Sharpness:</strong></td>
                                                <td><?php echo e($photo->sharpness ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Custom Rendered:</strong></td>
                                                <td><?php echo e($photo->custom_rendered ?: 'N/A'); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="card-box">
                                    <h6 class="header-title">GPS & Location</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>GPS Latitude:</strong></td>
                                                <td><?php echo e($photo->gps_latitude ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>GPS Longitude:</strong></td>
                                                <td><?php echo e($photo->gps_longitude ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>GPS Altitude:</strong></td>
                                                <td><?php echo e($photo->gps_altitude ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>GPS Location:</strong></td>
                                                <td><?php echo e($photo->gps_location ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Orientation:</strong></td>
                                                <td><?php echo e($photo->orientation ?: 'N/A'); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card-box">
                                    <h6 class="header-title">Image Quality</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>Image Quality:</strong></td>
                                                <td><?php echo e($photo->image_quality ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Light Source:</strong></td>
                                                <td><?php echo e($photo->light_source ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Gain Control:</strong></td>
                                                <td><?php echo e($photo->gain_control ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Date Taken:</strong></td>
                                                <td><?php echo e($photo->date_taken ? $photo->date_taken->format('M d, Y H:i') : 'N/A'); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card-box">
                                    <h6 class="header-title">Copyright & Info</h6>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm">
                                            <tr>
                                                <td><strong>Copyright:</strong></td>
                                                <td><?php echo e($photo->copyright ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Artist:</strong></td>
                                                <td><?php echo e($photo->artist ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Subject:</strong></td>
                                                <td><?php echo e($photo->subject ?: 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Added By:</strong></td>
                                                <td><?php echo e($photo->user ? $photo->user->name : 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Added Date:</strong></td>
                                                <td><?php echo e($photo->created_at->format('M d, Y H:i')); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Last Updated:</strong></td>
                                                <td><?php echo e($photo->updated_at->format('M d, Y H:i')); ?></td>
                                            </tr>
                                        </table>
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

<?php echo $__env->make("admin.admin_app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/admin/photos/show.blade.php ENDPATH**/ ?>