<?php

namespace App\Enums;

enum NoteTypeEnum: string
{
    case DICTATION = 'dictation';
    case QUIZ = 'quiz';
    case HOMEWORK = 'homework';
    case GENERAL = 'general';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function toArray(): array
    {
        return array_combine(self::names(), self::values());
    }

    public function label(): string
    {
        return match($this) {
            self::DICTATION => 'تسميع',
            self::QUIZ => 'اختبار',
            self::HOMEWORK => 'واجب منزلي',
            self::GENERAL => 'عام',
        };
    }
} 