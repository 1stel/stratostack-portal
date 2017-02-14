<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VMPasswordChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $vm;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($vm)
    {
        $this->vm = $vm;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Instance Password Reset')->view('emails.vmPasswordReset');
    }
}
