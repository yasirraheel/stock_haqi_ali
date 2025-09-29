<?php $__env->startSection('head_title', $page_info->page_title.' | '.getcong('site_name') ); ?>
<?php $__env->startSection('head_url', Request::url()); ?>
<?php $__env->startSection('content'); ?>  

<?php if(getcong('recaptcha_on_contact_us')): ?>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script>
function submitForm() {
    var response = grecaptcha.getResponse();
    if(response.length == 0) {
        document.getElementById('g-recaptcha-error').innerHTML = '<span style="color:red;">This field is required.</span>';
        return false;
    }
   
    return true;
}
 
function verifyCaptcha() {
    document.getElementById('g-recaptcha-error').innerHTML = '';
}
</script>
<?php endif; ?>

<!-- Start Breadcrumb -->
<div class="breadcrumb-section bg-xs" style="background-image: url(<?php echo e(URL::asset('site_assets/images/breadcrum-bg.jpg')); ?>)">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xl-12"> 
          <h2><?php echo e(stripslashes($page_info->page_title)); ?></h2>
          <nav id="breadcrumbs">
            <ul>
              <li><a href="<?php echo e(URL::to('/')); ?>" title="<?php echo e(trans('words.home')); ?>"><?php echo e(trans('words.home')); ?></a></li>
              <li><?php echo e(stripslashes($page_info->page_title)); ?></li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
<!-- End Breadcrumb --> 

<!-- Start Contat Page -->
<div class="contact-page-area vfx-item-ptb vfx-item-info">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-12 col-xs-12">
		 <div class="contact-form">
              
           <?php echo Form::open(array('url' => 'contact_send','class'=>'row','id'=>'contact_form','role'=>'form','onsubmit'=>'return submitForm();')); ?>  
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
              <div class="form-group mb-3">
                <label><?php echo e(trans('words.name')); ?></label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Name" >
              </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
              <div class="form-group mb-3">
				<label><?php echo e(trans('words.email')); ?></label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" >
              </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
              <div class="form-group mb-3">
				<label><?php echo e(trans('words.phone')); ?></label>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone Number">
              </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
              <div class="form-group mb-3">
				<label><?php echo e(trans('words.subject')); ?></label>
                <input type="text" name="subject" id="subject" class="form-control" placeholder="Subject" >
              </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <div class="form-group mb-3">
              <label><?php echo e(trans('words.your_message')); ?></label>
                <textarea name="message" id="message" class="form-control" cols="30" rows="4" placeholder="Your Message..." ></textarea>
              </div>
            </div>
            <?php if(getcong('recaptcha_on_contact_us')): ?>
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
              <div class="form-group mb-3">
                  <div class="g-recaptcha" data-sitekey="<?php echo e(getcong('recaptcha_site_key')); ?>" data-callback="verifyCaptcha"></div>
                  <div id="g-recaptcha-error"></div>
              </div>
            </div>
            <?php endif; ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <div class="form-group">
                <button type="submit" class="vfx-item-btn-danger text-uppercase"><?php echo e(trans('words.submit')); ?></button>
              </div>
            </div>
          <?php echo Form::close(); ?>

        </div>
      </div>
	  <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
	    <?php if($page_info->page_content): ?>
        <div class="contact-form">          
            <?php echo stripslashes($page_info->page_content); ?>

        </div>  
        <?php endif; ?>
	  </div>	
    </div>
  </div>
</div>
<!-- End Contact Page --> 

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

  <?php if(Session::has('error_flash_message')): ?>     
 
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
        icon: 'error',
        title: '<?php echo e(Session::get('error_flash_message')); ?>'
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
<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/pages/extra/pages_contact.blade.php ENDPATH**/ ?>