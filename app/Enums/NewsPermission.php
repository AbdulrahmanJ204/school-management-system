<?php

namespace App\Enums;

enum NewsPermission : string{
    case create ='انشاء خبر';
    case update = 'تحديث خبر';
    case delete = 'حذف خبر';
    case restore = 'استعادة خبر';
    //case show = 'عرض خبر';
}
