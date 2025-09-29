<?php $__env->startSection('head_title', $audio->title.' | '.getcong('site_name') ); ?>

<?php $__env->startSection('head_url', Request::url()); ?>

<?php $__env->startSection('content'); ?>

<style>
.audio-details-container {
    background: #1a1a2e;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #2d2d44;
}

.audio-player-section {
    position: relative;
    background: #2d2d44;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 15px;
    padding: 20px;
}

.audio-thumbnail {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 64px;
    border-radius: 8px;
    margin-bottom: 20px;
    position: relative;
}

.audio-play-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,0,0,0.7);
    border-radius: 50%;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.audio-play-btn:hover {
    background: #167ac6;
    transform: translate(-50%, -50%) scale(1.1);
}

.audio-player {
    width: 100%;
    height: 50px;
    background: #1a1a2e;
    border-radius: 25px;
    border: 1px solid #3d3d5c;
    outline: none;
}

.audio-player::-webkit-media-controls-panel {
    background-color: #1a1a2e;
}

.audio-overlay-actions {
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

.audio-info {
    background: #1a1a2e;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #2d2d44;
}

.audio-title {
    color: #ffffff;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 10px;
    line-height: 1.2;
}

.audio-description {
    color: #b5b5b5;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 15px;
}

.audio-metadata {
    background: #1a1a2e;
    border-radius: 8px;
    padding: 15px;
    border: 1px solid #2d2d44;
}

.audio-metadata h4 {
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

.related-audios {
    background: #1a1a2e;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
    border: 1px solid #2d2d44;
}

.related-audios h4 {
    color: #ffffff;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
}

.single-audio {
    background: #2d2d44;
    border-radius: 6px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #3d3d5c;
}

.single-audio:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

.single-audio a {
    color: #ffffff;
    text-decoration: none;
}

.single-audio a:hover {
    color: #167ac6;
    text-decoration: none;
}

.audio-img {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 32px;
    position: relative;
}

.audio-img .play-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,0,0,0.7);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.audio-title-item h6 {
    color: #ffffff;
    font-size: 12px;
    font-weight: 600;
    padding: 8px;
    margin: 0;
    line-height: 1.3;
}

@media (max-width: 768px) {
    .audio-overlay-actions {
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

    .audio-title {
        font-size: 20px;
    }
}
</style>

<!-- Start Audio Details -->
<div class="view-all-video-area vfx-item-ptb">
  <div class="container-fluid">
    <div class="audio-details-container">
      <div class="row">
        <div class="col-lg-8">
          <div class="audio-player-section">
            <?php if($audio->license_price && $audio->license_price > 0): ?>
            <div class="premium-alert">
              <i class="fa fa-crown"></i> Premium Audio - <?php echo e(getcong('currency_symbol')); ?><?php echo e(number_format($audio->license_price, 2)); ?>

            </div>
            <?php endif; ?>

            <div class="audio-thumbnail">
              <div class="audio-play-btn" onclick="toggleAudio()">
                <i class="fa fa-play" id="playIcon"></i>
              </div>
            </div>

            <audio id="audioPlayer" class="audio-player" controls preload="metadata">
              <source src="<?php echo e(asset('storage/' . $audio->audio_path)); ?>" type="audio/<?php echo e($audio->format ?: 'mp3'); ?>">
              Your browser does not support the audio element.
            </audio>

            <div class="audio-overlay-actions">
              <?php if($audio->license_price && $audio->license_price > 0): ?>
                <button class="action-btn btn-purchase" onclick="showPremiumInfo(<?php echo e($audio->id); ?>, <?php echo e($audio->license_price); ?>)">
                  <i class="fa fa-lock"></i> Purchase
                </button>
              <?php else: ?>
                <a href="<?php echo e(URL::to('audio/download/'.$audio->id)); ?>" class="action-btn btn-download">
                  <i class="fa fa-download"></i> Download
                </a>
              <?php endif; ?>
              <button class="action-btn btn-share" onclick="shareAudio()">
                <i class="fa fa-share"></i> Share
              </button>
            </div>
          </div>

          <div class="audio-info">
            <h1 class="audio-title"><?php echo e($audio->title); ?></h1>

            <?php if($audio->description): ?>
              <div class="audio-description">
                <p><?php echo e($audio->description); ?></p>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="audio-metadata">
            <h4>Audio Information</h4>

          <div class="metadata-section">
            <h6>Basic Info</h6>
            <table class="table table-sm">
              <tr>
                <td><strong>Title:</strong></td>
                <td><?php echo e($audio->title); ?></td>
              </tr>
              <tr>
                <td><strong>Genre:</strong></td>
                <td><?php echo e($audio->genre ?: 'N/A'); ?></td>
              </tr>
              <tr>
                <td><strong>Duration:</strong></td>
                <td><?php echo e($audio->duration ?: 'N/A'); ?></td>
              </tr>
              <tr>
                <td><strong>Format:</strong></td>
                <td><?php echo e($audio->format ? strtoupper($audio->format) : 'N/A'); ?></td>
              </tr>
              <tr>
                <td><strong>File Size:</strong></td>
                <td><?php echo e($audio->file_size ?: 'N/A'); ?></td>
              </tr>
              <tr>
                <td><strong>Views:</strong></td>
                <td><?php echo e(number_format($audio->views_count)); ?></td>
              </tr>
              <tr>
                <td><strong>Downloads:</strong></td>
                <td><?php echo e(number_format($audio->downloads_count)); ?></td>
              </tr>
            </table>
          </div>

          <?php if($audio->bitrate || $audio->sample_rate): ?>
          <div class="metadata-section mt-4">
            <h6>Technical Info</h6>
            <table class="table table-sm">
              <?php if($audio->bitrate): ?>
              <tr>
                <td><strong>Bitrate:</strong></td>
                <td><?php echo e($audio->bitrate); ?> kbps</td>
              </tr>
              <?php endif; ?>
              <?php if($audio->sample_rate): ?>
              <tr>
                <td><strong>Sample Rate:</strong></td>
                <td><?php echo e($audio->sample_rate); ?> Hz</td>
              </tr>
              <?php endif; ?>
            </table>
          </div>
          <?php endif; ?>

          <?php if($audio->mood): ?>
          <div class="metadata-section mt-4">
            <h6>Mood</h6>
            <div class="badge badge-secondary"><?php echo e($audio->mood); ?></div>
          </div>
          <?php endif; ?>

          <?php if($audio->tags): ?>
          <div class="metadata-section mt-4">
            <h6>Tags</h6>
            <div class="tags">
              <?php $__currentLoopData = explode(',', $audio->tags); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="badge badge-secondary mr-1"><?php echo e(trim($tag)); ?></span>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php if($related_audios->count() > 0): ?>
    <div class="related-audios">
      <h4>Related Audio</h4>
      <div class="row">
        <?php $__currentLoopData = $related_audios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related_audio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
          <div class="single-audio">
            <a href="<?php echo e(URL::to('audio/details/'.$related_audio->id)); ?>" title="<?php echo e($related_audio->title); ?>">
              <div class="audio-img">
                <div class="play-icon">
                  <i class="fa fa-play"></i>
                </div>
              </div>
              <div class="audio-title-item">
                <h6><?php echo e(Str::limit($related_audio->title, 20)); ?></h6>
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
<!-- End Audio Details -->

<script>
let isPlaying = false;

function toggleAudio() {
    const audio = document.getElementById('audioPlayer');
    const playIcon = document.getElementById('playIcon');

    if (isPlaying) {
        audio.pause();
        playIcon.className = 'fa fa-play';
        isPlaying = false;
    } else {
        audio.play();
        playIcon.className = 'fa fa-pause';
        isPlaying = true;
    }
}

// Update play button when audio ends
document.getElementById('audioPlayer').addEventListener('ended', function() {
    document.getElementById('playIcon').className = 'fa fa-play';
    isPlaying = false;
});

// Update play button when audio is paused
document.getElementById('audioPlayer').addEventListener('pause', function() {
    document.getElementById('playIcon').className = 'fa fa-play';
    isPlaying = false;
});

// Update play button when audio starts playing
document.getElementById('audioPlayer').addEventListener('play', function() {
    document.getElementById('playIcon').className = 'fa fa-pause';
    isPlaying = true;
});

function shareAudio() {
  if (navigator.share) {
    navigator.share({
      title: '<?php echo e($audio->title); ?>',
      text: '<?php echo e($audio->description); ?>',
      url: window.location.href
    });
  } else {
    // Fallback for browsers that don't support Web Share API
    navigator.clipboard.writeText(window.location.href).then(function() {
      alert('Audio link copied to clipboard!');
    });
  }
}

function showPremiumInfo(audioId, price) {
  alert('This is a premium audio.\n\nLicense Price: <?php echo e(getcong("currency_symbol")); ?>' + price + '\n\nPlease contact us to purchase the license for this audio.\n\nEmail: <?php echo e(getcong("site_email")); ?>');
}
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/pages/audio/details.blade.php ENDPATH**/ ?>