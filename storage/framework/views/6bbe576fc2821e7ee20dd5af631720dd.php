<?php if($movies_info->seo_title): ?>
  <?php $__env->startSection('head_title', stripslashes($movies_info->seo_title).' | '.getcong('site_name')); ?>
<?php else: ?>
  <?php $__env->startSection('head_title', stripslashes($movies_info->video_title).' | '.getcong('site_name')); ?>
<?php endif; ?>

<?php if($movies_info->seo_description): ?>
  <?php $__env->startSection('head_description', stripslashes($movies_info->seo_description)); ?>
<?php else: ?>
  <?php $__env->startSection('head_description', Str::limit(stripslashes($movies_info->video_description),160)); ?>
<?php endif; ?>

<?php if($movies_info->seo_keyword): ?>
  <?php $__env->startSection('head_keywords', stripslashes($movies_info->seo_keyword)); ?>
<?php endif; ?>


<?php $__env->startSection('head_image', URL::to('/'.$movies_info->video_image)); ?>

<?php $__env->startSection('head_url', Request::url()); ?>

<?php $__env->startSection('content'); ?>


<!-- Banner -->
<?php if(get_web_banner('details_top')!=""): ?>
<div class="vid-item-ptb banner_ads_item">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<?php echo stripslashes(get_web_banner('details_top')); ?>

			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if($movies_info->trailer_url!=""): ?>

<link rel="stylesheet" type="text/css" href="<?php echo e(URL::asset('site_assets/player/content/global.css')); ?>">
<script type="text/javascript" src="<?php echo e(URL::asset('site_assets/player/java/' . $FWDEVPlayer)); ?>"></script>

    <?php echo $__env->make("pages.movies.player.trailer", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php endif; ?>


<!-- Start Page Content Area -->
<div class="page-content-area vfx-item-ptb pt-3">
  <div class="container-fluid">
    <div class="row">
    <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12 mb-4">
    <div class="detail-poster-area">

     <div class="play-icon-item">
		<a class="icon" href="<?php echo e(URL::to('movies/watch/'.$movies_info->video_slug.'/'.$movies_info->id)); ?>" title="play">
			<i class="icon fa fa-play"></i><span class="ripple"></span>
		</a>
	 </div>

      <div class="video-post-date">
        <span class="video-posts-author"><i class="fa fa-eye"></i><?php echo e(number_format_short($movies_info->views)); ?> <?php echo e(trans('words.video_views')); ?></span>

        <?php if($movies_info->release_date): ?>
          <span class="video-posts-author"><i class="fa fa-calendar-alt"></i><?php echo e(isset($movies_info->release_date) ? date('M d Y',$movies_info->release_date) : null); ?></span>
        <?php endif; ?>

        <?php if($movies_info->duration): ?>
         <span class="video-posts-author"><i class="fa fa-clock"></i><?php echo e($movies_info->duration); ?></span>
        <?php endif; ?>

        <?php if($movies_info->imdb_rating): ?>
         <span class="video-imdb-view"><img src="<?php echo e(URL::to('site_assets/images/imdb-logo.png')); ?>" alt="imdb-logo" title="imdb-logo" /><?php echo e($movies_info->imdb_rating); ?></span>
        <?php endif; ?>

        <div class="video-watch-share-item">

          <?php if(Auth::check()): ?>

             <?php if(check_watchlist(Auth::user()->id,$movies_info->id,'Movies')): ?>
              <span class="btn-watchlist"><a href="<?php echo e(URL::to('watchlist/remove')); ?>?post_id=<?php echo e($movies_info->id); ?>&post_type=Movies" title="watchlist"><i class="fa fa-check"></i><?php echo e(trans('words.remove_from_watchlist')); ?></a></span>
             <?php else: ?>
              <span class="btn-watchlist"><a href="<?php echo e(URL::to('watchlist/add')); ?>?post_id=<?php echo e($movies_info->id); ?>&post_type=Movies" title="watchlist"><i class="fa fa-plus"></i><?php echo e(trans('words.add_to_watchlist')); ?></a></span>
             <?php endif; ?>
          <?php else: ?>
             <span class="btn-watchlist"><a href="<?php echo e(URL::to('watchlist/add')); ?>?post_id=<?php echo e($movies_info->id); ?>&post_type=Movies" title="watchlist"><i class="fa fa-plus"></i><?php echo e(trans('words.add_to_watchlist')); ?></a></span>
          <?php endif; ?>

          <span class="btn-share"><a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#social-media"><i class="fas fa-share-alt mr-5"></i><?php echo e(trans('words.share_text')); ?></a></span>

        </div>
      </div>

      <!-- Start Social Media Icon Popup -->
          <div id="social-media" class="modal fade centered-modal" tabindex="-1" role="dialog" aria-labelledby="myModal" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content bg-dark-2 text-light">
              <div class="modal-header">
              <h4 class="modal-title text-white"><?php echo e(trans('words.share_text')); ?></h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-4">
              <div class="social-media-modal">
                <ul>
                  <li><a title="Sharing" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(share_url_get('movies',$movies_info->video_slug,$movies_info->id)); ?>" class="facebook-icon" target="_blank"><i class="ion-social-facebook"></i></a></li>
                  <li><a title="Sharing" href="https://twitter.com/intent/tweet?text=<?php echo e($movies_info->video_title); ?>&amp;url=<?php echo e(share_url_get('movies',$movies_info->video_slug,$movies_info->id)); ?>" class="twitter-icon" target="_blank"><i class="ion-social-twitter"></i></a></li>
                  <li><a title="Sharing" href="https://www.instagram.com/?url=<?php echo e(share_url_get('movies',$movies_info->video_slug,$movies_info->id)); ?>" class="instagram-icon" target="_blank"><i class="ion-social-instagram"></i></a></li>
                   <li><a title="Sharing" href="https://wa.me?text=<?php echo e(share_url_get('movies',$movies_info->video_slug,$movies_info->id)); ?>" class="whatsapp-icon" target="_blank"><i class="ion-social-whatsapp"></i></a></li>
                </ul>
              </div>
              </div>
            </div>
            </div>
          </div>
          <!-- End Social Media Icon Popup -->

      <div class="dtl-poster-img">
        <img src="<?php echo e(URL::to('/'.$movies_info->video_image)); ?>" alt="<?php echo e(stripslashes($movies_info->video_title)); ?>" title="<?php echo e(stripslashes($movies_info->video_title)); ?>" />
      </div>
    </div>
    </div>
    <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12 mb-4">
      <div class="poster-dtl-item">
      <h2><a href="<?php echo e(URL::to('movies/watch/'.$movies_info->video_slug.'/'.$movies_info->id)); ?>" title="<?php echo e(stripslashes($movies_info->video_title)); ?>"><?php echo e(stripslashes($movies_info->video_title)); ?></a></h2>
      <ul class="dtl-list-link">
        <?php $__currentLoopData = explode(',',$movies_info->movie_genre_id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genres_ids): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><a href="<?php echo e(URL::to('movies?genre_id='.$genres_ids)); ?>" title="<?php echo e(App\Genres::getGenresInfo($genres_ids,'genre_name')); ?>"><?php echo e(App\Genres::getGenresInfo($genres_ids,'genre_name')); ?></a></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <li>
          <a href="<?php echo e(URL::to('movies?lang_id='.$movies_info->movie_lang_id)); ?>" title="<?php echo e(App\Language::getLanguageInfo($movies_info->movie_lang_id,'language_name')); ?>"><?php echo e(App\Language::getLanguageInfo($movies_info->movie_lang_id,'language_name')); ?></a>
        </li>

        <?php if($movies_info->content_rating): ?>

        <li><span class="channel_info_count"><?php echo e($movies_info->content_rating); ?></span></li>

        <?php endif; ?>


      </ul>

       <?php if($movies_info->trailer_url!=""): ?>
          <div class="video-watch-share-item mb-3">
            <div class="subscribe-btn-item" style="margin-left:0px !important">
            <a href="javascript:window['player2'].showLightbox();" title="<?php echo e(trans('words.watch_triler')); ?>"><i class="fa fa-play-circle"></i> <?php echo e(trans('words.watch_triler')); ?></a>
            </div>
          </div>
       <?php endif; ?>


      <?php if(!is_null($movies_info->actor_id)>0): ?>

        <span class="des-bold-text"><strong><?php echo e(trans('words.actors')); ?>:</strong>
          <?php $a = ''; $n = count(explode(',',$movies_info->actor_id,6));?>
          <?php $__currentLoopData = explode(',',$movies_info->actor_id,6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $actor_ids): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e(URL::to('actors/'.App\ActorDirector::getActorDirectorInfo($actor_ids,'ad_slug'))); ?>/<?php echo e($actor_ids); ?>" title="actors"><?php echo e(App\ActorDirector::getActorDirectorInfo($actor_ids,'ad_name')); ?></a><?php if (($i+1) != $n) echo $a = ',';?>

          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </span>

      <?php endif; ?>

      <?php if(!is_null($movies_info->director_id)>0): ?>
      <span class="des-bold-text"><strong><?php echo e(trans('words.directors')); ?>:</strong>

              <?php $a = ''; $n = count(explode(',',$movies_info->director_id,6));?>
              <?php $__currentLoopData = explode(',',$movies_info->director_id,6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i =>$director_ids): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <a href="<?php echo e(URL::to('directors/'.App\ActorDirector::getActorDirectorInfo($director_ids,'ad_slug'))); ?>/<?php echo e($director_ids); ?>" title="directors"><?php echo e(App\ActorDirector::getActorDirectorInfo($director_ids,'ad_name')); ?></a><?php if (($i+1) != $n) echo $a = ',';?>

              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      </span>
      <?php endif; ?>

      <h3><?php echo strip_tags(Str::limit(stripslashes($movies_info->video_description),350)); ?></h3>

      </div>
    </div>
    </div>
    <!-- Start Popular Videos -->

    <!-- Start You May Also Like Video Carousel -->
    <div class="row">
    <div class="video-carousel-area vfx-item-ptb related-video-item">
      <div class="container-fluid">
      <div class="row">
        <div class="col-md-12 p-0">
        <div class="vfx-item-section">
          <h3><?php echo e(trans('words.you_may_like')); ?></h3>

        </div>
        <div class="video-carousel owl-carousel">
          <?php $__currentLoopData = $related_movies_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movies_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="single-video">
          <a href="<?php echo e(URL::to('movies/details/'.$movies_data->video_slug.'/'.$movies_data->id)); ?>" title="<?php echo e(stripslashes($movies_data->video_title)); ?>">
             <div class="video-img">
              <?php if($movies_data->video_access =="Paid"): ?>
              <div class="vid-lab-premium">
                <img src="<?php echo e(URL::asset('site_assets/images/ic-premium.png')); ?>" alt="ic-premium" title="ic-premium">
              </div>
              <?php endif; ?>
              <span class="video-item-content"><?php echo e(stripslashes($movies_data->video_title)); ?></span>
              <img src="<?php echo e(URL::to('/'.$movies_data->video_image_thumb)); ?>" alt="<?php echo e(stripslashes($movies_data->video_title)); ?>" title="<?php echo e(stripslashes($movies_data->video_title)); ?>">
             </div>
          </a>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </div>
        </div>
      </div>
      </div>
    </div>
    </div>
    <!-- End You May Also Like Video Carousel -->
  </div>
</div>
<!-- End Page Content Area -->

<!-- Banner -->
<!--<?php if(get_web_banner('details_bottom')!=""): ?>-->
<!--<div class="vid-item-ptb banner_ads_item pb-3">-->
<!--	<div class="container-fluid">-->
<!--		<div class="row">-->
<!--			<div class="col-md-12">-->
<!--				<?php echo stripslashes(get_web_banner('details_bottom')); ?>-->
<!--			</div>-->
<!--		</div>-->
<!--	</div>-->
<!--</div>-->
<!--<?php endif; ?>-->


<script type="text/javascript">

    <?php if(Session::has('flash_message')): ?>

      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: false,
        /*didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }*/
      })

      Toast.fire({
        icon: 'success',
        title: '<?php echo e(Session::get('flash_message')); ?>'
      })

  <?php endif; ?>

  </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/pages/movies/details.blade.php ENDPATH**/ ?>