<link rel="stylesheet" type="text/css" href="{{ URL::asset('site_assets/player/content/global.css') }}">
<script type="text/javascript" src="{{ URL::asset('site_assets/player/java/' . $FWDEVPlayer) }}"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="slider-area">
    @if ($movies_info && $movies_info->video_url != '')
        @include('pages.movies.player.google_drive_player')
    @else
        <div
            style="text-align: center; padding: 70px 30px; font-size: 24px; font-weight: 700; background: #101011; border-radius: 10px; margin-top: 15px; min-height: 280px; line-height: 6;">
            NO Source URL Set
        </div>
    @endif

</div>
