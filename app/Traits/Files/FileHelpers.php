<?php

namespace App\Traits\Files;

use App\Enums\StringsManager\FileStr;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

trait FileHelpers
{
    private function handleFile($request, $subjectCode, $deletePath = null): string
    {
        if ($deletePath) {
            $this->deleteFileFromStorage($deletePath);
        }
        $file = $request->file($this->apiFile);
        $extension = $file->getClientOriginalExtension();
        $hashedWithoutExtension = pathinfo($file->hashName(), PATHINFO_FILENAME);

        $fileName = $subjectCode . FileStr::Separator->value . $hashedWithoutExtension . '.' . $extension;

        $path = FileStr::LibraryPath->value . '/' . $subjectCode;
        $filePath = $path . '/' . $fileName;
        if (!Storage::disk($this->storageDisk)->exists($filePath)) {
            $file->storeAs($path, $fileName, $this->storageDisk);
        }
        return $filePath;
    }

    private function moveFile(File $file, $subjectCode): string
    {

        $oldName = basename($file->file);
        $nameWithoutCode = $this->removePrefixBeforeSeparator($oldName);
        $fileName = $subjectCode . FileStr::Separator->value . $nameWithoutCode;
        $path = FileStr::LibraryPath->value . '/' . $subjectCode;
        $filePath = $path . '/' . $fileName;
        Storage::disk($this->storageDisk)->move($file->file, $filePath);
        return $filePath;
    }

    function removePrefixBeforeSeparator(string $filename, string $separator = FileStr::Separator->value): string
    {
        $parts = explode($separator, $filename, 2);
        return $parts[1] ?? $filename;
    }

    public function deleteFileFromStorage($path): void
    {
        if (Storage::disk($this->storageDisk)->exists($path))
            Storage::disk($this->storageDisk)->delete($path);
    }
}
