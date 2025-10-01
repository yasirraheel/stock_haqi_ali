<!DOCTYPE html>
<html lang="{{getcong('default_language')}}">
<head>
<meta name="theme-color" content="#ff0015">
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="">
<title>@yield('head_title', getcong('site_name'))</title>
<meta name="description" content="@yield('head_description', getcong('site_description'))" />
<meta name="keywords" content="@yield('head_keywords', getcong('site_keywords'))" />
<link rel="canonical" href="@yield('head_url', url('/'))">

<meta property="og:type" content="movie" />
<meta property="og:title" content="@yield('head_title',  getcong('site_name'))" />
<meta property="og:description" content="@yield('head_description', getcong('site_description'))" />
<meta property="og:image" content="@yield('head_image', URL::asset('/'.getcong('site_logo')))" />
<meta property="og:url" content="@yield('head_url', url('/'))" />
<meta property="og:image:width" content="1024" />
<meta property="og:image:height" content="1024" />
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:image" content="@yield('head_image', URL::asset('/'.getcong('site_logo')))">
<link rel="image_src" href="@yield('head_image', URL::asset('/'.getcong('site_logo')))">

<!-- Favicon -->
<link rel="icon" href="{{ URL::asset('/'.getcong('site_favicon')) }}">


<!-- LOAD LOCAL CSS -->
<link rel="stylesheet" href="{{ URL::asset('site_assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('site_assets/css/owl.carousel.min.css') }}">

<link rel="stylesheet" href="{{ URL::asset('site_assets/css/megamenu.css') }}">
<link rel="stylesheet" href="{{ URL::asset('site_assets/css/ionicons.css') }}">
<link rel="stylesheet" href="{{ URL::asset('site_assets/css/font-awesome.min.css') }}">


<link rel="stylesheet" href="{{ URL::asset('site_assets/css/color-style/'.getcong('styling').'.css') }}" id="theme">

<link rel="stylesheet" href="{{ URL::asset('site_assets/css/responsive.css') }}">

<!-- Splide Slider CSS -->
<link rel="stylesheet" href="{{ URL::asset('site_assets/css/splide.min.css') }}">

<link rel="stylesheet" href="{{ URL::asset('site_assets/css/jquery-eu-cookie-law-popup.css') }}">

<!-- SweetAlert2 -->
<script src="{{ URL::asset('site_assets/js/sweetalert2@11.js') }}"></script>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,800&display=swap" rel="stylesheet">

@if(getcong('site_header_code'))
    {!!stripslashes(getcong('site_header_code'))!!}
 @endif

 @if(getcong('styling')=="style-one")

    <?php $search_bg="#22134e";?>

 @elseif(getcong('styling')=="style-two")

    <?php $search_bg="#0d0620";?>

 @elseif(getcong('styling')=="style-three")

    <?php $search_bg="#0d071e";?>

@elseif(getcong('styling')=="style-four")

    <?php $search_bg="#0d0620";?>

@elseif(getcong('styling')=="style-five")

    <?php $search_bg="#0f0823";?>

 @else

  <?php $search_bg="#000000";?>

 @endif

 <style type="text/css">
      .search .search-input input[type=text]::placeholder, .search .search-input input[type=text].focus {
          background: {{$search_bg}} !important;
      }

      /* Audio Section Styling - Matching Audio Page Design */
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
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
      }

      .audio-genre {
          background: rgba(254, 2, 120, 0.2);
          color: #fe0278;
          font-size: 9px;
          padding: 2px 8px;
          border-radius: 10px;
          margin-right: 8px;
          display: inline-block;
      }

      .audio-duration {
          color: #b0b0b0;
          font-size: 10px;
          display: inline-block;
      }

      .audio-actions {
          display: flex;
          gap: 5px;
          flex-shrink: 0;
      }

      .action-btn {
          width: 32px;
          height: 32px;
          background: rgba(255, 255, 255, 0.1);
          border: 1px solid rgba(255, 255, 255, 0.2);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          color: #ffffff;
          text-decoration: none;
          transition: all 0.3s ease;
          font-size: 12px;
      }

      .action-btn:hover {
          background: rgba(254, 2, 120, 0.2);
          border-color: #fe0278;
          color: #fe0278;
          transform: scale(1.05);
      }

      .audio-premium-badge {
          position: absolute;
          top: 8px;
          right: 8px;
          background: linear-gradient(45deg, #ffd700, #ffed4e);
          color: #000;
          font-size: 9px;
          padding: 3px 8px;
          border-radius: 12px;
          font-weight: 600;
          display: flex;
          align-items: center;
          gap: 3px;
          box-shadow: 0 2px 4px rgba(255, 215, 0, 0.3);
      }

      .audio-premium-badge i {
          font-size: 8px;
      }

      /* View All Button Styling */
      .vfx-item-section {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 20px;
      }

      .vfx-item-section h3 {
          margin: 0;
          color: #fff;
          font-size: 24px;
          font-weight: 600;
      }

      .view-all-btn {
          background: linear-gradient(45deg, #ff8508, #fd0575);
          color: #fff;
          padding: 8px 20px;
          border-radius: 25px;
          text-decoration: none;
          font-size: 14px;
          font-weight: 600;
          transition: all 0.3s ease;
          border: none;
          cursor: pointer;
          display: inline-block;
      }

      .view-all-btn:hover {
          background: linear-gradient(45deg, #fd0575, #ff8508);
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(253, 5, 120, 0.4);
          color: #fff;
          text-decoration: none;
      }

      .view-all-btn:focus {
          outline: none;
          box-shadow: 0 0 0 3px rgba(253, 5, 120, 0.3);
      }

      /* Photo Section Styling */
      .photo-card {
          background: #2d2d44;
          border-radius: 8px;
          padding: 0;
          margin-bottom: 15px;
          border: 1px solid #3d3d5c;
          transition: all 0.3s ease;
          overflow: hidden;
          position: relative;
      }

      .photo-card:hover {
          transform: translateY(-2px);
          box-shadow: 0 4px 15px rgba(0,0,0,0.2);
          border-color: #fe0278;
      }

      .photo-card-content {
          position: relative;
      }

      .photo-image {
          position: relative;
          overflow: hidden;
          height: 200px;
      }

      .photo-image img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          transition: transform 0.3s ease;
      }

      .photo-card:hover .photo-image img {
          transform: scale(1.05);
      }

      .photo-overlay {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: rgba(0,0,0,0.7);
          display: flex;
          align-items: center;
          justify-content: center;
          opacity: 0;
          transition: opacity 0.3s ease;
      }

      .photo-card:hover .photo-overlay {
          opacity: 1;
      }

      .photo-actions {
          display: flex;
          gap: 10px;
      }

      .photo-info {
          padding: 15px;
      }

      .photo-title {
          color: #ffffff;
          font-size: 14px;
          font-weight: 600;
          margin: 0 0 8px 0;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
      }

      .photo-category {
          background: rgba(254, 2, 120, 0.2);
          color: #fe0278;
          font-size: 9px;
          padding: 2px 8px;
          border-radius: 10px;
          margin-right: 8px;
          display: inline-block;
      }

      .photo-dimensions {
          color: #b0b0b0;
          font-size: 10px;
          display: inline-block;
      }

      .photo-premium-badge {
          position: absolute;
          top: 8px;
          right: 8px;
          background: linear-gradient(45deg, #ffd700, #ffed4e);
          color: #000;
          font-size: 9px;
          padding: 3px 8px;
          border-radius: 12px;
          font-weight: 600;
          display: flex;
          align-items: center;
          gap: 3px;
          box-shadow: 0 2px 4px rgba(255, 215, 0, 0.3);
          z-index: 2;
      }

      .photo-premium-badge i {
          font-size: 8px;
      }

      /* Mobile Responsive Audio Cards */
      @media (max-width: 768px) {
          .vfx-item-section {
              flex-direction: column;
              align-items: flex-start;
              gap: 15px;
          }

          .vfx-item-section h3 {
              font-size: 20px;
          }

          .view-all-btn {
              padding: 6px 16px;
              font-size: 12px;
          }

          .audio-card {
              margin-bottom: 10px;
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

          /* Mobile Photo Cards */
          .photo-image {
              height: 150px;
          }

          .photo-info {
              padding: 10px;
          }

          .photo-title {
              font-size: 13px;
          }

          .photo-category {
              font-size: 8px;
              padding: 1px 6px;
          }

          .photo-dimensions {
              font-size: 9px;
          }

          .photo-premium-badge {
              font-size: 8px;
              padding: 2px 6px;
          }
      }
 </style>

</head>
<body>


@if(!classActivePathSite('login') AND !classActivePathSite('signup') AND !classActivePathSite('password'))

    @include("_particles.header")

@endif

    @yield("content")

@if(!classActivePathSite('login') AND !classActivePathSite('signup') AND !classActivePathSite('password'))

    @include("_particles.footer")

@endif

<div id="popup1" class="popup-view popup-overlay">
  <div class="search">
    <div class="search-container has-results"><span class="title">{{trans('words.search')}}</span>
      <div class="search-input">
        <input type="text" name="s" id="search_box" class="search-container-input" placeholder="{{trans('words.title')}}" onkeyup="showSuggestions(this.value)" style="background: {{$search_bg}};">
      </div>
    </div>
    <div class="search-results mt-4" id="search_output">


    </div>
  </div>
  <a class="close" href="#" title="close"><i class="ion-close-round"></i></a>
</div>

<div class="eupopup eupopup-bottom"></div>


  <!-- Load Local JS -->
<script src="{{ URL::asset('site_assets/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ URL::asset('site_assets/js/jquery.easing.min.js') }}"></script>
<script src="{{ URL::asset('site_assets/js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('site_assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ URL::asset('site_assets/js/jquery.nice-select.min.js') }}"></script>
<script src="{{ URL::asset('site_assets/js/megamenu.js') }}"></script>


<!-- Splide Slider JS -->
<script src="{{ URL::asset('site_assets/js/splide.min.js') }}"></script>

<!-- Custom Main JS -->
<script src="{{ URL::asset('site_assets/js/custom-main.js') }}"></script>


<script src="{{ URL::asset('site_assets/js/jquery-eu-cookie-law-popup.js') }}"></script>

<script type="text/javascript">

@if(getcong('gdpr_cookie_on_off'))
  $(document).ready( function() {
  if ($(".eupopup").length > 0) {
    $(document).euCookieLawPopup().init({
       'cookiePolicyUrl' : '{{stripslashes(getcong('gdpr_cookie_url'))}}',
       'buttonContinueTitle' : '{{trans('words.gdpr_continue')}}',
       'buttonLearnmoreTitle' : '{{trans('words.gdpr_learn_more')}}',
       'popupPosition' : 'bottom',
       'colorStyle' : 'default',
       'compactStyle' : false,
       'popupTitle' : '{{stripslashes(getcong('gdpr_cookie_title'))}}',
       'popupText' : '{{stripslashes(getcong('gdpr_cookie_text'))}}'
    });
  }
});
@endif

function showSuggestions(inputString) {
  if(inputString.length <= 1){
    //document.getElementById('search_output').innerHTML = 'Search field empty!';
    document.getElementById('search_output').innerHTML = '';
  }else{
    $.ajax({
      url: "{{ URL::to('search_elastic') }}",
      method:"GET",
      data: { 's' : inputString},
      dataType:'text',
      beforeSend: function(){
      $("#search_box").css("background","{{$search_bg}} url({{ URL::asset('site_assets/images/LoaderIcon.gif') }}) no-repeat 100%");
      },
      success: function(result){
        //alert(result);
          //$("#search_output").html = result;
          $("#search_output").html(result);
          $("#search_box").css("background","{{$search_bg}}");
        }
    });
  }
}


</script>

<script type="text/javascript">

  $("li[data-path]").click(function() {

    $("head link#theme").attr("href", $(this).data("path"));
});

</script>

@if(Auth::check())

@if(Auth::user()->usertype!="Admin" AND Auth::user()->usertype!="Sub_Admin")
  @if(user_device_limit_reached(Auth::user()->id,Auth::user()->plan_id))
  <script type="text/javascript">
       //alert({{Auth::user()->id}});
    $(document).ready( function() {
      $('#user_device_list').modal('show');

    });
  </script>
  @endif
@endif

@if(Auth::user()->usertype!="Admin" AND Auth::user()->usertype!="Sub_Admin")

<script type="text/javascript">

  function executeQuery() {
  $.ajax({
    url: "{{url('check_user_remotely_logout_or_not/'.Session::getId())}}",
    success: function(data) {

      if(data=="false")
      {
         jQuery('#logout_remotly').modal('show');

         var timer = setTimeout(function() {
                  window.location="{{ URL::to('/') }}"
              }, 5000);
      }

    }
  });
  setTimeout(executeQuery, 10000); // you could choose not to continue on failure...
}

$(document).ready(function() {
  // run the first time; all subsequent calls will take care of themselves
  setTimeout(executeQuery, 10000);
});

</script>

@endif


@endif


@if(getcong('site_footer_code'))
    {!!stripslashes(getcong('site_footer_code'))!!}
@endif

<script>
// Audio Player Management for Home Page
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
        } catch (e) {
            console.log('Web Audio API not supported');
        }
    }
}

// Toggle Audio Play/Pause
function toggleAudio(audioId) {
    const audio = document.getElementById('audioPlayer' + audioId);
    const playIcon = document.getElementById('playIcon' + audioId);
    const audioCard = audio.closest('.audio-card');

    if (!audio) {
        console.log('Audio element not found for ID:', audioId);
        return;
    }

    // Initialize audio context if needed
    initAudioContext();

    // Stop any currently playing audio
    if (currentPlayingAudio && currentPlayingAudio !== audio) {
        currentPlayingAudio.pause();
        currentPlayingAudio.currentTime = 0;
        const currentPlayIcon = document.getElementById('playIcon' + currentPlayingAudio.id.replace('audioPlayer', ''));
        const currentCard = currentPlayingAudio.closest('.audio-card');
        if (currentPlayIcon) currentPlayIcon.className = 'fa fa-play';
        if (currentCard) currentCard.classList.remove('playing');
        stopVisualization();
    }

    if (audio.paused) {
        // Play audio
        const playPromise = audio.play();

        if (playPromise !== undefined) {
            playPromise.then(() => {
                playIcon.className = 'fa fa-pause';
                audioCard.classList.add('playing');
                currentPlayingAudio = audio;
                startVisualization(audioId);
                console.log('Audio playing successfully');
            }).catch(e => {
                console.log('Error playing audio:', e);
                // Try to resume audio context if it's suspended
                if (audioContext && audioContext.state === 'suspended') {
                    audioContext.resume().then(() => {
                        audio.play().then(() => {
                            playIcon.className = 'fa fa-pause';
                            audioCard.classList.add('playing');
                            currentPlayingAudio = audio;
                            startVisualization(audioId);
                        });
                    });
                }
            });
        }
    } else {
        // Pause audio
        audio.pause();
        playIcon.className = 'fa fa-play';
        audioCard.classList.remove('playing');
        currentPlayingAudio = null;
        stopVisualization();
    }

    // Handle audio end
    audio.onended = function() {
        playIcon.className = 'fa fa-play';
        audioCard.classList.remove('playing');
        currentPlayingAudio = null;
        stopVisualization();
    };
}

// Start audio visualization
function startVisualization(audioId) {
    if (!audioContext) return;

    const audio = document.getElementById('audioPlayer' + audioId);
    if (!audio) return;

    const source = audioContext.createMediaElementSource(audio);
    analyser = audioContext.createAnalyser();
    analyser.fftSize = 256;
    source.connect(analyser);
    analyser.connect(audioContext.destination);

    dataArray = new Uint8Array(analyser.frequencyBinCount);
    animateSpectrum(audioId);
}

// Animate spectrum bars
function animateSpectrum(audioId) {
    if (!analyser) return;

    analyser.getByteFrequencyData(dataArray);

    const spectrum = document.getElementById('spectrum' + audioId);
    if (!spectrum) return;

    const bars = spectrum.querySelectorAll('.spectrum-bar');
    const barCount = bars.length;
    const dataStep = Math.floor(dataArray.length / barCount);

    bars.forEach((bar, index) => {
        const dataIndex = index * dataStep;
        const value = dataArray[dataIndex] || 0;
        const height = Math.max(4, (value / 255) * 20);
        bar.style.height = height + 'px';
    });

    if (currentPlayingAudio && !currentPlayingAudio.paused) {
        animationId = requestAnimationFrame(() => animateSpectrum(audioId));
    }
}

// Stop visualization
function stopVisualization() {
    if (animationId) {
        cancelAnimationFrame(animationId);
        animationId = null;
    }

    // Reset all spectrum bars
    document.querySelectorAll('.spectrum-bar').forEach(bar => {
        bar.style.height = '4px';
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initAudioContext();

    // Add click listeners to all audio play buttons to initialize audio context
    document.querySelectorAll('.audio-play-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (audioContext && audioContext.state === 'suspended') {
                audioContext.resume();
            }
        });
    });
});
</script>

</body>
</html>
