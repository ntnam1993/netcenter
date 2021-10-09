<?php


namespace App\Utils;

use Illuminate\Support\Facades\Mail;

class MailNet
{
    public static function sendMail($zip_path, $shop_names, $address_to)
    {
        $data = array('zip_path' => $zip_path, 'shop_names' => $shop_names);
        Mail::send('emails.mail', $data, function ($message) use ($address_to) {
            $message->to($address_to)->subject(self::getSubject());
        });
    }

    public static function getSubject()
    {
        return '【Netメンテ代行】​ご提案メール送付_'.date('Ymd');
    }
}
