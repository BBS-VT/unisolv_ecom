<?php

namespace App\Mail;

use App\Models\Location;
use App\Models\Order;
use App\Models\SalesLocation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FulfillmentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $itemsByLocation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;

        // Group items by location for the email view
        $this->itemsByLocation = $order->items()
            ->with('product')
            ->get()
            ->groupBy('LocationCode');
    }

    public function build()
    {
        $company = $this->order->company;

        // Get global fulfillment mailbox(es)
        $globalMailbox = $company->getSetting('fulfillment_mailbox');
        $globalEmails = $this->parseEmailList($globalMailbox);

        // Collect all unique locations from order items
        $locationCodes = $this->order->items()->distinct('LocationCode')->pluck('LocationCode');

        $to = [];
        $cc = [];

        // If multi-location is enabled and we have location codes
        if ($company->getSetting('sales_locations') && $locationCodes->isNotEmpty()) {

            // Get all locations with fulfillment emails
            $locationsWithEmails = Location::whereIn('LocationCode', $locationCodes)
                ->whereNotNull('fulfillment_email')
                ->where('fulfillment_email', '!=', '')
                ->get();

            // Strategy: Send TO all location-specific emails, CC global
            if ($locationsWithEmails->isNotEmpty()) {
                // Primary recipients: all location fulfillment emails
                $to = $locationsWithEmails->pluck('fulfillment_email')->toArray();

                // CC the global mailbox for oversight
                $cc = $globalEmails;
            } else {
                // No location-specific emails, use global as primary
                $to = $globalEmails;
            }
        } else {
            // Multi-location not enabled or no locations, use global
            $to = $globalEmails;
        }

        // Build the email
        $mail = $this->subject('New Order #' . $this->order->OrderNumber . ' - Fulfillment Required')
            ->view('emails.fulfillment_notification');

        // Set recipients
        if (!empty($to)) {
            $mail->to($to);
        }

        if (!empty($cc)) {
            $mail->cc($cc);
        }

        return $mail;
    }

    /**
     * Parse comma-separated email list into array
     */
    protected function parseEmailList($emailString)
    {
        if (empty($emailString)) {
            return [];
        }

        return array_filter(
            array_map('trim', explode(',', $emailString)),
            function($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            }
        );
    }
}
