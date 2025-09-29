<?php $__env->startSection("content"); ?>



  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card-box">


                 <?php echo Form::open(array('url' => array('admin/menu_settings'),'class'=>'form-horizontal','name'=>'settings_form','id'=>'settings_form','role'=>'form','enctype' => 'multipart/form-data')); ?>


                  <input type="hidden" name="id" value="<?php echo e(isset($settings->id) ? $settings->id : null); ?>">

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Photos</label>
                      <div class="col-sm-9">
                            <select class="form-control" name="menu_shows">

                                <option value="1" <?php if($settings->menu_shows=="1"): ?> selected <?php endif; ?>>ON</option>
                                <option value="0" <?php if($settings->menu_shows=="0"): ?> selected <?php endif; ?>>OFF</option>

                            </select>
                      </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label"><?php echo e(trans('words.movies_text')); ?></label>
                      <div class="col-sm-9">
                            <select class="form-control" name="menu_movies">

                                <option value="1" <?php if($settings->menu_movies=="1"): ?> selected <?php endif; ?>>ON</option>
                                <option value="0" <?php if($settings->menu_movies=="0"): ?> selected <?php endif; ?>>OFF</option>

                            </select>
                      </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label"><?php echo e(trans('words.sports_text')); ?></label>
                      <div class="col-sm-9">
                            <select class="form-control" name="menu_sports">

                                <option value="1" <?php if($settings->menu_sports=="1"): ?> selected <?php endif; ?>>ON</option>
                                <option value="0" <?php if($settings->menu_sports=="0"): ?> selected <?php endif; ?>>OFF</option>

                            </select>
                      </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label"><?php echo e(trans('words.live_tv')); ?></label>
                      <div class="col-sm-9">
                            <select class="form-control" name="menu_livetv">

                                <option value="1" <?php if($settings->menu_livetv=="1"): ?> selected <?php endif; ?>>ON</option>
                                <option value="0" <?php if($settings->menu_livetv=="0"): ?> selected <?php endif; ?>>OFF</option>

                            </select>
                      </div>
                  </div>

                  <div class="form-group">
                    <div class="offset-sm-2 col-sm-9 pl-1">
                      <button type="submit" class="btn btn-primary waves-effect waves-light"> <?php echo e(trans('words.save_settings')); ?> </button>
                    </div>
                  </div>
                <?php echo Form::close(); ?>

              </div>
            </div>
          </div>
        </div>
      </div>
      <?php echo $__env->make("admin.copyright", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
            background:"#1a2234",
            color:"#fff"
           })
  <?php endif; ?>

  </script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make("admin.admin_app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/admin/pages/settings/menu.blade.php ENDPATH**/ ?>