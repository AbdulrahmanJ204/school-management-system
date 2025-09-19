<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum QuizPermission: string
{
    use EnumHelper;
    
    // Quiz Management
    case CREATE_AUTOMATED_QUIZ = 'انشاء اختبار مؤتمت';
    case ACTIVATE_AUTOMATED_QUIZ = 'تفعيل اختبار مؤتمت';
    case DEACTIVATE_AUTOMATED_QUIZ = 'تعطيل اختبار مؤتمت';
    case UPDATE_AUTOMATED_QUIZ = 'تعديل اختبار مؤتمت';
    case DELETE_AUTOMATED_QUIZ = 'حذف اختبار مؤتمت';
    case CREATE_QUESTION = 'انشاء سؤال';
    case UPDATE_QUESTION = 'تعديل سؤال';
    case DELETE_QUESTION = 'حذف سؤال';
    case CREATE_QUIZ_RESULT = 'انشاء نتيجة اختبار مؤتمت';
    case VIEW_AUTOMATED_QUIZZES = 'عرض الاختبارات المؤتمتة';
    case VIEW_AUTOMATED_QUIZ = 'عرض الاختبار المؤتمت';
}
