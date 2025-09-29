<?php $__env->startSection('head_title', getcong('site_name')); ?>

<?php $__env->startSection('head_url', Request::url()); ?>

<?php $__env->startSection('content'); ?>
    <div class="video-shows-section vfx-item-ptb">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <?php echo $__env->make('pages.home.slider', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>





    <!-- Banner -->
    <?php if(get_web_banner('home_top') != ''): ?>
        <div class="vid-item-ptb banner_ads_item pb-1" style="padding: 15px 0;">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo stripslashes(get_web_banner('home_top')); ?>

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(Auth::check() && $recently_watched->count() > 0): ?>
        <!-- Start Recently Watched Video Section -->
        <div class="video-shows-section vfx-item-ptb">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="vfx-item-section">
                            <h3><?php echo e(trans('words.recently_watched')); ?></h3>
                        </div>
                        <div class="recently-watched-video-carousel owl-carousel">
                            <?php $__currentLoopData = $recently_watched; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $watched_videos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="single-video">
                                    <?php if($watched_videos->video_type == 'Movies'): ?>
                                        <?php
                                            $info = recently_watched_info(
                                                $watched_videos->video_type,
                                                $watched_videos->video_id,
                                            );
                                        ?>
                                        <?php if($info): ?>
                                            <a href="<?php echo e(URL::to('movies/details/' . $info->video_slug . '/' . $info->id)); ?>"
                                                title="<?php echo e($info->video_title); ?>">
                                                <div class="video-img">
                                                    <span class="video-item-content"><?php echo e($info->video_title); ?></span>
                                                    <img src="<?php echo e(URL::to('/' . $info->video_image)); ?>"
                                                        alt="<?php echo e($info->video_title); ?>"
                                                        title="Movies-<?php echo e($info->video_title); ?>">
                                                </div>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if($watched_videos->video_type == 'Episodes'): ?>
                                        <?php
                                            $episode_series_id = \App\Episodes::getEpisodesInfo(
                                                $watched_videos->video_id,
                                                'episode_series_id',
                                            );
                                            $info = recently_watched_info(
                                                $watched_videos->video_type,
                                                $watched_videos->video_id,
                                            );
                                        ?>
                                        <?php if($info): ?>
                                            <div class="single-video">
                                                <a href="<?php echo e(URL::to('shows/' . \App\Series::getSeriesInfo($episode_series_id, 'series_slug') . '/' . $info->video_slug . '/' . $info->id)); ?>"
                                                    title="<?php echo e($info->video_title); ?>">
                                                    <div class="video-img">
                                                        <span class="video-item-content"><?php echo e($info->video_title); ?></span>
                                                        <img src="<?php echo e(URL::to('/' . $info->video_image)); ?>"
                                                            alt="<?php echo e($info->video_title); ?>"
                                                            title="Episodes-<?php echo e($info->video_title); ?>">
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if($watched_videos->video_type == 'Sports'): ?>
                                        <?php
                                            $info = recently_watched_info(
                                                $watched_videos->video_type,
                                                $watched_videos->video_id,
                                            );
                                        ?>
                                        <?php if($info): ?>
                                            <div class="single-video">
                                                <a href="<?php echo e(URL::to('sports/details/' . $info->video_slug . '/' . $info->id)); ?>"
                                                    title="<?php echo e($info->video_title); ?>">
                                                    <div class="video-img">
                                                        <span class="video-item-content"><?php echo e($info->video_title); ?></span>
                                                        <img src="<?php echo e(URL::to('/' . $info->video_image)); ?>"
                                                            alt="<?php echo e($info->video_title); ?>"
                                                            title="Sports-<?php echo e($info->video_title); ?>">
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if($watched_videos->video_type == 'LiveTV'): ?>
                                        <?php
                                            $info = recently_watched_info(
                                                $watched_videos->video_type,
                                                $watched_videos->video_id,
                                            );
                                        ?>
                                        <?php if($info): ?>
                                            <div class="single-video">
                                                <a href="<?php echo e(URL::to('livetv/details/' . $info->channel_slug . '/' . $info->id)); ?>"
                                                    title="<?php echo e($info->channel_name); ?>">
                                                    <div class="video-img">
                                                        <span class="video-item-content"><?php echo e($info->channel_name); ?></span>
                                                        <img src="<?php echo e(URL::to('/' . $info->channel_thumb)); ?>"
                                                            alt="<?php echo e($info->channel_name); ?>"
                                                            title="LiveTV-<?php echo e($info->channel_name); ?>">
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Recently Watched Video Section -->
    <?php endif; ?>
    <div class="video-shows-section vfx-item-ptb">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="vfx-item-section">
                        <h3>Movies List</h3>
                    </div>
                    <div class="recently-watched-video-carousel owl-carousel">
                        <?php $__currentLoopData = $movies_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movies_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="single-video">
                                <?php if(Auth::check()): ?>
                                    <a href="<?php echo e(URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id)); ?>"
                                        title="<?php echo e($movies_data->video_title); ?>">
                                    <?php else: ?>
                                        <?php if($movies_data->video_access == 'Paid'): ?>
                                            <a href="<?php echo e(URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id)); ?>"
                                                title="<?php echo e($movies_data->video_title); ?>" data-toggle="modal"
                                                data-target="#loginAlertModal">
                                            <?php else: ?>
                                                <a href="<?php echo e(URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id)); ?>"
                                                    title="<?php echo e($movies_data->video_title); ?>">
                                        <?php endif; ?>
                                <?php endif; ?>
                                <div class="video-img">
                                    <?php if($movies_data->video_access == 'Paid'): ?>
                                        <div class="vid-lab-premium">
                                            <img src="<?php echo e(URL::asset('site_assets/images/ic-premium.png')); ?>" alt="premium"
                                                title="premium">
                                        </div>
                                    <?php endif; ?>
                                    <img src="<?php echo e(URL::to('/' . $movies_data->video_image_thumb)); ?>"
                                        alt="<?php echo e(stripslashes($movies_data->video_title)); ?>"
                                        title="<?php echo e(stripslashes($movies_data->video_title)); ?>" class="img-fluid fixed-img">
                                    <div style="background:rgba(255,0,0,0.6);color:white;padding:3px;">
                                        <span><?php echo e(Str::limit(stripslashes($movies_data->video_title), 20)); ?></span>
                                        <br>
                                        
                                        <strong>Genre:</strong>
                                        <?php echo e(App\Genres::where('id', $movies_data->movie_genre_id)->first()->genre_name ?? 'Not specified'); ?>

                                    </div>

                                </div>
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__currentLoopData = $genres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $filteredMovies = $movies_list->where('movie_genre_id', $genre->id);
        ?>

        <?php if($filteredMovies->count() > 0): ?>
            <div class="video-shows-section vfx-item-ptb">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="vfx-item-section">
                                <h3><?php echo e($genre->genre_name); ?></h3>
                            </div>
                            <div class="recently-watched-video-carousel owl-carousel">
                                <?php $__currentLoopData = $filteredMovies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movies_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="single-video">
                                        <?php if(Auth::check()): ?>
                                            <a href="<?php echo e(URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id)); ?>"
                                                title="<?php echo e($movies_data->video_title); ?>">
                                            <?php else: ?>
                                                <?php if($movies_data->video_access == 'Paid'): ?>
                                                    <a href="<?php echo e(URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id)); ?>"
                                                        title="<?php echo e($movies_data->video_title); ?>" data-toggle="modal"
                                                        data-target="#loginAlertModal">
                                                    <?php else: ?>
                                                        <a href="<?php echo e(URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id)); ?>"
                                                            title="<?php echo e($movies_data->video_title); ?>">
                                                <?php endif; ?>
                                        <?php endif; ?>
                                        <div class="video-img">
                                            <?php if($movies_data->video_access == 'Paid'): ?>
                                                <div class="vid-lab-premium">
                                                    <img src="<?php echo e(URL::asset('site_assets/images/ic-premium.png')); ?>"
                                                        alt="premium" title="premium">
                                                </div>
                                            <?php endif; ?>
                                            <img src="<?php echo e(URL::to('/' . $movies_data->video_image_thumb)); ?>"
                                                alt="<?php echo e(stripslashes($movies_data->video_title)); ?>"
                                                title="<?php echo e(stripslashes($movies_data->video_title)); ?>"
                                                class="img-fluid fixed-img">
                                            <div style="background:rgba(255,0,0,0.6);color:white;padding:3px;">
                                                <span><?php echo e(Str::limit(stripslashes($movies_data->video_title), 20)); ?></span>
                                                <br>
                                                
                                                <strong>Genre:</strong>
                                                <?php echo e(App\Genres::where('id', $movies_data->movie_genre_id)->first()->genre_name ?? 'Not specified'); ?>

                                            </div>
                                        </div>
                                        </a>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="video-shows-section vfx-item-ptb">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="vfx-item-section">
                                <h3><?php echo e($genre->genre_name); ?></h3>
                            </div>
                            <p>No videos available for this genre.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



    </div>

    <?php if(getcong('menu_movies')): ?>
        <!-- Start Upcoming Section -->
        <?php if($upcoming_movies->count() > 0): ?>

            <!-- Start Movies Video Carousel -->
            <div class="video-carousel-area vfx-item-ptb">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="vfx-item-section">
                                <h3><?php echo e(trans('words.upcoming_movies')); ?></h3>
                            </div>
                            <div class="video-carousel owl-carousel">

                                <?php $__currentLoopData = $upcoming_movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movies_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="single-video">
                                        <a href="<?php echo e(URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id)); ?>"
                                            title="<?php echo e($movies_data->video_title); ?>">
                                            <div class="video-img">
                                                <?php if($movies_data->video_access == 'Paid'): ?>
                                                    <div class="vid-lab-premium">
                                                        <img src="<?php echo e(URL::asset('site_assets/images/ic-premium.png')); ?>"
                                                            alt="ic-premium" title="Movies">
                                                    </div>
                                                <?php endif; ?>
                                                <span
                                                    class="video-item-content"><?php echo e(stripslashes($movies_data->video_title)); ?></span>
                                                <img src="<?php echo e(URL::to('/' . $movies_data->video_image_thumb)); ?>"
                                                    alt="<?php echo e($movies_data->video_title); ?>"
                                                    title="Movies-<?php echo e($movies_data->video_title); ?>">
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Latest Movies Video Carousel -->
        <?php endif; ?>
        <!-- End Upcoming Section -->
    <?php endif; ?>

    <?php if(getcong('menu_shows')): ?>
        <!-- Start Upcoming Section -->
        <?php if($upcoming_series->count() > 0): ?>

            <!-- Start Latest Shows Video Section -->
            <div class="video-shows-section vfx-item-ptb">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="vfx-item-section">
                                <h3><?php echo e(trans('words.upcoming_shows')); ?></h3>

                            </div>
                            <div class="video-shows-carousel owl-carousel">
                                <?php $__currentLoopData = $upcoming_series; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $series_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="single-video">
                                        <a href="<?php echo e(URL::to('shows/details/' . $series_data->series_slug . '/' . $series_data->id)); ?>"
                                            title="<?php echo e($series_data->series_name); ?>">
                                            <div class="video-img">
                                                <?php if($series_data->series_access == 'Paid'): ?>
                                                    <div class="vid-lab-premium"><img
                                                            src="<?php echo e(URL::asset('site_assets/images/ic-premium.png')); ?>"
                                                            alt="premium" title="premium"></div>
                                                <?php endif; ?>
                                                <span
                                                    class="video-item-content"><?php echo e(stripslashes($series_data->series_name)); ?></span>
                                                <img src="<?php echo e(URL::to('/' . $series_data->series_poster)); ?>"
                                                    alt="<?php echo e($series_data->series_name); ?>"
                                                    title="Shows-<?php echo e($series_data->series_name); ?>">
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Latest Shows Video Section -->
        <?php endif; ?>
        <!-- End Upcoming Section -->
    <?php endif; ?>

    <?php $__currentLoopData = $home_sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sections_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php if(getcong('menu_movies')): ?>
            <?php if($sections_data->post_type == 'Movie'): ?>
                <!-- Start Movies Video Carousel -->
                <div class="video-carousel-area vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="<?php echo e(URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id)); ?>"
                                        title="<?php echo e($sections_data->section_name); ?>">
                                        <h3><?php echo e($sections_data->section_name); ?></h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="<?php echo e(URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id)); ?>"
                                            title="view-more"><?php echo e(trans('words.view_all')); ?></a>
                                    </span>
                                </div>
                                <div class="video-carousel owl-carousel">

                                    <?php $__currentLoopData = explode(',', $sections_data->movie_ids); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="single-video">
                                            <a href="<?php echo e(URL::to('movies/details/' . App\Movies::getMoviesInfo($movie_data, 'video_slug') . '/' . App\Movies::getMoviesInfo($movie_data, 'id'))); ?>"
                                                title="<?php echo e(App\Movies::getMoviesInfo($movie_data, 'video_title')); ?>">
                                                <div class="video-img">
                                                    <?php if(App\Movies::getMoviesInfo($movie_data, 'video_access') == 'Paid'): ?>
                                                        <div class="vid-lab-premium">
                                                            <img src="<?php echo e(URL::asset('site_assets/images/ic-premium.png')); ?>"
                                                                alt="ic-premium" title="Movies-ic-premium">
                                                        </div>
                                                    <?php endif; ?>
                                                    <span
                                                        class="video-item-content"><?php echo e(stripslashes(App\Movies::getMoviesInfo($movie_data, 'video_title'))); ?></span>
                                                    <img src="<?php echo e(URL::to('/' . App\Movies::getMoviesInfo($movie_data, 'video_image_thumb'))); ?>"
                                                        alt="<?php echo e(App\Movies::getMoviesInfo($movie_data, 'video_title')); ?>"
                                                        title="Movies-<?php echo e(App\Movies::getMoviesInfo($movie_data, 'video_title')); ?>">
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Latest Movies Video Carousel -->
            <?php endif; ?>
        <?php endif; ?>

        <?php if(getcong('menu_shows')): ?>
            <?php if($sections_data->post_type == 'Shows'): ?>
                <!-- Start Latest Shows Video Section -->
                <div class="video-shows-section vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="<?php echo e(URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id)); ?>"
                                        title="<?php echo e($sections_data->section_name); ?>">
                                        <h3><?php echo e($sections_data->section_name); ?></h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="<?php echo e(URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id)); ?>"
                                            title="view-more"><?php echo e(trans('words.view_all')); ?></a>
                                    </span>
                                </div>
                                <div class="video-shows-carousel owl-carousel">
                                    <?php $__currentLoopData = explode(',', $sections_data->show_ids); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $show_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="single-video">
                                            <a href="<?php echo e(URL::to('shows/details/' . App\Series::getSeriesInfo($show_data, 'series_slug') . '/' . $show_data)); ?>"
                                                title="<?php echo e(App\Series::getSeriesInfo($show_data, 'series_name')); ?>">
                                                <div class="video-img">
                                                    <?php if(App\Series::getSeriesInfo($show_data, 'series_access') == 'Paid'): ?>
                                                        <div class="vid-lab-premium"><img
                                                                src="<?php echo e(URL::asset('site_assets/images/ic-premium.png')); ?>"
                                                                alt="ic-premium" title="Shows-ic-premium"></div>
                                                    <?php endif; ?>
                                                    <span
                                                        class="video-item-content"><?php echo e(stripslashes(App\Series::getSeriesInfo($show_data, 'series_name'))); ?></span>
                                                    <img src="<?php echo e(URL::to('/' . App\Series::getSeriesInfo($show_data, 'series_poster'))); ?>"
                                                        alt="<?php echo e(App\Series::getSeriesInfo($show_data, 'series_name')); ?>"
                                                        title="Shows-<?php echo e(App\Series::getSeriesInfo($show_data, 'series_name')); ?>">
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Latest Shows Video Section -->
            <?php endif; ?>
        <?php endif; ?>


        <?php if(getcong('menu_sports')): ?>
            <?php if($sections_data->post_type == 'Sports'): ?>
                <!-- Start Sports Video Section -->
                <div class="video-shows-section sport-video-block vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="<?php echo e(URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id)); ?>"
                                        title="<?php echo e($sections_data->section_name); ?>">
                                        <h3><?php echo e($sections_data->section_name); ?></h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="<?php echo e(URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id)); ?>"
                                            title="view-more"><?php echo e(trans('words.view_all')); ?></a>
                                    </span>
                                </div>

                                <div class="tv-season-video-carousel owl-carousel">
                                    <?php $__currentLoopData = explode(',', $sections_data->sport_ids); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sport_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="single-video">
                                            <a href="<?php echo e(URL::to('sports/details/' . App\Sports::getSportsInfo($sport_data, 'video_slug') . '/' . $sport_data)); ?>"
                                                title="<?php echo e(App\Sports::getSportsInfo($sport_data, 'video_title')); ?>">
                                                <div class="video-img">
                                                    <?php if(App\Sports::getSportsInfo($sport_data, 'video_access') == 'Paid'): ?>
                                                        <div class="vid-lab-premium"><img
                                                                src="<?php echo e(URL::asset('site_assets/images/ic-premium.png')); ?>"
                                                                alt="ic-premium" title="Sports-ic-premium"></div>
                                                    <?php endif; ?>
                                                    <span
                                                        class="video-item-content"><?php echo e(App\Sports::getSportsInfo($sport_data, 'video_title')); ?></span>
                                                    <img src="<?php echo e(URL::to('/' . App\Sports::getSportsInfo($sport_data, 'video_image'))); ?>"
                                                        alt="<?php echo e(App\Sports::getSportsInfo($sport_data, 'video_title')); ?>"
                                                        title="Sports-<?php echo e(App\Sports::getSportsInfo($sport_data, 'video_title')); ?>" />
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Sports Section -->
            <?php endif; ?>
        <?php endif; ?>


        <?php if(getcong('menu_livetv')): ?>
            <?php if($sections_data->post_type == 'LiveTV'): ?>
                <!-- Start Live TV Video Section -->
                <div class="video-shows-section live-tv-video-block vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="<?php echo e(URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id)); ?>"
                                        title="<?php echo e($sections_data->section_name); ?>">
                                        <h3><?php echo e($sections_data->section_name); ?></h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="<?php echo e(URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id)); ?>"
                                            title="view-more"><?php echo e(trans('words.view_all')); ?></a>
                                    </span>
                                </div>

                                <div class="tv-season-video-carousel owl-carousel">
                                    <?php $__currentLoopData = explode(',', $sections_data->tv_ids); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tv_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="single-video">
                                            <a href="<?php echo e(URL::to('livetv/details/' . App\LiveTV::getLiveTvInfo($tv_data, 'channel_slug') . '/' . $tv_data)); ?>"
                                                title="<?php echo e(App\LiveTV::getLiveTvInfo($tv_data, 'channel_name')); ?>">
                                                <div class="video-img">
                                                    <?php if(App\LiveTV::getLiveTvInfo($tv_data, 'channel_access') == 'Paid'): ?>
                                                        <div class="vid-lab-premium"><img
                                                                src="<?php echo e(URL::asset('site_assets/images/ic-premium.png')); ?>"
                                                                alt="ic-premium" title="LiveTV-ic-premium"></div>
                                                    <?php endif; ?>
                                                    <span
                                                        class="video-item-content"><?php echo e(App\LiveTV::getLiveTvInfo($tv_data, 'channel_name')); ?></span>
                                                    <img src="<?php echo e(URL::to('/' . App\LiveTV::getLiveTvInfo($tv_data, 'channel_thumb'))); ?>"
                                                        alt="<?php echo e(App\LiveTV::getLiveTvInfo($tv_data, 'channel_name')); ?>"
                                                        title="LiveTV-<?php echo e(App\LiveTV::getLiveTvInfo($tv_data, 'channel_name')); ?>" />
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Live TV Section -->
            <?php endif; ?>
        <?php endif; ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <!-- Banner -->
    <!--<?php if(get_web_banner('home_bottom') != ''): ?>
    -->
    <!--    <div class="vid-item-ptb banner_ads_item pb-1" style="padding: 15px 0;">-->
    <!--        <div class="container-fluid">-->
    <!--            <div class="row">-->
    <!--                <div class="col-md-12">-->
    <!--                    <?php echo stripslashes(get_web_banner('home_bottom')); ?>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--
    <?php endif; ?>-->


<?php $__env->stopSection(); ?>

<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/pages/index.blade.php ENDPATH**/ ?>