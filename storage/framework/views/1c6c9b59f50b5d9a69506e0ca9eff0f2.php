<?php $__env->startSection('head_title', 'Audio Library | ' . getcong('site_name')); ?>

<?php $__env->startSection('head_description', 'Browse our collection of high-quality audio files, music, and sound effects.'); ?>

<?php $__env->startSection('content'); ?>

<style>
.audio-list-container {
    background: #1a1a2e;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #2d2d44;
}

.audio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.audio-item {
    background: #2d2d44;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #3d3d5c;
    position: relative;
}

.audio-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    border-color: #167ac6;
}

.audio-item a {
    color: #ffffff;
    text-decoration: none;
    display: block;
}

.audio-item a:hover {
    color: #167ac6;
    text-decoration: none;
}

.audio-thumbnail {
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 48px;
}

.audio-play-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,0,0,0.7);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    transition: all 0.3s ease;
}

.audio-item:hover .audio-play-btn {
    background: #167ac6;
    transform: translate(-50%, -50%) scale(1.1);
}

.audio-info {
    padding: 15px;
}

.audio-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    line-height: 1.3;
    color: #ffffff;
}

.audio-meta {
    font-size: 12px;
    color: #b5b5b5;
    margin-bottom: 5px;
}

.audio-genre {
    background: #167ac6;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

.premium-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    color: #333;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: bold;
    z-index: 10;
}

.filters-section {
    background: #2d2d44;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #3d3d5c;
}

.filter-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 15px;
}

.filter-btn {
    padding: 8px 16px;
    border: 1px solid #3d3d5c;
    background: #1a1a2e;
    color: #b5b5b5;
    border-radius: 20px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.filter-btn:hover, .filter-btn.active {
    background: #167ac6;
    color: white;
    border-color: #167ac6;
    text-decoration: none;
}

.genre-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.genre-btn {
    padding: 6px 12px;
    border: 1px solid #3d3d5c;
    background: #1a1a2e;
    color: #b5b5b5;
    border-radius: 15px;
    text-decoration: none;
    font-size: 11px;
    transition: all 0.3s ease;
}

.genre-btn:hover, .genre-btn.active {
    background: #10c469;
    color: white;
    border-color: #10c469;
    text-decoration: none;
}

.no-audio {
    text-align: center;
    padding: 60px 20px;
    color: #b5b5b5;
}

.no-audio i {
    font-size: 48px;
    margin-bottom: 15px;
    color: #3d3d5c;
}

.pagination-wrapper {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}

.pagination .page-link {
    background: #2d2d44;
    border: 1px solid #3d3d5c;
    color: #b5b5b5;
}

.pagination .page-link:hover {
    background: #167ac6;
    border-color: #167ac6;
    color: white;
}

.pagination .page-item.active .page-link {
    background: #167ac6;
    border-color: #167ac6;
    color: white;
}

@media (max-width: 768px) {
    .audio-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }

    .filter-buttons {
        justify-content: center;
    }

    .genre-filters {
        justify-content: center;
    }
}
</style>

<!-- Start Audio List -->
<div class="view-all-video-area vfx-item-ptb">
  <div class="container-fluid">
    <div class="audio-list-container">

      <!-- Filters Section -->
      <div class="filters-section">
        <h4 style="color: #ffffff; margin-bottom: 20px; font-weight: 600;">
          <i class="fa fa-music"></i> Audio Library
        </h4>

        <!-- Sort Filters -->
        <div class="filter-buttons">
          <a href="<?php echo e(URL::to('audio')); ?>" class="filter-btn <?php echo e(!request('filter') ? 'active' : ''); ?>">
            <i class="fa fa-clock"></i> Latest
          </a>
          <a href="<?php echo e(URL::to('audio?filter=old')); ?>" class="filter-btn <?php echo e(request('filter') == 'old' ? 'active' : ''); ?>">
            <i class="fa fa-history"></i> Oldest
          </a>
          <a href="<?php echo e(URL::to('audio?filter=alpha')); ?>" class="filter-btn <?php echo e(request('filter') == 'alpha' ? 'active' : ''); ?>">
            <i class="fa fa-sort-alpha-down"></i> A-Z
          </a>
          <a href="<?php echo e(URL::to('audio?filter=rand')); ?>" class="filter-btn <?php echo e(request('filter') == 'rand' ? 'active' : ''); ?>">
            <i class="fa fa-random"></i> Random
          </a>
        </div>

        <!-- Genre Filters -->
        <?php if($genres->count() > 0): ?>
        <div class="genre-filters">
          <span style="color: #b5b5b5; font-size: 12px; margin-right: 10px;">Genres:</span>
          <?php $__currentLoopData = $genres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(URL::to('audio?genre=' . urlencode($genre))); ?>" class="genre-btn <?php echo e(request('genre') == $genre ? 'active' : ''); ?>">
              <?php echo e($genre); ?>

            </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Audio Grid -->
      <?php if($audios_list->count() > 0): ?>
        <div class="audio-grid">
          <?php $__currentLoopData = $audios_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $audio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="audio-item">
              <?php if($audio->license_price && $audio->license_price > 0): ?>
                <div class="premium-badge">
                  <i class="fa fa-crown"></i> Premium
                </div>
              <?php endif; ?>

              <a href="<?php echo e(URL::to('audio/details/' . $audio->id)); ?>" title="<?php echo e($audio->title); ?>">
                <div class="audio-thumbnail">
                  <div class="audio-play-btn">
                    <i class="fa fa-play"></i>
                  </div>
                </div>

                <div class="audio-info">
                  <h5 class="audio-title"><?php echo e(Str::limit($audio->title, 30)); ?></h5>

                  <?php if($audio->duration): ?>
                    <div class="audio-meta">
                      <i class="fa fa-clock"></i> <?php echo e($audio->duration); ?>

                    </div>
                  <?php endif; ?>

                  <?php if($audio->format): ?>
                    <div class="audio-meta">
                      <i class="fa fa-file-audio"></i> <?php echo e(strtoupper($audio->format)); ?>

                    </div>
                  <?php endif; ?>

                  <?php if($audio->genre): ?>
                    <div class="audio-genre"><?php echo e($audio->genre); ?></div>
                  <?php endif; ?>
                </div>
              </a>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
          <?php echo e($audios_list->links()); ?>

        </div>
      <?php else: ?>
        <div class="no-audio">
          <i class="fa fa-music"></i>
          <h4>No Audio Files Found</h4>
          <p>There are no audio files available at the moment.</p>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>
<!-- End Audio List -->

<?php $__env->stopSection(); ?>

<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/pages/audio/list.blade.php ENDPATH**/ ?>