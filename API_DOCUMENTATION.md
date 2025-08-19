# School Management System API Documentation

## Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [API Response Format](#api-response-format)
4. [User Management](#user-management)
5. [Assignment Management](#assignment-management)
6. [Attendance Management](#attendance-management)
7. [News Management](#news-management)
8. [File Management](#file-management)
9. [Quiz Management](#quiz-management)
10. [Academic Management](#academic-management)
11. [Permission System](#permission-system)
12. [Error Handling](#error-handling)
13. [Rate Limiting](#rate-limiting)

---

## Overview

This is a comprehensive school management system built with Laravel 11, featuring role-based permissions, attendance tracking, assignment management, news distribution, and academic record keeping. The system supports multiple user types: Admin, Teacher, and Student.

**Base URL:** `http://your-domain.com/api`

**API Version:** v1

**Content-Type:** `application/json`

---

## Authentication

The system uses JWT (JSON Web Token) authentication with Sanctum for API access.

### Login

```http
POST /auth/login?user_type={admin|teacher|student}
```

**Request:**
```json
{
  "email": "admin@school.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "successful": true,
  "message": "تم تسجيل الدخول بنجاح",
  "data": {
    "user": {
      "id": 1,
      "first_name": "Ahmed",
      "last_name": "Ali",
      "email": "admin@school.com",
      "user_type": "admin"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 3600
  },
  "status_code": 200
}
```

### Register User (Admin Only)

```http
POST /register
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request:**
```json
{
  "first_name": "Mohammed",
  "last_name": "Hassan",
  "father_name": "Ali",
  "mother_name": "Fatima",
  "birth_date": "1990-05-15",
  "gender": "male",
  "email": "teacher@school.com",
  "user_name": "mhassan",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+1234567890",
  "user_type": "teacher"
}
```

### Refresh Token

```http
POST /auth/refresh
```

**Headers:**
```
Authorization: Bearer {token}
```

### Logout

```http
POST /auth/logout
```

**Headers:**
```
Authorization: Bearer {token}
```

### Change Password

```http
POST /change-password
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request:**
```json
{
  "current_password": "oldpassword",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

### Forgot Password

```http
POST /auth/forgot-password
```

**Request:**
```json
{
  "email": "user@school.com"
}
```

### Reset Password

```http
POST /auth/reset-password
```

**Request:**
```json
{
  "email": "user@school.com",
  "token": "reset_token_here",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

---

## API Response Format

All API responses follow a consistent format:

**Success Response:**
```json
{
  "successful": true,
  "message": "Operation completed successfully",
  "data": {
    // Response data here
  },
  "status_code": 200
}
```

**Error Response:**
```json
{
  "successful": false,
  "message": "Error message here",
  "status_code": 400
}
```

**Paginated Response:**
```json
{
  "successful": true,
  "message": "Data retrieved successfully",
  "data": {
    "data": [
      // Array of items
    ],
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  },
  "status_code": 200
}
```

---

## User Management

### Get Staff Members (Admin Only)

```http
GET /staff
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "successful": true,
  "message": "Staff retrieved successfully",
  "data": [
    {
      "id": 1,
      "first_name": "Ahmed",
      "last_name": "Ali",
      "email": "admin@school.com",
      "user_type": "admin",
      "roles": ["Super Admin"]
    }
  ],
  "status_code": 200
}
```

### Get User Details

```http
GET /users/{id}
```

### Update User

```http
POST /users/{id}
```

**Request:**
```json
{
  "first_name": "Updated Name",
  "last_name": "Updated Last",
  "email": "updated@school.com",
  "phone": "+1234567890"
}
```

### Delete User

```http
DELETE /users/{id}
```

---

## Assignment Management

The assignment system allows teachers to create, manage, and track student assignments.

### List Assignments

```http
GET /assignments
```

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `subject_id` (integer): Filter by subject
- `section_id` (integer): Filter by section
- `type` (string): Filter by type (homework, oral, quiz, project)
- `assigned_session_id` (integer): Filter by assigned session
- `due_session_id` (integer): Filter by due session
- `date_from` (date): Filter assignments from date
- `date_to` (date): Filter assignments to date
- `page` (integer): Page number for pagination

**Example:**
```http
GET /assignments?subject_id=1&type=homework&page=1
```

**Response:**
```json
{
  "successful": true,
  "message": "تم عرض الواجبات بنجاح",
  "data": {
    "data": [
      {
        "id": 1,
        "assigned_session": {
          "id": 1,
          "date": "2024-01-15",
          "time": "08:00"
        },
        "due_session": {
          "id": 2,
          "date": "2024-01-17",
          "time": "08:00"
        },
        "type": "homework",
        "title": "Math Chapter 5 Exercises",
        "description": "Complete exercises 1-20 from Chapter 5",
        "photo": "http://domain.com/storage/assignments/photo.jpg",
        "subject": {
          "id": 1,
          "name": "Mathematics"
        },
        "section": {
          "id": 1,
          "title": "Section A",
          "grade": {
            "id": 1,
            "name": "Grade 10"
          }
        },
        "created_at": "2024-01-15 08:00:00",
        "updated_at": "2024-01-15 08:00:00",
        "deleted_at": null,
        "created_by": {
          "id": 2,
          "name": "Ahmed Ali Hassan"
        }
      }
    ],
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 42
  },
  "status_code": 200
}
```

### Create Assignment

```http
POST /assignments/store
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:**
```json
{
  "assigned_session_id": 1,
  "due_session_id": 2,
  "type": "homework",
  "title": "Math Chapter 5 Exercises",
  "description": "Complete exercises 1-20 from Chapter 5",
  "photo": "file_upload",
  "subject_id": 1,
  "section_id": 1
}
```

**Validation Rules:**
- `assigned_session_id`: required, must exist in class_sessions table
- `due_session_id`: optional, must exist in class_sessions table
- `type`: required, must be one of: homework, oral, quiz, project
- `title`: required, string, max 255 characters
- `description`: required, string
- `photo`: optional, image file, max 4MB
- `subject_id`: required, must exist in subjects table
- `section_id`: required, must exist in sections table

**Response:**
```json
{
  "successful": true,
  "message": "تم إنشاء الواجب بنجاح",
  "data": {
    // Assignment object with relationships loaded
  },
  "status_code": 201
}
```

### Get Assignment Details

```http
GET /assignments/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "successful": true,
  "message": "تم عرض الواجب بنجاح",
  "data": {
    // Complete assignment object with all relationships
  },
  "status_code": 200
}
```

### Update Assignment

```http
POST /assignments/{id}
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:** Same as create assignment

### Delete Assignment (Soft Delete)

```http
DELETE /assignments/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "successful": true,
  "message": "تم حذف الواجب بنجاح",
  "status_code": 200
}
```

### Restore Assignment

```http
POST /assignments/restore/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

### Permanently Delete Assignment

```http
DELETE /assignments/delete/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

---

## Attendance Management

### Student Attendance

#### List Student Attendances

```http
GET /student-attendances
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "successful": true,
  "message": "Student attendances retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "student": {
          "id": 1,
          "first_name": "Sara",
          "last_name": "Ahmed"
        },
        "class_session": {
          "id": 1,
          "date": "2024-01-15",
          "time": "08:00"
        },
        "status": "present",
        "notes": "On time",
        "recorded_at": "2024-01-15 08:05:00"
      }
    ]
  },
  "status_code": 200
}
```

#### Create Student Attendance

```http
POST /student-attendances/store
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request:**
```json
{
  "student_id": 1,
  "class_session_id": 1,
  "status": "present",
  "notes": "On time"
}
```

**Validation Rules:**
- `student_id`: required, must exist in students table
- `class_session_id`: required, must exist in class_sessions table
- `status`: required, must be one of: present, absent, late, excused
- `notes`: optional, string

#### Update Student Attendance

```http
POST /student-attendances/{id}
```

#### Delete Student Attendance

```http
DELETE /student-attendances/{id}
```

### Teacher Attendance

Teacher attendance follows the same pattern as student attendance but with different endpoints:

- `GET /teacher-attendances`
- `POST /teacher-attendances/store`
- `POST /teacher-attendances/{id}`
- `DELETE /teacher-attendances/{id}`

---

## News Management

### List News

```http
GET /news
```

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `search` (string): Search in title and content
- `category` (string): Filter by category
- `status` (string): Filter by status
- `target_type` (string): Filter by target type
- `page` (integer): Page number

**Response:**
```json
{
  "successful": true,
  "message": "News retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "title": "School Holiday Announcement",
        "content": "The school will be closed on...",
        "category": "announcement",
        "status": "published",
        "image": "http://domain.com/storage/news/image.jpg",
        "targets": [
          {
            "target_type": "grade",
            "target_id": 1
          }
        ],
        "created_at": "2024-01-15 08:00:00"
      }
    ]
  },
  "status_code": 200
}
```

### Create News

```http
POST /news/store
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request:**
```json
{
  "title": "Important Announcement",
  "content": "This is an important announcement...",
  "category": "announcement",
  "status": "published",
  "image": "file_upload",
  "targets": [
    {
      "target_type": "grade",
      "target_id": 1
    }
  ]
}
```

### Get News Details

```http
GET /news/{id}
```

### Update News

```http
POST /news/{id}
```

### Delete News (Soft Delete)

```http
DELETE /news/{id}
```

### Restore News

```http
POST /news/restore/{id}
```

### Permanently Delete News

```http
DELETE /news/delete/{id}
```

---

## File Management

### List Files

```http
GET /files
```

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `subject_id` (integer): Filter by subject
- `file_type` (string): Filter by file type
- `search` (string): Search in filename and description

### Get Files by Subject

```http
GET /files/subject/{id}
```

### Upload File

```http
POST /files/store
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request:**
```json
{
  "file": "file_upload",
  "title": "Chapter 5 Notes",
  "description": "Math chapter 5 study notes",
  "subject_id": 1,
  "file_type": "document",
  "targets": [
    {
      "target_type": "section",
      "target_id": 1
    }
  ]
}
```

### Download File

```http
GET /files/download/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** File download with appropriate headers

### Get File Details

```http
GET /files/{id}
```

### Update File

```http
POST /files/{id}
```

### Delete File (Soft Delete)

```http
DELETE /files/{id}
```

### Restore File

```http
POST /files/restore/{id}
```

### Permanently Delete File

```http
DELETE /files/delete/{id}
```

---

## Quiz Management

### List Quizzes

```http
GET /quizzes
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "successful": true,
  "message": "Quizzes retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "Math Quiz - Chapter 5",
      "description": "Quiz covering algebra basics",
      "subject_id": 1,
      "duration_minutes": 30,
      "total_marks": 20,
      "is_active": true,
      "questions_count": 10,
      "created_at": "2024-01-15 08:00:00"
    }
  ],
  "status_code": 200
}
```

### Create Quiz

```http
POST /quizzes
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request:**
```json
{
  "title": "Math Quiz - Chapter 5",
  "description": "Quiz covering algebra basics",
  "subject_id": 1,
  "duration_minutes": 30,
  "total_marks": 20,
  "targets": [
    {
      "target_type": "section",
      "target_id": 1
    }
  ]
}
```

### Get Quiz Details

```http
GET /quiz/{id}
```

### Update Quiz

```http
PUT /quizzes/{id}
```

### Delete Quiz

```http
DELETE /quizzes/{id}
```

### Activate Quiz

```http
PUT /quizzes/{id}/activate
```

### Deactivate Quiz

```http
PUT /quizzes/{id}/deactivate
```

### Add Question to Quiz

```http
POST /quizzes/{quiz_id}/questions
```

**Request:**
```json
{
  "question_text": "What is 2 + 2?",
  "question_type": "multiple_choice",
  "options": [
    {"text": "3", "is_correct": false},
    {"text": "4", "is_correct": true},
    {"text": "5", "is_correct": false},
    {"text": "6", "is_correct": false}
  ],
  "marks": 2
}
```

### Update Question

```http
POST /quizzes/{quiz_id}/questions/{question_id}
```

### Delete Question

```http
DELETE /quizzes/{quiz_id}/questions/{question_id}
```

### Submit Quiz (Student)

```http
POST /score-quizzes
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request:**
```json
{
  "quiz_id": 1,
  "answers": [
    {
      "question_id": 1,
      "selected_option_id": 2
    },
    {
      "question_id": 2,
      "selected_option_id": 5
    }
  ]
}
```

---

## Academic Management

### Years

#### List Academic Years

```http
GET /years
```

#### Create Academic Year

```http
POST /years
```

**Request:**
```json
{
  "name": "2024-2025",
  "start_date": "2024-09-01",
  "end_date": "2025-06-30",
  "is_active": true
}
```

#### Update Academic Year

```http
PUT /years/{id}
```

#### Delete Academic Year

```http
DELETE /years/{id}
```

### Semesters

#### List Semesters

```http
GET /semesters
```

#### Create Semester

```http
POST /semesters
```

**Request:**
```json
{
  "name": "First Semester",
  "year_id": 1,
  "start_date": "2024-09-01",
  "end_date": "2025-01-31",
  "is_active": true
}
```

### Grades

#### List Grades

```http
GET /grades
```

#### Create Grade

```http
POST /grades
```

**Request:**
```json
{
  "name": "Grade 10",
  "level": 10,
  "description": "Tenth grade level"
}
```

### Sections

#### List Sections

```http
GET /sections
```

#### Create Section

```http
POST /sections
```

**Request:**
```json
{
  "title": "Section A",
  "grade_id": 1,
  "capacity": 30,
  "description": "Science section"
}
```

### Subjects

#### List Subjects

```http
GET /subjects
```

#### Create Subject

```http
POST /subjects
```

**Request:**
```json
{
  "name": "Mathematics",
  "main_subject_id": 1,
  "code": "MATH101",
  "credits": 3
}
```

### Exams

#### List Exams

```http
GET /exams
```

#### Get Exams by School Day

```http
GET /exams/school-day/{schoolDayId}
```

#### Get Exams by Grade

```http
GET /exams/grade/{gradeId}
```

#### Create Exam

```http
POST /exams
```

**Request:**
```json
{
  "title": "Math Midterm Exam",
  "subject_id": 1,
  "grade_id": 1,
  "school_day_id": 1,
  "exam_date": "2024-02-15",
  "start_time": "09:00",
  "duration_minutes": 120,
  "total_marks": 100
}
```

#### Update Exam

```http
PUT /exams/{id}
```

#### Delete Exam

```http
DELETE /exams/{id}
```

#### Get Trashed Exams

```http
GET /exams/trashed
```

#### Restore Exam

```http
PATCH /exams/{id}/restore
```

#### Force Delete Exam

```http
DELETE /exams/{id}/force-delete
```

---

## Permission System

The system uses Spatie Laravel Permission package for role-based access control.

### Available Permissions

The system defines permissions for each major module:

#### User Management
- انشاء مستخدم (Create User)
- تعديل مستخدم (Update User)
- عرض المشرفين (View Admins)
- عرض الاساتذة (View Teachers)
- عرض الطلاب (View Students)
- حذف مستخدم (Delete User)

#### Assignment Management
- عرض الواجبات (View Assignments)
- انشاء واجب (Create Assignment)
- عرض واجب (View Assignment)
- تعديل واجب (Update Assignment)
- حذف واجب (Delete Assignment)
- إدارة الواجبات المحذوفة (Manage Deleted Assignments)

#### Attendance Management
- عرض حضور الطلاب (View Student Attendances)
- إضافة حضور الطلاب (Create Student Attendance)
- تعديل حضور الطلاب (Update Student Attendance)
- حذف حضور الطلاب (Delete Student Attendance)

### Get All Permissions

```http
GET /permissions
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "successful": true,
  "message": "Permissions retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "انشاء مستخدم",
      "guard_name": "api"
    }
  ],
  "status_code": 200
}
```

### Role Management

#### List Roles

```http
GET /roles
```

#### Create Role

```http
POST /roles
```

**Request:**
```json
{
  "name": "Teacher",
  "permissions": [
    "عرض الواجبات",
    "انشاء واجب",
    "تعديل واجب"
  ]
}
```

#### Update Role

```http
PUT /roles/{id}
```

#### Delete Role

```http
DELETE /roles/{id}
```

---

## Error Handling

### Common HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Internal Server Error

### Validation Errors

```json
{
  "successful": false,
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  },
  "status_code": 422
}
```

### Permission Errors

```json
{
  "successful": false,
  "message": "You don't have permission to perform this action.",
  "status_code": 403
}
```

### Authentication Errors

```json
{
  "successful": false,
  "message": "Unauthenticated.",
  "status_code": 401
}
```

---

## Rate Limiting

The API implements rate limiting to prevent abuse:

- **Authentication endpoints**: 10 requests per minute
- **Password reset endpoints**: 5 requests per minute
- **Admin operations**: 5 requests per minute
- **General API**: 60 requests per minute

When rate limit is exceeded:

```json
{
  "successful": false,
  "message": "Too many requests. Please try again later.",
  "status_code": 429
}
```

---

## Models and Relationships

### User Model

**Fillable Attributes:**
- `first_name`, `last_name`, `father_name`, `mother_name`
- `birth_date`, `gender`, `email`, `user_name`, `password`
- `phone`, `image`, `user_type`, `last_login`

**Relationships:**
- `admin()` - hasOne relationship to Admin model
- `teacher()` - hasOne relationship to Teacher model
- `student()` - hasOne relationship to Student model
- `devices()` - belongsToMany relationship to Device_info model

### Assignment Model

**Fillable Attributes:**
- `assigned_session_id`, `due_session_id`, `type`
- `title`, `description`, `photo`
- `subject_id`, `section_id`, `created_by`

**Relationships:**
- `assignedSession()` - belongsTo ClassSession
- `dueSession()` - belongsTo ClassSession
- `subject()` - belongsTo Subject
- `section()` - belongsTo Section
- `createdBy()` - belongsTo User

**Assignment Types:**
- `homework` - Regular homework assignments
- `oral` - Oral presentation assignments
- `quiz` - Quiz assignments
- `project` - Project assignments

---

## Helper Classes

### ResponseHelper

A utility class for standardizing API responses:

```php
ResponseHelper::jsonResponse($data, $message, $statusCode, $successful)
```

**Parameters:**
- `$data` - Response data (optional)
- `$message` - Response message
- `$statusCode` - HTTP status code (default: 200)
- `$successful` - Success flag (default: true)

**Usage Example:**
```php
return ResponseHelper::jsonResponse(
    $assignments,
    'Assignments retrieved successfully',
    200,
    true
);
```

---

## Testing Examples

### Using cURL

#### Login Example
```bash
curl -X POST "http://your-domain.com/api/auth/login?user_type=admin" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@school.com",
    "password": "password123"
  }'
```

#### Create Assignment Example
```bash
curl -X POST "http://your-domain.com/api/assignments/store" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "assigned_session_id": 1,
    "due_session_id": 2,
    "type": "homework",
    "title": "Math Homework",
    "description": "Complete exercises 1-10",
    "subject_id": 1,
    "section_id": 1
  }'
```

### Using JavaScript (Fetch API)

```javascript
// Login
const login = async () => {
  const response = await fetch('/api/auth/login?user_type=admin', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email: 'admin@school.com',
      password: 'password123'
    })
  });
  
  const data = await response.json();
  if (data.successful) {
    localStorage.setItem('token', data.data.token);
  }
  return data;
};

// Get Assignments
const getAssignments = async () => {
  const token = localStorage.getItem('token');
  const response = await fetch('/api/assignments', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    }
  });
  
  return await response.json();
};
```

---

## Deployment Notes

### Environment Variables

Ensure these environment variables are properly configured:

```env
APP_URL=http://your-domain.com
JWT_SECRET=your-jwt-secret
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_db
DB_USERNAME=db_user
DB_PASSWORD=db_password

# File Storage
FILESYSTEM_DISK=public

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

### Required Permissions

The storage directory must be writable:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Database Setup

1. Run migrations:
```bash
php artisan migrate
```

2. Seed the database:
```bash
php artisan db:seed
```

3. Create storage link:
```bash
php artisan storage:link
```

---

This documentation covers all the major API endpoints and functionality of the school management system. Each endpoint includes proper authentication, validation, and permission checks to ensure secure operation.
