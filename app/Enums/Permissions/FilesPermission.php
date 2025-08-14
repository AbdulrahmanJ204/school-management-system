<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum FilesPermission : string{
    use EnumHelper;
    case store ='تخزين ملف';
    case update = 'تحديث ملف';
    case delete = 'حذف ملف نهائي';
    case softDelete = 'حذف ملف';
    case restore = 'استعادة ملف';
    //case show = 'عرض ملف';
    case download = 'تنزيل ملف';
}
