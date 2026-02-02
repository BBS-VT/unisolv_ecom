<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FulfillmentNotificationMail extends Mailable
{
    public $order;
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        $company = $this->order->company;

        // Get global fulfillment mailbox(es)
        $globalMailbox = $company->getSetting('fulfillment_mailbox');
        $globalEmails = $this->parseEmailList($globalMailbox);

        // Determine primary recipient
        $to = [];
        $cc = [];

        // If multi-location is enabled and order has a location
        if ($company->getSetting('multi_location_enabled') && $this->order->sales_location_id) {
            $location = $this->order->items->location;

            if ($location && $location->fulfillment_email) {
                // Location-specific email is primary
                $to = [$location->fulfillment_email];
                // Global mailbox becomes CC
                $cc = $globalEmails;
            } else {
                // No location email, use global as primary
                $to = $globalEmails;
            }

        } else {

            // Single location or no location specified, use global
            $to = $globalEmails;
        }

        // Build the email
        $mail = $this->subject('New Order #' . $this->order->id . ' - Fulfillment Required')
            ->view('emails.orders.fulfillment');

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

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    /*public function envelope()
    {
        return new Envelope(
            subject: 'Fulfillment Notification Mail',
        );
    }*/

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    /*public function content()
    {
        return new Content(
            view: 'emails.fulfillment_notification',
        );
    }*/

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    /*public function attachments()
    {
        return [];
    }*/
}
