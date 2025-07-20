<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class OrderIssueReported extends Notification
{
    public $order;
    public $message;

    public function __construct($order, $message)
    {
        $this->order = $order;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'message' => "Issue reported for Order #{$this->order->id}: {$this->message}"
        ];
    }
}
