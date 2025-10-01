@extends('site_app')

@section('head_title', getcong('site_name'))

@section('head_url', Request::url())

@section('content')
<style>
@import url('{{ URL::asset('site_assets/css/home-modern.css') }}');
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12">
                    <div class="hero-content">
                        <h1 class="hero-title">Welcome to {{ getcong('site_name') }}</h1>
                        <p class="hero-subtitle">Discover amazing movies, audio content, and stunning photos all in one place</p>
                        <div class="hero-stats">
                            <div class="stat-item">
                                <span class="stat-number">{{ $movies_list->total() }}+</span>
                                <span class="stat-label">Movies</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">{{ $audio_list->count() }}+</span>
                                <span class="stat-label">Audio Files</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">{{ $photos_list->count() }}+</span>
                                <span class="stat-label">Photos</span>
                            </div>
                        </div>
                        <div class="hero-actions">
                            <a href="{{ URL::to('movies') }}" class="btn btn-primary btn-lg">Explore Movies</a>
                            @if(!Auth::check())
                                <a href="{{ URL::to('signup') }}" class="btn btn-outline-light btn-lg ml-3">Join Now</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="hero-featured-content">
                        @if($movies_info)
                            <div class="featured-movie-card">
                                <div class="featured-movie-image">
                                    <img src="{{ URL::to('/' . $movies_info->video_image_thumb) }}" 
                                         alt="{{ stripslashes($movies_info->video_title) }}" 
                                         class="img-fluid">
                                    @if($movies_info->video_access == 'Paid')
                                        <div class="premium-badge">
                                            <i class="fas fa-crown"></i> Premium
                                        </div>
                                    @endif
                                </div>
                                <div class="featured-movie-info">
                                    <h3>{{ stripslashes($movies_info->video_title) }}</h3>
                                    <p>{{ Str::limit(strip_tags(stripslashes($movies_info->video_description)), 120) }}</p>
                                    <a href="{{ URL::to('movies/details/' . $movies_info->video_slug . '/' . $movies_info->id) }}" 
                                       class="btn btn-watch">
                                        <i class="fas fa-play"></i> Watch Now
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Banner -->
    @if (get_web_banner('home_top') != '')
        <div class="banner-section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        {!! stripslashes(get_web_banner('home_top')) !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Featured Movies Section -->
    <section class="content-section featured-movies">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="section-header">
                        <h2 class="section-title">Featured Movies</h2>
                        <a href="{{ URL::to('movies') }}" class="view-all-btn">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="content-carousel owl-carousel" id="featured-movies-carousel">
                        @foreach ($movies_list->take(12) as $movies_data)
                            <div class="content-card movie-card">
                                <div class="card-image">
                                    @if (Auth::check())
                                        <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                            title="{{ $movies_data->video_title }}">
                                    @else
                                        @if ($movies_data->video_access == 'Paid')
                                            <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                                title="{{ $movies_data->video_title }}" data-toggle="modal"
                                                data-target="#loginAlertModal">
                                        @else
                                            <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                                title="{{ $movies_data->video_title }}">
                                        @endif
                                    @endif
                                    <img src="{{ URL::to('/' . $movies_data->video_image_thumb) }}"
                                        alt="{{ stripslashes($movies_data->video_title) }}"
                                        title="{{ stripslashes($movies_data->video_title) }}" 
                                        class="img-fluid">
                                    @if ($movies_data->video_access == 'Paid')
                                        <div class="premium-overlay">
                                            <i class="fas fa-crown"></i>
                                        </div>
                                    @endif
                                    <div class="card-overlay">
                                        <div class="play-btn">
                                            <i class="fas fa-play"></i>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                                <div class="card-content">
                                    <h4 class="card-title">{{ Str::limit(stripslashes($movies_data->video_title), 25) }}</h4>
                                    <p class="card-genre">
                                        {{ App\Genres::where('id', $movies_data->movie_genre_id)->first()->genre_name ?? 'Not specified' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (Auth::check() && $recently_watched->count() > 0)
        <!-- Start Recently Watched Video Section -->
        <div class="video-shows-section vfx-item-ptb">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="vfx-item-section">
                            <h3>{{ trans('words.recently_watched') }}</h3>
                        </div>
                        <div class="recently-watched-video-carousel owl-carousel">
                            @foreach ($recently_watched as $i => $watched_videos)
                                <div class="single-video">
                                    @if ($watched_videos->video_type == 'Movies')
                                        @php
                                            $info = recently_watched_info(
                                                $watched_videos->video_type,
                                                $watched_videos->video_id,
                                            );
                                        @endphp
                                        @if ($info)
                                            <a href="{{ URL::to('movies/details/' . $info->video_slug . '/' . $info->id) }}"
                                                title="{{ $info->video_title }}">
                                                <div class="video-img">
                                                    <span class="video-item-content">{{ $info->video_title }}</span>
                                                    <img src="{{ URL::to('/' . $info->video_image) }}"
                                                        alt="{{ $info->video_title }}"
                                                        title="Movies-{{ $info->video_title }}">
                                                </div>
                                            </a>
                                        @endif
                                    @endif

                                    @if ($watched_videos->video_type == 'Episodes')
                                        @php
                                            $episode_series_id = \App\Episodes::getEpisodesInfo(
                                                $watched_videos->video_id,
                                                'episode_series_id',
                                            );
                                            $info = recently_watched_info(
                                                $watched_videos->video_type,
                                                $watched_videos->video_id,
                                            );
                                        @endphp
                                        @if ($info)
                                            <div class="single-video">
                                                <a href="{{ URL::to('shows/' . \App\Series::getSeriesInfo($episode_series_id, 'series_slug') . '/' . $info->video_slug . '/' . $info->id) }}"
                                                    title="{{ $info->video_title }}">
                                                    <div class="video-img">
                                                        <span class="video-item-content">{{ $info->video_title }}</span>
                                                        <img src="{{ URL::to('/' . $info->video_image) }}"
                                                            alt="{{ $info->video_title }}"
                                                            title="Episodes-{{ $info->video_title }}">
                                                    </div>
                                                </a>
                                            </div>
                                        @endif
                                    @endif

                                    @if ($watched_videos->video_type == 'Sports')
                                        @php
                                            $info = recently_watched_info(
                                                $watched_videos->video_type,
                                                $watched_videos->video_id,
                                            );
                                        @endphp
                                        @if ($info)
                                            <div class="single-video">
                                                <a href="{{ URL::to('sports/details/' . $info->video_slug . '/' . $info->id) }}"
                                                    title="{{ $info->video_title }}">
                                                    <div class="video-img">
                                                        <span class="video-item-content">{{ $info->video_title }}</span>
                                                        <img src="{{ URL::to('/' . $info->video_image) }}"
                                                            alt="{{ $info->video_title }}"
                                                            title="Sports-{{ $info->video_title }}">
                                                    </div>
                                                </a>
                                            </div>
                                        @endif
                                    @endif

                                    @if ($watched_videos->video_type == 'LiveTV')
                                        @php
                                            $info = recently_watched_info(
                                                $watched_videos->video_type,
                                                $watched_videos->video_id,
                                            );
                                        @endphp
                                        @if ($info)
                                            <div class="single-video">
                                                <a href="{{ URL::to('livetv/details/' . $info->channel_slug . '/' . $info->id) }}"
                                                    title="{{ $info->channel_name }}">
                                                    <div class="video-img">
                                                        <span class="video-item-content">{{ $info->channel_name }}</span>
                                                        <img src="{{ URL::to('/' . $info->channel_thumb) }}"
                                                            alt="{{ $info->channel_name }}"
                                                            title="LiveTV-{{ $info->channel_name }}">
                                                    </div>
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Recently Watched Video Section -->
    @endif
    

    <!-- Audio Library Section -->
    @if(isset($audio_list) && $audio_list->count() > 0)
    <section class="content-section audio-library">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="section-header">
                        <h2 class="section-title">Audio Library</h2>
                        <a href="{{ URL::to('audio') }}" class="view-all-btn">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach ($audio_list as $audio_data)
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-3">
                        <div class="content-card audio-card">
                            <div class="audio-card-body">
                                <div class="audio-play-section">
                                    <div class="audio-play-btn" onclick="toggleAudio({{$audio_data->id}})">
                                        <i class="fa fa-play" id="playIcon{{$audio_data->id}}"></i>
                                    </div>
                                    <div class="audio-spectrum" id="spectrum{{$audio_data->id}}">
                                        <div class="spectrum-bar"></div>
                                        <div class="spectrum-bar"></div>
                                        <div class="spectrum-bar"></div>
                                        <div class="spectrum-bar"></div>
                                        <div class="spectrum-bar"></div>
                                        <div class="spectrum-bar"></div>
                                        <div class="spectrum-bar"></div>
                                        <div class="spectrum-bar"></div>
                                    </div>
                                </div>
                                
                                <div class="audio-info">
                                    <h4 class="audio-title">{{Str::limit(stripslashes($audio_data->title),35)}}</h4>
                                    <div class="audio-meta">
                                        @if($audio_data->genre)
                                            <span class="audio-genre">{{$audio_data->genre}}</span>
                                        @endif
                                        @if($audio_data->duration)
                                            <span class="audio-duration">{{$audio_data->duration}}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="audio-actions">
                                    <a href="{{ URL::to('audio/details/'.$audio_data->id) }}" class="action-btn primary" title="View Details">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @if(!$audio_data->license_price || $audio_data->license_price == 0)
                                        <a href="{{ URL::to('audio/download/'.$audio_data->id) }}" class="action-btn secondary" title="Download">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    @endif
                                </div>

                                @if($audio_data->license_price && $audio_data->license_price > 0)
                                <div class="premium-badge">
                                    <i class="fas fa-crown"></i>
                                </div>
                                @endif
                            </div>

                            <!-- Hidden Audio Player -->
                            <audio id="audioPlayer{{$audio_data->id}}" preload="none">
                                <source src="{{ asset('storage/' . $audio_data->audio_path) }}" type="audio/{{ $audio_data->format ?: 'mp3' }}">
                            </audio>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Photo Gallery Section -->
    @if(isset($photos_list) && $photos_list->count() > 0)
    <section class="content-section photo-gallery">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="section-header">
                        <h2 class="section-title">Photo Gallery</h2>
                        <a href="{{ URL::to('photos') }}" class="view-all-btn">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach ($photos_list as $photo_data)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
                        <div class="content-card photo-card">
                            <div class="card-image">
                                <img src="{{ $photo_data->image_url }}" 
                                     alt="{{ stripslashes($photo_data->title) }}"
                                     title="{{ stripslashes($photo_data->title) }}"
                                     class="img-fluid">
                                
                                @if($photo_data->license_price && $photo_data->license_price > 0)
                                <div class="premium-badge">
                                    <i class="fas fa-crown"></i>
                                </div>
                                @endif

                                <div class="card-overlay">
                                    <div class="photo-actions">
                                        <a href="{{ URL::to('photos/details/'.$photo_data->id) }}" class="action-btn primary" title="View Details">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @if(!$photo_data->license_price || $photo_data->license_price == 0)
                                            <a href="{{ URL::to('photos/download/'.$photo_data->id) }}" class="action-btn secondary" title="Download">
                                                <i class="fa fa-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-content">
                                <h4 class="card-title">{{Str::limit(stripslashes($photo_data->title),25)}}</h4>
                                <div class="photo-meta">
                                    @if($photo_data->category)
                                        <span class="photo-category"><i class="fas fa-tag"></i> {{$photo_data->category}}</span>
                                    @endif
                                    @if($photo_data->dimensions)
                                        <span class="photo-dimensions"><i class="fas fa-expand-arrows-alt"></i> {{$photo_data->dimensions}}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Genre Sections -->
    @foreach ($genres as $genre)
        @php
            $filteredMovies = $movies_list->where('movie_genre_id', $genre->id);
        @endphp

        @if ($filteredMovies->count() > 0)
            <section class="content-section genre-section">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="section-header">
                                <h2 class="section-title">{{ $genre->genre_name }}</h2>
                                <a href="{{ URL::to('genres/movies/' . $genre->genre_slug) }}" class="view-all-btn">
                                    View All <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="content-carousel owl-carousel" id="genre-{{ $genre->id }}-carousel">
                                @foreach ($filteredMovies->take(10) as $movies_data)
                                    <div class="content-card movie-card">
                                        <div class="card-image">
                                            @if (Auth::check())
                                                <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                                    title="{{ $movies_data->video_title }}">
                                            @else
                                                @if ($movies_data->video_access == 'Paid')
                                                    <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                                        title="{{ $movies_data->video_title }}" data-toggle="modal"
                                                        data-target="#loginAlertModal">
                                                @else
                                                    <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                                        title="{{ $movies_data->video_title }}">
                                                @endif
                                            @endif
                                            <img src="{{ URL::to('/' . $movies_data->video_image_thumb) }}"
                                                alt="{{ stripslashes($movies_data->video_title) }}"
                                                title="{{ stripslashes($movies_data->video_title) }}" 
                                                class="img-fluid">
                                            @if ($movies_data->video_access == 'Paid')
                                                <div class="premium-overlay">
                                                    <i class="fas fa-crown"></i>
                                                </div>
                                            @endif
                                            <div class="card-overlay">
                                                <div class="play-btn">
                                                    <i class="fas fa-play"></i>
                                                </div>
                                            </div>
                                            </a>
                                        </div>
                                        <div class="card-content">
                                            <h4 class="card-title">{{ Str::limit(stripslashes($movies_data->video_title), 25) }}</h4>
                                            <p class="card-genre">{{ $genre->genre_name }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endforeach



    @if (getcong('menu_movies'))
        <!-- Start Upcoming Section -->
        @if ($upcoming_movies->count() > 0)

            <!-- Start Movies Video Carousel -->
            <div class="video-carousel-area vfx-item-ptb">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="vfx-item-section">
                                <h3>{{ trans('words.upcoming_movies') }}</h3>
                            </div>
                            <div class="video-carousel owl-carousel">

                                @foreach ($upcoming_movies as $movies_data)
                                    <div class="single-video">
                                        <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                            title="{{ $movies_data->video_title }}">
                                            <div class="video-img">
                                                @if ($movies_data->video_access == 'Paid')
                                                    <div class="vid-lab-premium">
                                                        <img src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                            alt="ic-premium" title="Movies">
                                                    </div>
                                                @endif
                                                <span
                                                    class="video-item-content">{{ stripslashes($movies_data->video_title) }}</span>
                                                <img src="{{ URL::to('/' . $movies_data->video_image_thumb) }}"
                                                    alt="{{ $movies_data->video_title }}"
                                                    title="Movies-{{ $movies_data->video_title }}">
                                            </div>
                                        </a>
                                    </div>
                                @endforeach


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Latest Movies Video Carousel -->
        @endif
        <!-- End Upcoming Section -->
    @endif

    @if (getcong('menu_shows'))
        <!-- Start Upcoming Section -->
        @if ($upcoming_series->count() > 0)

            <!-- Start Latest Shows Video Section -->
            <div class="video-shows-section vfx-item-ptb">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="vfx-item-section">
                                <h3>{{ trans('words.upcoming_shows') }}</h3>

                            </div>
                            <div class="video-shows-carousel owl-carousel">
                                @foreach ($upcoming_series as $series_data)
                                    <div class="single-video">
                                        <a href="{{ URL::to('shows/details/' . $series_data->series_slug . '/' . $series_data->id) }}"
                                            title="{{ $series_data->series_name }}">
                                            <div class="video-img">
                                                @if ($series_data->series_access == 'Paid')
                                                    <div class="vid-lab-premium"><img
                                                            src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                            alt="premium" title="premium"></div>
                                                @endif
                                                <span
                                                    class="video-item-content">{{ stripslashes($series_data->series_name) }}</span>
                                                <img src="{{ URL::to('/' . $series_data->series_poster) }}"
                                                    alt="{{ $series_data->series_name }}"
                                                    title="Shows-{{ $series_data->series_name }}">
                                            </div>
                                        </a>
                                    </div>
                                @endforeach


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Latest Shows Video Section -->
        @endif
        <!-- End Upcoming Section -->
    @endif

    @foreach ($home_sections as $sections_data)

        @if (getcong('menu_movies'))
            @if ($sections_data->post_type == 'Movie')
                <!-- Start Movies Video Carousel -->
                <div class="video-carousel-area vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                        title="{{ $sections_data->section_name }}">
                                        <h3>{{ $sections_data->section_name }}</h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                            title="view-more">{{ trans('words.view_all') }}</a>
                                    </span>
                                </div>
                                <div class="video-carousel owl-carousel">

                                    @foreach (explode(',', $sections_data->movie_ids) as $movie_data)
                                        <div class="single-video">
                                            <a href="{{ URL::to('movies/details/' . App\Movies::getMoviesInfo($movie_data, 'video_slug') . '/' . App\Movies::getMoviesInfo($movie_data, 'id')) }}"
                                                title="{{ App\Movies::getMoviesInfo($movie_data, 'video_title') }}">
                                                <div class="video-img">
                                                    @if (App\Movies::getMoviesInfo($movie_data, 'video_access') == 'Paid')
                                                        <div class="vid-lab-premium">
                                                            <img src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                                alt="ic-premium" title="Movies-ic-premium">
                                                        </div>
                                                    @endif
                                                    <span
                                                        class="video-item-content">{{ stripslashes(App\Movies::getMoviesInfo($movie_data, 'video_title')) }}</span>
                                                    <img src="{{ URL::to('/' . App\Movies::getMoviesInfo($movie_data, 'video_image_thumb')) }}"
                                                        alt="{{ App\Movies::getMoviesInfo($movie_data, 'video_title') }}"
                                                        title="Movies-{{ App\Movies::getMoviesInfo($movie_data, 'video_title') }}">
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Latest Movies Video Carousel -->
            @endif
        @endif

        @if (getcong('menu_shows'))
            @if ($sections_data->post_type == 'Shows')
                <!-- Start Latest Shows Video Section -->
                <div class="video-shows-section vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                        title="{{ $sections_data->section_name }}">
                                        <h3>{{ $sections_data->section_name }}</h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                            title="view-more">{{ trans('words.view_all') }}</a>
                                    </span>
                                </div>
                                <div class="video-shows-carousel owl-carousel">
                                    @foreach (explode(',', $sections_data->show_ids) as $show_data)
                                        <div class="single-video">
                                            <a href="{{ URL::to('shows/details/' . App\Series::getSeriesInfo($show_data, 'series_slug') . '/' . $show_data) }}"
                                                title="{{ App\Series::getSeriesInfo($show_data, 'series_name') }}">
                                                <div class="video-img">
                                                    @if (App\Series::getSeriesInfo($show_data, 'series_access') == 'Paid')
                                                        <div class="vid-lab-premium"><img
                                                                src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                                alt="ic-premium" title="Shows-ic-premium"></div>
                                                    @endif
                                                    <span
                                                        class="video-item-content">{{ stripslashes(App\Series::getSeriesInfo($show_data, 'series_name')) }}</span>
                                                    <img src="{{ URL::to('/' . App\Series::getSeriesInfo($show_data, 'series_poster')) }}"
                                                        alt="{{ App\Series::getSeriesInfo($show_data, 'series_name') }}"
                                                        title="Shows-{{ App\Series::getSeriesInfo($show_data, 'series_name') }}">
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Latest Shows Video Section -->
            @endif
        @endif


        @if (getcong('menu_sports'))
            @if ($sections_data->post_type == 'Sports')
                <!-- Start Sports Video Section -->
                <div class="video-shows-section sport-video-block vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                        title="{{ $sections_data->section_name }}">
                                        <h3>{{ $sections_data->section_name }}</h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                            title="view-more">{{ trans('words.view_all') }}</a>
                                    </span>
                                </div>

                                <div class="tv-season-video-carousel owl-carousel">
                                    @foreach (explode(',', $sections_data->sport_ids) as $sport_data)
                                        <div class="single-video">
                                            <a href="{{ URL::to('sports/details/' . App\Sports::getSportsInfo($sport_data, 'video_slug') . '/' . $sport_data) }}"
                                                title="{{ App\Sports::getSportsInfo($sport_data, 'video_title') }}">
                                                <div class="video-img">
                                                    @if (App\Sports::getSportsInfo($sport_data, 'video_access') == 'Paid')
                                                        <div class="vid-lab-premium"><img
                                                                src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                                alt="ic-premium" title="Sports-ic-premium"></div>
                                                    @endif
                                                    <span
                                                        class="video-item-content">{{ App\Sports::getSportsInfo($sport_data, 'video_title') }}</span>
                                                    <img src="{{ URL::to('/' . App\Sports::getSportsInfo($sport_data, 'video_image')) }}"
                                                        alt="{{ App\Sports::getSportsInfo($sport_data, 'video_title') }}"
                                                        title="Sports-{{ App\Sports::getSportsInfo($sport_data, 'video_title') }}" />
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Sports Section -->
            @endif
        @endif


        @if (getcong('menu_livetv'))
            @if ($sections_data->post_type == 'LiveTV')
                <!-- Start Live TV Video Section -->
                <div class="video-shows-section live-tv-video-block vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                        title="{{ $sections_data->section_name }}">
                                        <h3>{{ $sections_data->section_name }}</h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                            title="view-more">{{ trans('words.view_all') }}</a>
                                    </span>
                                </div>

                                <div class="tv-season-video-carousel owl-carousel">
                                    @foreach (explode(',', $sections_data->tv_ids) as $tv_data)
                                        <div class="single-video">
                                            <a href="{{ URL::to('livetv/details/' . App\LiveTV::getLiveTvInfo($tv_data, 'channel_slug') . '/' . $tv_data) }}"
                                                title="{{ App\LiveTV::getLiveTvInfo($tv_data, 'channel_name') }}">
                                                <div class="video-img">
                                                    @if (App\LiveTV::getLiveTvInfo($tv_data, 'channel_access') == 'Paid')
                                                        <div class="vid-lab-premium"><img
                                                                src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                                alt="ic-premium" title="LiveTV-ic-premium"></div>
                                                    @endif
                                                    <span
                                                        class="video-item-content">{{ App\LiveTV::getLiveTvInfo($tv_data, 'channel_name') }}</span>
                                                    <img src="{{ URL::to('/' . App\LiveTV::getLiveTvInfo($tv_data, 'channel_thumb')) }}"
                                                        alt="{{ App\LiveTV::getLiveTvInfo($tv_data, 'channel_name') }}"
                                                        title="LiveTV-{{ App\LiveTV::getLiveTvInfo($tv_data, 'channel_name') }}" />
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Live TV Section -->
            @endif
        @endif

    @endforeach

    <!-- Banner -->
    @if (get_web_banner('home_bottom') != '')
        <div class="banner-section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        {!! stripslashes(get_web_banner('home_bottom')) !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize Featured Movies Carousel
    $('#featured-movies-carousel').owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 3
            },
            992: {
                items: 4
            },
            1200: {
                items: 5
            },
            1400: {
                items: 6
            }
        }
    });

    // Initialize other carousels if they exist
    $('.content-carousel').each(function() {
        if (!$(this).hasClass('owl-loaded')) {
            $(this).owlCarousel({
                loop: false,
                margin: 20,
                nav: true,
                dots: false,
                autoplay: false,
                responsive: {
                    0: {
                        items: 1
                    },
                    576: {
                        items: 2
                    },
                    768: {
                        items: 3
                    },
                    992: {
                        items: 4
                    },
                    1200: {
                        items: 5
                    }
                }
            });
        }
    });

    // Audio functionality
    window.toggleAudio = function(audioId) {
        const audio = document.getElementById('audioPlayer' + audioId);
        const playIcon = document.getElementById('playIcon' + audioId);
        const spectrum = document.getElementById('spectrum' + audioId);
        const audioCard = audio.closest('.audio-card');
        
        if (audio.paused) {
            // Pause all other audio players
            document.querySelectorAll('audio').forEach(function(otherAudio) {
                if (!otherAudio.paused && otherAudio !== audio) {
                    otherAudio.pause();
                    const otherId = otherAudio.id.replace('audioPlayer', '');
                    const otherCard = otherAudio.closest('.audio-card');
                    document.getElementById('playIcon' + otherId).className = 'fa fa-play';
                    document.getElementById('spectrum' + otherId).style.opacity = '0.6';
                    if (otherCard) otherCard.classList.remove('playing');
                }
            });
            
            audio.play();
            playIcon.className = 'fa fa-pause';
            spectrum.style.opacity = '1';
            if (audioCard) audioCard.classList.add('playing');
        } else {
            audio.pause();
            playIcon.className = 'fa fa-play';
            spectrum.style.opacity = '0.6';
            if (audioCard) audioCard.classList.remove('playing');
        }
        
        // Handle audio end event
        audio.addEventListener('ended', function() {
            playIcon.className = 'fa fa-play';
            spectrum.style.opacity = '0.6';
            if (audioCard) audioCard.classList.remove('playing');
        });
        
        // Handle pause event (for better sync)
        audio.addEventListener('pause', function() {
            playIcon.className = 'fa fa-play';
            spectrum.style.opacity = '0.6';
            if (audioCard) audioCard.classList.remove('playing');
        });
        
        // Handle play event (for better sync)
        audio.addEventListener('play', function() {
            playIcon.className = 'fa fa-pause';
            spectrum.style.opacity = '1';
            if (audioCard) audioCard.classList.add('playing');
        });
    };

    // Smooth scroll for hero buttons
    $('.hero-actions a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $($(this).attr('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });

    // Add loading animation to cards
    $('.content-card img').on('load', function() {
        $(this).addClass('loaded');
    });

    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});

// Add scroll animations
$(window).scroll(function() {
    $('.content-section').each(function() {
        const elementTop = $(this).offset().top;
        const elementBottom = elementTop + $(this).outerHeight();
        const viewportTop = $(window).scrollTop();
        const viewportBottom = viewportTop + $(window).height();
        
        if (elementBottom > viewportTop && elementTop < viewportBottom) {
            $(this).addClass('animate-in');
        }
    });
});
</script>
@endsection
