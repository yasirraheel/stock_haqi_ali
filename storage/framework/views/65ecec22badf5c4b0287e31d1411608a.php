<?php $__env->startSection('head_title', $photo->title.' | '.getcong('site_name') ); ?>

<?php $__env->startSection('head_url', Request::url()); ?>

<?php $__env->startSection('content'); ?>

<style>
.photo-details-container {
    background: #1a1a2e;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #2d2d44;
}

.photo-display {
    position: relative;
    background: #2d2d44;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 15px;
}

.photo-display img {
    width: 100%;
    max-height: 500px;
    object-fit: contain;
    display: block;
}

.photo-overlay-actions {
    position: absolute;
    top: 15px;
    right: 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    z-index: 10;
}

.action-btn {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.action-btn:hover {
    transform: translateY(-2px);
    text-decoration: none;
}

.btn-download {
    background: #10c469;
    color: white;
}

.btn-download:hover {
    background: #0ea55e;
    color: white;
}

.btn-purchase {
    background: #f9c851;
    color: #333;
}

.btn-purchase:hover {
    background: #f7b731;
    color: #333;
}

.btn-share {
    background: #167ac6;
    color: white;
}

.btn-share:hover {
    background: #0d6efd;
    color: white;
}

.premium-alert {
    position: absolute;
    top: 15px;
    left: 15px;
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    color: #333;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(255,215,0,0.3);
}

.photo-info {
    background: #1a1a2e;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #2d2d44;
}

.photo-title {
    color: #ffffff;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 10px;
    line-height: 1.2;
}

.photo-description {
    color: #b5b5b5;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 15px;
}

.photo-metadata {
    background: #1a1a2e;
    border-radius: 8px;
    padding: 15px;
    border: 1px solid #2d2d44;
}

.photo-metadata h4 {
    color: #ffffff;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
    border-bottom: 2px solid #167ac6;
    padding-bottom: 8px;
}

.metadata-section h6 {
    color: #167ac6;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 10px;
    margin-top: 15px;
}

.metadata-section h6:first-child {
    margin-top: 0;
}

.metadata-section .table {
    background: transparent;
    margin-bottom: 0;
}

.metadata-section .table td {
    color: #b5b5b5;
    font-size: 12px;
    padding: 5px 0;
    border: none;
    vertical-align: top;
}

.metadata-section .table td:first-child {
    color: #ffffff;
    font-weight: 600;
    width: 40%;
}

.tags .badge {
    background: #2d2d44;
    color: #b5b5b5;
    border: 1px solid #3d3d5c;
    margin-right: 5px;
    margin-bottom: 5px;
    font-size: 10px;
    padding: 4px 8px;
}

.related-photos {
    background: #1a1a2e;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
    border: 1px solid #2d2d44;
}

.related-photos h4 {
    color: #ffffff;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
}

.single-video {
    background: #2d2d44;
    border-radius: 6px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #3d3d5c;
}

.single-video:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

.single-video a {
    color: #ffffff;
    text-decoration: none;
}

.single-video a:hover {
    color: #167ac6;
    text-decoration: none;
}

.video-img img {
    width: 100%;
    height: 120px;
    object-fit: cover;
}

.season-title-item h6 {
    color: #ffffff;
    font-size: 12px;
    font-weight: 600;
    padding: 8px;
    margin: 0;
    line-height: 1.3;
}

@media (max-width: 768px) {
    .photo-overlay-actions {
        position: static;
        flex-direction: row;
        justify-content: center;
        margin-bottom: 15px;
    }

    .premium-alert {
        position: static;
        margin-bottom: 15px;
        text-align: center;
    }

    .photo-title {
        font-size: 20px;
    }
}
</style>

<!-- Start Photo Details -->
<div class="view-all-video-area vfx-item-ptb">
  <div class="container-fluid">
    <div class="photo-details-container">
      <div class="row">
        <div class="col-lg-8">
          <div class="photo-display">
            <?php if($photo->license_price && $photo->license_price > 0): ?>
            <div class="premium-alert">
              <i class="fa fa-crown"></i> Premium Photo - <?php echo e(getcong('currency_symbol')); ?><?php echo e(number_format($photo->license_price, 2)); ?>

            </div>
            <?php endif; ?>

            <img src="<?php echo e($photo->image_url); ?>" alt="<?php echo e($photo->title); ?>">

            <div class="photo-overlay-actions">
              <?php if($photo->license_price && $photo->license_price > 0): ?>
                <button class="action-btn btn-purchase" onclick="showPremiumInfo(<?php echo e($photo->id); ?>, <?php echo e($photo->license_price); ?>)">
                  <i class="fa fa-lock"></i> Purchase
                </button>
              <?php else: ?>
                <a href="<?php echo e(URL::to('photos/download/'.$photo->id)); ?>" class="action-btn btn-download">
                  <i class="fa fa-download"></i> Download
                </a>
              <?php endif; ?>
              <button class="action-btn btn-share" onclick="sharePhoto()">
                <i class="fa fa-share"></i> Share
              </button>
            </div>
          </div>

          <div class="photo-info">
            <h1 class="photo-title"><?php echo e($photo->title); ?></h1>

            <?php if($photo->description): ?>
              <div class="photo-description">
                <p><?php echo e($photo->description); ?></p>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="photo-metadata">
            <h4>Photo Information</h4>

          <div class="metadata-section">
            <h6>Basic Info</h6>
            <table class="table table-sm">
              <tr>
                <td><strong>Title:</strong></td>
                <td><?php echo e($photo->title); ?></td>
              </tr>
              <tr>
                <td><strong>Category:</strong></td>
                <td><?php echo e($photo->category ?: 'N/A'); ?></td>
              </tr>
              <tr>
                <td><strong>File Size:</strong></td>
                <td><?php echo e($photo->formatted_file_size ?: 'N/A'); ?></td>
              </tr>
              <tr>
                <td><strong>Dimensions:</strong></td>
                <td><?php echo e($photo->dimensions ?: 'N/A'); ?></td>
              </tr>
              <tr>
                <td><strong>File Type:</strong></td>
                <td><?php echo e($photo->file_type ? strtoupper($photo->file_type) : 'N/A'); ?></td>
              </tr>
              <tr>
                <td><strong>Views:</strong></td>
                <td><?php echo e(number_format($photo->view_count)); ?></td>
              </tr>
              <tr>
                <td><strong>Downloads:</strong></td>
                <td><?php echo e(number_format($photo->download_count)); ?></td>
              </tr>
            </table>
          </div>

          <?php if($photo->camera_make || $photo->camera_model): ?>
          <div class="metadata-section mt-4">
            <h6>Camera Info</h6>
            <table class="table table-sm">
              <?php if($photo->camera_make): ?>
              <tr>
                <td><strong>Make:</strong></td>
                <td><?php echo e($photo->camera_make); ?></td>
              </tr>
              <?php endif; ?>
              <?php if($photo->camera_model): ?>
              <tr>
                <td><strong>Model:</strong></td>
                <td><?php echo e($photo->camera_model); ?></td>
              </tr>
              <?php endif; ?>
              <?php if($photo->lens_model): ?>
              <tr>
                <td><strong>Lens:</strong></td>
                <td><?php echo e($photo->lens_model); ?></td>
              </tr>
              <?php endif; ?>
              <?php if($photo->focal_length): ?>
              <tr>
                <td><strong>Focal Length:</strong></td>
                <td><?php echo e($photo->focal_length); ?></td>
              </tr>
              <?php endif; ?>
              <?php if($photo->aperture): ?>
              <tr>
                <td><strong>Aperture:</strong></td>
                <td><?php echo e($photo->aperture); ?></td>
              </tr>
              <?php endif; ?>
              <?php if($photo->shutter_speed): ?>
              <tr>
                <td><strong>Shutter Speed:</strong></td>
                <td><?php echo e($photo->shutter_speed); ?></td>
              </tr>
              <?php endif; ?>
              <?php if($photo->iso): ?>
              <tr>
                <td><strong>ISO:</strong></td>
                <td><?php echo e($photo->iso); ?></td>
              </tr>
              <?php endif; ?>
            </table>
          </div>
          <?php endif; ?>

          <?php if($photo->gps_latitude && $photo->gps_longitude): ?>
          <div class="metadata-section mt-4">
            <h6>Location</h6>
            <table class="table table-sm">
              <tr>
                <td><strong>Latitude:</strong></td>
                <td><?php echo e($photo->gps_latitude); ?></td>
              </tr>
              <tr>
                <td><strong>Longitude:</strong></td>
                <td><?php echo e($photo->gps_longitude); ?></td>
              </tr>
              <?php if($photo->gps_altitude): ?>
              <tr>
                <td><strong>Altitude:</strong></td>
                <td><?php echo e($photo->gps_altitude); ?></td>
              </tr>
              <?php endif; ?>
            </table>
          </div>
          <?php endif; ?>

          <?php if($photo->tags): ?>
          <div class="metadata-section mt-4">
            <h6>Tags</h6>
            <div class="tags">
              <?php $__currentLoopData = explode(',', $photo->tags); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="badge badge-secondary mr-1"><?php echo e(trim($tag)); ?></span>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php if($related_photos->count() > 0): ?>
    <div class="related-photos">
      <h4>Related Photos</h4>
      <div class="row">
        <?php $__currentLoopData = $related_photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related_photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
          <div class="single-video">
            <a href="<?php echo e(URL::to('photos/details/'.$related_photo->id)); ?>" title="<?php echo e($related_photo->title); ?>">
              <div class="video-img">
                <img src="<?php echo e($related_photo->image_url); ?>" alt="<?php echo e($related_photo->title); ?>">
              </div>
              <div class="season-title-item">
                <h6><?php echo e(Str::limit($related_photo->title, 20)); ?></h6>
              </div>
            </a>
          </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
<!-- End Photo Details -->

<script>
function sharePhoto() {
  if (navigator.share) {
    navigator.share({
      title: '<?php echo e($photo->title); ?>',
      text: '<?php echo e($photo->description); ?>',
      url: window.location.href
    });
  } else {
    // Fallback for browsers that don't support Web Share API
    navigator.clipboard.writeText(window.location.href).then(function() {
      alert('Photo link copied to clipboard!');
    });
  }
}

function showPremiumInfo(photoId, price) {
  alert('This is a premium photo.\n\nLicense Price: <?php echo e(getcong("currency_symbol")); ?>' + price + '\n\nPlease contact us to purchase the license for this photo.\n\nEmail: <?php echo e(getcong("site_email")); ?>');
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/pages/photos/details.blade.php ENDPATH**/ ?>