<?php

namespace App\Enums\StringsManager\Files;
enum FileStr: string
{

    // Paths
    case LibraryPath = 'library';
    case Separator = "@_@";
    case GeneralPath = "general";
    case StorageDisk = 'public';

    // Messages
    case messageUpdated = 'messages.file.updated';
    case messageStored = 'messages.file.stored';

    case messageRestored = 'messages.file.restored';
    case messageUnknownType = 'messages.file.unknown_type'; // TODO : i guess it should be auth
    case messageRetrieved = 'messages.file.retrieved';
    case messageForceDelete = 'messages.file.force_delete';
    case messageSoftDelete = 'messages.file.soft_delete';
    case messageNoEnrollments = 'messages.file.no_enrollments'; // TODO : Should Be in student messages i guess
}

