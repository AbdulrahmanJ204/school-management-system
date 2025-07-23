<?php

namespace App\Services;

use App\Exceptions\ImageUploadFailed;
use App\Helpers\ResponseHelper;
use App\Http\Requests\file\StoreFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\FileTarget;
use App\Models\SchoolDay;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;

class FileService
{

    public function getFiles()
    {
        $role = auth()->user()->role;
        if ($role == 'admin') {
            return $this->getAdminFiles();
        }

    }

    public function getAdminFiles()
    {
        $files = File::with('schoolDay')->get();
        return ResponseHelper::jsonResponse(FileResource::collection($files), 'files retrieved successfully');
    }

    public function getLastSchoolDayID()
    {
        $today = now()->toDateString();

        $todaySchoolDay = SchoolDay::where('date', $today)->first();

        if ($todaySchoolDay) {
            return $todaySchoolDay->id;
        }

        $lastSchoolDay = SchoolDay::where('date', '<', $today)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $lastSchoolDay ? $lastSchoolDay->id : null;
    }

    public function store(StoreFileRequest $request)
    {
        $data = $request->validated();

        $lastSchoolDayID = $this->getLastSchoolDayID();
        $subjectCode = Subject::find($data['subject_id'])->first()->code;
        $lastFileID = File::latest()->first()->id ?? 0;

        $file = $this->handleFile($request, $subjectCode, $lastFileID);
        $size = Storage::disk('public')->size($file);
        $result = File::create([
            'subject_id' => $data['subject_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'school_day_id' => $lastSchoolDayID,
            'file' => $file,
            'size' => $size,
            'created_by' => $request->user()->id,
        ]);
        $this->handleFileTargetsOnCreate($result, $request);
        return ResponseHelper::jsonResponse(FileResource::make($result), 'file stored successfully');
    }




    public function handleFile(StoreFileRequest $request, $subjectCode, $id)
    {
        $rPath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = $subjectCode . '-' . ($id + 1) . '.' . $extension;
            $path = 'library/files/' . $subjectCode;
            $filePath = $path . '/' . $fileName;

            if (!Storage::disk('public')->exists($filePath)) {
                $file->storeAs($path, $fileName, 'public');
            }
            $rPath = $filePath;
        }
        return $rPath;
    }

    public function handleFileTargetsOnCreate(File $file, StoreFileRequest $request)
    {
        $user = auth()->user();
        if ($request->filled('section_ids')) {
            foreach ($request->section_ids as $section_id) {
                FileTarget::create([
                    'file_id' => $file->id,
                    'grade_id' => null,
                    'section_id' => $section_id,
                    'created_by' => $user->id,
                ]);
            }
        } else if ($request->filled('grade_ids')) {
            foreach ($request->grade_ids as $grade_id) {
                FileTarget::create([
                    'file_id' => $file->id,
                    'grade_id' => $grade_id,
                    'section_id' => null,
                    'created_by' => $user->id,
                ]);
            }
        } else {
            // Target all users
            FileTarget::create([
                'file_id' => $file->id,
                'grade_id' => null,
                'section_id' => null,
                'created_by' => $user->id,
            ]);
        }
    }
}
