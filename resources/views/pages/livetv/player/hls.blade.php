<video id="v_player" class="video-js vjs-big-play-centered skin-blue vjs-16-9" controls preload="auto" playsinline crossorigin="anonymous" width="640" height="450" poster="{{URL::to('/'.$tv_info->channel_thumb)}}" data-setup="{}" @if(get_player_cong('autoplay')=="true")autoplay="true"@endif>

		  	<!-- video source -->
		  @if(isset($_GET['server']) AND $_GET['server']==2)
          <source src="{{$tv_info->channel_url2}}" type="application/x-mpegURL" />
          @elseif(isset($_GET['server']) AND $_GET['server']==3)
          <source src="{{$tv_info->channel_url3}}" type="application/x-mpegURL" />
          @else
          <source src="{{$tv_info->channel_url}}" type="application/x-mpegURL" />
          @endif

			<!-- worning text if needed -->
			<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
</video>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- Custom Script to Auto-click the Unmute Button -->
<script type="text/javascript">
    $(window).on('load', function() {
        setTimeout(function() {
            $("*:contains('Click to Unmute')").each(function() {
                if ($(this).text().trim() === 'Click to Unmute') {
                    $(this).click();
                }
            });
        }, 5000); // Adjust the delay as necessary to ensure the player has fully loaded
    });
</script>
