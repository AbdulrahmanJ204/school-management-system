<?php

namespace App\Http\Controllers;

use App\Services\Notification\FcmService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    protected FcmService $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public
    function sendToUser(Request $request)
    {
        $token = "dv2xHNo0SbeMeB4AjP1uwD:APA91bFzxJJpNPGD0qrArABGMKA7gHaMdjAl7RtfZHtvvvIN3ij36xkH369Y-O6h0Ob4Pq0nXPXkWsp0OOxn70oLeaqfwiLPOiUVFHp8nAPTw9huxxhzz7g";
        $tobic = 'teacher';
      return  $this->fcmService->sendNotification($token, 'TITLE', 'اشعار مشان علاء يروق', []);

    }
}
