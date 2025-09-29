@extends('admin.admin_app')

@section('content')
    <style type="text/css">
        .iframe-container {
            overflow: hidden;
            padding-top: 56.25% !important;
            position: relative;
        }

        .iframe-container iframe {
            border: 0;
            height: 100%;
            left: 0;
            position: absolute;
            top: 0;
            width: 100%;
        }
    </style>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">
                            {{-- <div class="row">
                                <div class="col-sm-6">
                                    <form action="{{ url('admin/generateScreenshot') }}" method="post">
                                        @csrf
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Google Drive URL</label>
                                            <div class="col-sm-8">
                                                <input type="url" name="video_url" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="offset-sm-3 col-sm-9 pl-1">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                                    Generate Screenshot
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div> --}}
                            <br>
                            <hr>

                            <!-- Thumbnails -->
                            <div class="row">
                                @foreach ($thumnails as $i => $thumnail)
                                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6" id="card_box_id_{{ $thumnail->id }}">
                                        <div class="card m-b-20">
                                            <div class="wall-list-item">
                                                @if (isset($thumnail->video_image_thumb))
                                                    <img class="card-img-top thumb-xs img-fluid"
                                                        src="{{ url($thumnail->video_image_thumb) }}"
                                                        alt="{{ stripslashes($thumnail->file_id) }}">
                                                @else
                                                    <img class="card-img-top thumb-xs img-fluid"
                                                        src="{{ asset('path/to/default/image.jpg') }}">
                                                @endif
                                            </div>
                                            @php

                                                App\Movies::where('file_id', $thumnail->file_id)->update([
                                                    'video_image_thumb' => $thumnail->video_image_thumb,
                                                ]);
                                            @endphp
                                            <div class="card-body p-3">
                                                <h4 class="card-title book_title mb-3 d-flex align-items-center">
                                                    {{ stripslashes($thumnail->file_id) }}
                                                </h4>
                                                <div class="d-flex align-items-center">
                                                    <!-- Generate Screenshot Form -->
                                                    <form action="{{ url('admin/generateScreenshot') }}" method="post"
                                                        class="m-0">
                                                        @csrf
                                                        <input type="hidden" name="file_id"
                                                            value="{{ $thumnail->file_id }}">
                                                        <button
                                                            class="btn btn-icon waves-effect waves-light btn-success m-r-5"
                                                            data-toggle="tooltip" title="Re Generate Screenshot">
                                                            <i class="fa fa-refresh"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Play Button -->
                                                    <a href="javascript:void(0);"
                                                        class="btn btn-icon waves-effect waves-light btn-success m-r-5 play-video"
                                                        data-toggle="modal" data-target="#videoModal" title="Preview Image"
                                                        data-image-url="{{ url($thumnail->video_image_thumb) }}">
                                                        <i class="fa fa-play"></i>
                                                    </a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach

                                <!-- Video Modal -->
                                <div class="modal fade" id="videoModal" tabindex="-1" role="dialog"
                                    aria-labelledby="videoModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="videoModalLabel">Image Preview</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <img id="modal-image" src="" alt="Image Preview" class="img-fluid">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                            <!-- JavaScript -->
                            <script>
                                $(document).ready(function() {
                                    $('.play-video').on('click', function() {
                                        var imageUrl = $(this).data('image-url');

                                        // Check if image URL is valid
                                        if (imageUrl) {
                                            console.log("Image URL:", imageUrl); // Log for debugging
                                            $('#modal-image').attr('src', imageUrl);
                                        } else {
                                            console.log("No image URL provided.");
                                            $('#modal-image').attr('src', '{{ asset('path/to/default/image.jpg') }}');
                                        }
                                    });
                                });
                            </script>
                            <style>
                                .card {
                                    width: 100%;
                                    /* Adjust as necessary */
                                    max-width: 250px;
                                    /* Control the maximum width */
                                    height: auto;
                                }

                                .wall-list-item {
                                    position: relative;
                                    overflow: hidden;
                                    width: 100%;
                                    padding-top: 56.25%;
                                    /* This maintains a 16:9 aspect ratio */
                                }

                                .wall-list-item img {
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    width: 100%;
                                    height: 100%;
                                    object-fit: cover;
                                    /* Ensures the image covers the area without distortion */
                                }
                            </style>
                            <!-- Pagination -->
                            <nav class="paging_simple_numbers">
                                @include('admin.pagination', ['paginator' => $thumnails])
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            @include('admin.copyright')

        </div>


        <!-- SweetAlert -->
        <script type="text/javascript">
            @if (Session::has('flash_message'))
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: false,
                });

                Toast.fire({
                    icon: 'success',
                    title: '{{ Session::get('flash_message') }}'
                });
            @endif

            @if (count($errors) > 0)
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: '<p>@foreach ($errors->all() as $error) {{ $error }}<br/> @endforeach</p>',
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background: "#1a2234",
                    color: "#fff"
                });
            @endif
        </script>
    </div>
@endsection
