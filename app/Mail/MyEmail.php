<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $type;

    public function __construct($type,$data)
    {
        $this->data = $data;
        $this->type=$type;
    }

    public function build()
    {
        if($this->type == 'otp')
        {
            return $this->view('emails.otp_email')->subject('Mussoorie Registration OTP');
        }
        else{
            return $this->view('emails.tour_created_email')->subject('Mussoorie Tour Created');
        }
        
    }
}