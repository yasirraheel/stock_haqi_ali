<?php $__env->startSection('head_title', 'Audio Library | '.getcong('site_name') ); ?>

<?php $__env->startSection('head_url', Request::url()); ?>

<?php $__env->startSection('content'); ?>

<style>
/* SoundCloud-style Audio Card Styling */
.audio-card {
    background: #2d2d44;
    border-radius: 8px;
    padding: 0;
    margin-bottom: 15px;
    border: 1px solid #3d3d5c;
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
    height: 80px;
}

.audio-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    border-color: #fe0278;
}

.audio-card-content {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    height: 100%;
    position: relative;
}

.audio-play-btn {
    width: 40px;
    height: 40px;
    background: linear-gradient(90deg, #ff8508, #fd0575);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(255, 133, 8, 0.3);
    flex-shrink: 0;
    margin-right: 12px;
}

.audio-play-btn:hover {
    background: #fe0278;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(254, 2, 120, 0.4);
}

.audio-play-btn i {
    color: white;
    font-size: 14px;
    margin-left: 1px;
}

.audio-info {
    flex: 1;
    min-width: 0;
    margin-right: 10px;
}

.audio-spectrum {
    display: flex;
    align-items: end;
    gap: 2px;
    height: 20px;
    margin-right: 10px;
    opacity: 0.3;
    transition: opacity 0.3s ease;
}

.audio-card:hover .audio-spectrum {
    opacity: 0.7;
}

.audio-card.playing .audio-spectrum {
    opacity: 1;
}

.spectrum-bar {
    width: 2px;
    background: linear-gradient(to top, #ff8508, #fd0575);
    border-radius: 1px;
    height: 4px;
    transition: height 0.05s ease;
    min-height: 4px;
}

.audio-title {
    color: #ffffff;
    font-size: 14px;
    font-weight: 600;
    margin: 0 0 4px 0;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.audio-genre {
    display: inline-block;
    background: #2d2d44;
    color: #fe0278;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    margin-right: 6px;
    border: 1px solid #fe0278;
}

.audio-duration {
    color: #b5b5b5;
    font-size: 10px;
    font-weight: 500;
}

.audio-actions {
    display: flex;
    gap: 6px;
    flex-shrink: 0;
}

.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 12px;
}

.action-btn:first-child {
    background: #2d2d44;
    color: #b5b5b5;
    border: 1px solid #3d3d5c;
}

.action-btn:first-child:hover {
    background: #fe0278;
    color: white;
    text-decoration: none;
    transform: scale(1.05);
    border-color: #fe0278;
}

.action-btn:last-child {
    background: #2dbe60;
    color: white;
}

.action-btn:last-child:hover {
    background: #28a745;
    color: white;
    text-decoration: none;
    transform: scale(1.05);
}

.audio-premium-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    color: #333;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 9px;
    font-weight: bold;
    z-index: 10;
    box-shadow: 0 1px 4px rgba(255,215,0,0.3);
    display: flex;
    align-items: center;
    gap: 3px;
}

.audio-premium-badge i {
    font-size: 8px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .audio-card {
        height: 70px;
    }

    .audio-card-content {
        padding: 10px 12px;
    }

    .audio-play-btn {
        width: 35px;
        height: 35px;
        margin-right: 10px;
    }

    .audio-play-btn i {
        font-size: 12px;
    }

    .audio-title {
        font-size: 13px;
    }

    .audio-genre {
        font-size: 8px;
        padding: 1px 6px;
    }

    .audio-duration {
        font-size: 9px;
    }

    .action-btn {
        width: 28px;
        height: 28px;
        font-size: 10px;
    }

    .audio-premium-badge {
        font-size: 8px;
        padding: 2px 6px;
    }
}

/* Loading state for audio */
.audio-loading {
    opacity: 0.7;
    pointer-events: none;
}

.audio-loading .audio-play-btn {
    background: #6c757d;
}
</style>

<?php if(count($slider)!=0): ?>
  <?php echo $__env->make("pages.sports.slider", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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

<!-- Start View All Sports -->
<div class="view-all-video-area vfx-item-ptb">
  <div class="container-fluid">
   <div class="filter-list-area">
    <div class="row">
      <div class="col-lg-12">
        <div class="page-title-item">Audio Library</div>
        <div class="custom_select_filter">
          <select class="selectpicker show-menu-arrow" id="filter_by_genre">
            <option value="">Genre</option>
            <?php $__currentLoopData = $genres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e(URL::to('audio/?genre='.urlencode($genre))); ?>" <?php if(isset($_GET['genre']) && $_GET['genre']==$genre ): ?> selected <?php endif; ?>><?php echo e($genre); ?></option>
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


         <?php $__currentLoopData = $audios_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $audio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
         <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 col-6">
          <div class="single-video audio-card">
            <div class="audio-card-content">
              <div class="audio-play-btn" onclick="toggleAudio(<?php echo e($audio->id); ?>)">
                <i class="fa fa-play" id="playIcon<?php echo e($audio->id); ?>"></i>
              </div>

              <div class="audio-info">
                <h3 class="audio-title"><?php echo e(Str::limit(stripslashes($audio->title),30)); ?></h3>
                <?php if($audio->genre): ?>
                  <span class="audio-genre"><?php echo e($audio->genre); ?></span>
                <?php endif; ?>
                <?php if($audio->duration): ?>
                  <span class="audio-duration"><?php echo e($audio->duration); ?></span>
                <?php endif; ?>
              </div>

              <div class="audio-spectrum" id="spectrum<?php echo e($audio->id); ?>">
                <div class="spectrum-bar"></div>
                <div class="spectrum-bar"></div>
                <div class="spectrum-bar"></div>
                <div class="spectrum-bar"></div>
                <div class="spectrum-bar"></div>
                <div class="spectrum-bar"></div>
                <div class="spectrum-bar"></div>
                <div class="spectrum-bar"></div>
                <div class="spectrum-bar"></div>
                <div class="spectrum-bar"></div>
                <div class="spectrum-bar"></div>
                <div class="spectrum-bar"></div>
              </div>

              <div class="audio-actions">
                <a href="<?php echo e(URL::to('audio/details/'.$audio->id)); ?>" class="action-btn" title="View Details">
                  <i class="fa fa-eye"></i>
                </a>
                <?php if(!$audio->license_price || $audio->license_price == 0): ?>
                  <a href="<?php echo e(URL::to('audio/download/'.$audio->id)); ?>" class="action-btn" title="Download">
                    <i class="fa fa-download"></i>
                  </a>
                <?php endif; ?>
              </div>

              <?php if($audio->license_price && $audio->license_price > 0): ?>
              <div class="audio-premium-badge">
                <i class="fa fa-crown"></i>
                <span>Premium</span>
              </div>
              <?php endif; ?>
            </div>

            <!-- Hidden Audio Player -->
            <audio id="audioPlayer<?php echo e($audio->id); ?>" preload="none">
              <source src="<?php echo e(asset('storage/' . $audio->audio_path)); ?>" type="audio/<?php echo e($audio->format ?: 'mp3'); ?>">
            </audio>
          </div>
             </div>
         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


      </div>

    <div class="col-xs-12">

      <?php echo $__env->make('_particles.pagination', ['paginator' => $audios_list], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


   </div>
   </div>
</div>
<!-- End View All Sports -->

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

<script>
// Audio Player Management
let currentPlayingAudio = null;
let audioContext = null;
let analyser = null;
let dataArray = null;
let animationId = null;

// Initialize Audio Context
function initAudioContext() {
    if (!audioContext) {
        try {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            analyser = audioContext.createAnalyser();
            analyser.fftSize = 256;
            dataArray = new Uint8Array(analyser.frequencyBinCount);
        } catch (error) {
            console.warn('Web Audio API not supported:', error);
            return false;
        }
    }
    return true;
}

// Real-time spectrum visualization
function updateSpectrum(audioId) {
    if (!analyser || !dataArray) return;

    analyser.getByteFrequencyData(dataArray);
    const spectrum = document.getElementById('spectrum' + audioId);
    if (!spectrum) return;

    const bars = spectrum.querySelectorAll('.spectrum-bar');
    const barCount = bars.length;
    const dataStep = Math.floor(dataArray.length / barCount);

    bars.forEach((bar, index) => {
        const dataIndex = index * dataStep;
        const frequency = dataArray[dataIndex] || 0;
        const height = Math.max(4, (frequency / 255) * 20);
        bar.style.height = height + 'px';
    });

    animationId = requestAnimationFrame(() => updateSpectrum(audioId));
}

// Stop spectrum animation
function stopSpectrum() {
    if (animationId) {
        cancelAnimationFrame(animationId);
        animationId = null;
    }
}

function toggleAudio(audioId) {
    const audio = document.getElementById('audioPlayer' + audioId);
    const playIcon = document.getElementById('playIcon' + audioId);
    const card = audio.closest('.audio-card');

    // Stop any currently playing audio
    if (currentPlayingAudio && currentPlayingAudio !== audio) {
        currentPlayingAudio.pause();
        const currentPlayIcon = document.getElementById('playIcon' + currentPlayingAudio.id.replace('audioPlayer', ''));
        const currentCard = currentPlayingAudio.closest('.audio-card');
        if (currentPlayIcon) {
            currentPlayIcon.className = 'fa fa-play';
        }
        if (currentCard) {
            currentCard.classList.remove('playing');
        }
        stopSpectrum();
    }

    if (audio.paused) {
        // Add loading state
        card.classList.add('audio-loading');

        // Try to play audio first
        audio.play().then(() => {
            playIcon.className = 'fa fa-pause';
            currentPlayingAudio = audio;
            card.classList.remove('audio-loading');
            card.classList.add('playing');

            // Initialize audio context and connect to analyser
            try {
                if (initAudioContext()) {
                    const source = audioContext.createMediaElementSource(audio);
                    source.connect(analyser);
                    analyser.connect(audioContext.destination);

                    // Start real-time spectrum visualization
                    updateSpectrum(audioId);
                } else {
                    console.warn('Web Audio API not supported - spectrum visualization disabled');
                }
            } catch (audioContextError) {
                console.warn('Audio context error (spectrum may not work):', audioContextError);
                // Audio still plays, just without spectrum
            }
        }).catch(error => {
            console.error('Error playing audio:', error);
            card.classList.remove('audio-loading');

            // More specific error messages
            if (error.name === 'NotAllowedError') {
                alert('Please click the play button to start audio playback.');
            } else if (error.name === 'NotSupportedError') {
                alert('Audio format not supported. Please try a different audio file.');
            } else {
                alert('Error playing audio. Please try again.');
            }
        });
    } else {
        audio.pause();
        playIcon.className = 'fa fa-play';
        card.classList.remove('playing');
        stopSpectrum();
        currentPlayingAudio = null;
    }
}

// Update play button when audio ends
document.addEventListener('DOMContentLoaded', function() {
    const audioElements = document.querySelectorAll('audio[id^="audioPlayer"]');

    audioElements.forEach(audio => {
        audio.addEventListener('ended', function() {
            const audioId = this.id.replace('audioPlayer', '');
            const playIcon = document.getElementById('playIcon' + audioId);
            const card = this.closest('.audio-card');
            if (playIcon) {
                playIcon.className = 'fa fa-play';
            }
            if (card) {
                card.classList.remove('playing');
            }
            stopSpectrum();
            currentPlayingAudio = null;
        });

        audio.addEventListener('pause', function() {
            const audioId = this.id.replace('audioPlayer', '');
            const playIcon = document.getElementById('playIcon' + audioId);
            const card = this.closest('.audio-card');
            if (playIcon) {
                playIcon.className = 'fa fa-play';
            }
            if (card) {
                card.classList.remove('playing');
            }
            stopSpectrum();
        });

        audio.addEventListener('play', function() {
            const audioId = this.id.replace('audioPlayer', '');
            const playIcon = document.getElementById('playIcon' + audioId);
            const card = this.closest('.audio-card');
            if (playIcon) {
                playIcon.className = 'fa fa-pause';
            }
            if (card) {
                card.classList.add('playing');
            }

            // Initialize audio context and start spectrum
            try {
                if (initAudioContext()) {
                    const source = audioContext.createMediaElementSource(this);
                    source.connect(analyser);
                    analyser.connect(audioContext.destination);
                    updateSpectrum(audioId);
                } else {
                    console.warn('Web Audio API not supported - spectrum visualization disabled');
                }
            } catch (audioContextError) {
                console.warn('Audio context error (spectrum may not work):', audioContextError);
                // Audio still plays, just without spectrum
            }
        });
    });
});

// Pause all audio when page is hidden
document.addEventListener('visibilitychange', function() {
    if (document.hidden && currentPlayingAudio) {
        currentPlayingAudio.pause();
        const audioId = currentPlayingAudio.id.replace('audioPlayer', '');
        const playIcon = document.getElementById('playIcon' + audioId);
        const card = currentPlayingAudio.closest('.audio-card');
        if (playIcon) {
            playIcon.className = 'fa fa-play';
        }
        if (card) {
            card.classList.remove('playing');
        }
        stopSpectrum();
        currentPlayingAudio = null;
    }
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/pages/sports/list.blade.php ENDPATH**/ ?>