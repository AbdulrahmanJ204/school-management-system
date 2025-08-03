<?php

namespace App\Enums\StringsManager;
enum FileStr: string
{

    // Paths
    case LibraryPath = 'library';
    case Separator = "@_@";
    case GeneralPath = "general";

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
    // Messages
    case messageUpdated = 'messages.file.updated';
    case messageStored = 'messages.file.stored';

    case messageRestored = 'messages.file.restored';
    case messageUnknownType = 'messages.file.unknown_type'; // TODO : i guess it should be auth
    case messageFilesRetrieved = 'messages.file.files_retrieved';
    case messageDeletePermanent = 'messages.file.delete_permanent';
    case messageDeleted = 'messages.file.deleted';
}
