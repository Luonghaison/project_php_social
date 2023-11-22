<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp_code;

    public function __construct($otp_code)
    {
        $this->otp_code = $otp_code;
    }

    public function build()
    {
        return $this->markdown('verify')
            ->subject('【ジョブチャット】ご登録について');
    }

    }
