<?php

namespace App\Observers;

use App\Enums\NoteTypeEnum;
use App\Models\BehaviorNote;
use App\Services\Notification\FcmService;

class BehaviorNoteObserver
{
    /**
     * Handle the BehaviorNote "created" event.
     */
    public function created(BehaviorNote $behaviorNote): void
    {
        //
        $type = $behaviorNote->behavior_type == 'positive' ? 'ايجابية' : 'سلبية';
        $title = 'ملاحظة سلوكية ' . $type . '.';
        $body = $behaviorNote->note;
        (new FcmService())->
        sendNotification($behaviorNote->student->user->fcm_token,
            $title,
            $body
        );
    }

    /**
     * Handle the BehaviorNote "updated" event.
     */
    public function updated(BehaviorNote $behaviorNote): void
    {
        //
    }

    /**
     * Handle the BehaviorNote "deleted" event.
     */
    public function deleted(BehaviorNote $behaviorNote): void
    {
        //
    }

    /**
     * Handle the BehaviorNote "restored" event.
     */
    public function restored(BehaviorNote $behaviorNote): void
    {
        //
    }

    /**
     * Handle the BehaviorNote "force deleted" event.
     */
    public function forceDeleted(BehaviorNote $behaviorNote): void
    {
        //
    }
}
