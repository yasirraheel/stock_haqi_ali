<?php $__env->startSection('head_title', trans('words.login_text').' | '.getcong('site_name') ); ?>

<?php $__env->startSection('head_url', Request::url()); ?>

<?php $__env->startSection('content'); ?>

<?php if(getcong('recaptcha_on_login')): ?>
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
   
 
<!-- Login Main Wrapper Start -->
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
                    <a href="<?php echo e(URL::to('/')); ?>" title="logo"><img src="<?php echo e(URL::asset('/'.getcong('site_logo'))); ?>" alt="logo" title="logo" class="login-signup-logo"></a>
                  <?php else: ?>
                    <a href="<?php echo e(URL::to('/')); ?>" title="logo"><img src="<?php echo e(URL::asset('site_assets/images/logo.png')); ?>" alt="logo" title="logo" class="login-signup-logo"></a>                          
                  <?php endif; ?>

                </div>
              </div>
            </div>
            <!-- Login Form -->
        <div class="col-lg-4 col-md-6 col-sm-6 mx-auto d-flex align-items-center login-item-block">
        <div class="container login-part">
          <div class="row">
          <div class="col-12 col-lg-12 col-xl-12 mx-auto">
            <h2 class="form-title-item mb-4"><?php echo e(trans('words.login_text')); ?></h2>
             <?php echo Form::open(array('url' => 'login','class'=>'','id'=>'loginform','role'=>'form','onsubmit'=>'return submitForm();')); ?>  

             

            <div class="form-group">
              <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" class="form-control" placeholder="<?php echo e(trans('words.email')); ?>" >
            </div>
            <div class="form-group">
              <input type="password" name="password" id="password" value="" class="form-control" placeholder="<?php echo e(trans('words.password')); ?>" >
            </div>
            
            <?php if(getcong('recaptcha_on_login')): ?>
            <div class="form-group">
               <div class="g-recaptcha" data-sitekey="<?php echo e(getcong('recaptcha_site_key')); ?>" data-callback="verifyCaptcha"></div>
               <div id="g-recaptcha-error"></div>
            </div>     
            <?php endif; ?>

              <div class="row mt-4">
              <div class="col">
              <div class="form-check custom-control custom-checkbox">
                <input id="remember-me" name="remember" class="form-check-input" type="checkbox">
                <label class="custom-control-label" for="remember-me"><?php echo e(trans('words.remember_me')); ?></label>                
              </div>
              </div>
              <div class="col text-end"><a href="<?php echo e(URL::to('password/email')); ?>" class="btn-link" title="forgot password"> <?php echo e(trans('words.forgot_password')); ?>?</a></div>
            </div>
            <button class="btn-submit btn-block my-4 mb-4" type="submit"><?php echo e(trans('words.login_text')); ?></button>
            <?php echo Form::close(); ?>

            <p class="text-3 text-center mb-3"><?php echo e(trans('words.dont_have_account')); ?> <a href="<?php echo e(url('signup')); ?>" class="btn-link" title="signup"><?php echo e(trans('words.sign_up')); ?></a></p>
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
        <!-- Login Form End --> 
          </div>
        </div>
      </div>
      <!-- Welcome Text End -->       
    </div>
  </div>
</div>
<!-- End Login Main Wrapper --> 


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
  
  <?php if(Session::has('login_flash_error')): ?> 
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
  <?php endif; ?>

  </script>

 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/pages/user/login.blade.php ENDPATH**/ ?>