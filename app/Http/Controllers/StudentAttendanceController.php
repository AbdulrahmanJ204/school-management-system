<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\StudentAttendance\ListStudentAttendanceRequest;
use App\Http\Requests\StudentAttendance\StoreStudentAttendanceRequest;
use App\Http\Requests\StudentAttendance\UpdateStudentAttendanceRequest;
use App\Http\Requests\StudentAttendance\StudentAttendanceReportRequest;
use App\Http\Resources\DailyStudentAttendanceResource;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Services\StudentAttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentAttendanceController extends Controller
{
    protected StudentAttendanceService $studentAttendanceService;

    public function __construct(StudentAttendanceService $studentAttendanceService)
    {
        $this->studentAttendanceService = $studentAttendanceService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(ListStudentAttendanceRequest $request): ?JsonResponse
    {
        return $this->studentAttendanceService->listStudentAttendances($request);
    }

    /**
     * Store a newly created resource in storage.
     * @throws PermissionException
     */
    public function store(StoreStudentAttendanceRequest $request)
    {
        return $this->studentAttendanceService->createStudentAttendance($request);
    }

    /**
     * Display the specified resource.
     * @throws PermissionException
     */
    public function show($studentAttendanceId): JsonResponse
    {
        return $this->studentAttendanceService->showStudentAttendance($studentAttendanceId);
    }

    /**
     * Update the specified resource in storage.
     * @throws PermissionException
     */
    public function update(UpdateStudentAttendanceRequest $request, StudentAttendance $studentAttendance)
    {
        return $this->studentAttendanceService->updateStudentAttendance($request, $studentAttendance->id);
    }

    /**
     * Remove the specified resource from storage.
     * @throws PermissionException
     */
    public function destroy(StudentAttendance $studentAttendance): JsonResponse
    {
        return $this->studentAttendanceService->deleteStudentAttendance($studentAttendance->id);
    }

    /**
     * Generate detailed attendance report for a student
     */
    public function generateReport(): JsonResponse
    {
        return $this->studentAttendanceService->generateAttendanceReport();
    }

    /**
     * Get daily student attendance for admin users
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getDailyStudentsAttendance(Request $request): JsonResponse
    {
        // Get query parameters
        $schoolDayId = $request->query('school_day_id');
        $section = $request->query('section');

        // For now, return mock data as requested
        // In a real implementation, you would query the database based on these parameters

        $mockStudents = [
            [
                'id' => 1,
                'full_name' => 'Manar Ahmed',
                'status' => 'present'
            ],
            [
                'id' => 2,
                'full_name' => 'Ahmed Hassan',
                'status' => 'present'
            ],
            [
                'id' => 3,
                'full_name' => 'Fatima Ali',
                'status' => 'absent'
            ],
            [
                'id' => 4,
                'full_name' => 'Omar Khalil',
                'status' => 'lateness'
            ],
            [
                'id' => 5,
                'full_name' => 'Layla Mahmoud',
                'status' => 'justified_absent'
            ]
        ];

        return response()->json([
            'data' => [
                'students' => $mockStudents
            ],
            'message' => 'Daily student attendance retrieved successfully',
            'status' => true
        ]);
    }
    public function getSessionsStudentsAttendance(Request $request): JsonResponse
    {
        $schoolDayId = $request->query('school_day_id');
        $section = $request->query('section');

        $data = [
            'school_day_id' => $schoolDayId,
            'section' => $section,
            'students' => [
                [
                    'id' => 1,
                    'full_name' => 'Manar Ahmed',
                    'status' => 'present',
                    'class_periods' => [
                        [
                            'class_period_id' => 1,
                            'class_session_id' => 2,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 2,
                            'class_session_id' => 5,
                            'status' => 'absent'
                        ],
                        [
                            'class_period_id' => 3,
                            'class_session_id' => 8,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 4,
                            'class_session_id' => 11,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 5,
                            'class_session_id' => 14,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 6,
                            'class_session_id' => 17,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 7,
                            'class_session_id' => 20,
                            'status' => 'present'
                        ]
                        
                    ]
                ],
                [
                    'id' => 2,
                    'full_name' => 'Ahmed Hassan',
                    'status' => 'present',
                    'class_periods' => [
                        [
                            'class_period_id' => 1,
                            'class_session_id' => 2,
                            'status' => 'present'
                        ]
                    ]
                ],
                [
                    'id' => 3,
                    'full_name' => 'Fatima Ali',
                    'status' => 'absent',
                    'class_periods' => [
                        [
                            'class_period_id' => 1,
                            'class_session_id' => 2,
                            'status' => 'absent'
                        ],  
                        [
                            'class_period_id' => 2,
                            'class_session_id' => 5,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 3,
                            'class_session_id' => 8,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 4,
                            'class_session_id' => 11,
                            'status' => 'present'
                        ],
                        
                        [
                            'class_period_id' => 5,
                            'class_session_id' => 14,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 6,
                            'class_session_id' => 17,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 7,
                            'class_session_id' => 20,
                            'status' => 'present'
                        ]
                        
                    ]
                ],
                [
                    'id' => 4,
                    'full_name' => 'Omar Khalil',
                    'status' => 'lateness',
                    'class_periods' => [
                        [
                            'class_period_id' => 1,
                            'class_session_id' => 2,
                            'status' => 'lateness'
                        ],
                        [
                            'class_period_id' => 2,
                            'class_session_id' => 5,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 3,
                            'class_session_id' => 8,
                            'status' => 'present'
                        ]   ,
                        [
                            'class_period_id' => 4,
                            'class_session_id' => 11,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 5,
                            'class_session_id' => 14,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 6,
                            'class_session_id' => 17,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 7,
                            'class_session_id' => 20,
                            'status' => 'present'
                        ]
                    ]
                ],
                [
                    'id' => 5,
                    'full_name' => 'Layla Mahmoud',
                    'status' => 'justified_absent',
                    'class_periods' => [
                        [
                            'class_period_id' => 1,
                            'class_session_id' => 2,
                            'status' => 'justified_absent'
                        ],
                        [
                            'class_period_id' => 2,
                            'class_session_id' => 5,
                            'status' => 'present'
                        ],
                        [
                            'class_period_id' => 3,
                            'class_session_id' => 8,
                            'status' => 'present'
                        ]
                    ]
                ]
            ]
        ];

        return response()->json([
            'data' => $data,
            'message' => 'Sessions student attendance retrieved successfully',
            'status' => true
        ]);
    }
}
