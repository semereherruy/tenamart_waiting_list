<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array */
    public $stats;

    /**
     * Create a new message instance.
     */
    public function __construct(array $stats)
    {
        $this->stats = $stats;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->subject('Weekly Waiting List Report')
            ->markdown('emails.weekly_report')
            ->with('stats', $this->stats);
    }
}
