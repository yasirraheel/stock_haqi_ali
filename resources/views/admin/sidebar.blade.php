<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <div id="sidebar-menu">
            @if (Auth::User()->usertype == 'Admin')

                <ul>
                    <li><a href="{{ URL::to('admin/dashboard') }}"
                            class="waves-effect {{ classActivePath('dashboard') }}"><i
                                class="fa fa-dashboard"></i><span>{{ trans('words.dashboard_text') }}</span></a></li>
                    <li><a href="{{ URL::to('admin/language') }}"
                            class="waves-effect {{ classActivePath('language') }}"><i
                                class="fa fa-language"></i><span>{{ trans('words.language_text') }}</span></a></li>
                    <li><a href="{{ URL::to('admin/genres') }}" class="waves-effect {{ classActivePath('genres') }}"><i
                                class="fa fa-list"></i><span>{{ trans('words.genres_text') }}</span></a></li>

                    @if (getcong('menu_movies'))
                        <li><a href="{{ URL::to('admin/movies') }}"
                                class="waves-effect {{ classActivePath('movies') }}"><i
                                    class="fa fa-video-camera"></i><span>{{ trans('words.movies_text') }}</span></a>
                        </li>
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect">
                                <i class="fa fa-image"></i>
                                <span>Manage Screenshot</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="list-unstyled">
                                <li class="{{ classActivePath('generateScreenshot') }}"><a
                                    href="{{ URL::to('admin/generateScreenshot') }}"
                                    class="{{ classActivePath('generateScreenshot') }}"><i
                                        class="fa fa-image"></i><span>Screenshot</span></a></li>
                                <li class="{{ classActivePath('google_drive_api') }}"><a
                                        href="{{ URL::to('admin/google_drive_api') }}"
                                        class="{{ classActivePath('google_drive_api') }}"><i
                                        class="fa fa-google"></i><span>Google Drive API</span></a></li>
                            </ul>
                        </li>
                    @endif

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect {{ classActivePath('photos') }} {{ classActivePath('photo-categories') }}">
                            <i class="fa fa-camera"></i>
                            <span>Photos</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li class="{{ classActivePath('photos') }}"><a href="{{ route('admin.photos.index') }}"
                                    class="{{ classActivePath('photos') }}"><i
                                        class="fa fa-image"></i><span>All Photos</span></a></li>
                            <li class="{{ classActivePath('photo-categories') }}"><a href="{{ route('admin.photo-categories.index') }}"
                                    class="{{ classActivePath('photo-categories') }}"><i
                                        class="fa fa-tags"></i><span>Categories</span></a></li>
                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="fa fa-users"></i>
                            <span>{{ trans('words.users') }}</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li class="{{ classActivePath('sub_admin') }}"><a href="{{ URL::to('admin/sub_admin') }}"
                                    class="{{ classActivePath('sub_admin') }}"><i
                                        class="fa fa-users"></i><span>{{ trans('words.users') }}</span></a></li>
                        </ul>
                    </li>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="fa fa-money"></i>
                            <span>Sales</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li class="{{ classActivePath('paypal_dashboard') }}"><a
                                    href="{{ URL::to('admin/paypal_dashboard') }}"
                                    class="{{ classActivePath('paypal_dashboard') }}"><i
                                        class="fa fa-paypal"></i><span>PayPal</span></a></li>
                            <li class="{{ classActivePath('paypal_payout') }}"><a
                                    href="{{ URL::to('admin/paypal_payout') }}"
                                    class="{{ classActivePath('paypal_payout') }}"><i class="fa fa-paypal"></i><span>
                                        Manual Payout</span></a></li>
                            <li class="{{ classActivePath('sold_licenses') }}"><a
                                    href="{{ URL::to('admin/sold_licenses') }}"
                                    class="{{ classActivePath('sold_licenses') }}"><i class="fa fa-paypal"></i><span>
                                        Sold License</span></a></li>
                        </ul>
                    </li>

                    <li><a href="{{ URL::to('admin/payment_gateway') }}"
                            class="waves-effect {{ classActivePath('payment_gateway') }}"><i
                                class="fa fa-credit-card-alt"></i><span>{{ trans('words.payment_gateway') }}</span></a>
                    </li>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-edit"></i><span>{{ trans('words.pages') }}</span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="{{ classActivePath('pages') }}"><a href="{{ URL::to('admin/pages') }}"
                                    class="{{ classActivePath('pages') }}"><i
                                        class="fa fa-file"></i><span>{{ trans('words.pages') }}</span></a></li>
                            <li class="{{ classActivePath('pages/add') }}"><a
                                    href="{{ URL::to('admin/pages/add') }}"
                                    class="{{ classActivePath('pages') }}"><i
                                        class="fa fa-plus"></i><span>{{ trans('words.add_page') }}</span></a></li>
                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-play-circle"></i><span>{{ trans('words.player_settings') }}</span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="{{ classActivePath('player_settings') }}"><a
                                    href="{{ URL::to('admin/player_settings') }}"
                                    class="{{ classActivePath('player_settings') }}"><i
                                        class="fa fa-cog"></i><span>{{ trans('words.settings') }}</span></a></li>

                            <li class="{{ classActivePath('player_ad_settings') }}"><a
                                    href="{{ URL::to('admin/player_ad_settings') }}"
                                    class="{{ classActivePath('player_ad_settings') }}"><i
                                        class="fa fa-buysellads"></i><span>{{ trans('words.player_ads') }}</span></a>
                            </li>

                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-cog"></i><span>{{ trans('words.settings') }}</span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="{{ classActivePath('general_settings') }}"><a
                                    href="{{ URL::to('admin/general_settings') }}"
                                    class="{{ classActivePath('general_settings') }}"><i
                                        class="fa fa-cog"></i><span>{{ trans('words.general') }}</span></a></li>
                            <li class="{{ classActivePath('email_settings') }}"><a
                                    href="{{ URL::to('admin/email_settings') }}"
                                    class="{{ classActivePath('email_settings') }}"><i
                                        class="fa fa-send"></i><span>{{ trans('words.smtp_email') }}</span></a></li>
                            <li class="{{ classActivePath('social_login_settings') }}"><a
                                    href="{{ URL::to('admin/social_login_settings') }}"
                                    class="{{ classActivePath('social_login_settings') }}"><i
                                        class="fa fa-usb"></i><span>{{ trans('words.social_login') }}</span></a></li>

                            <li class="{{ classActivePath('menu_settings') }}"><a
                                    href="{{ URL::to('admin/menu_settings') }}"
                                    class="{{ classActivePath('menu_settings') }}"><i
                                        class="fa fa-list"></i><span>{{ trans('words.menu') }}</span></a></li>
                            <li class="{{ classActivePath('recaptcha_settings') }}"><a
                                    href="{{ URL::to('admin/recaptcha_settings') }}"
                                    class="{{ classActivePath('recaptcha_settings') }}"><i
                                        class="fa fa-refresh"></i><span> {{ trans('words.reCAPTCHA') }}</span></a>
                            </li>


                            <li class="{{ classActivePath('site_maintenance') }}"><a
                                    href="{{ URL::to('admin/site_maintenance') }}"
                                    class="{{ classActivePath('site_maintenance') }}"><i
                                        class="fa fa-wrench"></i><span>
                                        {{ trans('words.site_maintenance') }}</span></a></li>
                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="fa fa-android"></i>
                            <span>{{ trans('words.android_app') }}</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">

                            @if (env('BUYER_NAME') and env('BUYER_PURCHASE_CODE'))
                                <li class="{{ classActivePath('android_settings') }}">
                                    <a href="{{ URL::to('admin/android_settings') }}"
                                        class="{{ classActivePath('android_settings') }}">
                                        <i class="fa fa-cog"></i>
                                        <span>{{ trans('words.android_app_settings') }}</span>
                                    </a>
                                </li>

                    </li>
<li><a href="{{ URL::to('admin/language') }}"
                        class="waves-effect {{ classActivePath('language') }}"><i
                            class="fa fa-language"></i><span>{{ trans('words.language_text') }}</span></a>
                </li>
                <li><a href="{{ URL::to('admin/genres') }}"
                        class="waves-effect {{ classActivePath('genres') }}"><i
                            class="fa fa-list"></i><span>{{ trans('words.genres_text') }}</span></a></li>
 <li class="{{ classActivePath('chat') }}">
                <a href="{{ URL::to('/messages') }}" class="waves-effect">
                    <i class="fa fa-envelope"></i>
                    <span>Messages</span>
                    {{-- <span class="menu-arrow"></span> --}}
                </a>

            </li>
            @endif
            </ul>
            </li>

            <li class="{{ classActivePath('chat') }}">
                <a href="{{ URL::to('/admin/clear-cache') }}" class="waves-effect">
                    <i class="fa fa-cogs"></i>
                    <span>Clear Cache</span>
                    {{-- <span class="menu-arrow"></span> --}}
                </a>

            </li>
            <li class="{{ classActivePath('slider') }}"><a href="{{ URL::to('admin/slider') }}"
                    class="{{ classActivePath('slider') }}"><i
                        class="fa fa-sliders"></i><span>{{ trans('words.slider') }}</span></a></li>

    <li><a href="{{ URL::to('admin/transactions') }}"
                        class="waves-effect {{ classActivePath('transactions') }}"><i
                            class="fa fa-list"></i><span>{{ trans('words.transactions') }}</span></a></li>

                        @else
            <ul>

                @if (getcong('menu_movies'))
                    <li><a href="{{ URL::to('admin/movies') }}"
                            class="waves-effect {{ classActivePath('movies') }}"><i
                                class="fa fa-video-camera"></i><span>{{ trans('words.movies_text') }}</span></a>
                    </li>
                @endif
                 <li class="{{ classActivePath('sold_licenses') }}"><a
                                    href="{{ URL::to('admin/sold_licenses') }}"
                                    class="{{ classActivePath('sold_licenses') }}"><i class="fa fa-paypal"></i><span>
                                        Sold License</span></a></li>

            @endif

            </ul>
        </div>
    </div>
</div>


<script type="text/javascript">
    @if (Session::has('flash_message'))
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: false
        });

        Toast.fire({
            icon: 'success',
            title: '{{ Session::get('flash_message') }}'
        });
    @endif

    @if (Session::has('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            html: '{{ Session::get('error') }}',
            showConfirmButton: true,
            confirmButtonColor: '#10c469',
            background: "#1a2234",
            color: "#fff"
        });
    @endif
</script>
