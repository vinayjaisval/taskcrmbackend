<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MyMail extends Mailable
{
    public function build()
    {
        return $this->subject('Daily Task Report')
            ->view('report_mail')
            ->with([
                'message' => 'This is the email message content.',
            ]);
    }
}
