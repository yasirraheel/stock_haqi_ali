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

      /* Audio Section Styling */
      .audio-section .single-video {
          position: relative;
          margin-bottom: 20px;
      }

      .audio-section .video-img {
          position: relative;
          overflow: hidden;
          border-radius: 8px;
          box-shadow: 0 4px 8px rgba(0,0,0,0.1);
          transition: transform 0.3s ease;
      }

      .audio-section .video-img:hover {
          transform: translateY(-5px);
      }

      .audio-section .video-img img {
          width: 100%;
          height: 200px;
          object-fit: cover;
          border-radius: 8px;
      }

      .audio-section .audio-placeholder {
          width: 100%;
          height: 200px;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          border-radius: 8px;
          position: relative;
          overflow: hidden;
      }

      .audio-section .audio-waveform {
          display: flex;
          align-items: center;
          gap: 3px;
          margin-top: 10px;
      }

      .audio-section .wave-bar {
          width: 4px;
          background: rgba(255,255,255,0.8);
          border-radius: 2px;
          animation: wave 1.5s ease-in-out infinite;
      }

      .audio-section .wave-bar:nth-child(1) { height: 20px; animation-delay: 0s; }
      .audio-section .wave-bar:nth-child(2) { height: 30px; animation-delay: 0.1s; }
      .audio-section .wave-bar:nth-child(3) { height: 25px; animation-delay: 0.2s; }
      .audio-section .wave-bar:nth-child(4) { height: 35px; animation-delay: 0.3s; }
      .audio-section .wave-bar:nth-child(5) { height: 15px; animation-delay: 0.4s; }

      @keyframes wave {
          0%, 100% { transform: scaleY(0.5); }
          50% { transform: scaleY(1); }
      }

      .audio-section .vid-lab-premium {
          position: absolute;
          top: 10px;
          right: 10px;
          z-index: 2;
      }

      .audio-section .vid-lab-premium img {
          width: 30px;
          height: 30px;
      }

      .audio-section .video-item-content {
          position: absolute;
          bottom: 0;
          left: 0;
          right: 0;
          background: linear-gradient(transparent, rgba(0,0,0,0.8));
          color: white;
          padding: 20px 15px 15px;
          font-size: 14px;
          font-weight: 600;
          text-align: center;
      }

      .audio-section .vfx-item-section h3 {
          color: #fff;
          font-size: 24px;
          font-weight: 600;
          margin-bottom: 20px;
      }

      .audio-section .recently-watched-video-carousel .owl-item {
          padding: 0 10px;
      }

      .audio-section .recently-watched-video-carousel .owl-nav {
          position: absolute;
          top: -60px;
          right: 0;
      }

      .audio-section .recently-watched-video-carousel .owl-nav button {
          background: rgba(255,255,255,0.1);
          border: 1px solid rgba(255,255,255,0.2);
          color: #fff;
          padding: 8px 12px;
          margin-left: 5px;
          border-radius: 4px;
          transition: all 0.3s ease;
      }

      .audio-section .recently-watched-video-carousel .owl-nav button:hover {
          background: rgba(255,255,255,0.2);
          border-color: rgba(255,255,255,0.3);
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



</body>
</html>
