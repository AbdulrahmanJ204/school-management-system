# School Management System - Component Documentation

## Table of Contents

1. [Service Classes](#service-classes)
2. [Model Classes](#model-classes)
3. [Enums](#enums)
4. [Helper Classes](#helper-classes)
5. [Exception Classes](#exception-classes)
6. [Traits](#traits)
7. [Request Classes](#request-classes)
8. [Resource Classes](#resource-classes)
9. [Console Commands](#console-commands)

---

## Service Classes

Service classes contain the business logic of the application and handle complex operations.

### AssignmentService

**Location:** `app/Services/AssignmentService.php`

**Purpose:** Manages assignment operations including CRUD operations, filtering, and permission checks.

**Dependencies:**

- `PermissionEnum`
- `AssignmentResource`
- `Assignment` model
- `HasPermissionChecks` trait
- `ResponseHelper`

#### Public Methods

##### `listAssignments(Request $request): JsonResponse`

Lists assignments with filtering and pagination support.

**Parameters:**

- `$request` - HTTP request with optional filter parameters

**Filters Available:**

- `subject_id` - Filter by subject
- `section_id` - Filter by section
- `type` - Filter by assignment type
- `assigned_session_id` - Filter by assigned session
- `due_session_id` - Filter by due session
- `date_from` - Filter assignments from date
- `date_to` - Filter assignments to date

**Returns:** Paginated list of assignments (15 per page)

**Permissions Required:** `VIEW_ASSIGNMENTS`

**Example Usage:**

```php
$assignmentService = new AssignmentService();
$request = new Request(['subject_id' => 1, 'type' => 'homework']);
$response = $assignmentService->listAssignments($request);
```

##### `createAssignment(Request $request): JsonResponse`

Creates a new assignment.

**Parameters:**

- `$request` - Validated request with assignment data

**Features:**

- Handles photo upload to `storage/assignments`
- Automatically sets `created_by` to authenticated user
- Loads all relationships in response

**Permissions Required:** `CREATE_ASSIGNMENT`

##### `showAssignment($id): JsonResponse`

Retrieves a single assignment with all relationships.

**Parameters:**

- `$id` - Assignment ID

**Permissions Required:** `VIEW_ASSIGNMENT`

##### `updateAssignment(Request $request, $id): JsonResponse`

Updates an existing assignment.

**Features:**

- Handles photo replacement (deletes old photo)
- Updates only provided fields

**Permissions Required:** `UPDATE_ASSIGNMENT`

##### `deleteAssignment($id): JsonResponse`

Soft deletes an assignment.

**Permissions Required:** `DELETE_ASSIGNMENT`

##### `restoreAssignment($id): JsonResponse`

Restores a soft-deleted assignment.

**Permissions Required:** `MANAGE_DELETED_ASSIGNMENTS`

##### `forceDeleteAssignment($id): JsonResponse`

Permanently deletes an assignment and its associated files.

**Features:**

- Deletes associated photo file from storage
- Permanently removes record from database

**Permissions Required:** `MANAGE_DELETED_ASSIGNMENTS`

---

### AuthService

**Location:** `app/Services/AuthService.php`

**Purpose:** Handles user authentication, registration, and password management.

#### Key Methods

##### `login(LoginRequest $request, string $userType): JsonResponse`

Authenticates a user and returns JWT token.

**Parameters:**

- `$request` - Login credentials
- `$userType` - User type (admin, teacher, student)

**Features:**

- Validates credentials against specific user type
- Updates last login timestamp
- Returns JWT token with user information

##### `register(RegisterRequest $request): JsonResponse`

Registers a new user (admin only operation).

**Features:**

- Creates user account
- Assigns appropriate role based on user type
- Sends welcome email

##### `changePassword(ChangePasswordRequest $request): JsonResponse`

Changes user's password.

**Features:**

- Validates current password
- Updates password with new hash
- Invalidates existing tokens

##### `forgotPassword(ForgotPasswordRequest $request): JsonResponse`

Initiates password reset process.

**Features:**

- Generates reset token
- Sends reset email
- Sets token expiration

##### `resetPassword(ResetPasswordRequest $request): JsonResponse`

Resets password using reset token.

**Features:**

- Validates reset token
- Updates password
- Invalidates reset token

---

### News Service Classes

News management is split into multiple service classes for better organization:

#### NewsService

**Location:** `app/Services/News/NewsService.php`

Main service that coordinates news operations.

#### StoreNews

**Location:** `app/Services/News/StoreNews.php`

**Purpose:** Handles creation of news items.

**Features:**

- Image upload handling
- Target assignment (grades, sections, students)
- Content validation
- Permission checks

#### UpdateNews

**Location:** `app/Services/News/UpdateNews.php`

**Purpose:** Handles updating existing news items.

**Features:**

- Image replacement handling
- Target updates
- Maintains creation metadata

#### ListNews

**Location:** `app/Services/News/ListNews.php`

**Purpose:** Handles listing and filtering news.

**Features:**

- Advanced filtering by category, status, targets
- Search functionality
- Pagination support
- User-specific news filtering

#### ShowNews

**Location:** `app/Services/News/ShowNews.php`

**Purpose:** Displays single news item with all details.

#### SoftDeleteNews

**Location:** `app/Services/News/SoftDeleteNews.php`

**Purpose:** Handles soft deletion of news items.

#### RestoreNews

**Location:** `app/Services/News/RestoreNews.php`

**Purpose:** Restores soft-deleted news items.

#### ForceDeleteNews

**Location:** `app/Services/News/ForceDeleteNews.php`

**Purpose:** Permanently deletes news items and associated files.

---

### File Service Classes

File management follows the same pattern as news:

#### FileService

**Location:** `app/Services/Files/FileService.php`

#### StoreFile

**Features:**

- Multiple file type support
- Virus scanning (if configured)
- File size validation
- MIME type validation
- Target assignment

#### DownloadFile

**Features:**

- Access control
- Download tracking
- Secure file serving
- Range request support

---

### Other Service Classes

#### UserService

- User management operations
- Profile updates
- Account activation/deactivation

#### StudentService

- Student-specific operations
- Enrollment management
- Academic record handling

#### TeacherService

- Teacher-specific operations
- Subject assignment
- Schedule management

#### StudentAttendanceService

- Attendance recording
- Attendance reports
- Bulk attendance operations

#### TeacherAttendanceService

- Teacher attendance tracking
- Leave management
- Substitute teacher handling

---

## Model Classes

### User Model

**Location:** `app/Models/User.php`

**Extends:** `Authenticatable`

**Implements:** `MustVerifyEmail`

**Traits Used:**

- `HasRoles` (Spatie Permission)
- `HasApiTokens` (Laravel Sanctum)
- `HasFactory`
- `Notifiable`

#### Fillable Attributes

```php
[
    'first_name', 'last_name', 'father_name', 'mother_name',
    'birth_date', 'gender', 'email', 'user_name', 'password',
    'phone', 'image', 'user_type', 'last_login'
]
```

#### Hidden Attributes

```php
['password', 'remember_token']
```

#### Casts

```php
[
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'last_login' => 'datetime'
]
```

#### Relationships

##### `devices()`

Many-to-many relationship with Device_info model.

```php
return $this->belongsToMany(Device_info::class, 'user_devices', 'user_id', 'device_id')
    ->withTimestamps();
```

##### `admin()`

One-to-one relationship with Admin model.

```php
return $this->hasOne(Admin::class, 'user_id');
```

##### `teacher()`

One-to-one relationship with Teacher model.

```php
return $this->hasOne(Teacher::class, 'user_id');
```

##### `student()`

One-to-one relationship with Student model.

```php
return $this->hasOne(Student::class, 'user_id');
```

##### `createdAdmins()`, `createdTeachers()`, `createdStudents()`

One-to-many relationships for tracking who created other users.

---

### Assignment Model

**Location:** `app/Models/Assignment.php`

**Extends:** `Model`

**Traits Used:**

- `SoftDeletes`

#### Fillable Attributes

```php
[
    'assigned_session_id', 'due_session_id', 'type',
    'title', 'description', 'photo',
    'subject_id', 'section_id', 'created_by'
]
```

#### Relationships

##### `assignedSession()`

```php
return $this->belongsTo(ClassSession::class, 'assigned_session_id');
```

##### `dueSession()`

```php
return $this->belongsTo(ClassSession::class, 'due_session_id');
```

##### `subject()`

```php
return $this->belongsTo(Subject::class);
```

##### `section()`

```php
return $this->belongsTo(Section::class);
```

##### `createdBy()`

```php
return $this->belongsTo(User::class, 'created_by');
```

---

### StudentAttendance Model

**Location:** `app/Models/StudentAttendance.php`

**Purpose:** Tracks student attendance for class sessions.

#### Fillable Attributes

```php
[
    'student_id', 'class_session_id', 'status', 'notes', 'recorded_by'
]
```

#### Relationships

- `student()` - belongsTo Student
- `classSession()` - belongsTo ClassSession
- `recordedBy()` - belongsTo User

#### Attendance Status Values

- `present` - Student was present
- `absent` - Student was absent
- `late` - Student arrived late
- `excused` - Excused absence

---

### TeacherAttendance Model

**Location:** `app/Models/TeacherAttendance.php`

Similar structure to StudentAttendance but for teachers.

---

## Enums

### PermissionEnum

**Location:** `app/Enums/PermissionEnum.php`

**Type:** String enum

**Purpose:** Defines all system permissions with Arabic names.

#### Permission Categories

##### User Management

- `CREATE_USER` = 'انشاء مستخدم'
- `UPDATE_USER` = 'تعديل مستخدم'
- `VIEW_ADMINS` = 'عرض المشرفين'
- `VIEW_TEACHERS` = 'عرض الاساتذة'
- `VIEW_STUDENTS` = 'عرض الطلاب'
- `DELETE_USER` = 'حذف مستخدم'

##### Assignment Management

- `VIEW_ASSIGNMENTS` = 'عرض الواجبات'
- `CREATE_ASSIGNMENT` = 'انشاء واجب'
- `UPDATE_ASSIGNMENT` = 'تعديل واجب'
- `DELETE_ASSIGNMENT` = 'حذف واجب'
- `MANAGE_DELETED_ASSIGNMENTS` = 'إدارة الواجبات المحذوفة'

#### Static Methods

##### `getAllPermissions(): array`

Returns all permissions as an array.

##### `getModulePermissions(string $module): array`

Returns permissions for a specific module.

**Available Modules:**

- `assignment`, `year`, `semester`, `grade`, `section`
- `student_attendance`, `teacher_attendance`
- `news`, `file`, `exam`, `message`

**Example Usage:**

```php
$assignmentPermissions = PermissionEnum::getModulePermissions('assignment');
// Returns: ['عرض الواجبات', 'انشاء واجب', 'تعديل واجب', ...]
```

---

### UserType Enum

**Location:** `app/Enums/UserType.php`

**Values:**

- `ADMIN` = 'admin'
- `TEACHER` = 'teacher'
- `STUDENT` = 'student'

---

### FileType Enum

**Location:** `app/Enums/FileType.php`

**Values:**

- `DOCUMENT` = 'document'
- `IMAGE` = 'image'
- `VIDEO` = 'video'
- `AUDIO` = 'audio'
- `ARCHIVE` = 'archive'

---

### WeekDay Enum

**Location:** `app/Enums/WeekDay.php`

**Values:**

- `SUNDAY` = 'sunday'
- `MONDAY` = 'monday'
- `TUESDAY` = 'tuesday'
- `WEDNESDAY` = 'wednesday'
- `THURSDAY` = 'thursday'
- `FRIDAY` = 'friday'
- `SATURDAY` = 'saturday'

---

## Helper Classes

### ResponseHelper

**Location:** `app/Helpers/ResponseHelper.php`

**Purpose:** Standardizes API response format across the application.

#### `jsonResponse($data, string $message, int $statusCode, bool $successful): JsonResponse`

**Parameters:**

- `$data` - Response data (can be null)
- `$message` - Response message
- `$statusCode` - HTTP status code (default: 200)
- `$successful` - Success flag (default: true)

**Features:**

- Removes empty data from response
- Consistent response structure
- Proper HTTP status codes

**Response Structure:**

```json
{
  "successful": true,
  "message": "Operation completed successfully",
  "data": {},
  "status_code": 200
}
```

**Example Usage:**

```php
// Success response with data
return ResponseHelper::jsonResponse(
    $assignments,
    'Assignments retrieved successfully'
);

// Success response without data
return ResponseHelper::jsonResponse(
    null,
    'Assignment deleted successfully'
);

// Error response
return ResponseHelper::jsonResponse(
    null,
    'Assignment not found',
    404,
    false
);
```

---

### AuthHelper

**Location:** `app/Helpers/AuthHelper.php`

**Purpose:** Provides authentication-related utility methods.

#### Common Methods

##### `getCurrentUser(): User|null`

Returns the currently authenticated user.

##### `getUserType(): string|null`

Returns the user type of the authenticated user.

##### `hasPermission(string $permission): bool`

Checks if the current user has a specific permission.

##### `isAdmin(): bool`

Checks if the current user is an admin.

##### `isTeacher(): bool`

Checks if the current user is a teacher.

##### `isStudent(): bool`

Checks if the current user is a student.

**Example Usage:**

```php
if (AuthHelper::isAdmin()) {
    // Admin-specific logic
}

if (AuthHelper::hasPermission('انشاء واجب')) {
    // User can create assignments
}
```

---

## Exception Classes

### Custom Exceptions

#### PermissionException

**Location:** `app/Exceptions/PermissionException.php`

**Purpose:** Thrown when user lacks required permissions.

**Usage:**

```php
throw new PermissionException('You do not have permission to perform this action');
```

#### UserNotFoundException

**Location:** `app/Exceptions/UserNotFoundException.php`

**Purpose:** Thrown when a user is not found.

#### InvalidUserTypeException

**Location:** `app/Exceptions/InvalidUserTypeException.php`

**Purpose:** Thrown when an invalid user type is provided.

#### AssignmentNotFoundException

**Location:** `app/Exceptions/AssignmentNotFoundException.php`

**Purpose:** Thrown when an assignment is not found.

#### ImageUploadFailed

**Location:** `app/Exceptions/ImageUploadFailed.php`

**Purpose:** Thrown when image upload fails.

---

## Traits

### HasPermissionChecks

**Location:** `app/Traits/HasPermissionChecks.php`

**Purpose:** Provides permission checking functionality to service classes.

#### Methods

##### `checkPermission(PermissionEnum $permission): void`

Checks if the current user has the specified permission.

**Throws:** `PermissionException` if user lacks permission.

**Example Usage:**

```php
class AssignmentService
{
    use HasPermissionChecks;
  
    public function createAssignment(Request $request)
    {
        $this->checkPermission(PermissionEnum::CREATE_ASSIGNMENT);
        // Continue with assignment creation
    }
}
```

---

### TargetsHandler

**Location:** `app/Traits/TargetsHandler.php`

**Purpose:** Handles target assignment for news and files.

#### Methods

##### `handleTargets(array $targets, Model $model): void`

Assigns targets to a model (news or file).

**Target Types:**

- `grade` - Target specific grade
- `section` - Target specific section
- `student` - Target specific student
- `teacher` - Target specific teacher

---

### NewsAndFilesScopes

**Location:** `app/Traits/NewsAndFilesScopes.php`

**Purpose:** Provides query scopes for news and files based on user access.

#### Scopes

##### `scopeForUser(Builder $query, User $user): Builder`

Filters news/files visible to a specific user.

##### `scopeByTarget(Builder $query, string $targetType, int $targetId): Builder`

Filters by specific target.

---

## Request Classes

Request classes handle validation and authorization for API endpoints.

### Assignment Requests

#### StoreAssignmentRequest

**Location:** `app/Http/Requests/Assignment/StoreAssignmentRequest.php`

**Authorization:** User must have 'انشاء واجب' permission.

**Validation Rules:**

```php
[
    'assigned_session_id' => 'required|exists:class_sessions,id',
    'due_session_id' => 'nullable|exists:class_sessions,id',
    'type' => 'required|in:homework,oral,quiz,project',
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'photo' => 'sometimes|image|max:4096',
    'subject_id' => 'required|exists:subjects,id',
    'section_id' => 'required|exists:sections,id'
]
```

#### UpdateAssignmentRequest

Similar validation rules to StoreAssignmentRequest but for updates.

#### ListAssignmentRequest

**Validation Rules:**

```php
[
    'subject_id' => 'sometimes|exists:subjects,id',
    'section_id' => 'sometimes|exists:sections,id',
    'type' => 'sometimes|in:homework,oral,quiz,project',
    'date_from' => 'sometimes|date',
    'date_to' => 'sometimes|date|after_or_equal:date_from'
]
```

### Authentication Requests

#### LoginRequest

**Validation Rules:**

```php
[
    'email' => 'required|email',
    'password' => 'required|string'
]
```

#### RegisterRequest

**Validation Rules:**

```php
[
    'first_name' => 'required|string|max:255',
    'last_name' => 'required|string|max:255',
    'email' => 'required|email|unique:users',
    'password' => 'required|string|min:8|confirmed',
    'user_type' => 'required|in:admin,teacher,student'
]
```

#### ChangePasswordRequest

**Validation Rules:**

```php
[
    'current_password' => 'required|string',
    'password' => 'required|string|min:8|confirmed'
]
```

---

## Resource Classes

Resource classes transform model data for API responses.

### AssignmentResource

**Location:** `app/Http/Resources/AssignmentResource.php`

**Purpose:** Transforms Assignment model for API responses.

#### Transformed Fields

```php
[
    'id' => $this->id,
    'assigned_session' => [
        'id' => $this->assignedSession->id,
        'date' => $this->assignedSession->date->format('Y-m-d'),
        'time' => $this->assignedSession->time->format('H:i')
    ],
    'due_session' => $this->dueSession ? [
        'id' => $this->dueSession->id,
        'date' => $this->dueSession->date->format('Y-m-d'),
        'time' => $this->dueSession->time->format('H:i')
    ] : null,
    'type' => $this->type,
    'title' => $this->title,
    'description' => $this->description,
    'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
    'subject' => [
        'id' => $this->subject->id,
        'name' => $this->subject->name
    ],
    'section' => [
        'id' => $this->section->id,
        'title' => $this->section->title,
        'grade' => [
            'id' => $this->section->grade->id,
            'name' => $this->section->grade->name
        ]
    ],
    'created_at' => $this->created_at->format('Y-m-d H:i:s'),
    'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
    'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
    'created_by' => [
        'id' => $this->createdBy->id,
        'name' => $this->createdBy->first_name . ' ' . 
                  $this->createdBy->father_name . ' ' . 
                  $this->createdBy->last_name
    ]
]
```

**Features:**

- Formats dates consistently
- Includes full file URLs for photos
- Loads related models (subject, section, grade, creator)
- Handles nullable relationships

### StudentAttendanceResource

**Location:** `app/Http/Resources/StudentAttendanceResource.php`

**Transformed Fields:**

```php
[
    'id' => $this->id,
    'student' => [
        'id' => $this->student->id,
        'name' => $this->student->user->first_name . ' ' . 
                  $this->student->user->last_name,
        'student_id' => $this->student->student_id
    ],
    'class_session' => [
        'id' => $this->classSession->id,
        'date' => $this->classSession->date->format('Y-m-d'),
        'time' => $this->classSession->time->format('H:i'),
        'subject' => $this->classSession->subject->name
    ],
    'status' => $this->status,
    'notes' => $this->notes,
    'recorded_at' => $this->created_at->format('Y-m-d H:i:s')
]
```

---

## Console Commands

### GenerateFullProjectCompact

**Location:** `app/Console/Commands/GenerateFullProjectCompact.php`

**Purpose:** Generates a compact view of the entire project structure.

**Command:** `php artisan generate:project-compact`

**Features:**

- Scans all project files
- Generates hierarchical structure
- Excludes vendor and node_modules
- Creates output file with project overview

**Usage:**

```bash
php artisan generate:project-compact
```

**Output:** Creates `FullProject.php` in project root with complete project structure.

---

## Usage Examples

### Creating a New Service

```php
<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Helpers\ResponseHelper;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;

class CustomService
{
    use HasPermissionChecks;
  
    public function performAction(): JsonResponse
    {
        // Check permissions
        $this->checkPermission(PermissionEnum::CUSTOM_PERMISSION);
      
        // Perform business logic
        $result = $this->businessLogic();
      
        // Return standardized response
        return ResponseHelper::jsonResponse(
            $result,
            'Action performed successfully'
        );
    }
  
    private function businessLogic()
    {
        // Implementation here
    }
}
```

### Creating a New Resource

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'formatted_data' => $this->formatData(),
            // Related models
            'related_model' => $this->whenLoaded('relatedModel', function () {
                return [
                    'id' => $this->relatedModel->id,
                    'name' => $this->relatedModel->name
                ];
            })
        ];
    }
}
```

### Using Permissions in Controllers

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomRequest;
use App\Services\CustomService;

class CustomController extends Controller
{
    protected $customService;
  
    public function __construct(CustomService $customService)
    {
        $this->customService = $customService;
    }
  
    public function index(CustomRequest $request)
    {
        return $this->customService->listItems($request);
    }
  
    public function store(CustomRequest $request)
    {
        return $this->customService->createItem($request);
    }
}
```

This comprehensive component documentation covers all the major classes, services, and utilities in the school management system, providing developers with detailed information about the system's architecture and how to extend or modify it.
