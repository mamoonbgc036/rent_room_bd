<div>
    <div class="container mt-4">
        <h2 class="mb-4">Your Messages</h2>

        <ul class="list-group">
            @foreach($messages as $message)
                <li class="list-group-item d-flex justify-content-between align-items-center mb-2 {{ $message->is_read ? 'bg-light text-muted' : 'bg-dark text-light' }}">
                    <div>
                        <strong>{{ $message->title }}</strong>
                        <small class="text-muted d-block">
                            <!-- Display the first 20 characters of the message -->
                            {{ Str::limit($message->message, 20, '...') }}
                        </small>
                        <small class="text-muted d-block">{{ $message->created_at->diffForHumans() }}</small>
                    </div>

                    <button class="btn btn-primary" wire:click="showMessage({{ $message->id }})">
                        {{ $message->is_read ? 'View' : 'Unread' }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Modal for displaying the full message -->
    @if($showModal && $selectedMessage)
        <div class="modal show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Message from {{ $selectedMessage->sender->name }}</h5>
                        <button type="button" class="close" wire:click="$set('showModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Message:</strong> {{ $selectedMessage->message }}</p>
                        <small>Sent on: {{ $selectedMessage->created_at->format('d M, Y H:i') }}</small>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showModal', false)">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Add CSS to make modal more professional -->
    <style>
        .modal-dialog {
            margin-top: 10%;
        }
        .close {
            font-size: 1.5rem;
        }
    </style>
</div>
