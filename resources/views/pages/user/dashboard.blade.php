@extends('site_app')

@section('head_title', trans('words.dashboard_text') . ' | ' . getcong('site_name'))

@section('head_url', Request::url())

@section('content')

    <!-- Start Breadcrumb -->
    <div class="breadcrumb-section bg-xs"
        style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <h2>{{ trans('words.dashboard_text') }}</h2>
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="{{ URL::to('/') }}" title="{{ trans('words.home') }}">{{ trans('words.home') }}</a>
                            </li>
                            <li>{{ trans('words.dashboard_text') }}</li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <!-- Start Dashboard Page -->
    <div class="vfx-item-ptb vfx-item-info">
        <div class="container-fluid">
            <div class="row">
                <!-- Profile Section -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="profile-section">
                        <div class="row">
                            <!-- Profile Picture & Info -->
                            <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                                <div class="img-profile">
                                    @if (Auth::User()->user_image)
                                        <img src="{{ URL::asset('upload/' . Auth::User()->user_image) }}"
                                            class="img-rounded" alt="profile pic" title="profile pic">
                                    @else
                                        <img src="{{ URL::asset('site_assets/images/user-avatar.png') }}"
                                            class="img-rounded" alt="profile_img" title="profile pic">
                                    @endif
                                </div>
                                <div class="profile_title_item">
                                    <h5>{{ Auth::User()->name }}</h5>
                                    <p>{{ Auth::User()->email }}</p>
                                    <a href="{{ URL::to('profile') }}" class="vfx-item-btn-danger text-uppercase"><i
                                            class="fa fa-edit"></i>{{ trans('words.edit') }}</a><br><br>
                                    <a href="#" class="vfx-item-btn-danger text-uppercase data_remove"><i
                                            class="fa fa-trash"></i>Account Delete</a>
                                </div>
                            </div>

                            <!-- Add/Upload Video, Licenses, and Stats -->
                            <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
                                <div class="row">
                                    <!-- Add/Upload Video -->
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="member-ship-option">
                                            <h5 class="color-up">Add/Upload Video</h5>
                                            <div class="mt-3">
                                                <a href="{{ URL::to('admin/movies/add_movie') }}"
                                                    class="vfx-item-btn-danger text-uppercase"
                                                    style="display: block; width: 100%; text-align: center; padding: 12px; box-sizing: border-box; text-decoration: none;">
                                                    Add Video
                                                </a>
                                            </div>
                                        </div>
                                    </div>




                                    <!-- Stats Section -->
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="member-ship-option">
                                            <h5 class="color-up">Stats</h5>
                                            <span class="premuim-memplan-bold-text">
                                                <strong>Joined Since:</strong>
                                                {{ date('F, d, Y', strtotime(Auth::User()->created_at)) }}
                                            </span><br>
                                            <span class="premuim-memplan-bold-text">
                                                <strong>Total Films:</strong>
                                                {{ \App\Movies::where('added_by', Auth::user()->id)->count() }}
                                            </span><br>

                                        </div>
                                    </div>
                                    <!-- My Licenses -->
                                    @php
                                        $currency_code = getcong('currency_code') ? getcong('currency_code') : 'USD';
                                    @endphp
                                    <div class="col-lg-12 col-md-6 col-sm-12 mt-10">
                                        <div class="member-ship-option">
                                            <h5 class="color-up" style="color: white;">My Licenses</h5>
                                            @if ($licenses->count() > 0)
                                                <table class="table table-bordered" style="color: white;">
                                                    <thead>
                                                        <tr>
                                                            <th style="color: white; text-align: center;">Movie Title</th>
                                                            <th style="color: white; text-align: center;">License Price</th>
                                                            {{-- <th style="color: white; text-align: center;">{{ trans('words.payment_gateway') }}</th> --}}
                                                            {{-- <th style="color: white; text-align: center;">{{ trans('words.payment_id') }}</th> --}}
                                                            <th style="color: white; text-align: center;">
                                                                {{ trans('words.date') }}</th>
                                                            <th style="color: white; text-align: center;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($licenses as $license)
                                                            <tr>
                                                                <td class="truncate">
                                                                    {{ \App\Movies::find($license->video_id)->video_title }}
                                                                </td>

                                                                <td>{{ $license->license_price }}
                                                                    {{ $currency_code }}({{ $license->gateway }})</td>
                                                                {{-- <td></td> --}}
                                                                {{-- <td>{{ $license->payment_id }}</td> --}}
                                                                <td>{{ date('F d, Y', strtotime($license->created_at)) }}
                                                                </td>
                                                                <td style="text-align: center;">
                                                                    <a href="{{ route('download.license', ['license_key' => $license->license_key]) }}"
                                                                        class="vfx-item-btn-danger text-uppercase mb-30"
                                                                        title="download license">Download License</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p style="color: white;">License not Found</p>
                                            @endif
                                        </div>
                                        <style>
                                            .truncate {
                                                white-space: nowrap;
                                                overflow: hidden;
                                                text-overflow: ellipsis;
                                                max-width: 150px;
                                                /* Adjust the width as needed */
                                            }
                                        </style>
                                    </div>
                                    @php
                                        $currency_code = getcong('currency_code') ? getcong('currency_code') : 'USD';
                                    @endphp
                                    <div class="col-lg-12 col-md-6 col-sm-12 mt-10">
                                        <div class="member-ship-option">
                                            <h5 class="color-up" style="color: white;">Sold Licenses</h5>

                                            <!-- Dashboard summary section -->
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <div class="card bg-primary text-white p-4 shadow">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Total Sold Amount</h6>
                                                            <p class="fs-3 fw-bold">{{ number_format($soldLicenses->sum('license_price'), 2) }} {{ $currency_code }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="card bg-warning text-white p-4 shadow">
                                                        <div class="card-body">
                                                            <h6 class="card-title">You Earned</h6>
                                                            <p class="fs-3 fw-bold">{{ number_format($soldLicenses->sum('amount_paid_to_author'), 2) }} {{ $currency_code }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Sold licenses table -->
                                            @if ($soldLicenses->count() > 0)
                                                <table class="table table-bordered table-responsive" style="color: white;">
                                                    <thead>
                                                        <tr>
                                                            <th style="color: white; text-align: center;">Movie Title</th>
                                                            <th style="color: white; text-align: center;">License Price</th>
                                                            <th style="color: white; text-align: center;">Admin Fee</th>
                                                            <th style="color: white; text-align: center;">You Earned</th>
                                                            <th style="color: white; text-align: center;">{{ trans('words.date') }}</th>
                                                            <th style="color: white; text-align: center;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($soldLicenses as $license)
                                                            <tr>
                                                                <td class="truncate">
                                                                    {{ \App\Movies::find($license->video_id)->video_title }}
                                                                </td>

                                                                <td>{{ $license->license_price }} {{ $currency_code }} ({{ $license->gateway }})</td>
                                                                <td>{{ $license->admin_commission }} {{ $currency_code }} ({{ $license->gateway }})</td>
                                                                <td>{{ $license->amount_paid_to_author }} {{ $currency_code }} ({{ $license->gateway }})</td>

                                                                <td>{{ date('F d, Y', strtotime($license->created_at)) }}</td>

                                                                <td style="text-align: center;">
                                                                    <a href="{{ route('download.license', ['license_key' => $license->license_key]) }}"
                                                                        class="vfx-item-btn-danger text-uppercase mb-30" title="download license">
                                                                        Download License
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p style="color: white;">License not Found</p>
                                            @endif
                                        </div>

                                        <style>
                                            .truncate {
                                                white-space: nowrap;
                                                overflow: hidden;
                                                text-overflow: ellipsis;
                                                max-width: 150px;
                                            }
                                            .card {
                                                border-radius: 0.75rem;
                                            }
                                            .fs-20 {
                                                font-size: 20px;
                                            }
                                            .fw-bold {
                                                font-weight: bold;
                                            }
                                        </style>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Watch History Section -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mt-4">
                    <div class="table-wrapper">
                        <div class="vfx-item-section">
                            <h3>Watch History</h3>
                        </div>

                        <div class="recently-watched-video-carousel owl-carousel">
                            @foreach ($recently_watched as $i => $watched_videos)
                                <div class="single-video">
                                    @php
                                        $info = recently_watched_info(
                                            $watched_videos->video_type,
                                            $watched_videos->video_id,
                                        );
                                    @endphp
                                    @if ($info)
                                        <a href="{{ URL::to($watched_videos->video_type == 'Movies' ? 'movies/details/' . $info->video_slug . '/' . $info->id : 'shows/details/' . $info->video_slug . '/' . $info->id) }}"
                                            title="{{ $info->video_title }}">
                                            <div class="video-img">
                                                <span class="video-item-content">{{ $info->video_title }}</span>
                                                <img src="{{ URL::to('/' . $info->video_image) }}"
                                                    alt="{{ $info->video_title }}"
                                                    title="{{ $watched_videos->video_type }}-{{ $info->video_title }}">
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination Links -->
                        <div class="pagination-wrapper mt-4">
                            {{ $licenses->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- End Dashboard Page -->

        {{-- @include('pages.user.logged_in_device_list') --}}

        <script src="{{ URL::asset('site_assets/js/jquery-3.3.1.min.js') }}"></script>

        <script type="text/javascript">
            @if (Session::has('flash_message'))

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
                    title: '{{ Session::get('
                                                                                                                            flash_message ') }}'
                })
            @endif

            @if (Session::has('success'))

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
                    title: '{{ Session::get('
                                                                                                                            success ') }}'
                })
            @endif

            @if (Session::has('error_flash_message'))

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
                    icon: 'error',
                    title: '{{ Session::get('
                                                                                                                            error_flash_message ') }}'
                })
            @endif


            $(".data_remove").click(function() {

                var post_id = $(this).data("id");

                Swal.fire({
                    title: '{{ trans('
                                                                                                                              words.dlt_warning ') }}',
                    text: "{{ trans('words.user_dlt_confirm') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ trans('
                                                                                                                              words.dlt_confirm ') }}',
                    cancelButtonText: "{{ trans('words.btn_cancel') }}",
                    background: "#1a2234",
                    color: "#fff"

                }).then((result) => {

                    //alert(post_id);

                    //alert(JSON.stringify(result));

                    if (result.isConfirmed) {

                        $.ajax({
                            type: 'get',
                            url: "{{ URL::to('account_delete') }}",
                            dataType: 'json',
                            data: {
                                id: ''
                            },
                            success: function(res) {

                                if (res.status == '1') {

                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: '{{ trans('
                                                                                                                                                                                                                                                                words.deleted ') }}!',
                                        text: '{{ trans('
                                                                                                                                                                                                                                                                words.user_dlt_success ') }}',
                                        showConfirmButton: true,
                                        confirmButtonColor: '#10c469',
                                        background: "#1a2234",
                                        color: "#fff"
                                    }).then(function() {
                                        window.location = "{{ URL::to('/') }}";
                                    });

                                } else {
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'error',
                                        title: 'Something went wrong!',
                                        showConfirmButton: true,
                                        confirmButtonColor: '#10c469',
                                        background: "#1a2234",
                                        color: "#fff"
                                    })
                                }

                            }
                        });
                    }

                })

            });
        </script>

    @endsection
