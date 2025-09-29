<?php $__env->startSection('head_title', trans('words.movies_text') . ' | ' . getcong('site_name')); ?>

<?php $__env->startSection('head_url', Request::url()); ?>

<?php $__env->startSection('content'); ?>

    <?php if(count($slider) != 0): ?>
        <?php echo $__env->make('pages.movies.slider', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    <!-- Banner -->
    <?php if(get_web_banner('list_top') != ''): ?>
        <div class="vid-item-ptb banner_ads_item">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo stripslashes(get_web_banner('list_top')); ?>

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <link rel="stylesheet" href="<?php echo e(URL::asset('site_assets/css/nice-select.css')); ?>">

    <!-- Start View All Movies -->
    <div class="view-all-video-area view-movie-list-item vfx-item-ptb">
        <div class="container-fluid">
            <div class="filter-list-area">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="page-title-item"><?php echo e(trans('words.movies_text')); ?></div>
                        <div class="custom_select_filter">
                            <select class="selectpicker show-menu-arrow" id="filter_by_lang">
                                <option value=""><?php echo e(trans('words.languages')); ?></option>
                                <?php $__currentLoopData = \App\Language::where('status', '1')->orderBy('id')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e(URL::to('movies/?lang_id=' . $lang_data->id)); ?>"
                                        <?php if(isset($_GET['lang_id']) && $_GET['lang_id'] == $lang_data->id): ?> selected <?php endif; ?>>
                                        <?php echo e(stripslashes($lang_data->language_name)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>
                        </div>
                        <div class="custom_select_filter">
                            <select class="selectpicker show-menu-arrow" id="filter_by_genre">
                                <option value=""><?php echo e(trans('words.genres_text')); ?></option>
                                <?php $__currentLoopData = \App\Genres::where('status', '1')->orderBy('id')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genres_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e(URL::to('movies/?genre_id=' . $genres_data->id)); ?>"
                                        <?php if(isset($_GET['genre_id']) && $_GET['genre_id'] == $genres_data->id): ?> selected <?php endif; ?>>
                                        <?php echo e(stripslashes($genres_data->genre_name)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="custom_select_filter">
                            <select class="selectpicker show-menu-arrow" id="filter_list" required>
                                <option value="?filter=new" <?php if(isset($_GET['filter']) && $_GET['filter'] == 'new'): ?> selected <?php endif; ?>>
                                    <?php echo e(trans('words.newest')); ?></option>
                                <option value="?filter=old" <?php if(isset($_GET['filter']) && $_GET['filter'] == 'old'): ?> selected <?php endif; ?>>
                                    <?php echo e(trans('words.oldest')); ?></option>
                                <option value="?filter=alpha" <?php if(isset($_GET['filter']) && $_GET['filter'] == 'alpha'): ?> selected <?php endif; ?>>
                                    <?php echo e(trans('words.a_to_z')); ?></option>
                                <option value="?filter=rand" <?php if(isset($_GET['filter']) && $_GET['filter'] == 'rand'): ?> selected <?php endif; ?>>
                                    <?php echo e(trans('words.random')); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php $__currentLoopData = $movies_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movies_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12 col-6">
                        <div class="single-video">
                            <?php if(Auth::check()): ?>
                                <a href="<?php echo e(URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id)); ?>"
                                    title="<?php echo e($movies_data->video_title); ?>">
                                <?php else: ?>
                                    <?php if($movies_data->video_access == 'Paid'): ?>
                                        <a href="<?php echo e(URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id)); ?>"
                                            title="<?php echo e($movies_data->video_title); ?>" data-toggle="modal"
                                            data-target="#loginAlertModal" title="<?php echo e($movies_data->video_title); ?>">
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

                                <span
                                    class="video-item-content"><?php echo e(Str::limit(stripslashes($movies_data->video_title), 20)); ?></span>
                                <img src="<?php echo e(URL::to('/' . $movies_data->video_image_thumb)); ?>"
                                    alt="<?php echo e(stripslashes($movies_data->video_title)); ?>"
                                    title="<?php echo e(stripslashes($movies_data->video_title)); ?>">
                            </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="col-xs-12">
                <?php echo $__env->make('_particles.pagination', ['paginator' => $movies_list], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>
    <!-- End View All Movies -->

    <!-- Banner -->
    <?php if(get_web_banner('list_bottom') != ''): ?>
        <div class="vid-item-ptb banner_ads_item pb-3">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo stripslashes(get_web_banner('list_bottom')); ?>

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/pages/movies/list.blade.php ENDPATH**/ ?>