@extends('site_app')

@section('head_title', stripslashes($audio->title).' | '.getcong('site_name') )

@if($audio->description)
  @section('head_description', Str::limit(stripslashes($audio->description),160))
@endif

@section('head_image', URL::to('/site_assets/images/audio-placeholder.jpg') )

@section('head_url', Request::url())

@section('content')

<style>
/* Audio Details Page Styling - Matching List Page Theme */
.audio-card-related {
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

.audio-card-related:hover {
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

/* Main audio player styling */
#audioPlayer {
    background: #2d2d44;
    border-radius: 8px;
    border: 1px solid #3d3d5c;
}

#audioPlayer::-webkit-media-controls-panel {
    background-color: #2d2d44;
}

#audioPlayer::-webkit-media-controls-play-button {
    background-color: #fe0278;
    border-radius: 50%;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .audio-card-related {
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

    /* Mobile metadata adjustments */
    .audio-metadata {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 10px !important;
    }

    .metadata-actions {
        margin-left: 0 !important;
        width: 100%;
        justify-content: center;
    }

    .metadata-stats {
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
    }
}
</style>

<!-- Banner -->
@if(get_web_banner('details_top')!="")
<div class="vid-item-ptb banner_ads_item">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				{!!stripslashes(get_web_banner('details_top'))!!}
			</div>
		</div>
	</div>
</div>
@endif

<!-- Start Page Content Area -->
<div class="page-content-area vfx-item-ptb pt-3">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12 mb-4">
        <div class="detail-poster-area">
          <!-- Audio Poster -->
          <div class="dtl-poster-img" style="background: linear-gradient(90deg, #ff8508, #fd0575); height: 250px; display: flex; align-items: center; justify-content: center; color: white; font-size: 64px; border-radius: 8px; position: relative; margin-bottom: 20px;">
            @if($audio->license_price && $audio->license_price > 0)
              <div class="premium-badge" style="position: absolute; top: 15px; right: 15px; background: linear-gradient(45deg, #ffd700, #ffed4e); color: #333; padding: 8px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; z-index: 10; box-shadow: 0 2px 8px rgba(255,215,0,0.3);">
                <i class="fa fa-crown"></i> Premium - {{getcong('currency_symbol')}}{{number_format($audio->license_price, 2)}}
              </div>
            @endif
            <i class="fa fa-music"></i>
          </div>

          <!-- Audio Player -->
          <div class="audio-player-container" style="background: #2d2d44; border-radius: 8px; padding: 15px; border: 1px solid #3d3d5c; margin-bottom: 20px;">
            <audio id="audioPlayer" controls style="width: 100%; height: 50px;">
              <source src="{{ asset('storage/' . $audio->audio_path) }}" type="audio/{{ $audio->format ?: 'mp3' }}">
              Your browser does not support the audio element.
            </audio>
          </div>

          <!-- Compact Metadata -->
          <div class="audio-metadata" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center; margin-bottom: 20px; padding: 15px; background: #1a1a2e; border-radius: 8px; border: 1px solid #2d2d44;">
            <div class="metadata-stats" style="display: flex; gap: 15px; align-items: center;">
              <span style="color: #b5b5b5; font-size: 12px;"><i class="fa fa-eye"></i> {{number_format_short($audio->views_count)}} Views</span>
              @if($audio->created_at)
                <span style="color: #b5b5b5; font-size: 12px;"><i class="fa fa-calendar-alt"></i> {{ date('M d Y', strtotime($audio->created_at)) }}</span>
              @endif
              @if($audio->duration)
                <span style="color: #b5b5b5; font-size: 12px;"><i class="fa fa-clock"></i> {{$audio->duration}}</span>
              @endif
            </div>

            <div class="metadata-actions" style="display: flex; gap: 10px; margin-left: auto;">
              @if(Auth::check())
                @if(check_watchlist(Auth::user()->id,$audio->id,'Audio'))
                  <a href="{{URL::to('watchlist/remove')}}?post_id={{$audio->id}}&post_type=Audio" class="btn btn-sm" style="background: #fe0278; color: white; padding: 5px 12px; border-radius: 15px; font-size: 11px; text-decoration: none;"><i class="fa fa-check"></i> Remove from Watchlist</a>
                @else
                  <a href="{{URL::to('watchlist/add')}}?post_id={{$audio->id}}&post_type=Audio" class="btn btn-sm" style="background: #2dbe60; color: white; padding: 5px 12px; border-radius: 15px; font-size: 11px; text-decoration: none;"><i class="fa fa-plus"></i> Add to Watchlist</a>
                @endif
              @else
                <a href="{{URL::to('watchlist/add')}}?post_id={{$audio->id}}&post_type=Audio" class="btn btn-sm" style="background: #2dbe60; color: white; padding: 5px 12px; border-radius: 15px; font-size: 11px; text-decoration: none;"><i class="fa fa-plus"></i> Add to Watchlist</a>
              @endif

              <a href="#" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#social-media" style="background: #167ac6; color: white; padding: 5px 12px; border-radius: 15px; font-size: 11px; text-decoration: none;"><i class="fas fa-share-alt"></i> Share</a>
            </div>
          </div>

          <!-- Start Social Media Icon Popup -->
          <div id="social-media" class="modal fade centered-modal" tabindex="-1" role="dialog" aria-labelledby="myModal" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
              <div class="modal-content bg-dark-2 text-light">
                <div class="modal-header">
                  <h4 class="modal-title text-white">{{trans('words.share_text')}}</h4>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                  <div class="social-media-modal">
                    <ul>
                      <li><a title="Sharing" href="https://www.facebook.com/sharer/sharer.php?u={{URL::to('audio/details/'.$audio->id)}}" class="facebook-icon" target="_blank"><i class="ion-social-facebook"></i></a></li>
                      <li><a title="Sharing" href="https://twitter.com/intent/tweet?text={{$audio->title}}&amp;url={{URL::to('audio/details/'.$audio->id)}}" class="twitter-icon" target="_blank"><i class="ion-social-twitter"></i></a></li>
                      <li><a title="Sharing" href="https://www.instagram.com/?url={{URL::to('audio/details/'.$audio->id)}}" class="instagram-icon" target="_blank"><i class="ion-social-instagram"></i></a></li>
                      <li><a title="Sharing" href="https://wa.me?text={{URL::to('audio/details/'.$audio->id)}}" class="whatsapp-icon" target="_blank"><i class="ion-social-whatsapp"></i></a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End Social Media Icon Popup -->
        </div>
      </div>

      <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12 mb-4">
        <div class="poster-dtl-item">
          <h2>{{stripslashes($audio->title)}}</h2>
          <ul class="dtl-list-link">
            @if($audio->genre)
              <li><a href="{{ URL::to('audio/?genre='.urlencode($audio->genre)) }}" title="{{$audio->genre}}">{{$audio->genre}}</a></li>
            @endif
            @if($audio->format)
              <li><span>{{strtoupper($audio->format)}}</span></li>
            @endif
          </ul>

          @if($audio->description)
            <h3>{!!strip_tags(Str::limit(stripslashes($audio->description),350))!!}</h3>
          @endif
        </div>
      </div>
    </div>

    <!-- Start You May Also Like Audio Carousel -->
    <div class="row">
      <div class="video-carousel-area vfx-item-ptb related-video-item">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 p-0">
              <div class="vfx-item-section">
                <h3>{{trans('words.you_may_like')}}</h3>
              </div>
              <div class="tv-season-video-carousel owl-carousel">
                @foreach($related_audios as $related_audio)
                  <div class="single-video audio-card-related">
                    <div class="audio-card-content">
                      <div class="audio-play-btn" onclick="toggleRelatedAudio({{$related_audio->id}})">
                        <i class="fa fa-play" id="playIcon{{$related_audio->id}}"></i>
                      </div>

                      <div class="audio-info">
                        <h3 class="audio-title">{{Str::limit(stripslashes($related_audio->title),25)}}</h3>
                        @if($related_audio->genre)
                          <span class="audio-genre">{{$related_audio->genre}}</span>
                        @endif
                        @if($related_audio->duration)
                          <span class="audio-duration">{{$related_audio->duration}}</span>
                        @endif
                      </div>

                      <div class="audio-actions">
                        <a href="{{ URL::to('audio/details/'.$related_audio->id) }}" class="action-btn" title="View Details">
                          <i class="fa fa-eye"></i>
                        </a>
                        @if(!$related_audio->license_price || $related_audio->license_price == 0)
                          <a href="{{ URL::to('audio/download/'.$related_audio->id) }}" class="action-btn" title="Download">
                            <i class="fa fa-download"></i>
                          </a>
                        @endif
                      </div>

                      @if($related_audio->license_price && $related_audio->license_price > 0)
                        <div class="audio-premium-badge">
                          <i class="fa fa-crown"></i>
                          <span>Premium</span>
                        </div>
                      @endif
                    </div>

                    <!-- Hidden Audio Player -->
                    <audio id="audioPlayer{{$related_audio->id}}" preload="none">
                      <source src="{{ asset('storage/' . $related_audio->audio_path) }}" type="audio/{{ $related_audio->format ?: 'mp3' }}">
                    </audio>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End You May Also Like Audio Carousel -->
  </div>
</div>
<!-- End Page Content Area -->

<!-- Banner Bottom -->
@if(get_web_banner('details_bottom')!="")
<div class="vid-item-ptb banner_ads_item pb-3">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        {!!stripslashes(get_web_banner('details_bottom'))!!}
      </div>
    </div>
  </div>
</div>
@endif

<script type="text/javascript">

    @if(Session::has('flash_message'))
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: false,
      })

      Toast.fire({
        icon: 'success',
        title: '{{ Session::get('flash_message') }}'
      })
    @endif

  // Audio Player Functionality
  let isPlaying = false;

  function toggleAudio() {
      const audio = document.getElementById('audioPlayer');
      const playIcon = document.getElementById('playIcon');

      if (isPlaying) {
          audio.pause();
          playIcon.className = 'icon fa fa-play';
          isPlaying = false;
      } else {
          audio.play();
          playIcon.className = 'icon fa fa-pause';
          isPlaying = true;
      }
  }

  // Update play button when audio ends
  document.getElementById('audioPlayer').addEventListener('ended', function() {
      document.getElementById('playIcon').className = 'icon fa fa-play';
      isPlaying = false;
  });

  document.getElementById('audioPlayer').addEventListener('pause', function() {
      document.getElementById('playIcon').className = 'icon fa fa-play';
      isPlaying = false;
  });

  document.getElementById('audioPlayer').addEventListener('play', function() {
      document.getElementById('playIcon').className = 'icon fa fa-pause';
      isPlaying = true;
  });

  // Related Audio Player Management
  let currentPlayingRelatedAudio = null;

  function toggleRelatedAudio(audioId) {
      const audio = document.getElementById('audioPlayer' + audioId);
      const playIcon = document.getElementById('playIcon' + audioId);
      const card = audio.closest('.audio-card-related');

      if (currentPlayingRelatedAudio && currentPlayingRelatedAudio !== audio) {
          currentPlayingRelatedAudio.pause();
          const currentPlayIcon = document.getElementById('playIcon' + currentPlayingRelatedAudio.id.replace('audioPlayer', ''));
          const currentCard = currentPlayingRelatedAudio.closest('.audio-card-related');
          if (currentPlayIcon) {
              currentPlayIcon.className = 'fa fa-play';
          }
          if (currentCard) {
              currentCard.classList.remove('playing');
          }
      }

      if (audio.paused) {
          audio.play().then(() => {
              playIcon.className = 'fa fa-pause';
              currentPlayingRelatedAudio = audio;
              card.classList.add('playing');
          }).catch(error => {
              console.error('Error playing related audio:', error);
          });
      } else {
          audio.pause();
          playIcon.className = 'fa fa-play';
          card.classList.remove('playing');
          currentPlayingRelatedAudio = null;
      }
  }

  document.addEventListener('DOMContentLoaded', function() {
      const relatedAudioElements = document.querySelectorAll('audio[id^="audioPlayer"]:not(#audioPlayer)');

      relatedAudioElements.forEach(audio => {
          audio.addEventListener('ended', function() {
              const audioId = this.id.replace('audioPlayer', '');
              const playIcon = document.getElementById('playIcon' + audioId);
              const card = this.closest('.audio-card-related');
              if (playIcon) {
                  playIcon.className = 'fa fa-play';
              }
              if (card) {
                  card.classList.remove('playing');
              }
              currentPlayingRelatedAudio = null;
          });

          audio.addEventListener('pause', function() {
              const audioId = this.id.replace('audioPlayer', '');
              const playIcon = document.getElementById('playIcon' + audioId);
              const card = this.closest('.audio-card-related');
              if (playIcon) {
                  playIcon.className = 'fa fa-play';
              }
              if (card) {
                  card.classList.remove('playing');
              }
          });

          audio.addEventListener('play', function() {
              const audioId = this.id.replace('audioPlayer', '');
              const playIcon = document.getElementById('playIcon' + audioId);
              const card = this.closest('.audio-card-related');
              if (playIcon) {
                  playIcon.className = 'fa fa-pause';
              }
              if (card) {
                  card.classList.add('playing');
              }
          });
      });
  });

</script>

@endsection
