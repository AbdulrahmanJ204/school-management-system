<?php
namespace App\Enums\StringsManager;
enum NewsStr: string{
    // paths
    case newsPath  = 'news_images';
    // API keys
    case apiTitle= 'title';
    case apiContent = 'content';
    case apiPhoto = 'photo';
    case apiSectionIds = 'section_ids';
    case apiGradeIds = 'grade_ids';
    case apiIsGeneral = 'is_general';


    case apiRemovePhoto = 'remove_photo';
    case StorageDisk = 'public';

    case messageNoEnrollments = 'messages.student.no_enrollments';
    case messageRetrieved = 'messages.news.retrieved';
    case messageForceDelete = 'messages.news.force_delete';
    case messageSoftDelete = 'messages.news.soft_delete';
    case messageShow = 'messages.news.show';
    case messageRestored = 'messages.news.restored';
    case messageUpdated = 'messages.news.updated';
}
