<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Queue\SerializesModels;

class NotificationCreated
{
    use SerializesModels;

    public $notification;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }
}
