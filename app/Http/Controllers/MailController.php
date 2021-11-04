<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function send()
    {
        $data = array("name" => "Achmad Fawait");
        $mail = Mail::send('mail', $data, function ($message) {
            $message->to('fawait626@gmail.com', 'Programmer Lokal')->subject('Tes email');
            $message->from('achmadfawait66@gmail.com', 'Programmer Lokal');
        });
        return response()->json([
            'status' => true,
            'message' => 'Mail Send',
        ], 200);
    }
}
