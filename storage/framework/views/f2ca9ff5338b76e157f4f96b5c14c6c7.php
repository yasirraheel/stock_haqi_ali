<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <div id="sidebar-menu">
            <?php if(Auth::User()->usertype == 'Admin'): ?>

                <ul>
                    <li><a href="<?php echo e(URL::to('admin/dashboard')); ?>"
                            class="waves-effect <?php echo e(classActivePath('dashboard')); ?>"><i
                                class="fa fa-dashboard"></i><span><?php echo e(trans('words.dashboard_text')); ?></span></a></li>
                    <li><a href="<?php echo e(URL::to('admin/language')); ?>"
                            class="waves-effect <?php echo e(classActivePath('language')); ?>"><i
                                class="fa fa-language"></i><span><?php echo e(trans('words.language_text')); ?></span></a></li>
                    <li><a href="<?php echo e(URL::to('admin/genres')); ?>" class="waves-effect <?php echo e(classActivePath('genres')); ?>"><i
                                class="fa fa-list"></i><span><?php echo e(trans('words.genres_text')); ?></span></a></li>

                    <?php if(getcong('menu_movies')): ?>
                        <li><a href="<?php echo e(URL::to('admin/movies')); ?>"
                                class="waves-effect <?php echo e(classActivePath('movies')); ?>"><i
                                    class="fa fa-video-camera"></i><span><?php echo e(trans('words.movies_text')); ?></span></a>
                        </li>
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect">
                                <i class="fa fa-image"></i>
                                <span>Manage Screenshot</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="list-unstyled">
                                <li class="<?php echo e(classActivePath('generateScreenshot')); ?>"><a
                                    href="<?php echo e(URL::to('admin/generateScreenshot')); ?>"
                                    class="<?php echo e(classActivePath('generateScreenshot')); ?>"><i
                                        class="fa fa-image"></i><span>Screenshot</span></a></li>
                                <li class="<?php echo e(classActivePath('google_drive_api')); ?>"><a
                                        href="<?php echo e(URL::to('admin/google_drive_api')); ?>"
                                        class="<?php echo e(classActivePath('google_drive_api')); ?>"><i
                                        class="fa fa-google"></i><span>Google Drive API</span></a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect <?php echo e(classActivePath('photos')); ?> <?php echo e(classActivePath('photo-categories')); ?>">
                            <i class="fa fa-camera"></i>
                            <span>Photos</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li class="<?php echo e(classActivePath('photos')); ?>"><a href="<?php echo e(route('admin.photos.index')); ?>"
                                    class="<?php echo e(classActivePath('photos')); ?>"><i
                                        class="fa fa-image"></i><span>All Photos</span></a></li>
                            <li class="<?php echo e(classActivePath('photo-categories')); ?>"><a href="<?php echo e(route('admin.photo-categories.index')); ?>"
                                    class="<?php echo e(classActivePath('photo-categories')); ?>"><i
                                        class="fa fa-tags"></i><span>Categories</span></a></li>
                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect <?php echo e(classActivePath('audio')); ?>">
                            <i class="fa fa-music"></i>
                            <span>Audio</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li class="<?php echo e(classActivePath('audio')); ?>"><a href="<?php echo e(route('admin.audio.index')); ?>"
                                    class="<?php echo e(classActivePath('audio')); ?>"><i
                                        class="fa fa-music"></i><span>All Audio</span></a></li>
                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="fa fa-users"></i>
                            <span><?php echo e(trans('words.users')); ?></span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li class="<?php echo e(classActivePath('sub_admin')); ?>"><a href="<?php echo e(URL::to('admin/sub_admin')); ?>"
                                    class="<?php echo e(classActivePath('sub_admin')); ?>"><i
                                        class="fa fa-users"></i><span><?php echo e(trans('words.users')); ?></span></a></li>
                        </ul>
                    </li>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="fa fa-money"></i>
                            <span>Sales</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li class="<?php echo e(classActivePath('paypal_dashboard')); ?>"><a
                                    href="<?php echo e(URL::to('admin/paypal_dashboard')); ?>"
                                    class="<?php echo e(classActivePath('paypal_dashboard')); ?>"><i
                                        class="fa fa-paypal"></i><span>PayPal</span></a></li>
                            <li class="<?php echo e(classActivePath('paypal_payout')); ?>"><a
                                    href="<?php echo e(URL::to('admin/paypal_payout')); ?>"
                                    class="<?php echo e(classActivePath('paypal_payout')); ?>"><i class="fa fa-paypal"></i><span>
                                        Manual Payout</span></a></li>
                            <li class="<?php echo e(classActivePath('sold_licenses')); ?>"><a
                                    href="<?php echo e(URL::to('admin/sold_licenses')); ?>"
                                    class="<?php echo e(classActivePath('sold_licenses')); ?>"><i class="fa fa-paypal"></i><span>
                                        Sold License</span></a></li>
                        </ul>
                    </li>

                    <li><a href="<?php echo e(URL::to('admin/payment_gateway')); ?>"
                            class="waves-effect <?php echo e(classActivePath('payment_gateway')); ?>"><i
                                class="fa fa-credit-card-alt"></i><span><?php echo e(trans('words.payment_gateway')); ?></span></a>
                    </li>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-edit"></i><span><?php echo e(trans('words.pages')); ?></span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="<?php echo e(classActivePath('pages')); ?>"><a href="<?php echo e(URL::to('admin/pages')); ?>"
                                    class="<?php echo e(classActivePath('pages')); ?>"><i
                                        class="fa fa-file"></i><span><?php echo e(trans('words.pages')); ?></span></a></li>
                            <li class="<?php echo e(classActivePath('pages/add')); ?>"><a
                                    href="<?php echo e(URL::to('admin/pages/add')); ?>"
                                    class="<?php echo e(classActivePath('pages')); ?>"><i
                                        class="fa fa-plus"></i><span><?php echo e(trans('words.add_page')); ?></span></a></li>
                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-play-circle"></i><span><?php echo e(trans('words.player_settings')); ?></span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="<?php echo e(classActivePath('player_settings')); ?>"><a
                                    href="<?php echo e(URL::to('admin/player_settings')); ?>"
                                    class="<?php echo e(classActivePath('player_settings')); ?>"><i
                                        class="fa fa-cog"></i><span><?php echo e(trans('words.settings')); ?></span></a></li>

                            <li class="<?php echo e(classActivePath('player_ad_settings')); ?>"><a
                                    href="<?php echo e(URL::to('admin/player_ad_settings')); ?>"
                                    class="<?php echo e(classActivePath('player_ad_settings')); ?>"><i
                                        class="fa fa-buysellads"></i><span><?php echo e(trans('words.player_ads')); ?></span></a>
                            </li>

                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-cog"></i><span><?php echo e(trans('words.settings')); ?></span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="<?php echo e(classActivePath('general_settings')); ?>"><a
                                    href="<?php echo e(URL::to('admin/general_settings')); ?>"
                                    class="<?php echo e(classActivePath('general_settings')); ?>"><i
                                        class="fa fa-cog"></i><span><?php echo e(trans('words.general')); ?></span></a></li>
                            <li class="<?php echo e(classActivePath('email_settings')); ?>"><a
                                    href="<?php echo e(URL::to('admin/email_settings')); ?>"
                                    class="<?php echo e(classActivePath('email_settings')); ?>"><i
                                        class="fa fa-send"></i><span><?php echo e(trans('words.smtp_email')); ?></span></a></li>
                            <li class="<?php echo e(classActivePath('social_login_settings')); ?>"><a
                                    href="<?php echo e(URL::to('admin/social_login_settings')); ?>"
                                    class="<?php echo e(classActivePath('social_login_settings')); ?>"><i
                                        class="fa fa-usb"></i><span><?php echo e(trans('words.social_login')); ?></span></a></li>

                            <li class="<?php echo e(classActivePath('menu_settings')); ?>"><a
                                    href="<?php echo e(URL::to('admin/menu_settings')); ?>"
                                    class="<?php echo e(classActivePath('menu_settings')); ?>"><i
                                        class="fa fa-list"></i><span><?php echo e(trans('words.menu')); ?></span></a></li>
                            <li class="<?php echo e(classActivePath('recaptcha_settings')); ?>"><a
                                    href="<?php echo e(URL::to('admin/recaptcha_settings')); ?>"
                                    class="<?php echo e(classActivePath('recaptcha_settings')); ?>"><i
                                        class="fa fa-refresh"></i><span> <?php echo e(trans('words.reCAPTCHA')); ?></span></a>
                            </li>


                            <li class="<?php echo e(classActivePath('site_maintenance')); ?>"><a
                                    href="<?php echo e(URL::to('admin/site_maintenance')); ?>"
                                    class="<?php echo e(classActivePath('site_maintenance')); ?>"><i
                                        class="fa fa-wrench"></i><span>
                                        <?php echo e(trans('words.site_maintenance')); ?></span></a></li>
                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="fa fa-android"></i>
                            <span><?php echo e(trans('words.android_app')); ?></span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">

                            <?php if(env('BUYER_NAME') and env('BUYER_PURCHASE_CODE')): ?>
                                <li class="<?php echo e(classActivePath('android_settings')); ?>">
                                    <a href="<?php echo e(URL::to('admin/android_settings')); ?>"
                                        class="<?php echo e(classActivePath('android_settings')); ?>">
                                        <i class="fa fa-cog"></i>
                                        <span><?php echo e(trans('words.android_app_settings')); ?></span>
                                    </a>
                                </li>

                    </li>
<li><a href="<?php echo e(URL::to('admin/language')); ?>"
                        class="waves-effect <?php echo e(classActivePath('language')); ?>"><i
                            class="fa fa-language"></i><span><?php echo e(trans('words.language_text')); ?></span></a>
                </li>
                <li><a href="<?php echo e(URL::to('admin/genres')); ?>"
                        class="waves-effect <?php echo e(classActivePath('genres')); ?>"><i
                            class="fa fa-list"></i><span><?php echo e(trans('words.genres_text')); ?></span></a></li>
 <li class="<?php echo e(classActivePath('chat')); ?>">
                <a href="<?php echo e(URL::to('/messages')); ?>" class="waves-effect">
                    <i class="fa fa-envelope"></i>
                    <span>Messages</span>
                    
                </a>

            </li>
            <?php endif; ?>
            </ul>
            </li>

            <li class="<?php echo e(classActivePath('chat')); ?>">
                <a href="<?php echo e(URL::to('/admin/clear-cache')); ?>" class="waves-effect">
                    <i class="fa fa-cogs"></i>
                    <span>Clear Cache</span>
                    
                </a>

            </li>
            <li class="<?php echo e(classActivePath('slider')); ?>"><a href="<?php echo e(URL::to('admin/slider')); ?>"
                    class="<?php echo e(classActivePath('slider')); ?>"><i
                        class="fa fa-sliders"></i><span><?php echo e(trans('words.slider')); ?></span></a></li>

    <li><a href="<?php echo e(URL::to('admin/transactions')); ?>"
                        class="waves-effect <?php echo e(classActivePath('transactions')); ?>"><i
                            class="fa fa-list"></i><span><?php echo e(trans('words.transactions')); ?></span></a></li>

                        <?php else: ?>
            <ul>

                <?php if(getcong('menu_movies')): ?>
                    <li><a href="<?php echo e(URL::to('admin/movies')); ?>"
                            class="waves-effect <?php echo e(classActivePath('movies')); ?>"><i
                                class="fa fa-video-camera"></i><span><?php echo e(trans('words.movies_text')); ?></span></a>
                    </li>
                <?php endif; ?>
                 <li class="<?php echo e(classActivePath('sold_licenses')); ?>"><a
                                    href="<?php echo e(URL::to('admin/sold_licenses')); ?>"
                                    class="<?php echo e(classActivePath('sold_licenses')); ?>"><i class="fa fa-paypal"></i><span>
                                        Sold License</span></a></li>

            <?php endif; ?>

            </ul>
        </div>
    </div>
</div>


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
<?php /**PATH C:\xampp\htdocs\stock_new\resources\views/admin/sidebar.blade.php ENDPATH**/ ?>