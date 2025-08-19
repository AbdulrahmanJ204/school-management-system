<?php

namespace App\Enums\StringsManager\Files;

enum FileApi : string
{
    // API Keys
    case apiTitle = "title";
    case apiDescription = "description";
    case apiType = 'type';
    case apiSubjectId = "subject_id";
    case apiFile = 'file';
    case apiIsGeneral = "is_general";
    case apiNoSubject = "no_subject";
    case apiSectionIds = 'section_ids';
    case apiGradeIds = 'grade_ids';
    case apiCanDelete = 'can_delete';


}
