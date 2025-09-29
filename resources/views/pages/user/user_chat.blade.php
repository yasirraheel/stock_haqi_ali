@extends('site_app')

@section('head_title', trans('words.profile') . ' | ' . getcong('site_name'))

@section('head_url', Request::url())

@section('content')

    <!-- Start Breadcrumb -->
    <div class="breadcrumb-section bg-xs"
        style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <h2>{{ $page_title }}</h2>
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="{{ URL::to('/') }}" title="{{ trans('words.home') }}">{{ trans('words.home') }}</a>
                            </li>
                            <li><a href="{{ URL::to('/dashboard') }}"
                                    title="{{ trans('words.dashboard_text') }}">{{ trans('words.dashboard_text') }}</a></li>
                            <li>{{ $page_title }}</li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <!-- Start Chat -->
    <div class="chat-area vfx-item-ptb vfx-item-info">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-1 col-md-5 col-sm-12 col-xs-12">
                    <!-- Chat list (hidden) -->
                    <div class="user-list d-none">
                        <ul class="list-group">
                            @foreach ($users as $user)
                                <li class="list-group-item {{ $user->hasUnreadMessages() ? 'font-weight-bold' : '' }}">
                                    <a href="#" class="user-chat" data-user-id="{{ $user->id }}">
                                        {{ $user->name }}
                                        @if ($user->hasUnreadMessages())
                                            <span class="badge badge-danger">New</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="col-lg-11 col-md-7 col-sm-12 col-xs-12">
                    <div class="chat-box" id="chat-box">
                        @include('admin.pages.chat.partials.messages', [
                            'messages' => $messages,
                        ])
                    </div>

                    <form action="{{ route('chat.store') }}" method="POST" class="row mt-3" id="chat-form">
                        @csrf
                        <div class="col-12">
                            <input type="hidden" name="user_id" value="">
                            <textarea name="messages" id="messages" class="form-control mb-2"></textarea>
                        </div>
                        <div class="col-12 d-flex">
                            <button type="submit" class="btn btn-primary me-4">Send</button>
                            <button type="button" class="btn btn-secondary" id="refresh-button">Refresh</button>
                        </div>
                    </form>



                </div>
            </div>
        </div>
    </div>
    <!-- End Chat -->

    <script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.user-chat').on('click', function(e) {
                e.preventDefault();
                var userId = $(this).data('user-id');

                // Set the user_id in the hidden input field
                $('input[name="user_id"]').val(userId);

                // Show the chat form
                $('#chat-form').show();

                $.ajax({
                    url: '/chat/messages/' + userId,
                    method: 'GET',
                    success: function(data) {
                        $('#chat-box').html(data);
                    }
                });
            });

            // Automatically load messages for the logged-in user
            var currentUserId = {{ Auth::id() }};
            $('.user-chat[data-user-id="' + currentUserId + '"]').trigger('click');

            // Reload the page when the refresh button is clicked
            $('#refresh-button').on('click', function() {
                location.reload();
            });
        });
    </script>

@endsection
