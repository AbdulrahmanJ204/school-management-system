# Assignment Management API Documentation

This document describes the Assignment Management API endpoints for the Laravel School Management System.

## Overview

The Assignment Management API provides endpoints for teachers to create, update, list, and delete assignments, and for students to view assignments in their section.

## Authentication

All endpoints require authentication using Laravel Sanctum. Include the `Authorization: Bearer {token}` header in your requests.

## Base URL

```
https://your-domain.com/api
```

## Teacher Endpoints

### 1. Create New Assignment

**Endpoint:** `POST /api/teacher/assignments`

**Headers:**
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Request Body:**
```json
{
    "assigned_session_id": 15,
    "due_session_id": 23,
    "type": "homework",
    "title": "تمارين الجبر - الوحدة 1",
    "description": "حل جميع التمارين في الصفحات 25-30",
    "subject_id": 5,
    "section_id": 3,
    "photo": "base64_image_string_or_file"
}
```

**Response (201 Created):**
```json
{
    "message": "تم إنشاء التكليف بنجاح",
    "data": {
        "assignment": {
            "id": 1,
            "type": "homework",
            "subjectName": "الرياضيات",
            "teacherName": "أحمد محمود",
            "title": "تمارين الجبر - الوحدة 1",
            "description": "حل جميع التمارين في الصفحات 25-30",
            "creation": {
                "date": "2025-08-17",
                "periodNumber": 3
            },
            "delivery": {
                "date": "2025-08-19",
                "periodNumber": 3
            },
            "imageUrl": "assets/images/assignments/assignment_1.jpg"
        }
    },
    "status_code": 201
}
```

**Validation Rules:**
- `assigned_session_id`: Required, must exist in class_sessions table
- `due_session_id`: Optional, must exist in class_sessions table
- `type`: Required, must be one of: homework, oral, quiz
- `title`: Required, string, max 255 characters
- `description`: Required, string
- `photo`: Optional, base64 encoded image string
- `subject_id`: Required, must exist in subjects table
- `section_id`: Required, must exist in sections table

**Authorization:**
- User must be authenticated as a teacher
- Teacher must have access to the specified subject and section
- Assigned and due sessions must belong to the authenticated teacher

### 2. Update Existing Assignment

**Endpoint:** `PUT /api/teacher/assignments/{id}`

**Headers:**
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Request Body:** (Same as create, but all fields are optional)

**Response (200 OK):**
```json
{
    "message": "تم تحديث التكليف بنجاح",
    "data": {
        "assignment": {
            "id": 1,
            "type": "homework",
            "subjectName": "الرياضيات",
            "teacherName": "أحمد محمود",
            "title": "تمارين الجبر المحدثة - الوحدة 1",
            "description": "حل جميع التمارين في الصفحات 25-35 مع إضافة الأمثلة",
            "creation": {
                "date": "2025-08-17",
                "periodNumber": 3
            },
            "delivery": {
                "date": "2025-08-21",
                "periodNumber": 4
            },
            "imageUrl": "assets/images/assignments/assignment_1_updated.jpg"
        }
    },
    "status_code": 200
}
```

**Authorization:**
- User must be authenticated as a teacher
- Teacher must own the assignment being updated

### 3. List All Assignments (Teacher View)

**Endpoint:** `GET /api/teacher/assignments`

**Headers:**
- `Authorization: Bearer {token}`

**Query Parameters:**
- `subject_id` (optional): Filter by subject ID
- `section_id` (optional): Filter by section ID
- `type` (optional): Filter by assignment type (homework, oral, quiz)
- `date_from` (optional): Filter assignments from this date (YYYY-MM-DD)
- `date_to` (optional): Filter assignments until this date (YYYY-MM-DD)

**Example Request:**
```
GET /api/teacher/assignments?subject_id=5&section_id=3&type=homework
```

**Response (200 OK):**
```json
{
    "message": "قائمة التكليفات",
    "data": {
        "assignments": [
            {
                "id": 1,
                "type": "homework",
                "subjectName": "الرياضيات",
                "teacherName": "أحمد محمود",
                "title": "تمارين الجبر - الوحدة 1",
                "description": "حل جميع التمارين في الصفحات 25-30",
                "creation": {
                    "date": "2025-08-17",
                    "periodNumber": 3
                },
                "delivery": {
                    "date": "2025-08-19",
                    "periodNumber": 3
                },
                "imageUrl": "assets/images/assignments/assignment_1.jpg"
            }
        ]
    },
    "status_code": 200
}
```

**Authorization:**
- User must be authenticated as a teacher
- Only shows assignments created by the authenticated teacher

### 4. Delete Assignment

**Endpoint:** `DELETE /api/teacher/assignments/{id}`

**Headers:**
- `Authorization: Bearer {token}`

**Response (200 OK):**
```json
{
    "message": "تم حذف التكليف بنجاح",
    "data": null,
    "status_code": 200
}
```

**Authorization:**
- User must be authenticated as a teacher
- Teacher must own the assignment being deleted

## Student Endpoints

### 5. List Section Assignments (Student View)

**Endpoint:** `GET /api/student/assignments`

**Headers:**
- `Authorization: Bearer {token}`

**Query Parameters:**
- `subject_id` (optional): Filter by subject ID
- `type` (optional): Filter by assignment type (homework, oral, quiz)
- `date_from` (optional): Filter assignments from this date (YYYY-MM-DD)
- `date_to` (optional): Filter assignments until this date (YYYY-MM-DD)

**Example Request:**
```
GET /api/student/assignments?subject_id=5&type=homework
```

**Response (200 OK):**
```json
{
    "message": "قائمة التكليفات",
    "data": {
        "assignments": [
            {
                "id": 1,
                "type": "homework",
                "subjectName": "الرياضيات",
                "teacherName": "أحمد محمود",
                "title": "تمارين الجبر - الوحدة 1",
                "description": "حل جميع التمارين في الصفحات 25-30",
                "creation": {
                    "date": "2025-08-17",
                    "periodNumber": 3
                },
                "delivery": {
                    "date": "2025-08-19",
                    "periodNumber": 3
                },
                "imageUrl": "assets/images/assignments/assignment_1.jpg"
            }
        ]
    },
    "status_code": 200
}
```

**Authorization:**
- User must be authenticated as a student
- Only shows assignments for the student's current section

## Error Responses

### Validation Error (422 Unprocessable Entity)
```json
{
    "message": "فشل في التحقق من صحة البيانات",
    "errors": {
        "title": ["حقل العنوان مطلوب"],
        "assigned_session_id": ["معرف جلسة التكليف مطلوب"],
        "subject_id": ["معرف المادة مطلوب"]
    },
    "status_code": 422
}
```

### Not Found Error (404 Not Found)
```json
{
    "message": "التكليف غير موجود",
    "data": null,
    "status_code": 404
}
```

### Unauthorized Error (403 Forbidden)
```json
{
    "message": "غير مصرح لك بالوصول لهذا التكليف",
    "data": null,
    "status_code": 403
}
```

### Server Error (500 Internal Server Error)
```json
{
    "message": "حدث خطأ في الخادم",
    "data": null,
    "status_code": 500
}
```

## Data Models

### Assignment Model
```php
class Assignment extends Model
{
    protected $fillable = [
        'assigned_session_id',
        'due_session_id',
        'type',
        'title',
        'description',
        'photo',
        'subject_id',
        'section_id',
        'created_by'
    ];
}
```

### Relationships
- `assignedSession`: Belongs to ClassSession (when assignment was given)
- `dueSession`: Belongs to ClassSession (when assignment is due)
- `subject`: Belongs to Subject
- `section`: Belongs to Section
- `createdBy`: Belongs to User (teacher who created the assignment)

## Image Handling

### Base64 Image Upload
The API accepts base64 encoded images for the `photo` field. The image will be:
1. Decoded from base64
2. Stored in the `storage/app/public/assignments/` directory
3. Accessible via the `imageUrl` field in responses

### Image Storage
- Images are stored with unique filenames: `assignment_{timestamp}_{random}.jpg`
- Old images are automatically deleted when assignments are updated or deleted
- Images are served from the public storage disk

## Security Features

### Authorization
- Teachers can only manage their own assignments
- Students can only view assignments for their current section
- All endpoints require proper authentication

### Validation
- Comprehensive input validation for all fields
- Business logic validation (e.g., teacher access to subject/section)
- SQL injection protection through Laravel's query builder

### Rate Limiting
- All endpoints are protected by rate limiting middleware
- Default limit: 60 requests per minute per user

## Usage Examples

### Creating an Assignment (JavaScript)
```javascript
const response = await fetch('/api/teacher/assignments', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        assigned_session_id: 15,
        due_session_id: 23,
        type: 'homework',
        title: 'تمارين الجبر - الوحدة 1',
        description: 'حل جميع التمارين في الصفحات 25-30',
        subject_id: 5,
        section_id: 3
    })
});

const data = await response.json();
console.log(data.message); // "تم إنشاء التكليف بنجاح"
```

### Listing Assignments (JavaScript)
```javascript
const response = await fetch('/api/teacher/assignments?subject_id=5&type=homework', {
    headers: {
        'Authorization': `Bearer ${token}`
    }
});

const data = await response.json();
data.data.assignments.forEach(assignment => {
    console.log(`${assignment.title} - ${assignment.subjectName}`);
});
```

## Notes

1. **Period Numbers**: The `periodNumber` field represents the class period order (1st period, 2nd period, etc.)
2. **Dates**: All dates are returned in YYYY-MM-DD format
3. **Arabic Support**: All messages and field names support Arabic text
4. **Soft Deletes**: Assignments are soft deleted, so they can be restored if needed
5. **Eager Loading**: The API uses eager loading to optimize database queries and reduce N+1 problems

## Support

For technical support or questions about the API, please contact the development team.

