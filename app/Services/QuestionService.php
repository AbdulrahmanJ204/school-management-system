<?php

namespace App\Services;

use App\Exceptions\ImageUploadFailed;
use App\Exceptions\InvalidQuestionTextException;
use App\Exceptions\PermissionException;
use App\Exceptions\QuestionAlreadyExistsException;
use App\Exceptions\QuestionNotFoundException;
use App\Exceptions\QuizNotFoundException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class QuestionService
{
    private function handleImageUpload($request, string $fieldName, string $folder): ?string
    {
        if (!$request->hasFile($fieldName)) {
            if ($fieldName == 'question_photo')
                return 'question_images/default.png';
            else
                return 'hint_images/default.png';
        }

        try {
            $file = $request->file($fieldName);

            // Create unique filename from image content
            $hash = md5_file($file->getRealPath());
            $extension = $file->getClientOriginalExtension();
            $filename = "{$folder}/{$hash}.{$extension}";

            // Only store if not already present
            if (!Storage::disk('public')->exists($filename)) {
                Storage::disk('public')->putFileAs($folder, $file, "{$hash}.{$extension}");
            }

            return $filename;
        } catch (\Exception $e) {
            throw new ImageUploadFailed(); // your custom exception
        }
    }

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

    public function create($request, $quiz_id)
    {
        $user = Auth::user();

        $quiz = Quiz::find($quiz_id);

        if (!$quiz) {
            throw new QuizNotFoundException();
        }

        if ($user->user_type === 'student' || !$user->hasPermissionTo('انشاء سؤال')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        $plainText = $this->normalizeDeltaToPlainText($request->input('question_text'));

        $existingQuestion = Question::where('quiz_id', $quiz_id)
            ->whereRaw("LOWER(question_text_plain) = ?", [strtolower($plainText)])
            ->first();

        if ($existingQuestion || empty($plainText)) {
            throw new QuestionAlreadyExistsException();
        }
        $credentials['question_text'] = isset($credentials['question_text']) ? json_encode($credentials['question_text']) : null;
        $credentials['hint'] = isset($credentials['hint']) ? json_encode($credentials['hint']) : null;
        $credentials['quiz_id'] = $quiz_id;
        $credentials['question_text_plain'] = $plainText;

        $credentials['question_photo'] = $this->handleImageUpload($request, 'question_photo', 'question_images');

        $credentials['hint_photo'] = $this->handleImageUpload($request, 'hint_photo', 'hint_images');

        if ($credentials['right_choice'] > $credentials['choices_count']) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.question.invalid_right_choice'),
                400,
                false
            );
        }

        $question = Question::create($credentials);

        return ResponseHelper::jsonResponse(
            new QuestionResource($question),
            __('messages.question.created'),
        );
    }

    public function update($request, $quiz_id, $question_id)
    {
        $user = Auth::user();
        $quiz = Quiz::find($quiz_id);

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('تعديل سؤال')) || $quiz->created_by !== $user->id) {
            throw new PermissionException();
        }

        if (!$quiz) {
            throw new QuizNotFoundException();
        }

        $question = Question::where('id', $question_id)
            ->where('quiz_id', $quiz_id)
            ->first();

        if (!$question) {
            throw new QuestionNotFoundException();
        }

        $credentials = $request->validated();

        if (array_key_exists('question_text', $credentials)) {
            $plain = $this->normalizeDeltaToPlainText($credentials['question_text']);
            if (empty($plain)) {
                throw new InvalidQuestionTextException();
            }

            $credentials['question_text_plain'] = $plain;
        }

        if ($request->hasFile('question_photo')) {
            if ($question->question_photo && $question->question_photo !== 'question_images/default.png') {
                Storage::disk('public')->delete($question->question_photo);
                $credentials['question_photo'] = $this->handleImageUpload($request, 'question_photo', 'question_images');
            }
        }

        if ($request->hasFile('hint_photo')) {
            if ($question->hint_photo && $question->hint_photo !== 'hint_images/default.png') {
                Storage::disk('public')->delete($question->hint_photo);
                $credentials['hint_photo'] = $this->handleImageUpload($request, 'hint_photo', 'hint_images');
            }
        }

        if ($credentials['right_choice'] > $credentials['choices_count']) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.question.invalid_right_choice'),
                400,
                false
            );
        }

        $question->update($credentials);

        return ResponseHelper::jsonResponse(
            new QuestionResource($question),
            __('messages.question.updated'),
            201
        );
    }

    public function delete(int $quiz_id, int $question_id)
    {
        $user = Auth::user();

        $quiz = Quiz::find($quiz_id);

        if (!$quiz) {
            throw new QuizNotFoundException();
        }

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('حذف سؤال')) || $quiz->created_by !== $user->id) {
            throw new PermissionException();
        }

        $question = Question::where('id', $question_id)
            ->where('quiz_id', $quiz_id)
            ->first();

        if (!$question) {
            throw new QuestionNotFoundException();
        }

        if ($question->question_photo && $question->question_photo !== 'question_images/default.png') {
            Storage::disk('public')->delete($question->question_photo);
        }

        if ($question->hint_photo && $question->hint_photo !== 'hint_images/default.png') {
            Storage::disk('public')->delete($question->hint_photo);
        }

        $question->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.question.deleted'),
            200
        );
    }
}
