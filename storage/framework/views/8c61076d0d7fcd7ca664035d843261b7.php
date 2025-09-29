

<?php $__env->startSection("content"); ?>

<style type="text/css">
  .iframe-container {
  overflow: hidden;
  padding-top: 56.25% !important;
  position: relative;
}
 
.iframe-container iframe {
   border: 0;
   height: 100%;
   left: 0;
   position: absolute;
   top: 0;
   width: 100%;
}
</style>
 
  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card-box">

                <div class="row">
                 <div class="col-sm-6">
                      <a href="<?php echo e(URL::to('admin/payment_gateway')); ?>"><h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;"><i class="fa fa-arrow-left"></i> <?php echo e(trans('words.back')); ?></h4></a>
                 </div>                  
                 <div class="col-sm-6">
                    &nbsp;
                 </div>                  
                </div>
                  

                 <?php echo Form::open(array('url' => array('admin/payment_gateway/paypal'),'class'=>'form-horizontal','name'=>'settings_form','id'=>'settings_form','role'=>'form','enctype' => 'multipart/form-data')); ?>  
                  
                  <input type="hidden" name="id" value="<?php echo e(isset($post_info->id) ? $post_info->id : null); ?>">
  
                   
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?php echo e(trans('words.title')); ?>*</label>
                    <div class="col-sm-8">
                      <input type="text" name="gateway_name" value="<?php echo e(isset($post_info->gateway_name) ? stripslashes($post_info->gateway_name) : null); ?>" class="form-control">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?php echo e(trans('words.short_info')); ?></label>
                    <div class="col-sm-8">
                      <input type="text" name="gateway_short_info" value="<?php echo e(isset($post_info->gateway_short_info) ? stripslashes($post_info->gateway_short_info) : null); ?>" class="form-control">
                    </div>
                  </div>
                  <hr/>
                   
                   <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?php echo e(trans('words.payment_mode')); ?></label>
                    <div class="col-sm-8">
                      <select class="form-control" name="mode">                                
                                <option value="sandbox" <?php if($gateway_info->mode=="sandbox"): ?> selected <?php endif; ?>>Sandbox</option>
                                <option value="live" <?php if($gateway_info->mode=="live"): ?> selected <?php endif; ?>>Live</option>
                                              
                            </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?php echo e(trans('words.paypal_client_id')); ?></label>
                    <div class="col-sm-8">
                      <input type="text" name="paypal_client_id" value="<?php echo e(isset($gateway_info->paypal_client_id) ? stripslashes($gateway_info->paypal_client_id) : null); ?>" class="form-control">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?php echo e(trans('words.paypal_secret')); ?></label>
                    <div class="col-sm-8">
                      <input type="text" name="paypal_secret" value="<?php echo e(isset($gateway_info->paypal_secret) ? stripslashes($gateway_info->paypal_secret) : null); ?>" class="form-control">
                    </div>
                  </div>
                  <hr/>
                  <h5 class="mb-4" style="color: #f9f9f9;"><b>Braintree Settings for App</b></h5>
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Braintree Merchant ID</label>
                    <div class="col-sm-8">
                      <input type="text" name="braintree_merchant_id" value="<?php echo e(isset($gateway_info->braintree_merchant_id) ? stripslashes($gateway_info->braintree_merchant_id) : null); ?>" class="form-control">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Braintree Public Key</label>
                    <div class="col-sm-8">
                      <input type="text" name="braintree_public_key" value="<?php echo e(isset($gateway_info->braintree_public_key) ? stripslashes($gateway_info->braintree_public_key) : null); ?>" class="form-control">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Braintree Private Key</label>
                    <div class="col-sm-8">
                      <input type="text" name="braintree_private_key" value="<?php echo e(isset($gateway_info->braintree_private_key) ? stripslashes($gateway_info->braintree_private_key) : null); ?>" class="form-control">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Braintree Merchant Account ID</label>
                    <div class="col-sm-8">
                      <input type="text" name="braintree_merchant_account_id" value="<?php echo e(isset($gateway_info->braintree_merchant_account_id) ? stripslashes($gateway_info->braintree_merchant_account_id) : null); ?>" class="form-control">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?php echo e(trans('words.status')); ?></label>
                      <div class="col-sm-8">
                            <select class="form-control" name="status">                               
                                <option value="1" <?php if(isset($post_info->status) AND $post_info->status==1): ?> selected <?php endif; ?>><?php echo e(trans('words.active')); ?></option>
                                <option value="0" <?php if(isset($post_info->status) AND $post_info->status==0): ?> selected <?php endif; ?>><?php echo e(trans('words.inactive')); ?></option>                            
                            </select>
                      </div>
                  </div>
                  <div class="form-group">
                    <div class="offset-sm-3 col-sm-9 pl-1">
                      <button type="submit" class="btn btn-primary waves-effect waves-light"> <?php echo e(trans('words.save')); ?></button>                      
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
<?php echo $__env->make("admin.admin_app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/admin/pages/gateway/paypal.blade.php ENDPATH**/ ?>