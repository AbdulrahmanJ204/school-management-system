<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum NewsPermission : string{
    use EnumHelper;
    case create ='انشاء خبر';
    case update = 'تحديث خبر';
    case softDelete = 'حذف خبر';
    case delete = 'حذف خبر نهائي';
    case restore = 'استعادة خبر';
    //case show = 'عرض خبر';
    case ListNews = 'عرض الاخبار';
    case ListDeletedNews = 'عرض الاخبار المحذوفة';
}
