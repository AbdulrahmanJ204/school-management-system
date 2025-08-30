<?php

namespace App\Services;

use App\Exceptions\ImageUploadFailed;
use App\Exceptions\InvalidTargetException;
use App\Exceptions\PermissionException;
use App\Exceptions\QuizNotFoundException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\DetailedQuizResource;
use App\Http\Resources\QuizResource;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class QuizService
{
    private function handleImageUpload($request, string $fieldName, string $folder): ?string
    {
        if ($request->hasFile($fieldName)) {
            try {
                $file = $request->file($fieldName);

                $hash = md5_file($file->getRealPath());
                $extension = $file->getClientOriginalExtension();
                $filename = "{$folder}/{$hash}.{$extension}";

                if (!Storage::disk('public')->exists($filename)) {
                    Storage::disk('public')->putFileAs($folder, $file, "{$hash}.{$extension}");
                }

                return $filename;

            } catch (\Exception $e) {
                throw new ImageUploadFailed();
            }
        }

        return null;
    }
    public function listQuizzes($request)
    {
        $user = Auth::user();

        // Check if authenticated and has the correct user_type
//        if ( $user->hasPermissionTo('عرض الاختبارات المؤتمتة')) {
//            throw new PermissionException(); // or return error response
//        }

        $credentials = $request->validated();

        $query = Quiz::withCount('questions')
            ->with(['targets.subject', 'targets.section.grade', 'targets.semester', 'questions'])
            ;

        if ($user->user_type === 'teacher') {
            $query->where('created_by', $user->id);

        } elseif ($user->user_type === 'student') {
            $studentSections = $user->student->studentEnrollments()->pluck('section_id')->toArray();
            $query->whereHas('targets', function ($q) use ($studentSections) {
                $q->whereIn('section_id', $studentSections);
            });
            // $studentEnrollment = $user->student->studentEnrollments()->first();

            // if ($studentEnrollment) {
            //     $query->where('is_active', true)
            //         ->whereHas('targets', function ($q) use ($studentEnrollment) {
            //             $q->where('grade_id', $studentEnrollment->grade_id)
            //                 ->where(function ($sub) use ($studentEnrollment) {
            //                     $sub->where('section_id', $studentEnrollment->section_id)
            //                         ->orWhereNull('section_id');
            //                 });
            //         });
            // }
        }

        // if (isset($credentials['grade_id'])) {
        //     $query->whereHas('targets.section.grade', fn($q) => $q->where('id', $credentials['grade_id']));
        // }

        // if (isset($credentials['section_id'])) {
        //     $query->whereHas('targets', fn($q) => $q->where('section_id', $credentials['section_id']));
        // }

        // if (!empty($credentials['subject_id'])) {
        //     $query->whereHas('targets.subject', function ($q) use ($credentials) {$q->where('id', $credentials['subject_id']);});
        // }

        // if (isset($credentials['year_id'])) {
        //     $query->whereHas('targets.semester', function ($q) use ($credentials) {$q->where('year_id', $credentials['year_id']);});
        // }

        $quizzes = $query->get();

        return ResponseHelper::jsonResponse(
            QuizResource::collection($quizzes),
            __('messages.quiz.listed')
        );
    }
    public function showQuiz($id)
    {
        $user = Auth::user();

        $quiz = Quiz::with([
            'questions',
            'targets.subject',
            'targets.section.grade',
            'targets.semester',
        ])->find($id);

        if (!$quiz) {
            throw new QuizNotFoundException();
        }

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('عرض الاختبار المؤتمت')) || $quiz->created_by !== $user->id) {
            throw new PermissionException();
        }

        return ResponseHelper::jsonResponse(
            new DetailedQuizResource($quiz),
            __('messages.quiz.fetched')
        );
    }
    public function create($request)
    {
        $user = Auth::user();

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('انشاء اختبار مؤتمت'))) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        $credentials['quiz_photo'] = $this->handleImageUpload($request, 'quiz_photo', 'quiz_images');
        $credentials['created_by'] = $user->id;
        if(!isset($credentials['semester_id'])){
        $credentials['semester_id'] = Semester::active()->first()->id;
        }
        DB::beginTransaction();

        try {

            $quiz = Quiz::create([
                'name'       => $credentials['name'],
                'full_score' => $credentials['full_score'],
                'created_by' => $user->id,
                'quiz_photo' => $credentials['quiz_photo'],
            ]);

            $subject = Subject::findOrFail($credentials['subject_id']);

            // if ($subject->getGrade()->id != $credentials['grade_id']) {
            //     throw new InvalidTargetException(__('messages.quiz.subject_grade_mismatch'));
            // }

            if (!empty($credentials['section_ids'])) {
                foreach ($credentials['section_ids'] as $sectionId) {
                    $section = Section::findOrFail($sectionId);

                    if ($section->grade_id != $credentials['grade_id']) {
                        throw new InvalidTargetException(__('messages.quiz.section_grade_mismatch'));
                    }

                    $quiz->targets()->create([
                        'grade_id'    => $credentials['grade_id'],
                        'subject_id'  => $credentials['subject_id'],
                        'semester_id' => $credentials['semester_id'],
                        'section_id'  => $sectionId,
                    ]);
                }
            } else {
                $sections = Section::where('grade_id', $credentials['grade_id'])->pluck('id');
                foreach ($sections as $sectionId) {
                    $quiz->targets()->create([
                        'grade_id'    => $credentials['grade_id'],
                        'subject_id'  => $credentials['subject_id'],
                        'semester_id' => $credentials['semester_id'],
                        'section_id'  => $sectionId,
                    ]);
                }
            }

            DB::commit();

            return ResponseHelper::jsonResponse(
                new QuizResource($quiz->load('targets')),
                __('messages.quiz.created'),
                200
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function activate($id)
    {
        $user = Auth::user();
        $quiz = Quiz::find($id);

        if (!$quiz) {
            throw new QuizNotFoundException();
        }

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('تفعيل اختبار مؤتمت')) || $quiz->created_by !== $user->id) {
            throw new PermissionException();
        }

        if($quiz->is_active){
            return ResponseHelper::jsonResponse(
                null,
                __("messages.quiz.already_active"),
                422,
                false
            );
        }

        $quiz->update([
            'is_active' => 1,
            'taken_at'  => now(),
        ]);

        return ResponseHelper::jsonResponse (
            null,
            __("messages.quiz.activated")
        );
    }
    public function deactivate(int $id)
    {
        $user = Auth::user();
        $quiz = Quiz::find($id);

        if (!$quiz) {
            throw new QuizNotFoundException();
        }

//        if ($user->hasPermissionTo('تعطيل اختبار مؤتمت')) {
//            throw new PermissionException();
//        }

        if (!$quiz->is_active) {
            return ResponseHelper::jsonResponse(
                null,
                __("messages.quiz.already_inactive"),
                422,
                false
            );
        }

        $quiz->update([
            'is_active' => false
        ]);

        return ResponseHelper::jsonResponse(
            null,
            __("messages.quiz.deactivated")
        );
    }
    public function update($request, $id)
    {
        $user = Auth::user();
        $quiz = Quiz::with('targets')->find($id);

        if (!$quiz) {
            throw new QuizNotFoundException();
        }

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('تعديل اختبار مؤتمت')) || $quiz->created_by !== $user->id) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        $array = [
            'name'       => $credentials['name']       ?? $quiz->name,
            'full_score' => $credentials['full_score'] ?? $quiz->full_score,
            'is_active'  => $credentials['is_active'] ?? $quiz->is_active,
          ];

        if ($request->hasFile('quiz_photo')) {
            if ($quiz->quiz_photo) {
                Storage::disk('public')->delete($quiz->quiz_photo);
            }

            $array['quiz_photo'] = $this->handleImageUpload($request, 'quiz_photo', 'quiz_images');

        }

        DB::beginTransaction();

        try {
            $quiz->update($array);

            // Only rebuild targets if user passed grade/subject/semester/sections
            if (isset($credentials['grade_id'], $credentials['subject_id'], $credentials['semester_id'])) {

                $quiz->targets()->delete();

                $sectionIds = $credentials['section_ids'] ?? Section::where('grade_id', $credentials['grade_id'])->pluck('id')->toArray();

                foreach ($sectionIds as $sectionId) {
                    $quiz->targets()->create([
                        'grade_id'    => $credentials['grade_id'],
                        'subject_id'  => $credentials['subject_id'],
                        'semester_id' => $credentials['semester_id'],
                        'section_id'  => $sectionId,
                    ]);
                }
            }

            DB::commit();

            return ResponseHelper::jsonResponse(
                new QuizResource($quiz->load('targets')),
                __("messages.quiz.updated"),
                201
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function destroy($id)
    {
        $user = Auth::user();

        $quiz = Quiz::with('questions', 'targets')->find($id);

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('حذف اختبار مؤتمت')) || $quiz->created_by !== $user->id) {
            throw new PermissionException();
        }

        if (!$quiz) {
            throw new QuizNotFoundException();
        }

        if ($quiz->quiz_photo && $quiz->quiz_photo !== 'quiz_images/default.png') {
            Storage::disk('public')->delete($quiz->quiz_photo);
        }

        $quiz->delete();

        return ResponseHelper::jsonResponse(
            null,
            __("messages.quiz.deleted")
        );
    }

    /**
     * Generate a quiz using Google Gemini AI.
     */
    public function generateQuiz($request)
    {
        $user = Auth::user();

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('انشاء اختبار مؤتمت'))) {
            throw new PermissionException();
        }

        $credentials = $request->validated();
        $credentials['quiz_photo'] = $this->handleImageUpload($request, 'quiz_photo', 'quiz_images');
        $credentials['created_by'] = $user->id;
        
        if(!isset($credentials['semester_id'])){
            $credentials['semester_id'] = Semester::active()->first()->id;
        }

        // Get grade and subject information
        $grade = \App\Models\Grade::findOrFail($credentials['grade_id']);
        $subject = Subject::findOrFail($credentials['subject_id']);

        // Prepare the prompt for Gemini API
        $prompt = $this->buildGeminiPrompt(
            $credentials['text_to_extract_from'],
            $grade->name,
            $subject->name,
            $credentials['multiple_choice_count'],
            $credentials['true_false_count']
        );

        // Call Gemini API
        $aiResponse = $this->callGeminiAPI($prompt);

        if (!$aiResponse || !isset($aiResponse['questions'])) {
            return ResponseHelper::jsonResponse(
                null,
                'فشل في توليد الأسئلة من النص المقدم',
                400,
                false
            );
        }

        DB::beginTransaction();

        try {
            $quiz = Quiz::create([
                'name'       => $credentials['name'],
                'full_score' => $credentials['full_score'],
                'created_by' => $user->id,
                'quiz_photo' => $credentials['quiz_photo'],
                'is_active'  => false, // Set to false as requested
            ]);

            // Create quiz targets
            if (!empty($credentials['section_ids'])) {
                foreach ($credentials['section_ids'] as $sectionId) {
                    $section = Section::findOrFail($sectionId);

                    if ($section->grade_id != $credentials['grade_id']) {
                        throw new InvalidTargetException(__('messages.quiz.section_grade_mismatch'));
                    }

                    $quiz->targets()->create([
                        'grade_id'    => $credentials['grade_id'],
                        'subject_id'  => $credentials['subject_id'],
                        'semester_id' => $credentials['semester_id'],
                        'section_id'  => $sectionId,
                    ]);
                }
            } else {
                $sections = Section::where('grade_id', $credentials['grade_id'])->pluck('id');
                foreach ($sections as $sectionId) {
                    $quiz->targets()->create([
                        'grade_id'    => $credentials['grade_id'],
                        'subject_id'  => $credentials['subject_id'],
                        'semester_id' => $credentials['semester_id'],
                        'section_id'  => $sectionId,
                    ]);
                }
            }

            // Create questions from AI response
            $order = 1;
            foreach ($aiResponse['questions'] as $questionData) {
                $question = $quiz->questions()->create([
                    'question_text' => json_encode($questionData['question_text']),
                    'question_text_plain' => $this->normalizeDeltaToPlainText($questionData['question_text']),
                    'hint' => isset($questionData['question_hint']) ? json_encode($questionData['question_hint']) : null,
                    'choices' => $questionData['choices'],
                    'choices_count' => count($questionData['choices']),
                    'right_choice' => $questionData['right_choice'],
                    'order' => $order++,
                ]);
            }

            DB::commit();

            return ResponseHelper::jsonResponse(
                new DetailedQuizResource($quiz->load(['questions', 'targets.subject', 'targets.section.grade', 'targets.semester'])),
                __('messages.quiz.generated')
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Build the prompt for Gemini API.
     */
    private function buildGeminiPrompt($text, $grade, $subject, $mcqCount, $tfCount)
    {
        $totalQuestions = $mcqCount + $tfCount;
        
        return "You are an expert teacher creating a quiz for grade {$grade} students studying {$subject}. 
        
        Extract exactly {$totalQuestions} questions from the following text, ensuring they match the grade level and are in the same language as the text:
        
        TEXT TO EXTRACT FROM:
        {$text}
        
        REQUIREMENTS:
        - Create exactly {$mcqCount} multiple choice questions (4 choices each)
        - Create exactly {$tfCount} true/false questions (2 choices: true/false)
        - Questions should match the grade level ({$grade})
        - Questions should be in the same language as the provided text
        - Questions should be easy to medium difficulty for this grade level
        - Extract questions directly from the content, don't create new concepts
        
        RESPONSE FORMAT (JSON):
        {
            \"quiz_name\": \"Quiz Name\",
            \"full_mark\": {$totalQuestions},
            
            \"is_active\": false,
            \"questions\": [
                {
                    \"question_text\": {\"ops\": [{\"insert\": \"Question text here\"}]},
                    \"question_hint\": {\"ops\": [{\"insert\": \"Optional hint\"}]},
                    \"right_choice\": 1,
                    \"choices\": {
                        \"1\": {\"ops\": [{\"insert\": \"Choice 1\"}]},
                        \"2\": {\"ops\": [{\"insert\": \"Choice 2\"}]},
                        \"3\": {\"ops\": [{\"insert\": \"Choice 3\"}]},
                        \"4\": {\"ops\": [{\"insert\": \"Choice 4\"}]}
                    }
                }
            ]
        }
        
        For true/false questions, use only 2 choices with \"1\": {\"ops\": [{\"insert\": \"صح\"}]} and \"2\": {\"ops\": [{\"insert\": \"خطأ\"}]}.
        
        Return ONLY the JSON response, no additional text.";
    }

    /**
     * Call Google Gemini API.
     */
    private function callGeminiAPI($prompt)
    {
        $apiKey = config('services.gemini.api_key');
        
        if (!$apiKey) {
            throw new \Exception('Google Gemini API key not configured');
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}";
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 8192,
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("Gemini API request failed with HTTP code: {$httpCode}");
        }

        $responseData = json_decode($response, true);
        
        if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \Exception('Invalid response from Gemini API');
        }

        $aiText = $responseData['candidates'][0]['content']['parts'][0]['text'];
        
        // Extract JSON from the response
        $jsonStart = strpos($aiText, '{');
        $jsonEnd = strrpos($aiText, '}');
        
        if ($jsonStart === false || $jsonEnd === false) {
            throw new \Exception('No valid JSON found in AI response');
        }
        
        $jsonString = substr($aiText, $jsonStart, $jsonEnd - $jsonStart + 1);
        $parsedResponse = json_decode($jsonString, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse JSON from AI response: ' . json_last_error_msg());
        }
        
        return $parsedResponse;
    }

    /**
     * Normalize delta to plain text for duplicate checking.
     */
    private function normalizeDeltaToPlainText($delta)
    {
        if (is_string($delta)) {
            $decoded = json_decode($delta, true);
        } elseif (is_array($delta)) {
            $decoded = $delta;
        } else {
            return '';
        }

        $ops = $decoded['ops'] ?? [];

        $plainText = '';
        foreach ($ops as $op) {
            if (isset($op['insert'])) {
                $plainText .= $op['insert'];
            }
        }

        $plainText = strtolower($plainText);
        $plainText = preg_replace('/\s+/', '', $plainText);

        return trim($plainText);
    }
}
