<?php $__env->startSection('head_title', trans('words.sign_up').' | '.getcong('site_name') ); ?>

<?php $__env->startSection('head_url', Request::url()); ?>

<?php $__env->startSection('content'); ?>

<?php if(getcong('recaptcha_on_signup')): ?>
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

 
<!-- Signup Main Wrapper Start -->
<div id="main-wrapper">
  <div class="container-fluid px-0 m-0 h-100 mx-auto">
    <div class="row g-0 min-vh-100 overflow-hidden"> 
      <!-- Welcome Text -->
      <div class="col-md-12">
        <div class="hero-wrap d-flex align-items-center h-100">
          <div class="hero-mask"></div>
          <div class="hero-bg hero-bg-scroll" style="background-image:url('<?php echo e(URL::asset('site_assets/images/login-signup-bg-img.jpg')); ?>');"></div>
          <div class="hero-content mx-auto w-100 h-100 d-flex flex-column justify-content-center">
            <div class="row">
              <div class="col-12 col-lg-5 col-xl-5 mx-auto">
                <div class="logo mt-40 mb-20 mb-md-0 justify-content-center d-flex text-center">
                   <?php if(getcong('site_logo')): ?>                 
                    <a href="<?php echo e(URL::to('/')); ?>" title="logo" class="d-flex"><img src="<?php echo e(URL::asset('/'.getcong('site_logo'))); ?>" alt="logo" title="logo" class="login-signup-logo"></a>
                  <?php else: ?>
                    <a href="<?php echo e(URL::to('/')); ?>" title="logo" class="d-flex"><img src="<?php echo e(URL::asset('site_assets/images/logo.png')); ?>" alt="logo" title="logo" class="login-signup-logo"></a>                          
                  <?php endif; ?>
                 </div>
              </div>
            </div>
            <!-- Login Form -->
        <div class="col-lg-4 col-md-6 col-sm-6 mx-auto d-flex align-items-center login-item-block mb-4">
        <div class="container login-part">
          <div class="row">
          <div class="col-12 col-lg-12 col-xl-12 mx-auto">
            <h2 class="form-title-item mb-4"><?php echo e(trans('words.sign_up')); ?></h2>
            <?php echo Form::open(array('url' => 'signup','class'=>'','id'=>'signupform','role'=>'form','onsubmit'=>'return submitForm();')); ?>


              

            <div class="form-group">
                  <input type="text" class="form-control" name="name" id="name" value="<?php echo e(old('name')); ?>" placeholder="<?php echo e(trans('words.name')); ?>">
            </div>
            <div class="form-group">
              <input type="email" class="form-control" name="email" id="email" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(trans('words.email')); ?>">
            </div>
            <div class="form-group">
              <input type="password" class="form-control" id="password" name="password" placeholder="<?php echo e(trans('words.password')); ?> <?php echo e(trans('words.at_least_8_char')); ?>">
            </div>
            <div class="form-group">
              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="<?php echo e(trans('words.confirm_password')); ?>">
            </div>
            
            <?php if(getcong('recaptcha_on_signup')): ?>
            <div class="form-group">
               <div class="g-recaptcha" data-sitekey="<?php echo e(getcong('recaptcha_site_key')); ?>" data-callback="verifyCaptcha"></div>
               <div id="g-recaptcha-error"></div>
            </div>     
            <?php endif; ?>

            <button class="btn-submit btn-block mb-4" type="submit"><?php echo e(trans('words.sign_up')); ?></button>
            <?php echo Form::close(); ?>

            <p class="text-3 text-center mb-3"><?php echo e(trans('words.already_sign_up')); ?> <a class="btn-link" href="<?php echo e(url('login')); ?>" title="login"><?php echo e(trans('words.login_text')); ?></a></p>
              
             <div class="socail-login-item mx-auto w-100 text-center">
 
                <?php if(getcong('facebook_login')): ?>
                <label>
                  <a href="<?php echo e(url('auth/facebook')); ?>" class="btn btn-lg btn-success btn-block btn-facebook-item" title="facebook"><i class="ion-social-facebook"></i> Facebook</a>     
                </label>
                <?php endif; ?>
                <?php if(getcong('google_login')): ?>
                <label>
                  <a href="<?php echo e(url('auth/google')); ?>" class="btn btn-lg btn-success btn-block btn-g-plus-item" title="google"><i class="ion-social-google"></i> Google</a>     
                </label>
                <?php endif; ?>  
             
            </div> 


          </div>
          </div>
        </div>
        </div>
        <!-- Signup Form End --> 
          </div>
        </div>
      </div>
      <!-- Welcome Text End -->       
    </div>
  </div>
</div>
<!-- End Signup Main Wrapper --> 
 
<script type="text/javascript">
    
    <?php if(Session::has('signup_flash_message')): ?>     
 
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
        title: '<?php echo e(Session::get('signup_flash_message')); ?>'
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
        icon: 'success',
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
<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/pages/user/signup.blade.php ENDPATH**/ ?>