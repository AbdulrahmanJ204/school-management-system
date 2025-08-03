<?php

namespace App\Enums\Permissions;

enum FilesPermission : string{
    case store ='تخزين ملف';
    case update = 'تحديث ملف';
    case delete = 'حذف ملف نهائي';
    case softDelete = 'حذف ملف';
    case restore = 'استعادة ملف';
    //case show = 'عرض ملف';
}
