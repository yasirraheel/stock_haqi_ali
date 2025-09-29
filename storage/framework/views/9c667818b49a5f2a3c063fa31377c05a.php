

<?php $__env->startSection('content'); ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-6">
                                    <a href="<?php echo e(URL::to('admin/sub_admin')); ?>">
                                        <h4 class="header-title m-t-0 m-b-30 text-primary pull-left"
                                            style="font-size: 20px;"><i class="fa fa-arrow-left"></i>
                                            <?php echo e(trans('words.back')); ?></h4>
                                    </a>
                                </div>
                            </div>


                            <?php echo Form::open([
                                'url' => ['admin/sub_admin/add_edit_user'],
                                'class' => 'form-horizontal',
                                'name' => 'user_form',
                                'id' => 'user_form',
                                'role' => 'form',
                                'enctype' => 'multipart/form-data',
                            ]); ?>


                            <input type="hidden" name="id" value="<?php echo e(isset($user->id) ? $user->id : null); ?>">



                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label"><?php echo e(trans('words.name')); ?>*</label>
                                <div class="col-sm-8">
                                    <input type="text" name="name"
                                        value="<?php echo e(isset($user->name) ? $user->name : null); ?>" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label"><?php echo e(trans('words.email')); ?>*</label>
                                <div class="col-sm-8">
                                    <input type="text" name="email"
                                        value="<?php echo e(isset($user->email) ? $user->email : null); ?>" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label"><?php echo e(trans('words.password')); ?>*</label>
                                <div class="col-sm-8">
                                    <input type="password" name="password" value="" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label"><?php echo e(trans('words.phone')); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" name="phone"
                                        value="<?php echo e(isset($user->phone) ? $user->phone : null); ?>" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label"><?php echo e(trans('words.image')); ?></label>
                                <div class="col-sm-8">
                                    <input type="file" name="user_image" class="form-control">
                                </div>
                            </div>

                            <?php if(isset($user->user_image) and file_exists(public_path('upload/' . $user->user_image))): ?>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">&nbsp;</label>
                                    <div class="col-sm-8">

                                        <img src="<?php echo e(URL::to('upload/' . $user->user_image)); ?>" alt="video image"
                                            class="img-thumbnail" width="250">

                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label"><?php echo e(trans('words.admin_type')); ?></label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="usertype" id="admin_usertype">
                                        <option value="Sub_Admin" <?php if(isset($user->usertype) and $user->usertype == 'Sub_Admin'): ?> selected <?php endif; ?>>
                                            User</option>
                                        <option value="Admin" <?php if(isset($user->usertype) and $user->usertype == 'Admin'): ?> selected <?php endif; ?>>
                                            Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row d-none">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-8">
                                    <p id="sub_admin_id" <?php if(isset($user->usertype) and $user->usertype != 'Sub_Admin'): ?> style="display:none;" <?php endif; ?>>
                                        Permission for Sub Admin<small id="emailHelp"
                                            class="form-text text-muted">(<?php echo e(trans('words.language_text')); ?>,
                                            <?php echo e(trans('words.genres_text')); ?>, <?php echo e(trans('words.movies_text')); ?>,
                                            <?php echo e(trans('words.tv_shows_text')); ?>, <?php echo e(trans('words.seasons_text')); ?>,
                                            <?php echo e(trans('words.episodes_text')); ?>, <?php echo e(trans('words.sports_cat_text')); ?>,
                                            <?php echo e(trans('words.sports_video_text')); ?>, <?php echo e(trans('words.slider')); ?>,
                                            <?php echo e(trans('words.home_section')); ?>)</small></p>

                                    <p id="master_admin_id" <?php if(isset($user->usertype) and $user->usertype != 'Admin'): ?> style="display:none;" <?php endif; ?>
                                        <?php if(!isset($user->id)): ?> style="display:none;" <?php endif; ?>> Permission for
                                        Master Admin<small id="emailHelp" class="form-text text-muted">(All
                                            Permission)</small></p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label"><?php echo e(trans('words.status')); ?></label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="status">
                                        <option value="1" <?php if(isset($user->status) and $user->status == 1): ?> selected <?php endif; ?>>
                                            <?php echo e(trans('words.active')); ?></option>
                                        <option value="0" <?php if(isset($user->status) and $user->status == 0): ?> selected <?php endif; ?>>
                                            Ban</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">

                            </div>

                            <div class="form-group">
                                <div class="offset-sm-3 col-sm-9 pl-1">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <?php echo e(trans('words.save')); ?> </button>
                                </div>
                            </div>
                            <?php echo Form::close(); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $__env->make('admin.copyright', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

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

<?php echo $__env->make('admin.admin_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/admin/pages/users/addeditadmin.blade.php ENDPATH**/ ?>