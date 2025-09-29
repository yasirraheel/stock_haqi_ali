<?php $__env->startSection('head_title', 'Photos | '.getcong('site_name') ); ?>

<?php $__env->startSection('head_url', Request::url()); ?>

<?php $__env->startSection('content'); ?>

<?php if(count($slider)!=0): ?>
  <?php echo $__env->make("pages.photos.slider", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<!-- Banner -->
<?php if(get_web_banner('list_top')!=""): ?>
<div class="vid-item-ptb banner_ads_item">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<?php echo stripslashes(get_web_banner('list_top')); ?>

			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<link rel="stylesheet" href="<?php echo e(URL::asset('site_assets/css/nice-select.css')); ?>">

<style>
/* Ultra-specific scoped styles for photo listing page only - using unique class names */
.vfx-item-ptb .photo-listing-card {
    background: #1a1a2e !important;
    border-radius: 8px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3) !important;
    transition: all 0.3s ease !important;
    overflow: hidden !important;
    height: 100% !important;
    margin-bottom: 15px !important;
    border: 1px solid #2d2d44 !important;
}

.vfx-item-ptb .photo-listing-card:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.5) !important;
    border-color: #167ac6 !important;
}

.vfx-item-ptb .photo-listing-card-inner {
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
}

.vfx-item-ptb .photo-listing-image-container {
    position: relative !important;
    height: 180px !important;
    overflow: hidden !important;
}

.vfx-item-ptb .photo-listing-image {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    transition: transform 0.3s ease !important;
}

.vfx-item-ptb .photo-listing-card:hover .photo-listing-image {
    transform: scale(1.05) !important;
}

.vfx-item-ptb .photo-listing-premium-badge {
    position: absolute !important;
    top: 8px !important;
    right: 8px !important;
    background: linear-gradient(45deg, #ffd700, #ffed4e) !important;
    color: #333 !important;
    padding: 3px 8px !important;
    border-radius: 12px !important;
    font-size: 10px !important;
    font-weight: bold !important;
    z-index: 2 !important;
    box-shadow: 0 1px 4px rgba(255,215,0,0.3) !important;
}

.vfx-item-ptb .photo-listing-premium-badge i {
    margin-right: 3px !important;
}

.vfx-item-ptb .photo-listing-free-badge {
    position: absolute !important;
    top: 8px !important;
    right: 8px !important;
    background: linear-gradient(45deg, #10c469, #28a745) !important;
    color: #ffffff !important;
    padding: 3px 8px !important;
    border-radius: 12px !important;
    font-size: 10px !important;
    font-weight: bold !important;
    z-index: 2 !important;
    box-shadow: 0 1px 4px rgba(16,196,105,0.3) !important;
}

.vfx-item-ptb .photo-listing-free-badge i {
    margin-right: 3px !important;
}

.vfx-item-ptb .photo-listing-overlay {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: rgba(0,0,0,0.8) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    opacity: 0 !important;
    transition: opacity 0.3s ease !important;
}

.vfx-item-ptb .photo-listing-card:hover .photo-listing-overlay {
    opacity: 1 !important;
}

.vfx-item-ptb .photo-listing-actions {
    display: flex !important;
    gap: 8px !important;
}

.vfx-item-ptb .photo-listing-action-btn {
    border-radius: 50% !important;
    width: 32px !important;
    height: 32px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    border: none !important;
    transition: all 0.3s ease !important;
    font-size: 12px !important;
}

.vfx-item-ptb .photo-listing-action-btn:hover {
    transform: scale(1.1) !important;
}

.vfx-item-ptb .photo-listing-info {
    padding: 10px !important;
    flex-grow: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    background: #1a1a2e !important;
}

.vfx-item-ptb .photo-listing-title {
    font-size: 14px !important;
    font-weight: 600 !important;
    color: #ffffff !important;
    margin-bottom: 5px !important;
    line-height: 1.2 !important;
}

.vfx-item-ptb .photo-listing-category {
    color: #b5b5b5 !important;
    font-size: 11px !important;
    margin-bottom: 6px !important;
}

.vfx-item-ptb .photo-listing-category i {
    margin-right: 3px !important;
    color: #167ac6 !important;
}

.vfx-item-ptb .photo-listing-meta {
    display: flex !important;
    gap: 8px !important;
    margin-bottom: 6px !important;
    flex-wrap: wrap !important;
}

.vfx-item-ptb .photo-listing-meta-item {
    display: flex !important;
    align-items: center !important;
    color: #b5b5b5 !important;
    font-size: 10px !important;
}

.vfx-item-ptb .photo-listing-meta-item i {
    margin-right: 2px !important;
    color: #167ac6 !important;
    font-size: 10px !important;
}

.vfx-item-ptb .photo-listing-camera-info {
    margin-bottom: 6px !important;
    font-size: 10px !important;
    color: #b5b5b5 !important;
}

.vfx-item-ptb .photo-listing-price-info {
    background: #2d2d44 !important;
    padding: 4px 8px !important;
    border-radius: 4px !important;
    margin-bottom: 6px !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    border: 1px solid #3d3d5c !important;
}

.vfx-item-ptb .photo-listing-price-label {
    font-size: 10px !important;
    color: #b5b5b5 !important;
}

.vfx-item-ptb .photo-listing-price-value {
    font-weight: bold !important;
    color: #10c469 !important;
    font-size: 11px !important;
}

.vfx-item-ptb .photo-listing-tags {
    margin-top: auto !important;
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 3px !important;
}

.vfx-item-ptb .photo-listing-tag {
    background: #2d2d44 !important;
    color: #b5b5b5 !important;
    padding: 1px 6px !important;
    border-radius: 8px !important;
    font-size: 9px !important;
    font-weight: 500 !important;
    border: 1px solid #3d3d5c !important;
}

/* Dark theme link colors - ultra-specific scoping */
.vfx-item-ptb .photo-listing-card a {
    color: #ffffff !important;
    text-decoration: none !important;
}

.vfx-item-ptb .photo-listing-card a:hover {
    color: #167ac6 !important;
    text-decoration: none !important;
}

@media (max-width: 768px) {
    .vfx-item-ptb .photo-listing-image-container {
        height: 150px !important;
    }

    .vfx-item-ptb .photo-listing-meta {
        gap: 6px !important;
    }

    .vfx-item-ptb .photo-listing-meta-item {
        font-size: 9px !important;
    }

    .vfx-item-ptb .photo-listing-title {
        font-size: 13px !important;
    }

    .vfx-item-ptb .photo-listing-info {
        padding: 8px !important;
    }
}
</style>

<!-- Start View All Photos -->
<div class="view-all-video-area view-shows-list-item vfx-item-ptb">
  <div class="container-fluid">
   <div class="filter-list-area">
    <div class="row">
      <div class="col-lg-12">
        <div class="page-title-item">Photos</div>
        <div class="custom_select_filter">
          <select class="selectpicker show-menu-arrow" id="filter_by_category">
            <option value="">Categories</option>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e(URL::to('photos/?category='.urlencode($category))); ?>" <?php if(isset($_GET['category']) && $_GET['category']==$category ): ?> selected <?php endif; ?>><?php echo e($category); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        <div class="custom_select_filter">
          <select class="selectpicker show-menu-arrow" id="filter_list" required>
            <option value="?filter=new" <?php if(isset($_GET['filter']) && $_GET['filter']=='new' ): ?> selected <?php endif; ?>><?php echo e(trans('words.newest')); ?></option>
            <option value="?filter=old" <?php if(isset($_GET['filter']) && $_GET['filter']=='old' ): ?> selected <?php endif; ?>><?php echo e(trans('words.oldest')); ?></option>
            <option value="?filter=alpha" <?php if(isset($_GET['filter']) && $_GET['filter']=='alpha' ): ?> selected <?php endif; ?>><?php echo e(trans('words.a_to_z')); ?></option>
            <option value="?filter=rand" <?php if(isset($_GET['filter']) && $_GET['filter']=='rand' ): ?> selected <?php endif; ?>><?php echo e(trans('words.random')); ?></option>
          </select>
        </div>
      </div>
    </div>
   </div>

    <div class="row">
         <?php $__currentLoopData = $photos_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
         <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-4">
          <div class="photo-listing-card">
            <div class="photo-listing-card-inner">
              <a href="<?php echo e(URL::to('photos/details/'.$photo->id)); ?>" title="<?php echo e($photo->title); ?>">
                <div class="photo-listing-image-container">

                  <?php if($photo->license_price && $photo->license_price > 0): ?>
                  <div class="photo-listing-premium-badge">
                    <i class="fa fa-crown"></i>
                    <span>Premium</span>
                  </div>
                  <?php else: ?>
                  <div class="photo-listing-free-badge">
                    <i class="fa fa-gift"></i>
                    <span>Free</span>
                  </div>
                  <?php endif; ?>

                  <img src="<?php echo e($photo->image_url); ?>" alt="<?php echo e($photo->title); ?>" title="<?php echo e($photo->title); ?>" class="photo-listing-image">

                  <div class="photo-listing-overlay">
                    <div class="photo-listing-actions">
                      <button class="btn btn-sm btn-light photo-listing-action-btn" onclick="event.preventDefault(); viewPhoto(<?php echo e($photo->id); ?>)">
                        <i class="fa fa-eye"></i>
                      </button>
                      <?php if($photo->license_price && $photo->license_price > 0): ?>
                        <button class="btn btn-sm btn-warning photo-listing-action-btn" onclick="event.preventDefault(); showPremiumInfo(<?php echo e($photo->id); ?>, <?php echo e($photo->license_price); ?>)">
                          <i class="fa fa-lock"></i>
                        </button>
                      <?php else: ?>
                        <button class="btn btn-sm btn-success photo-listing-action-btn" onclick="event.preventDefault(); downloadPhoto(<?php echo e($photo->id); ?>)">
                          <i class="fa fa-download"></i>
                        </button>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <div class="photo-listing-info">
                  <h4 class="photo-listing-title"><?php echo e(Str::limit(stripslashes($photo->title),20)); ?></h4>

                  <?php if($photo->category): ?>
                    <div class="photo-listing-category">
                      <i class="fa fa-tag"></i> <?php echo e($photo->category); ?>

                    </div>
                  <?php endif; ?>

                  <div class="photo-listing-meta">
                    <div class="photo-listing-meta-item">
                      <i class="fa fa-eye"></i>
                      <span><?php echo e(number_format($photo->view_count)); ?></span>
                    </div>
                    <div class="photo-listing-meta-item">
                      <i class="fa fa-download"></i>
                      <span><?php echo e(number_format($photo->download_count)); ?></span>
                    </div>
                    <?php if($photo->width && $photo->height): ?>
                    <div class="photo-listing-meta-item">
                      <i class="fa fa-expand"></i>
                      <span><?php echo e($photo->width); ?>x<?php echo e($photo->height); ?></span>
                    </div>
                    <?php endif; ?>
                  </div>

                  <?php if($photo->license_price && $photo->license_price > 0): ?>
                  <div class="photo-listing-price-info">
                    <span class="photo-listing-price-label">License:</span>
                    <span class="photo-listing-price-value"><?php echo e(getcong('currency_symbol')); ?><?php echo e(number_format($photo->license_price, 2)); ?></span>
                  </div>
                  <?php endif; ?>
                </div>
              </a>
            </div>
          </div>
        </div>
         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <div class="col-xs-12">
      <?php echo $__env->make('_particles.pagination', ['paginator' => $photos_list], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
   </div>
</div>
<!-- End View All Photos -->

<!-- Banner -->
<?php if(get_web_banner('list_bottom')!=""): ?>
<div class="vid-item-ptb banner_ads_item pb-3">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<?php echo stripslashes(get_web_banner('list_bottom')); ?>

			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<script>
function viewPhoto(photoId) {
    window.location.href = '/photos/details/' + photoId;
}

function downloadPhoto(photoId) {
    window.location.href = '/photos/download/' + photoId;
}

function showPremiumInfo(photoId, price) {
    alert('This is a premium photo. License price: <?php echo e(getcong("currency_symbol")); ?>' + price + '\n\nPlease contact us to purchase the license for this photo.');
}

// Watermark functionality
function addWatermark(imageUrl, siteName) {
    return new Promise((resolve) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = function() {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            canvas.width = this.width;
            canvas.height = this.height;

            // Draw original image
            ctx.drawImage(this, 0, 0);

            // Add watermark
            ctx.font = 'bold 24px Arial';
            ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
            ctx.strokeStyle = 'rgba(0, 0, 0, 0.7)';
            ctx.lineWidth = 2;

            const text = siteName || '<?php echo e(getcong("site_name")); ?>';
            const textWidth = ctx.measureText(text).width;
            const x = (canvas.width - textWidth) / 2;
            const y = canvas.height - 30;

            // Draw text with stroke
            ctx.strokeText(text, x, y);
            ctx.fillText(text, x, y);

            resolve(canvas.toDataURL('image/jpeg', 0.8));
        };
        img.src = imageUrl;
    });
}

// Enhanced download with watermark
function downloadWithWatermark(photoId, imageUrl) {
    addWatermark(imageUrl, '<?php echo e(getcong("site_name")); ?>').then(watermarkedDataUrl => {
        const link = document.createElement('a');
        link.download = 'watermarked_photo_' + photoId + '.jpg';
        link.href = watermarkedDataUrl;
        link.click();
    });
}
</script>

<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/pages/shows/list.blade.php ENDPATH**/ ?>