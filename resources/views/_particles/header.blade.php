<!-- Start Header -->
<header>
    <!-- Start Navigation Area -->
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
    <div class="main-menu">
      <nav class="header-section pin-style">
        <div class="container-fluid">
          <div class="mod-menu">
            <div class="row">
              <div class="col-2">
                @if(getcong('site_logo'))
                  <a href="{{ URL::to('/') }}" title="logo" class="logo"><img src="{{ URL::asset('/'.getcong('site_logo')) }}" alt="logo" title="logo"></a>
                @else
                  <a href="{{ URL::to('/') }}" title="logo" class="logo"><img src="{{ URL::asset('site_assets/images/logo.png') }}" alt="logo" title="logo"></a>
                @endif

              </div>
              <div class="col-7 nav-order-last nopadding">
                <div class="main-nav leftnav">
                  <ul class="top-nav">
                    <li class="visible-this d-md-none menu-icon"> <a href="#" class="navbar-toggle collapsed" data-bs-toggle="collapse" data-bs-target="#menu" aria-expanded="false" title="menu-toggle"><i class="fa fa-bars"></i></a> </li>
                  </ul>
                  <div id="menu" class="collapse header-menu">
                    <ul class="nav vfx-item-nav">
                      <li><a href="{{ URL::to('/') }}" class="{{classActivePathSite('')}}" title="home">Home</a></li>

                      @if(getcong('menu_movies'))
                      <li><a href="{{ URL::to('movies/') }}" class="{{classActivePathSite('movies')}}" title="{{trans('words.movies_text')}}">{{trans('words.movies_text')}}</a></li>
                      @endif

                      @if(getcong('menu_photos'))
                      <li><a href="{{ URL::to('photos/') }}" class="{{classActivePathSite('photos')}}" title="Photos">Photos</a></li>
                      @endif

                      @if(getcong('menu_audio'))
                      <li><a href="{{ URL::to('audio/') }}" class="{{classActivePathSite('audio')}}" title="Audio">Audio</a></li>
                      @endif

                      @if(getcong('menu_livetv'))
                      <li><a href="{{ URL::to('livetv') }}" class="{{classActivePathSite('livetv')}}" title="{{trans('words.live_tv')}}">{{trans('words.live_tv')}}</a> <span class="arrow"></span>
                        <ul class="dm-align-2 mega-list">
                          @foreach(\App\TvCategory::where('status','1')->orderBy('category_name')->get() as $tv_cat)
                            <li><a href="{{ URL::to('livetv/?cat_id='.$tv_cat->id) }}" title="{{$tv_cat->category_name}}">{{$tv_cat->category_name}}</a></li>
                          @endforeach
                        </ul>
                      </li>
                      @endif

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
              <a href="{{ URL::to('/') }}" title="subscribe"><img src="{{ URL::asset('site_assets/images/ic-subscribe2.png') }}" alt="ic-subscribe" title="Stumble"></a>
            </div>
            @if(Auth::check())

            <div class="user-menu">
              <div class="user-name">
                <span>
                  @if(Auth::User()->user_image AND file_exists(public_path('upload/'.Auth::User()->user_image)))
                    <img src="{{ URL::asset('upload/'.Auth::User()->user_image) }}" alt="profile_img" title="{{Auth::User()->name,6}}" id="userPic">
                  @else
                      <img src="{{ URL::asset('site_assets/images/user-avatar.png') }}" alt="profile_img" title="{{Auth::User()->name,6}}" id="userPic">
                  @endif

                </span>
                {{ Str::limit(Auth::User()->name,6)}}<i class="fa fa-angle-down" id="userArrow"></i>
              </div>

              @if(Auth::User()->usertype =="Admin")

              <ul class="content-user">
                <li><a href="{{ URL::to('admin/dashboard') }}" title="{{trans('words.dashboard_text')}}"><i class="fa fa-database"></i>{{trans('words.dashboard_text')}}</a></li>
                <li><a href="{{ URL::to('profile') }}" title="{{trans('words.profile')}}"><i class="fa fa-user"></i>{{trans('words.profile')}}</a></li>
                <li><a href="{{ URL::to('messages') }}" title="Contact"><i class="fa fa-envelope"></i>Messages</a></li>
                <li><a href="{{ URL::to('admin/logout') }}" title="{{trans('words.logout')}}"><i class="fa fa-sign-out-alt"></i>{{trans('words.logout')}}</a></li>
              </ul>

              @else

              <ul class="content-user">
                <li><a href="{{ URL::to('dashboard') }}" title="{{trans('words.dashboard_text')}}"><i class="fa fa-database"></i>{{trans('words.dashboard_text')}}</a></li>
                <li><a href="{{ URL::to('profile') }}" title="{{trans('words.profile')}}"><i class="fa fa-user"></i>{{trans('words.profile')}}</a></li>
                   <li><a href="{{ URL::to('messages') }}" title="Contact"><i class="fa fa-envelope"></i>Contact</a></li>
                <li><a href="{{ URL::to('watchlist') }}" title="{{trans('words.my_watchlist')}}"><i class="fa fa-list"></i>{{trans('words.my_watchlist')}}</a></li>
                <li><a href="{{ URL::to('logout') }}" title="{{trans('words.logout')}}"><i class="fa fa-sign-out-alt"></i>{{trans('words.logout')}}</a></li>
              </ul>
              @endif


            </div>

            @else
            <div class="signup-btn-item">
              <a href="{{ URL::to('login') }}" title="login"><img src="{{ URL::asset('site_assets/images/ic-signup-user.png') }}" alt="ic-signup-user" title="signup-user"><span>{{trans('words.login_text')}}</span></a>
            @endif
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
