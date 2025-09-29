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
                                        class="fa fa-image"></i><span>
                                        Screenshot</span></a></li>

                                <li class="<?php echo e(classActivePath('google_drive_api')); ?>"><a
                                        href="<?php echo e(URL::to('admin/google_drive_api')); ?>"
                                        class="<?php echo e(classActivePath('google_drive_api')); ?>"><i class="fa fa-google"></i><span>
                                            Google Drive API</span></a></li>
                            </ul>


                        </li>






                    <?php endif; ?>

                    <!--<?php if(getcong('menu_shows')): ?>
-->
                    <!--<li class="has_sub"> -->
                    <!--  <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-film"></i><span><?php echo e(trans('words.tv_shows_text')); ?></span><span class="menu-arrow"></span></a>-->
                    <!--  <ul class="list-unstyled">                 -->
                    <!--    <li class="<?php echo e(classActivePath('series')); ?>"><a href="<?php echo e(URL::to('admin/series')); ?>" class="<?php echo e(classActivePath('series')); ?>"><i class="fa fa-image"></i><span><?php echo e(trans('words.shows_text')); ?></span></a></li>-->
                    <!--    <li class="<?php echo e(classActivePath('season')); ?>"><a href="<?php echo e(URL::to('admin/season')); ?>" class="<?php echo e(classActivePath('season')); ?>"><i class="fa fa-tree"></i><span><?php echo e(trans('words.seasons_text')); ?></span></a></li>-->
                    <!--    <li class="<?php echo e(classActivePath('episodes')); ?>"><a href="<?php echo e(URL::to('admin/episodes')); ?>" class="<?php echo e(classActivePath('episodes')); ?>"><i class="fa fa-list"></i><span><?php echo e(trans('words.episodes_text')); ?></span></a></li>-->
                    <!--  </ul>-->
                    <!--</li>-->
                    <!--
<?php endif; ?>-->

                    <!--<?php if(getcong('menu_sports')): ?>
-->
                    <!--<li class="has_sub"> -->
                    <!--  <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-futbol-o"></i><span><?php echo e(trans('words.sports_text')); ?></span><span class="menu-arrow"></span></a>-->
                    <!--  <ul class="list-unstyled">                 -->
                    <!--    <li class="<?php echo e(classActivePath('sports_category')); ?>"><a href="<?php echo e(URL::to('admin/sports_category')); ?>" class="<?php echo e(classActivePath('sports_category')); ?>"><i class="fa fa-list"></i><span><?php echo e(trans('words.sports_cat_text')); ?></span></a></li>-->
                    <!--    <li class="<?php echo e(classActivePath('sports')); ?>"><a href="<?php echo e(URL::to('admin/sports')); ?>" class="<?php echo e(classActivePath('sports')); ?>"><i class="fa fa-soccer-ball-o"></i><span><?php echo e(trans('words.sports_video_text')); ?></span></a></li>-->
                    <!--   </ul>-->
                    <!--</li>-->
                    <!--
<?php endif; ?>-->

                    <!--<?php if(getcong('menu_livetv')): ?>
-->
                    <!--<li class="has_sub"> -->
                    <!--  <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-tv"></i><span><?php echo e(trans('words.live_tv')); ?></span><span class="menu-arrow"></span></a>-->
                    <!--  <ul class="list-unstyled">                 -->
                    <!--    <li class="<?php echo e(classActivePath('tv_category')); ?>"><a href="<?php echo e(URL::to('admin/tv_category')); ?>" class="<?php echo e(classActivePath('tv_category')); ?>"><i class="fa fa-tags"></i><span><?php echo e(trans('words.live_tv_category')); ?></span></a></li>-->
                    <!--    <li class="<?php echo e(classActivePath('live_tv')); ?>"><a href="<?php echo e(URL::to('admin/live_tv')); ?>" class="<?php echo e(classActivePath('live_tv')); ?>"><i class="fa fa-list"></i><span><?php echo e(trans('words.tv_channel')); ?></span></a></li>-->
                    <!--   </ul>-->
                    <!--</li>-->
                    <!--
<?php endif; ?>-->

                    <!--<li class="has_sub"> -->
                    <!--  <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-users"></i><span><?php echo e(trans('words.cast_crew')); ?></span><span class="menu-arrow"></span></a>-->
                    <!--  <ul class="list-unstyled">                 -->
                    <!--    <li class="<?php echo e(classActivePath('actor')); ?>"><a href="<?php echo e(URL::to('admin/actor')); ?>" class="<?php echo e(classActivePath('actor')); ?>"><i class="fa fa-user"></i><span><?php echo e(trans('words.actors')); ?></span></a></li>-->
                    <!--    <li class="<?php echo e(classActivePath('director')); ?>"><a href="<?php echo e(URL::to('admin/director')); ?>" class="<?php echo e(classActivePath('director')); ?>"><i class="fa fa-user"></i><span><?php echo e(trans('words.directors')); ?></span></a></li>-->
                    <!--   </ul>-->
                    <!--</li>-->


                    <!--<li class="has_sub"> -->
                    <!--  <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-sliders"></i><span><?php echo e(trans('words.home')); ?></span><span class="menu-arrow"></span></a>-->
                    <!--  <ul class="list-unstyled">                 -->
                    <!--    <li class="<?php echo e(classActivePath('slider')); ?>"><a href="<?php echo e(URL::to('admin/slider')); ?>" class="<?php echo e(classActivePath('slider')); ?>"><i class="fa fa-sliders"></i><span><?php echo e(trans('words.slider')); ?></span></a></li>-->
                    <!--    <li class="<?php echo e(classActivePath('home_sections')); ?>"><a href="<?php echo e(URL::to('admin/home_sections')); ?>" class="<?php echo e(classActivePath('home_sections')); ?>"><i class="fa fa-th-list"></i><span><?php echo e(trans('words.home_section')); ?></span></a></li>-->
                    <!--   </ul>-->
                    <!--</li> -->


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
                        </ul>


                    </li>






                    <!--</li>-->
                    <li class="d-none"><a href="<?php echo e(URL::to('admin/subscription_plan')); ?>"
                            class="waves-effect <?php echo e(classActivePath('subscription_plan')); ?>"><i
                                class="fa fa-dollar"></i><span><?php echo e(trans('words.subscription_plan')); ?></span></a></li>
                    <!--<li><a href="<?php echo e(URL::to('admin/coupons')); ?>" class="waves-effect <?php echo e(classActivePath('coupons')); ?>"><i class="fa fa-gift"></i><span><?php echo e(trans('words.coupons')); ?></span></a></li>-->

                    <li><a href="<?php echo e(URL::to('admin/payment_gateway')); ?>"
                            class="waves-effect <?php echo e(classActivePath('payment_gateway')); ?>"><i
                                class="fa fa-credit-card-alt"></i><span><?php echo e(trans('words.payment_gateway')); ?></span></a>
                    </li>
                    <!--<li><a href="<?php echo e(URL::to('admin/transactions')); ?>" class="waves-effect <?php echo e(classActivePath('transactions')); ?>"><i class="fa fa-list"></i><span><?php echo e(trans('words.transactions')); ?></span></a></li>-->
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
                            <!--<li class="<?php echo e(classActivePath('verify_purchase_app')); ?>">-->
                            <!--  <a href="<?php echo e(URL::to('admin/verify_purchase_app')); ?>" class="<?php echo e(classActivePath('verify_purchase_app')); ?>">-->
                            <!--    <i class="fa fa-lock"></i>-->
                            <!--    <span>App Verify</span>-->
                            <!--  </a>-->
                            <!--</li>  -->
                            <?php if(env('BUYER_NAME') and env('BUYER_PURCHASE_CODE')): ?>
                                <li class="<?php echo e(classActivePath('android_settings')); ?>">
                                    <a href="<?php echo e(URL::to('admin/android_settings')); ?>"
                                        class="<?php echo e(classActivePath('android_settings')); ?>">
                                        <i class="fa fa-cog"></i>
                                        <span><?php echo e(trans('words.android_app_settings')); ?></span>
                                    </a>
                                </li>
                                <!--<li class="<?php echo e(classActivePath('ad_list')); ?>">-->
                                <!--  <a href="<?php echo e(URL::to('admin/ad_list')); ?>" class="waves-effect <?php echo e(classActivePath('ad_list')); ?>">-->
                                <!--    <i class="fa fa-buysellads"></i>-->
                                <!--    <span>Ad Settings</span>-->
                                <!--  </a>-->
                    </li>
                    <!--<li class="<?php echo e(classActivePath('android_notification')); ?>">-->
                    <!--  <a href="<?php echo e(URL::to('admin/android_notification')); ?>" class="<?php echo e(classActivePath('android_notification')); ?>">-->
                    <!--    <i class="fa fa-send"></i>-->
                    <!--    <span><?php echo e(trans('words.android_app_notification')); ?></span>-->
                    <!--  </a>-->
                    <!--</li>-->
            <?php endif; ?>
            </ul>
            </li>
            <li class="<?php echo e(classActivePath('chat')); ?>">
                <a href="<?php echo e(URL::to('/messages')); ?>" class="waves-effect">
                    <i class="fa fa-envelope"></i>
                    <span>Messages</span>
                    
                </a>

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
        <?php else: ?>
            <ul>
                
                <li><a href="<?php echo e(URL::to('admin/language')); ?>"
                        class="waves-effect <?php echo e(classActivePath('language')); ?>"><i
                            class="fa fa-language"></i><span><?php echo e(trans('words.language_text')); ?></span></a>
                </li>
                <li><a href="<?php echo e(URL::to('admin/genres')); ?>"
                        class="waves-effect <?php echo e(classActivePath('genres')); ?>"><i
                            class="fa fa-list"></i><span><?php echo e(trans('words.genres_text')); ?></span></a></li>

                <?php if(getcong('menu_movies')): ?>
                    <li><a href="<?php echo e(URL::to('admin/movies')); ?>"
                            class="waves-effect <?php echo e(classActivePath('movies')); ?>"><i
                                class="fa fa-video-camera"></i><span><?php echo e(trans('words.movies_text')); ?></span></a>
                    </li>
                <?php endif; ?>

                <?php if(getcong('menu_shows')): ?>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-film"></i><span><?php echo e(trans('words.tv_shows_text')); ?></span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="<?php echo e(classActivePath('series')); ?>"><a href="<?php echo e(URL::to('admin/series')); ?>"
                                    class="<?php echo e(classActivePath('series')); ?>"><i
                                        class="fa fa-image"></i><span><?php echo e(trans('words.shows_text')); ?></span></a>
                            </li>
                            <li class="<?php echo e(classActivePath('season')); ?>"><a href="<?php echo e(URL::to('admin/season')); ?>"
                                    class="<?php echo e(classActivePath('season')); ?>"><i
                                        class="fa fa-tree"></i><span><?php echo e(trans('words.seasons_text')); ?></span></a>
                            </li>
                            <li class="<?php echo e(classActivePath('episodes')); ?>"><a
                                    href="<?php echo e(URL::to('admin/episodes')); ?>"
                                    class="<?php echo e(classActivePath('episodes')); ?>"><i
                                        class="fa fa-list"></i><span><?php echo e(trans('words.episodes_text')); ?></span></a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if(getcong('menu_sports')): ?>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-futbol-o"></i><span><?php echo e(trans('words.sports_text')); ?></span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="<?php echo e(classActivePath('sports_category')); ?>"><a
                                    href="<?php echo e(URL::to('admin/sports_category')); ?>"
                                    class="<?php echo e(classActivePath('sports_category')); ?>"><i
                                        class="fa fa-list"></i><span><?php echo e(trans('words.sports_cat_text')); ?></span></a>
                            </li>
                            <li class="<?php echo e(classActivePath('sports')); ?>"><a href="<?php echo e(URL::to('admin/sports')); ?>"
                                    class="<?php echo e(classActivePath('sports')); ?>"><i
                                        class="fa fa-soccer-ball-o"></i><span><?php echo e(trans('words.sports_video_text')); ?></span></a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if(getcong('menu_livetv')): ?>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-tv"></i><span><?php echo e(trans('words.live_tv')); ?></span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="<?php echo e(classActivePath('tv_category')); ?>"><a
                                    href="<?php echo e(URL::to('admin/tv_category')); ?>"
                                    class="<?php echo e(classActivePath('tv_category')); ?>"><i
                                        class="fa fa-tags"></i><span><?php echo e(trans('words.live_tv_category')); ?></span></a>
                            </li>
                            <li class="<?php echo e(classActivePath('live_tv')); ?>"><a href="<?php echo e(URL::to('admin/live_tv')); ?>"
                                    class="<?php echo e(classActivePath('live_tv')); ?>"><i
                                        class="fa fa-list"></i><span><?php echo e(trans('words.tv_channel')); ?></span></a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <!--<li class="has_sub"> -->
                <!--  <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-sliders"></i><span><?php echo e(trans('words.home')); ?></span><span class="menu-arrow"></span></a>-->
                <!--  <ul class="list-unstyled">                 -->
                <!--    <li class="<?php echo e(classActivePath('slider')); ?>"><a href="<?php echo e(URL::to('admin/slider')); ?>" class="<?php echo e(classActivePath('slider')); ?>"><i class="fa fa-sliders"></i><span><?php echo e(trans('words.slider')); ?></span></a></li>-->
                <!--    <li class="<?php echo e(classActivePath('home_sections')); ?>"><a href="<?php echo e(URL::to('admin/home_sections')); ?>" class="<?php echo e(classActivePath('home_sections')); ?>"><i class="fa fa-th-list"></i><span><?php echo e(trans('words.home_section')); ?></span></a></li>-->
                <!--   </ul>-->
                <!--</li>-->

                <li><a href="<?php echo e(URL::to('admin/transactions')); ?>"
                        class="waves-effect <?php echo e(classActivePath('transactions')); ?>"><i
                            class="fa fa-list"></i><span><?php echo e(trans('words.transactions')); ?></span></a></li>

            </ul>

            <?php endif; ?>
            <li class="<?php echo e(classActivePath('web_ads_settings')); ?>"><a
                    href="<?php echo e(URL::to('admin/web_ads_settings')); ?>"
                    class="<?php echo e(classActivePath('web_ads_settings')); ?>"><i class="fa fa-buysellads"></i><span>
                        <?php echo e(trans('words.banner_ads')); ?></span></a></li>





            <!-- <li><a href="<?php echo e(URL::to('admin/language')); ?>" class="waves-effect <?php echo e(classActivePath('language')); ?>"><i class="fa fa-language"></i> <span> Language</span></a></li> -->
<br>
<br>
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
<?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/admin/sidebar.blade.php ENDPATH**/ ?>