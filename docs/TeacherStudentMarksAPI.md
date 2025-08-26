# Teacher Student Marks Management API

## Overview
This API allows teachers to add or update student marks for subjects they are authorized to teach. The API automatically calculates the total mark based on the subject's percentage weights and handles both creation and updates seamlessly.

## Endpoint
```
POST /api/teacher/students/{student_id}/marks
```

## Authentication
- **Required**: Bearer token authentication
- **User Type**: Teacher only
- **Middleware**: `auth:api`, `user_type:teacher`

## Request Body
```json
{
    "subject_id": 1,
    "semester_id": 2,
    "section_id": 3,
    "homework": 85,
    "oral": 90,
    "activity": 88,
    "quiz": 92,
    "exam": 87
}
```

### Field Descriptions
- `subject_id` (required): ID of the subject to grade
- `semester_id` (required): ID of the semester to add/update marks for
- `section_id` (required): ID of the section the student belongs to
- `homework` (optional): Homework mark (0 to subject's full_mark)
- `oral` (optional): Oral mark (0 to subject's full_mark)
- `activity` (optional): Activity mark (0 to subject's full_mark)
- `quiz` (optional): Quiz mark (0 to subject's full_mark)
- `exam` (optional): Exam mark (0 to subject's full_mark)

**Note**: At least one mark field must be provided.

## Response Format

### Success Response (New Marks Created - 201)
```json
{
    "successful": true,
    "message": "تم حفظ علامات الطالب بنجاح",
    "data": {
        "student_id": 1,
        "subject_id": 1,
        "homework": 85,
        "oral": 90,
        "activity": 88,
        "quiz": 92,
        "exam": 87,
        "total": 88.25,
        "enrollment_id": 15,
        "id": 23
    },
    "status_code": 201
}
```

### Success Response (Marks Updated - 200)
```json
{
    "successful": true,
    "message": "تم تحديث علامات الطالب بنجاح",
    "data": {
        "student_id": 1,
        "subject_id": 1,
        "homework": 85,
        "oral": 90,
        "activity": 88,
        "quiz": 92,
        "exam": 87,
        "total": 88.25,
        "enrollment_id": 15,
        "id": 23
    },
    "status_code": 200
}
```

### Error Responses

#### Validation Error (422)
```json
{
    "successful": false,
    "message": "Validation failed",
    "errors": {
        "homework": ["علامة الواجب يجب ألا تتجاوز الدرجة الكاملة للمادة"],
        "subject_id": ["المادة المحددة غير موجودة"]
    },
    "status_code": 422
}
```

#### Unauthorized (403)
```json
{
    "successful": false,
    "message": "غير مصرح لك بتدريس هذه المادة",
    "status_code": 403
}
```

#### Not Found (404)
```json
{
    "successful": false,
    "message": "الطالب غير موجود",
    "status_code": 404
}
```

## Business Logic

### Authorization & Validation
1. **Teacher Verification**: Ensures the authenticated user is a teacher
2. **Subject Assignment**: Verifies the teacher is assigned to teach the specified subject for the specified section
3. **Student Enrollment**: Confirms the student is enrolled in the specified semester and section
4. **Section Match**: Ensures the student is in the section specified in the request

### Mark Processing
1. **Insert/Update Logic**: Uses `firstOrNew()` pattern to handle both scenarios
2. **Total Calculation**: Automatically calculates weighted total based on subject percentages:
   ```
   total = (homework × homework_percentage/100) + 
           (oral × oral_percentage/100) + 
           (activity × activity_percentage/100) + 
           (quiz × quiz_percentage/100) + 
           (exam × exam_percentage/100)
   ```
3. **Null Handling**: Allows individual mark types to be null if not yet graded
4. **Mark Validation**: Ensures marks don't exceed the subject's `full_mark` value

### Data Integrity
- **Enrollment Linking**: Automatically links marks to the current semester enrollment
- **Audit Trail**: Records the teacher who created/updated the marks
- **Transaction Safety**: Uses database transactions for data consistency

## Validation Rules

### Request Validation
- `subject_id`: required, integer, exists in subjects table
- `semester_id`: required, integer, exists in semesters table
- `section_id`: required, integer, exists in sections table
- `homework`: nullable, integer, min:0, max:(subject's full_mark)
- `oral`: nullable, integer, min:0, max:(subject's full_mark)
- `activity`: nullable, integer, min:0, max:(subject's full_mark)
- `quiz`: nullable, integer, min:0, max:(subject's full_mark)
- `exam`: nullable, integer, min:0, max:(subject's full_mark)

### Business Validation
- Teacher must be assigned to teach the subject for the specified section
- Student must be enrolled in the specified semester and section
- Subject must have valid percentage allocations that sum to 100%
- At least one mark must be provided

## Error Handling

### HTTP Status Codes
- **200**: Success (marks updated)
- **201**: Success (new marks created)
- **400**: Bad Request (validation errors)
- **401**: Unauthorized (not authenticated)
- **403**: Forbidden (teacher not authorized for this subject/student)
- **404**: Not Found (student or subject doesn't exist)
- **422**: Unprocessable Entity (validation failed)

### Error Scenarios
1. **Invalid Subject**: Subject doesn't exist or teacher not assigned
2. **Student Not Found**: Student ID doesn't exist
3. **Enrollment Mismatch**: Student not enrolled in teacher's section
4. **Validation Errors**: Marks exceed limits or invalid data
5. **Permission Denied**: Non-teacher user attempting access

## Security Considerations

### Authentication & Authorization
- **JWT Token Required**: All requests must include valid authentication
- **Teacher Role Only**: Middleware restricts access to teacher users
- **Subject Assignment Check**: Verifies teacher-subject-section relationships
- **Audit Logging**: Records all mark changes with teacher identification

### Data Protection
- **Input Validation**: Comprehensive validation of all input data
- **SQL Injection Prevention**: Uses Eloquent ORM with parameterized queries
- **XSS Protection**: Input sanitization and output encoding
- **Rate Limiting**: API throttling to prevent abuse

## Performance Optimization

### Database Operations
- **Eager Loading**: Loads necessary relationships efficiently
- **Indexed Queries**: Uses database indexes on frequently queried fields
- **Transaction Handling**: Ensures data consistency with minimal overhead
- **Caching Strategy**: Consider caching teacher assignments for better performance

### Response Optimization
- **Minimal Data Transfer**: Returns only necessary mark information
- **Efficient Queries**: Optimized database queries with proper joins
- **Resource Loading**: Loads relationships only when needed

## Usage Examples

### Adding New Marks
```bash
curl -X POST "https://api.example.com/api/teacher/students/123/marks" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "subject_id": 5,
    "semester_id": 2,
    "section_id": 3,
    "homework": 85,
    "oral": 90,
    "activity": 88,
    "quiz": 92,
    "exam": 87
  }'
```

### Updating Existing Marks
```bash
curl -X POST "https://api.example.com/api/teacher/students/123/marks" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "subject_id": 5,
    "semester_id": 2,
    "section_id": 3,
    "homework": 90,
    "oral": 95,
    "activity": 92,
    "quiz": 98,
    "exam": 95
  }'
```

### Partial Mark Update
```bash
curl -X POST "https://api.example.com/api/teacher/students/123/marks" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "subject_id": 5,
    "semester_id": 2,
    "section_id": 3,
    "exam": 95
  }'
```

## Testing

### Test Coverage
The API includes comprehensive test coverage including:
- **Unit Tests**: Individual component testing
- **Integration Tests**: API endpoint testing
- **Authorization Tests**: Permission and access control testing
- **Validation Tests**: Input validation and business rule testing
- **Error Handling Tests**: Various error scenario testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/TeacherStudentMarksApiTest.php

# Run with coverage
php artisan test --coverage
```

## Dependencies

### Required Models
- `Student`: Student information and relationships
- `Subject`: Subject details and percentage weights
- `StudentEnrollment`: Student enrollment records
- `TeacherSectionSubject`: Teacher-subject-section assignments
- `Semester`: Active semester information
- `User`: Authentication and user management

### Required Services
- `TeacherService`: Core business logic implementation
- `ResponseHelper`: Standardized API response formatting

### Required Middleware
- `auth:api`: Authentication verification
- `user_type:teacher`: Teacher role verification
- `throttle:60,1`: Rate limiting protection

## Future Enhancements

### Planned Features
1. **Bulk Mark Entry**: Support for entering marks for multiple students
2. **Mark History**: Track changes and provide audit trail
3. **Grade Calculation**: Automatic grade assignment based on total marks
4. **Notification System**: Alert students/parents of mark updates
5. **Export Functionality**: Generate mark reports in various formats

### Performance Improvements
1. **Caching Layer**: Redis caching for frequently accessed data
2. **Database Optimization**: Query optimization and indexing improvements
3. **Async Processing**: Background processing for bulk operations
4. **API Versioning**: Support for multiple API versions

## Support & Maintenance

### Documentation Updates
- API documentation is maintained alongside code changes
- Version history and changelog available
- Migration guides for breaking changes

### Error Monitoring
- Comprehensive error logging and monitoring
- Performance metrics and analytics
- User feedback and issue tracking

### Contact Information
For technical support or questions about this API, please contact the development team or refer to the project documentation.
