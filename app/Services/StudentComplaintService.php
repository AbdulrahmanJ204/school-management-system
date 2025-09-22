<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Models\Complaint;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StudentComplaintService
{
    /**
     * Create a new complaint for student
     *
     * @param int $userId
     * @param array $data
     * @return JsonResponse
     */
    public function createStudentComplaint(int $userId, array $data): JsonResponse
    {
        try {
            // Verify user is student
            $user = User::with('student')->findOrFail($userId);
            if (!$user->student) {
                throw new Exception('الطالب غير موجود');
            }

            $complaint = Complaint::create([
                'user_id' => $userId,
                'title' => $data['title'],
                'content' => $data['content'],
                'created_by' => $userId,
            ]);

            $complaintData = $this->formatComplaintResponse($complaint);

            return ResponseHelper::jsonResponse(
                $complaintData,
                'تم إنشاء الشكوى بنجاح',
                Response::HTTP_CREATED
            );

        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                null,
                'حدث خطأ في إنشاء الشكوى: ' . $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
                false
            );
        }
    }

    /**
     * Update student complaint
     *
     * @param int $userId
     * @param int $complaintId
     * @param array $data
     * @return JsonResponse
     */
    public function updateStudentComplaint(int $userId, int $complaintId, array $data): JsonResponse
    {
        try {
            // Verify user is student
            $user = User::with('student')->findOrFail($userId);
            if (!$user->student) {
                throw new Exception('الطالب غير موجود');
            }

            // Find complaint and verify ownership
            $complaint = Complaint::where('id', $complaintId)
                ->where('user_id', $userId)
                ->first();

            if (!$complaint) {
                throw new Exception('الشكوى غير موجودة أو ليس لديك صلاحية للوصول إليها');
            }

            // Update only provided fields
            $updateData = [];
            if (isset($data['title'])) {
                $updateData['title'] = $data['title'];
            }
            if (isset($data['content'])) {
                $updateData['content'] = $data['content'];
            }

            $complaint->update($updateData);

            $complaintData = $this->formatComplaintResponse($complaint->fresh());

            return ResponseHelper::jsonResponse(
                $complaintData,
                'تم تحديث الشكوى بنجاح'
            );

        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                null,
                'حدث خطأ في تحديث الشكوى: ' . $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
                false
            );
        }
    }

    /**
     * Delete student complaint
     *
     * @param int $userId
     * @param int $complaintId
     * @return JsonResponse
     */
    public function deleteStudentComplaint(int $userId, int $complaintId): JsonResponse
    {
        try {
            // Verify user is student
            $user = User::with('student')->findOrFail($userId);
            if (!$user->student) {
                throw new Exception('الطالب غير موجود');
            }

            // Find complaint and verify ownership
            $complaint = Complaint::where('id', $complaintId)
                ->where('user_id', $userId)
                ->first();

            if (!$complaint) {
                throw new Exception('الشكوى غير موجودة أو ليس لديك صلاحية للوصول إليها');
            }

            $complaintData = $this->formatComplaintResponse($complaint);

            $complaint->delete();

            return ResponseHelper::jsonResponse(
                $complaintData,
                'تم حذف الشكوى بنجاح'
            );

        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                null,
                'حدث خطأ في حذف الشكوى: ' . $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
                false
            );
        }
    }

    /**
     * Get all complaints for student
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getStudentComplaints(int $userId): JsonResponse
    {
        try {
            // Verify user is student
            $user = User::with('student')->findOrFail($userId);
            if (!$user->student) {
                throw new Exception('الطالب غير موجود');
            }

            $complaints = Complaint::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            $complaintsData = $complaints->map(function ($complaint) {
                return $this->formatComplaintResponse($complaint);
            });

            return ResponseHelper::jsonResponse(
                $complaintsData,
                'تم جلب الشكاوى بنجاح'
            );

        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                null,
                'حدث خطأ في جلب الشكاوى: ' . $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
                false
            );
        }
    }

    /**
     * Format complaint response according to specifications
     *
     * @param Complaint $complaint
     * @return array
     */
    private function formatComplaintResponse(Complaint $complaint): array
    {
        // Determine status based on answer
        $status = $complaint->answer ? 'تم الرد' : 'في الانتظار';

        // Format reply data
        $reply = null;
        if ($complaint->answer) {
            $reply = [
                'reply_date' => $complaint->updated_at->format('Y-n-j'), // Format: 2025-6-30
                'reply_content' => $complaint->answer
            ];
        }

        return [
            'id' => $complaint->id,
            'title' => $complaint->title,
            'content' => $complaint->content,
            'send_date' => $complaint->created_at->format('Y-n-j'), // Format: 2025-6-30
            'status' => $status,
            'reply' => $reply
        ];
    }
}
