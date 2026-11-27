@extends('site_app')

@section('head_title', 'Audio Library | '.getcong('site_name') )

@section('head_url', Request::url())

@section('content')

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

@if(count($slider)!=0)
  @include("pages.sports.slider")
@endif

<!-- Banner -->
@if(get_web_banner('list_top')!="")
<div class="vid-item-ptb banner_ads_item">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				{!!stripslashes(get_web_banner('list_top'))!!}
			</div>
		</div>
	</div>
</div>
@endif

<link rel="stylesheet" href="{{ URL::asset('site_assets/css/nice-select.css') }}">

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
            @foreach($genres as $genre)
              <option value="{{ URL::to('audio/?genre='.urlencode($genre)) }}" @if(isset($_GET['genre']) && $_GET['genre']==$genre ) selected @endif>{{$genre}}</option>
            @endforeach

          </select>
        </div>

        <div class="custom_select_filter">
          <select class="selectpicker show-menu-arrow" id="filter_list" required>
            <option value="?filter=new" @if(isset($_GET['filter']) && $_GET['filter']=='new' ) selected @endif>{{trans('words.newest')}}</option>
            <option value="?filter=old" @if(isset($_GET['filter']) && $_GET['filter']=='old' ) selected @endif>{{trans('words.oldest')}}</option>
            <option value="?filter=alpha" @if(isset($_GET['filter']) && $_GET['filter']=='alpha' ) selected @endif>{{trans('words.a_to_z')}}</option>
            <option value="?filter=rand" @if(isset($_GET['filter']) && $_GET['filter']=='rand' ) selected @endif>{{trans('words.random')}}</option>
          </select>
        </div>
      </div>
    </div>
   </div>

    <div class="row">


         @foreach($audios_list as $audio)
         <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 col-6">
          <div class="single-video audio-card">
            <div class="audio-card-content">
              <div class="audio-play-btn" onclick="toggleAudio({{$audio->id}})">
                <i class="fa fa-play" id="playIcon{{$audio->id}}"></i>
              </div>

              <div class="audio-info">
                <h3 class="audio-title">{{Str::limit(stripslashes($audio->title),30)}}</h3>
                @if($audio->genre)
                  <span class="audio-genre">{{$audio->genre}}</span>
                @endif
                <span class="audio-duration" @if(!$audio->duration || in_array(trim($audio->duration), ['0:00', '00:00', '0'])) hidden @endif>{{trim($audio->duration ?? '')}}</span>
              </div>

              <div class="audio-spectrum" id="spectrum{{$audio->id}}">
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
                <a href="{{ URL::to('audio/details/'.$audio->id) }}" class="action-btn" title="View Details">
                  <i class="fa fa-eye"></i>
                </a>
                @if(!$audio->license_price || $audio->license_price == 0)
                  <a href="{{ URL::to('audio/download/'.$audio->id) }}" class="action-btn" title="Download">
                    <i class="fa fa-download"></i>
                  </a>
                @endif
              </div>

              @if($audio->license_price && $audio->license_price > 0)
              <div class="audio-premium-badge">
                <img class="premium-inline-icon" src="{{ URL::asset('site_assets/images/ic-premium.svg') }}" alt="Premium">
                <span>Premium</span>
              </div>
              @endif
            </div>

            <!-- Hidden Audio Player -->
            <audio id="audioPlayer{{$audio->id}}" preload="metadata">
              <source src="{{ $audio->audio_url }}" type="audio/{{ $audio->format ?: 'mp3' }}">
            </audio>
          </div>
             </div>
         @endforeach


      </div>

    <div class="col-xs-12">

      @include('_particles.pagination', ['paginator' => $audios_list])


   </div>
   </div>
</div>
<!-- End View All Sports -->

<!-- Banner -->
@if(get_web_banner('list_bottom')!="")
<div class="vid-item-ptb banner_ads_item pb-3">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				{!!stripslashes(get_web_banner('list_bottom'))!!}
			</div>
		</div>
	</div>
</div>
@endif

<script>
// Audio Player Management
let currentPlayingAudio = null;
let audioContext = null;
let analyser = null;
let dataArray = null;
let animationId = null;
let playRequestToken = 0;
let analyserConnected = false;

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

function connectAudioForVisualization(audio) {
    if (!initAudioContext()) return false;

    try {
        if (!audio.dataset.audioSourceConnected) {
            const source = audioContext.createMediaElementSource(audio);
            source.connect(analyser);
            audio.dataset.audioSourceConnected = 'true';
        }
        if (!analyserConnected) {
            analyser.connect(audioContext.destination);
            analyserConnected = true;
        }
        return true;
    } catch (audioContextError) {
        console.warn('Audio context error (spectrum may not work):', audioContextError);
        return false;
    }
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

function resetAudioCard(audio) {
    if (!audio) return;
    const audioId = audio.id.replace('audioPlayer', '');
    const playIcon = document.getElementById('playIcon' + audioId);
    const card = audio.closest('.audio-card');
    stopSpectrum();
    if (playIcon) playIcon.className = 'fa fa-play';
    if (card) card.classList.remove('playing', 'audio-loading');
}

function stopOtherAudio(exceptAudio) {
    document.querySelectorAll('audio[id^="audioPlayer"]').forEach(function(otherAudio) {
        if (otherAudio !== exceptAudio) {
            otherAudio.pause();
            resetAudioCard(otherAudio);
        }
    });
}

function toggleAudio(audioId) {
    const audio = document.getElementById('audioPlayer' + audioId);
    const playIcon = document.getElementById('playIcon' + audioId);
    const card = audio.closest('.audio-card');

    if (audio.paused) {
        const requestToken = ++playRequestToken;
        stopOtherAudio(audio);
        currentPlayingAudio = audio;
        card.classList.add('audio-loading');

        // Try to play audio first
        audio.play().then(() => {
            if (requestToken !== playRequestToken || currentPlayingAudio !== audio) {
                audio.pause();
                resetAudioCard(audio);
                return;
            }
            playIcon.className = 'fa fa-pause';
            card.classList.remove('audio-loading');
            card.classList.add('playing');

            // Connect this player only once; repeated plays must not duplicate sources.
            if (connectAudioForVisualization(audio)) {
                updateSpectrum(audioId);
            }
        }).catch(error => {
            console.error('Error playing audio:', error);
            if (currentPlayingAudio === audio) currentPlayingAudio = null;
            resetAudioCard(audio);

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
        ++playRequestToken;
        audio.pause();
        if (currentPlayingAudio === audio) currentPlayingAudio = null;
        resetAudioCard(audio);
    }
}

// Update play button when audio ends
document.addEventListener('DOMContentLoaded', function() {
    const audioElements = document.querySelectorAll('audio[id^="audioPlayer"]');

    function formatAudioDuration(seconds) {
        if (!Number.isFinite(seconds) || seconds <= 0) return '';
        const totalSeconds = Math.round(seconds);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const remainingSeconds = totalSeconds % 60;
        if (hours > 0) {
            return hours + ':' + String(minutes).padStart(2, '0') + ':' + String(remainingSeconds).padStart(2, '0');
        }
        return minutes + ':' + String(remainingSeconds).padStart(2, '0');
    }

    function updateAudioDuration(audio) {
        const durationElement = audio.closest('.audio-card')?.querySelector('.audio-duration');
        if (!durationElement) return;
        const formattedDuration = formatAudioDuration(audio.duration);
        durationElement.textContent = formattedDuration;
        durationElement.hidden = !formattedDuration;
    }

    audioElements.forEach(audio => {
        audio.addEventListener('loadedmetadata', function() { updateAudioDuration(this); });
        audio.addEventListener('durationchange', function() { updateAudioDuration(this); });
        updateAudioDuration(audio);

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
            if (currentPlayingAudio === this) currentPlayingAudio = null;
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
            if (currentPlayingAudio === this) currentPlayingAudio = null;
        });

        audio.addEventListener('play', function() {
            const audioId = this.id.replace('audioPlayer', '');
            const playIcon = document.getElementById('playIcon' + audioId);
            const card = this.closest('.audio-card');
            if (currentPlayingAudio && currentPlayingAudio !== this) {
                this.pause();
                return;
            }
            currentPlayingAudio = this;
            if (playIcon) {
                playIcon.className = 'fa fa-pause';
            }
            if (card) {
                card.classList.add('playing');
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

@endsection
