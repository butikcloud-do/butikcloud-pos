<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserVerified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $verificationType; // 'email' or 'mobile'

    /**
     * Create a new event instance.
     */
    public function __construct($user, $verificationType)
    {
        $this->user = $user;
        $this->verificationType = $verificationType;
    }
}
