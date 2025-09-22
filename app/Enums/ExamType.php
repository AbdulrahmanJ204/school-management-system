<?php

namespace App\Enums;

enum ExamType: string
{
    case EXAM = 'exam';
    case QUIZ = 'quiz';

    public function getArabicName(): string
    {
        return match($this) {
            self::EXAM => 'امتحان',
            self::QUIZ => 'مذاكرة',
        };
    }

    public static function getOptions(): array
    {
        return [
            self::EXAM->value => self::EXAM->getArabicName(),
            self::QUIZ->value => self::QUIZ->getArabicName(),
        ];
    }

    public static function getValues(): array
    {
        return [
            self::EXAM->value,
            self::QUIZ->value,
        ];
    }
}
