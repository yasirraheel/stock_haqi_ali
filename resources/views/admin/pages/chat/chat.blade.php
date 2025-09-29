@extends('admin.admin_app')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <!-- Start Chat -->
                        <div class="chat-area vfx-item-ptb vfx-item-info">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
                                        <div class="user-list">
                                            <ul class="list-group">
                                                @foreach ($users as $user)
                                                    <li class="list-group-item {{ $user->id == session('user_id') ? 'active' : '' }}">
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

                                    <div class="col-lg-8 col-md-7 col-sm-12 col-xs-12">
                                        <div class="chat-box" id="chat-box">
                                            @include('admin.pages.chat.partials.messages', [
                                                'messages' => $messages,
                                            ])
                                        </div>

                                        <form action="{{ route('chat.store') }}" method="POST" class="row mt-3" id="chat-form" style="display: none;">
                                            @csrf
                                            <div class="col-12">
                                                <input type="hidden" name="user_id" value="">
                                                <textarea name="messages" id="messages" class="form-control"></textarea>
                                                <button type="submit" class="btn btn-primary mt-2">Send</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Chat -->

                        <nav class="paging_simple_numbers">
                            {{-- {{ $emails->links() }} --}}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.copyright')
</div>

<!-- Inline CSS -->
<style>
    .list-group-item.active {
        background-color: #ff4d00; /* Matches the Send button color */
        color: white; /* Text color for the active chat item */
    }

    .message {
        color: white; /* Default color for all messages */
        padding: 10px;
        margin-bottom: 5px;
        border-radius: 5px;
    }

    .admin-message {
        background-color: #007bff; /* Background color for admin messages */
    }

    .user-message {
        background-color: #6c757d; /* Background color for user messages */
    }
</style>

<!-- Inline JavaScript -->
<script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.user-chat').on('click', function(e) {
            e.preventDefault();
            var userId = $(this).data('user-id');

            // Remove active class from all users
            $('.user-chat').closest('li').removeClass('active');

            // Add active class to the clicked user
            $(this).closest('li').addClass('active');

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

        // Automatically load messages for the selected user if user_id is in session
        var selectedUserId = @json(session('user_id'));
        if (selectedUserId) {
            $('.user-chat[data-user-id="' + selectedUserId + '"]').trigger('click');
            // Remove user_id from session
            @php
                session()->forget('user_id');
            @endphp
        }
    });
</script>
@endsection
