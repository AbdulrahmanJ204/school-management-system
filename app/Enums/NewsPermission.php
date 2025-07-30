<?php

namespace App\Enums;

enum NewsPermission : string{
    case create ='انشاء خبر';
    case update = 'تحديث خبر';
    case softDelete = 'حذف خبر';
    case delete = 'حذف خبر نهائي';
    case restore = 'استعادة خبر';
    //case show = 'عرض خبر';
}
