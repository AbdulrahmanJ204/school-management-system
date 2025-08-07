<?php

namespace App\Enums\StringsManager;
enum FileStr: string
{

    // Paths
    case LibraryPath = 'library';
    case Separator = "@_@";
    case GeneralPath = "general";
    case StorageDisk = 'public';

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
    case queryYear = 'year';
    case querySubject = 'subject';
    // Messages
    case messageUpdated = 'messages.file.updated';
    case messageStored = 'messages.file.stored';

    case messageRestored = 'messages.file.restored';
    case messageUnknownType = 'messages.file.unknown_type'; // TODO : i guess it should be auth
    case messageRetrieved = 'messages.file.retrieved';
    case messageForceDelete = 'messages.file.force_delete';
    case messageSoftDelete = 'messages.file.soft_delete';
    case messageNoEnrollments = 'messages.file.no_enrollments'; // TODO : Should Be in student messages i guess
    case apiIpAddress = 'ip_address';
    case apiUserId = 'user_id';
}
