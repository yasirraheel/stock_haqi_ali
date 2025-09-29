<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beautiful Responsive Video Player</title>
    <style>
        /* Basic styling for the container */
        .video-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            background-color: #000;
        }

        /* Beautiful video styling */
        video {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 10px;
        }

        /* Hide the three-dot menu button */
        video::-webkit-media-controls-enclosure {
            overflow: hidden !important;
        }

        video::-webkit-media-controls-panel {
            overflow: hidden !important;
        }

        video::-webkit-media-controls-download-button {
            display: none !important;
        }

        video::-webkit-media-controls-playback-rate-button {
            display: none !important;
        }

        /* Responsive design for mobile */
        @media (max-width: 768px) {
            .video-container {
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="video-container">
        <!-- Video player with dynamic URL from movie_details->video_url -->
<video id="videoPlayer" controls autoplay preload="metadata" 
    poster="{{ url($movies_info->video_image_thumb) }}" 
    controlsList="nodownload" oncontextmenu="return false;">
    <source src="{{ $movies_info->video_url }}" type="video/mp4">
    Your browser does not support the video tag.
</video>
    </div>
    <div
        style="text-align: center; padding: 5px 2px; font-size: 24px; font-weight: 700; background: #101011; border-radius: 10px; margin-top: 3px; line-height: 2;">
        <div class="row justify-content-center align-items-center mb-3">
            <div class="col-12">
                @if (isset($movies_info->video_slug) && isset($movies_info->id) && isset($movies_info->video_title))
                    <a
                        href="{{ url('movies/details', ['slug' => $movies_info->video_slug, 'id' => $movies_info->id]) }}">
                        <p>{{ $movies_info->video_title }}</p>
                    </a>
                @else
                    <p>{{ 'Movie details are not available' }}</p>
                @endif
            </div>
 @php
                    $currency_code = getcong('currency_code') ? getcong('currency_code') : 'USD';
                    $user = Auth::user();
                    $user_id = $user ? $user->id : null;
                    $license = App\Models\UserLicense::where('user_id', $user_id)
                        ->where('video_id', $movies_info->id)
                        ->first();

                @endphp
            <div class="col-12 text-right">
                @auth
                    <form
                        action="{{ $user_has_liked ? route('movie-videos.unlike', $movies_info->id) : route('movie-videos.like', $movies_info->id) }}"
                        method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn {{ $user_has_liked ? 'btn-success' : 'btn-primary' }} btn-custom">
                            <i class="fas fa-heart"></i>
                            <span class="like-text">{{ $user_has_liked ? 'Unlike' : 'Like' }}
                                ({{ $movies_info->likes ?? 0 }})
                            </span>
                        </button>
                    </form>

                    @if (isset($movies_info->video_url))
                     @if ($license)
                        <a href="{{ $movies_info->video_url }}" target="_blank" class="btn btn-primary btn-custom">
                            <i class="fas fa-download"></i> Download
                        </a>
                    @endif
                    @endif
                @endauth

                @if (isset($movies_info->webpage_url))
                    <a href="{{ $movies_info->webpage_url }}" target="_blank" class="btn btn-primary btn-custom">
                        <i class="fas fa-globe"></i> Webpage
                    </a>
                @endif

               
                @auth
                    @if (isset($movies_info->id))
                        @if ($license)
                            <a href="{{ route('download.license', ['license_key' => $license->license_key]) }}"
                                class="vfx-item-btn-danger text-uppercase mb-30" title="download license">Download
                                License</a>
                        @else
                            @if (Auth::user()->id == $movies_info->added_by)
                                <a href="#" class="vfx-item-btn-danger text-uppercase mb-30" title="edit movie">Added
                                    By You</a>
                            @else
                                <a href="{{ URL::to('payment_method/' . $movies_info->id) }}"
                                    class="vfx-item-btn-danger text-uppercase mb-30" title="buy license">Buy License
                                    ({{ $movies_info->license_price }}{{ $currency_code }})</a>
                            @endif
                        @endif
                    @endif
                @endauth

            </div>


        </div>

        <style>
            .btn-custom {
                height: 40px;
                /* Height of buttons */
                padding: 0 20px;
                /* Padding for horizontal space */
                line-height: 40px;
                /* Vertically center text */
                min-width: 100px;
                /* Ensure buttons have consistent width */
                text-align: center;
                font-size: 16px;
                border-radius: 5px;
                /* Smooth corners */
                display: inline-flex;
                justify-content: center;
                align-items: center;
                transition: background-color 0.3s ease;
            }

            .btn-primary.btn-custom {
                background-color: #007bff;
                border: none;
            }

            .btn-success.btn-custom {
                background-color: #28a745;
                border: none;
            }

            .btn-primary.btn-custom:hover,
            .btn-success.btn-custom:hover {
                background-color: #0056b3;
            }

            .btn-custom i {
                margin-right: 5px;
            }
        </style>

        <script>
            $(document).ready(function() {
                $('form').on('submit', function(e) {
                    e.preventDefault();
                    var form = $(this);
                    var url = form.attr('action');
                    var method = form.attr('method');
                    $.ajax({
                        type: method,
                        url: url,
                        data: form.serialize(),
                        success: function(response) {
                            var button = form.find('button');
                            var likeText = button.find('.like-text');
                            var count = parseInt(likeText.text().match(/\d+/)[0]);

                            if (button.hasClass('btn-primary')) {
                                button.removeClass('btn-primary').addClass('btn-success');
                                likeText.text('Unlike (' + (count + 1) + ')');
                            } else {
                                button.removeClass('btn-success').addClass('btn-primary');
                                likeText.text('Like (' + (count - 1) + ')');
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                        }
                    });
                });
            });
        </script>
    </div>
</body>

</html>
