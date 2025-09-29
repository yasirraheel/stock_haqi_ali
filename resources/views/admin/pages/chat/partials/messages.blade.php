<!-- resources/views/admin/pages/chat/partials/messages.blade.php -->
@foreach ($messages as $message)
    <div class="message {{ $message->sender == 'admin' ? 'admin-message' : 'user-message' }}">
        <strong>{{ $message->sender == 'admin' ? 'Admin' : $message->user->name }}</strong>:
        {{ $message->message }}
        <small class="message-time">{{ $message->created_at->diffForHumans() }}</small>
    </div>
@endforeach



<style>
    /* Add this to your CSS file or within a <style> block */
/* Add this to your CSS file or within a <style> block */
    .message {
    color: white; /* Default color for all messages */
    padding: 10px;
    margin-bottom: 5px;
    border-radius: 5px;
    position: relative;
}

.admin-message {
    background-color: #007bff; /* Background color for admin messages */
}

.user-message {
    background-color: #6c757d; /* Background color for user messages */
}

.message-time {
    display: block;
    font-size: 0.8em;
    color: #ddd;
    margin-top: 5px;
    text-align: right;
}

</style>
