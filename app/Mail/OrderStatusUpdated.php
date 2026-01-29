<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $notes;

    public function __construct(Order $order, $notes = null)
    {
        $this->order = $order;
        $this->notes = $notes;
    }

    public function build()
    {
        $statusName = $this->order->orderstatus->name;
        $subject = "Order #{$this->order->OrderNumber} - {$statusName}";

        return $this->subject($subject)
            ->view('emails.order-status-updated');
    }
}
