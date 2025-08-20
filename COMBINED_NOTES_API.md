# Combined Notes API Documentation

## Overview
The Combined Notes API provides a unified endpoint to retrieve both study notes and behavior notes with filtering capabilities.

## Endpoint
```
GET /api/combined-notes
```

## Authentication
This endpoint requires authentication. Include the Bearer token in the Authorization header:
```
Authorization: Bearer {your_token}
```

## Query Parameters

### Filter Parameters
All filter parameters are optional and can be combined:

- `section_id` (integer): Filter by section ID
- `grade_id` (integer): Filter by grade ID  
- `subject_id` (integer): Filter by subject ID (only affects study notes)
- `student_id` (integer): Filter by student ID

### Pagination Parameters
- `page` (integer, optional): Page number (default: 1)
- `per_page` (integer, optional): Items per page (default: 50)

## Example Requests

### Get all notes
```
GET /api/combined-notes
```

### Filter by section
```
GET /api/combined-notes?section_id=1
```

### Filter by grade
```
GET /api/combined-notes?grade_id=2
```

### Filter by subject (study notes only)
```
GET /api/combined-notes?subject_id=3
```

### Filter by student
```
GET /api/combined-notes?student_id=5
```

### Combine multiple filters
```
GET /api/combined-notes?grade_id=2&section_id=1&subject_id=3
```

### With pagination
```
GET /api/combined-notes?page=2&per_page=50
```

## Response Format

```json
{
    "successful": true,
    "message": "تم عرض الملاحظات الدراسية والسلوكية بنجاح.",
    "data": {
        "study_notes": [
            {
                "id": 1,
                "student_id": 1,
                "school_day_id": 1,
                "subject_id": 1,
                "note_type": "homework",
                "note": "Student completed homework",
                "marks": 85,
                "created_by": "1-John Doe",
                "school_day": {
                    "id": 1,
                    "date": "2025-01-15",
                    "day_name": "Wednesday"
                },
                "subject": {
                    "id": 1,
                    "name": "Mathematics",
                    "code": "MATH101"
                },
                "created_at": "2025-01-15 10:30:00",
                "updated_at": "2025-01-15 10:30:00"
            }
        ],
        "behavior_notes": [
            {
                "id": 1,
                "student_id": 1,
                "school_day_id": 1,
                "behavior_type": "positive",
                "note": "Student helped classmates",
                "created_by": "1-John Doe",
                "school_day": {
                    "id": 1,
                    "date": "2025-01-15",
                    "day_name": "Wednesday"
                },
                "created_at": "2025-01-15 10:30:00",
                "updated_at": "2025-01-15 10:30:00"
            }
        ]
    },
    "page_count": 5,
    "status_code": 200
}
```

## Error Responses

### Validation Error
```json
{
    "successful": false,
    "message": "معرف القسم يجب أن يكون رقماً صحيحاً",
    "status_code": 400
}
```

### Permission Error
```json
{
    "successful": false,
    "message": "ليس لديك صلاحية للقيام بهذا الأمر.",
    "status_code": 403
}
```

## Notes

1. **Subject Filter**: The `subject_id` filter only affects study notes since behavior notes are not associated with subjects.

2. **Relationships**: The API loads the following relationships:
   - Student information (including user details)
   - Student enrollments (section and grade)
   - School day information
   - Subject information (for study notes only)

3. **Permissions**: Users must have both `VIEW_STUDY_NOTES` and `VIEW_BEHAVIOR_NOTES` permissions to access this endpoint.

4. **Performance**: The API uses eager loading to minimize database queries and improve performance.

5. **Pagination**: Uses Laravel's built-in pagination with a default of 50 items per page. The `page_count` field indicates the total number of pages available.
