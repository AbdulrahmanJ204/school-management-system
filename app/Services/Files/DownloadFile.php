<?php

namespace App\Services\Files;

use App\Enums\UserType;
use App\Helpers\ResponseHelper;
use App\Models\File;
use App\Models\FileDownload;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

trait DownloadFile
{

    public function download($fileId)
    {
        $user = auth()->user();
        $file = $user->user_type === UserType::Admin->value ?
            File::withTrashed()->findOrFail($fileId)
            : File::findOrFail($fileId);

        FileDownload::create(
            [
                'file_id' => $fileId,
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'downloaded_at' => now(),
            ]
        );
        $name = $file->title;
        $filePath = $file->file;
        // TODO: Add Download Method;


        return Storage::disk('public')->download($filePath , $name );
    }

}
