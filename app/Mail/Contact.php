<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use stdClass;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    public stdClass $data;

    /**
     * Create a new message instance.
     *
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'))
            ->view('mails.template')
            ->text('mails.plain_template');
    }
}
