@extends('site_app')

@section('head_title', 'Chat | ' . getcong('site_name'))

@section('head_url', Request::url())

@section('content')

    <!-- Start Breadcrumb -->
    <div class="breadcrumb-section bg-xs"
        style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <h2>Chat</h2>
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="{{ URL::to('/') }}" title="Home">Home</a></li>
                            <li><a href="{{ URL::to('/dashboard') }}" title="Dashboard">Dashboard</a></li>
                            <li>Chat</li>
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
                <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 offset-lg-2 offset-md-0">
                    <div class="chat-box" id="chat-box">
                        @foreach ($messages as $message)
                            <div class="message {{ $message->user_id == Auth::id() ? 'user-message' : 'admin-message' }}">
                                <strong>{{ $message->user->name }}</strong>: {{ $message->message }}
                            </div>
                        @endforeach
                    </div>
                    <form action="{{ route('chat.store') }}" method="POST" class="row">
                        @csrf
                        <div class="col-12">
                            <textarea id="message" name="message" class="form-control" rows="3" placeholder="Type your message..."></textarea>
                            <button type="submit" class="btn btn-primary mt-2">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Chat -->

@endsection
