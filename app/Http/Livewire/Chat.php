<?php

namespace App\Http\Livewire;

use App\Models\Message;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Cookie;

class Chat extends Component
{
    /**
     * Property toggling chat box.
     *
     * @var boolean
     */
    public bool $popChatUp = false;

    /**
     * Property saving the message string value.
     *
     * @var string
     */
    public string $message = '';

    /**
     * Property static room id.
     *
     * @var int
     */
    public int $roomId;

    /**
     * Get popChatUp cookie value on mount.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->popChatUp = Cookie::get(key: 'popChatUp', default: false);

        $this->roomId = \in_array(auth()->id(), [1, 2])
            ? 1 // static room id for first (Billy) and second (Adrian) users.
            : 2; // static room id for third (Richard) and fourth (John) users
    }

    /**
     * Set popChatUp cookie key on updated.
     * Reload page to retrieve websocket db messages.
     *
     * @param  boolean $value
     * @return void
     */
    public function updatedPopChatUp(bool $value): void
    {
        Cookie::queue('popChatUp', $value, 60);
        if($value){
            $this->dispatchBrowserEvent('reload-page');
        }
    }

    /**
     * Save message into database and dispatch a "send-message-to-chat-server" event.
     *
     * @return void
     */
    public function send(): void
    {
        $message = $this->message;
        $this->message = '';
        $userId = auth()->id();

        Message::create([
            'room_id' => $this->roomId,
            'user_id' => $userId,
            'message' => $message,
        ]);

        $this->dispatchBrowserEvent('send-message-to-chat-server', [
            'user_id' => $userId,
            'room_id' => $this->roomId,
        ]);
    }

    /**
     * Render component blade view.
     *
     * @return View|Factory
     */
    public function render(): View|Factory
    {
        return view('livewire.chat');
    }
}
