<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\SchoolShiftResource;
use App\Models\SchoolShift;
use Illuminate\Support\Facades\DB;

class SchoolShiftService
{
    public function create($request)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('انشاء فترة دوام')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();
        $credentials['created_by'] = $user->id;

        DB::beginTransaction();

        $shift = SchoolShift::create($credentials);

        DB::commit();

        return ResponseHelper::jsonResponse(
            new SchoolShiftResource($shift),
            __('messages.school_shift.created'),
            200
        );
    }
}
