<?php $__env->startSection('head_title', trans('words.profile') . ' | ' . getcong('site_name')); ?>

<?php $__env->startSection('head_url', Request::url()); ?>

<?php $__env->startSection('content'); ?>


    <!-- Start Breadcrumb -->
    <div class="breadcrumb-section bg-xs"
        style="background-image: url('<?php echo e(URL::asset('site_assets/images/breadcrum-bg.jpg')); ?>')">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <h2><?php echo e(trans('words.edit_profile')); ?></h2>
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="<?php echo e(URL::to('/')); ?>" title="<?php echo e(trans('words.home')); ?>"><?php echo e(trans('words.home')); ?></a>
                            </li>
                            <li><a href="<?php echo e(URL::to('/dashboard')); ?>"
                                    title="<?php echo e(trans('words.dashboard_text')); ?>"><?php echo e(trans('words.dashboard_text')); ?></a></li>
                            <li><?php echo e(trans('words.edit_profile')); ?></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <!-- Start Edit Profile -->
    <div class="edit-profile-area vfx-item-ptb vfx-item-info">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 offset-lg-2 offset-md-0">
                    <div class="edit-profile-form">

                        <?php echo Form::open([
                            'url' => 'profile',
                            'class' => 'row',
                            'name' => 'profile_form',
                            'id' => 'profile_form',
                            'role' => 'form',
                            'enctype' => 'multipart/form-data',
                        ]); ?>

                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group mb-3">
                                <label><?php echo e(trans('words.name')); ?></label>
                                <input type="text" name="name" id="name" value="<?php echo e($user->name); ?>"
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group mb-3">
                                <label><?php echo e(trans('words.email')); ?></label>
                                <input type="email" name="email" id="email" value="<?php echo e($user->email); ?>"
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group mb-3">
                                <label><?php echo e(trans('words.password')); ?></label>
                                <input type="password" class="form-control" name="password" id="password" value="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group mb-3">
                                <label><?php echo e(trans('words.phone')); ?></label>
                                <input type="number" name="phone" id="phone" value="<?php echo e($user->phone); ?>"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group mb-3">
                                <label>PayPal Email</label>
                                <input type="email" name="paypal_email" id="paypal_email" value="<?php echo e($user->paypal_email); ?>" placeholder="Enter Correct PayPal Email"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group mb-3">
                                <label><?php echo e(trans('words.address')); ?></label>
                                <textarea name="user_address" id="user_address" class="form-control" cols="30" rows="4"><?php echo e($user->user_address); ?></textarea>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                            <div class="form-group mb-3">
                                <label><?php echo e(trans('words.profile_image')); ?></label>
                                <label class="browse_pic_file">
                                    <input type="file" id="user_image" name="user_image"
                                        aria-label="Profile picture browse" onchange="readURL(this);">
                                    <span class="browse_file_custom"></span>
                                </label>
                                <div class="user_pic_view">
                                    <div class="fileupload_img">
                                        <?php if(Auth::User()->user_image): ?>
                                            <img class="fileupload_img"
                                                src="<?php echo e(URL::asset('upload/' . Auth::User()->user_image)); ?>"
                                                alt="profile pic" title="profile pic">
                                        <?php else: ?>
                                            <img class="fileupload_img"
                                                src="<?php echo e(URL::asset('site_assets/images/user-avatar.png')); ?>"
                                                alt="profile pic" title="profile pic">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="form-group d-flex align-items-end flex-column mt-30">
                                <button type="submit"
                                    class="vfx-item-btn-danger text-uppercase"><?php echo e(trans('words.update')); ?></button>
                            </div>
                        </div>
                        <?php echo Form::close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Profile -->

    <script type="text/javascript">
        <?php if(Session::has('flash_message')): ?>

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: false,
                /*didOpen: (toast) => {
                  toast.addEventListener('mouseenter', Swal.stopTimer)
                  toast.addEventListener('mouseleave', Swal.resumeTimer)
                }*/
            })

            Toast.fire({
                icon: 'success',
                title: '<?php echo e(Session::get('flash_message')); ?>'
            })
        <?php endif; ?>


        <?php if(count($errors) > 0): ?>

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: '<p><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo e($error); ?><br/> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></p>',
                showConfirmButton: true,
                confirmButtonColor: '#10c469',
                background: "#1a2234",
                color: "#fff"
            })
        <?php endif; ?>
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/pages/user/profile.blade.php ENDPATH**/ ?>