

<?php $__env->startSection("content"); ?>


  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                <div class="row">
                  <div class="col-md-3">
                     <?php echo Form::open(array('url' => 'admin/sub_admin','class'=>'app-search','id'=>'search','role'=>'form','method'=>'get')); ?>

                      <input type="text" name="s" placeholder="<?php echo e(trans('words.search_by_name_email')); ?>" class="form-control">
                      <button type="submit"><i class="fa fa-search"></i></button>
                    <?php echo Form::close(); ?>

                  </div>
                <div class="col-md-3">
                  
                </div>
              </div>

                <?php if(Session::has('flash_message')): ?>
                    <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span></button>
                        <?php echo e(Session::get('flash_message')); ?>

                    </div>
                <?php endif; ?>
                <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th><?php echo e(trans('words.name')); ?></th>
                      <th><?php echo e(trans('words.email')); ?></th>
                      <th><?php echo e(trans('words.phone')); ?></th>
                      <th>Role</th>
                      <th><?php echo e(trans('words.status')); ?></th>
                      <th><?php echo e(trans('words.action')); ?></th>
                    </tr>
                  </thead>
                  <tbody>
                   <?php $__currentLoopData = $user_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $user_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr id="card_box_id_<?php echo e($user_data->id); ?>">
                      <td><?php echo e($user_data->name); ?></td>
                      <td><?php echo e($user_data->email); ?></td>
                      <td><?php echo e($user_data->phone); ?></td>
                      <td><?php if($user_data->usertype == 'Admin'): ?> <span class="badge badge-success">Admin</span> <?php else: ?> <span class="badge badge-primary">User</span><?php endif; ?></td>

                      <td><?php if($user_data->status==1): ?><span class="badge badge-success"><?php echo e(trans('words.active')); ?></span> <?php else: ?><span class="badge badge-danger"><?php echo e(trans('words.inactive')); ?></span><?php endif; ?></td>

                      <td>
                      <a href="<?php echo e(url('admin/sub_admin/edit_user/'.$user_data->id)); ?>" class="btn btn-icon waves-effect waves-light btn-success m-b-5 m-r-5" data-toggle="tooltip" title="<?php echo e(trans('words.edit')); ?>"> <i class="fa fa-edit"></i> </a>
                      <a href="#" class="btn btn-icon waves-effect waves-light btn-danger m-b-5 data_remove" data-toggle="tooltip" title="<?php echo e(trans('words.remove')); ?>" data-id="<?php echo e($user_data->id); ?>"> <i class="fa fa-remove"></i> </a>
                      </td>
                    </tr>
                   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



                  </tbody>
                </table>
              </div>
                <nav class="paging_simple_numbers">
                <?php echo $__env->make('admin.pagination', ['paginator' => $user_list], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </nav>

              </div>
            </div>
          </div>
        </div>
      </div>
      <?php echo $__env->make("admin.copyright", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <script src="<?php echo e(URL::asset('admin_assets/js/jquery.min.js')); ?>"></script>

 <script type="text/javascript">

 $(".data_remove").click(function () {

   var post_id = $(this).data("id");
   var action_name='user_delete';

   Swal.fire({
     title: '<?php echo e(trans('words.dlt_warning')); ?>',
   text: "<?php echo e(trans('words.dlt_warning_text')); ?>",
   icon: 'warning',
   showCancelButton: true,
   confirmButtonColor: '#3085d6',
   cancelButtonColor: '#d33',
   confirmButtonText: '<?php echo e(trans('words.dlt_confirm')); ?>',
   cancelButtonText: "<?php echo e(trans('words.btn_cancel')); ?>",
   background:"#1a2234",
   color:"#fff"

 }).then((result) => {

   //alert(post_id);

   //alert(JSON.stringify(result));

     if(result.isConfirmed) {

         $.ajax({
             type: 'post',
             url: "<?php echo e(URL::to('admin/ajax_delete')); ?>",
             dataType: 'json',
             data: {"_token": "<?php echo e(csrf_token()); ?>",id: post_id, action_for: action_name},
             success: function(res) {

               if(res.status=='1')
               {

                   var selector = "#card_box_id_"+post_id;
                     $(selector ).fadeOut(1000);
                     setTimeout(function(){
                             $(selector ).remove()
                         }, 1000);

                   Swal.fire({
                     position: 'center',
                     icon: 'success',
                     title: '<?php echo e(trans('words.deleted')); ?>!',
                     showConfirmButton: true,
                     confirmButtonColor: '#10c469',
                     background:"#1a2234",
                     color:"#fff"
                   })

               }
               else
               {
                 Swal.fire({
                         position: 'center',
                         icon: 'error',
                         title: 'Something went wrong!',
                         showConfirmButton: true,
                         confirmButtonColor: '#10c469',
                         background:"#1a2234",
                         color:"#fff"
                        })
               }

             }
         });
     }

 })

 });

 </script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make("admin.admin_app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/admin/pages/users/admin_list.blade.php ENDPATH**/ ?>