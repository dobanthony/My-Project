<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductDeletedByAdmin extends Notification
{
    use Queueable;

    protected $productName;
    protected $reason;
    protected $productId;

    public function __construct($productName, $reason, $productId)
    {
        $this->productName = $productName;
        $this->reason = $reason;
        $this->productId = $productId;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Your product \"{$this->productName}\" was deleted by an admin. Reason: {$this->reason}",
            'product_id' => $this->productId,
        ];
    }
}
