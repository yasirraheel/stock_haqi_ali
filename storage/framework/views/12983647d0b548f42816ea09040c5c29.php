<?php $__env->startSection('head_title', 'Page Not Found | '.getcong('site_name') ); ?>

<?php $__env->startSection('head_url', Request::url()); ?>

<?php $__env->startSection('content'); ?>
  
  
<div class="main-wrap">
  <div class="section">
    <div class="container">
      <div class="row section-padding" style="text-align:center;padding:80px 0;">
        <span><img src="<?php echo e(URL::asset('site_assets/images/404.png')); ?>"></span>
        <h5 class="mb-4">The Page you are Looking for Not Avaible!</h5>
        <a href="<?php echo e(URL::to('/')); ?>" class="vfx-item-btn-danger page-note-found text-uppercase">Go to Home</a>
      </div>      
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/errors/404.blade.php ENDPATH**/ ?>