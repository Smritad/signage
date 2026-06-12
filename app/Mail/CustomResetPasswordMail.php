<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\CustomUser;

class CustomResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;
    public $userName;

    public function __construct($token, $email)
    {
        $this->token    = $token;
        $this->email    = $email;

        // ✅ Fetch user name from DB
        $user           = CustomUser::where('email', $email)->first();
       $this->userName = $user?->name ?: 'User';
    }

    public function build()
    {
        return $this->subject('Reset Your Password')
                    ->view('frontend.reset_password');
    }
}