<?php
namespace App\Enums;
use App\Traits\EnumHelper;

enum FileType: string{
    use EnumHelper;
    case PUBLIC = 'public';
    case HELPER = 'helper';
}
