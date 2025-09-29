<!-- Start Header -->
<header>
    <!-- Start Navigation Area -->
    <script type="text/javascript">
        <?php if(Session::has('flash_message')): ?>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: false
            });

            Toast.fire({
                icon: 'success',
                title: '<?php echo e(Session::get('flash_message')); ?>'
            });
        <?php endif; ?>

        <?php if(Session::has('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: '<?php echo e(Session::get('error')); ?>',
                showConfirmButton: true,
                confirmButtonColor: '#10c469',
                background: "#1a2234",
                color: "#fff"
            });
        <?php endif; ?>
    </script>
    <div class="main-menu">
      <nav class="header-section pin-style">
        <div class="container-fluid">
          <div class="mod-menu">
            <div class="row">
              <div class="col-2">
                <?php if(getcong('site_logo')): ?>
                  <a href="<?php echo e(URL::to('/')); ?>" title="logo" class="logo"><img src="<?php echo e(URL::asset('/'.getcong('site_logo'))); ?>" alt="logo" title="logo"></a>
                <?php else: ?>
                  <a href="<?php echo e(URL::to('/')); ?>" title="logo" class="logo"><img src="<?php echo e(URL::asset('site_assets/images/logo.png')); ?>" alt="logo" title="logo"></a>
                <?php endif; ?>

              </div>
              <div class="col-7 nav-order-last nopadding">
                <div class="main-nav leftnav">
                  <ul class="top-nav">
                    <li class="visible-this d-md-none menu-icon"> <a href="#" class="navbar-toggle collapsed" data-bs-toggle="collapse" data-bs-target="#menu" aria-expanded="false" title="menu-toggle"><i class="fa fa-bars"></i></a> </li>
                  </ul>
                  <div id="menu" class="collapse header-menu">
                    <ul class="nav vfx-item-nav">
                      <li><a href="<?php echo e(URL::to('/')); ?>" class="<?php echo e(classActivePathSite('')); ?>" title="home">Home</a></li>

                      <?php if(getcong('menu_movies')): ?>
                      <li><a href="<?php echo e(URL::to('movies/')); ?>" class="<?php echo e(classActivePathSite('movies')); ?>" title="<?php echo e(trans('words.movies_text')); ?>"><?php echo e(trans('words.movies_text')); ?></a></li>
                      <?php endif; ?>

                      <?php if(getcong('menu_shows')): ?>
                      <li><a href="<?php echo e(URL::to('shows/')); ?>" class="<?php echo e(classActivePathSite('shows')); ?>" title="<?php echo e(trans('words.tv_shows_text')); ?>"><?php echo e(trans('words.tv_shows_text')); ?></a></li>
                      <?php endif; ?>

                      <?php if(getcong('menu_sports')): ?>
                      <li><a href="<?php echo e(URL::to('sports')); ?>" class="<?php echo e(classActivePathSite('sports')); ?>" title="<?php echo e(trans('words.sports_text')); ?>"><?php echo e(trans('words.sports_text')); ?></a> <span class="arrow"></span>
                        <ul class="dm-align-2 mega-list">
                          <?php $__currentLoopData = \App\SportsCategory::where('status','1')->orderBy('category_name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sports_cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <li><a href="<?php echo e(URL::to('sports/?cat_id='.$sports_cat->id)); ?>" title="<?php echo e($sports_cat->category_name); ?>"><?php echo e($sports_cat->category_name); ?></a></li>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                      </li>
                      <?php endif; ?>

                      <?php if(getcong('menu_livetv')): ?>
                      <li><a href="<?php echo e(URL::to('livetv')); ?>" class="<?php echo e(classActivePathSite('livetv')); ?>" title="<?php echo e(trans('words.live_tv')); ?>"><?php echo e(trans('words.live_tv')); ?></a> <span class="arrow"></span>
                        <ul class="dm-align-2 mega-list">
                          <?php $__currentLoopData = \App\TvCategory::where('status','1')->orderBy('category_name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tv_cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><a href="<?php echo e(URL::to('livetv/?cat_id='.$tv_cat->id)); ?>" title="<?php echo e($tv_cat->category_name); ?>"><?php echo e($tv_cat->category_name); ?></a></li>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                      </li>
                      <?php endif; ?>

                    </ul>
                  </div>
                </div>
              </div>
        <div class="col-3">
          <div class="right-sub-item-area">
            <div class="search-item-block">
              <form class="navbar-form navbar-left">
                <a type="submit" href="#popup1" class="btn btn-default open" title="search"><i class="fa fa-search"></i></a>
              </form>
            </div>
            <div class="subscribe-btn-item">
              <a href="<?php echo e(URL::to('/')); ?>" title="subscribe"><img src="<?php echo e(URL::asset('site_assets/images/ic-subscribe2.png')); ?>" alt="ic-subscribe" title="Stumble"></a>
            </div>
            <?php if(Auth::check()): ?>

            <div class="user-menu">
              <div class="user-name">
                <span>
                  <?php if(Auth::User()->user_image AND file_exists(public_path('upload/'.Auth::User()->user_image))): ?>
                    <img src="<?php echo e(URL::asset('upload/'.Auth::User()->user_image)); ?>" alt="profile_img" title="<?php echo e(Auth::User()->name,6); ?>" id="userPic">
                  <?php else: ?>
                      <img src="<?php echo e(URL::asset('site_assets/images/user-avatar.png')); ?>" alt="profile_img" title="<?php echo e(Auth::User()->name,6); ?>" id="userPic">
                  <?php endif; ?>

                </span>
                <?php echo e(Str::limit(Auth::User()->name,6)); ?><i class="fa fa-angle-down" id="userArrow"></i>
              </div>

              <?php if(Auth::User()->usertype =="Admin"): ?>

              <ul class="content-user">
                <li><a href="<?php echo e(URL::to('admin/dashboard')); ?>" title="<?php echo e(trans('words.dashboard_text')); ?>"><i class="fa fa-database"></i><?php echo e(trans('words.dashboard_text')); ?></a></li>
                <li><a href="<?php echo e(URL::to('profile')); ?>" title="<?php echo e(trans('words.profile')); ?>"><i class="fa fa-user"></i><?php echo e(trans('words.profile')); ?></a></li>
                <li><a href="<?php echo e(URL::to('messages')); ?>" title="Contact"><i class="fa fa-envelope"></i>Messages</a></li>
                <li><a href="<?php echo e(URL::to('admin/logout')); ?>" title="<?php echo e(trans('words.logout')); ?>"><i class="fa fa-sign-out-alt"></i><?php echo e(trans('words.logout')); ?></a></li>
              </ul>

              <?php else: ?>

              <ul class="content-user">
                <li><a href="<?php echo e(URL::to('dashboard')); ?>" title="<?php echo e(trans('words.dashboard_text')); ?>"><i class="fa fa-database"></i><?php echo e(trans('words.dashboard_text')); ?></a></li>
                <li><a href="<?php echo e(URL::to('profile')); ?>" title="<?php echo e(trans('words.profile')); ?>"><i class="fa fa-user"></i><?php echo e(trans('words.profile')); ?></a></li>
                   <li><a href="<?php echo e(URL::to('messages')); ?>" title="Contact"><i class="fa fa-envelope"></i>Contact</a></li>
                <li><a href="<?php echo e(URL::to('watchlist')); ?>" title="<?php echo e(trans('words.my_watchlist')); ?>"><i class="fa fa-list"></i><?php echo e(trans('words.my_watchlist')); ?></a></li>
                <li><a href="<?php echo e(URL::to('logout')); ?>" title="<?php echo e(trans('words.logout')); ?>"><i class="fa fa-sign-out-alt"></i><?php echo e(trans('words.logout')); ?></a></li>
              </ul>
              <?php endif; ?>


            </div>

            <?php else: ?>
            <div class="signup-btn-item">
              <a href="<?php echo e(URL::to('login')); ?>" title="login"><img src="<?php echo e(URL::asset('site_assets/images/ic-signup-user.png')); ?>" alt="ic-signup-user" title="signup-user"><span><?php echo e(trans('words.login_text')); ?></span></a>
            <?php endif; ?>
            </div>
          </div>
        </div>
            </div>
          </div>
        </div>
      </nav>
    </div>
    <!-- End Navigation Area -->
  </header>
  <!-- End Header -->
<?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/_particles/header.blade.php ENDPATH**/ ?>