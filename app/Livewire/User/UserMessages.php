<?php

namespace App\Livewire\User;

use App\Models\Message;
use Livewire\Component;

class UserMessages extends Component
{
    public $messages;
    public $selectedMessage;
    public $showModal = false;

    public function mount()
    {
        // Fetch unread messages initially
        $this->messages = Message::where('recipient_id', auth()->id())
                                  ->orderBy('created_at', 'desc')
                                  ->get();
    }

    public function markAsRead($messageId)
    {
        // Find the message
        $message = Message::where('id', $messageId)
                          ->where('recipient_id', auth()->id()) // Use recipient_id
                          ->first();
    
        // Mark as read
        if ($message && !$message->is_read) {
            $message->is_read = true;
            $message->save();
        }
    
        // Refresh the message list
        $this->mount();
    }

    public function showMessage($messageId)
    {
        // Find the message
        $this->selectedMessage = Message::find($messageId);

        // Show the modal
        $this->showModal = true;

        // Mark the message as read
        $this->markAsRead($messageId);
    }

    public function render()
    {
        return view('livewire.user.user-messages');
    }
}
