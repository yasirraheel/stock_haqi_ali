<div class="embed-responsive embed-responsive-16by9 ratio ratio-16x9">
	 {!! $tv_info->channel_url!!}
</div>
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
