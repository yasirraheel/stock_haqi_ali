<?php $__env->startSection('head_title', $page_info->page_title.' | '.getcong('site_name') ); ?>

<?php $__env->startSection('head_url', Request::url()); ?>

<?php $__env->startSection('content'); ?>
  
  
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

<!-- Banner -->
<?php if(get_web_banner('other_page_top')!=""): ?>      
      <div class="vid-item-ptb banner_ads_item pb-1" style="padding: 15px 0;">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
             <?php echo stripslashes(get_web_banner('other_page_top')); ?>

          </div>
        </div>  
        </div>
      </div>
  <?php endif; ?>

<!-- Start Details Info Page -->
<div class="details-page-area vfx-item-ptb vfx-item-info">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="details-item-block">
          
            <?php echo stripslashes($page_info->page_content); ?>


        </div>
      </div>
    </div>
  </div>
</div>
<!-- End Details Info Page -->  

<!-- Banner -->
<?php if(get_web_banner('other_page_bottom')!=""): ?>      
      <div class="vid-item-ptb banner_ads_item pb-1" style="padding: 15px 0;">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
             <?php echo stripslashes(get_web_banner('other_page_bottom')); ?>

          </div>
        </div>  
        </div>
      </div>
  <?php endif; ?>
 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/pages/extra/pages.blade.php ENDPATH**/ ?>