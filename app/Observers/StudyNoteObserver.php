<?php

namespace App\Observers;

use App\Models\StudyNote;
use App\Services\Notification\FcmService;

class StudyNoteObserver
{
    /**
     * Handle the StudyNote "created" event.
     */
    public function created(StudyNote $studyNote): void
    {

        $title = 'ملاحظة دراسية .';
        $body = $studyNote->note;
        $token = $studyNote->student->user->fcm_token;
        if ($token) {
            (new FcmService())->sendNotification(
                    $token,
                    $title,
                    $body
                );
        }
    }

    /**
     * Handle the StudyNote "updated" event.
     */
    public function updated(StudyNote $studyNote): void
    {
        //
    }

    /**
     * Handle the StudyNote "deleted" event.
     */
    public function deleted(StudyNote $studyNote): void
    {
        //
    }

    /**
     * Handle the StudyNote "restored" event.
     */
    public function restored(StudyNote $studyNote): void
    {
        //
    }

    /**
     * Handle the StudyNote "force deleted" event.
     */
    public function forceDeleted(StudyNote $studyNote): void
    {
        //
    }
}
