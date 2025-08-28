<?php

namespace App\Http\Controllers;

use App\Models\User;
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

      return  $this->fcmService->sendNotification(User::find(102)->fcm_token, 'TITLE', 'اشعار مشان علاء يروق', []);

    }
}
