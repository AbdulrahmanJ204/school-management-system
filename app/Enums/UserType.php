<?php
namespace App\Enums;
use App\Traits\EnumHelper;

enum UserType: string {
    use EnumHelper;
    case Teacher = 'teacher';
    case Student = 'student';
    case Admin = 'admin';
}
