<?php

namespace App\Customs\Services;

use App\Models\EmailVerificationToken;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class EmailVerificationservice
{
     /**
     * Send verification link to user
     */
    public function sendVerificationLink(object $user): void
    {
        Notification::send($user, new EmailVerificationNotification($this->generateVerificationLink($user->email)));
    }

    /**
     * Generate verification link
     */
   // public function generateVerificationLink(string $email):string
     public function generateVerificationLink(string $email)
     {
         $checkIfTokenExists = EmailVerificationToken::where('email', $email)->first();
            if ($checkIfTokenExists) $checkIfTokenExists->delete();
            $token = Str::uuid();
            $url = config('app.url'). "?token=".$token . "&email=".$email;
            $saveToken = EmailverificationToken::create([
                'email' => $email,
                'token' => $token,
                'expired_at' => now()->addMinutes(60),
            ]);
            if($saveToken){
                return $url;
            }
         

        //  if ($checkIfTokenExists) {
        //      $checkIfTokenExists->delete();
        //  }
     }
}