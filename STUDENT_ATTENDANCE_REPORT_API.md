# Student Attendance Report API

## Overview
This API provides detailed student attendance reports with monthly statistics and daily breakdowns for the current academic year.

**Academic Year Logic:**
- Gets the current active academic year from the database
- Uses the academic year's `start_date` and `end_date` to determine the date range
- Returns data for all months from start_date to end_date within the academic year
- Handles academic years named like "2024/2025" with cross-year periods

## Endpoint
```
GET /api/student-attendances/report/generate
```

## Parameters
No parameters required. The API automatically:
- Gets the student_id from the authenticated user's token
- Gets the current active academic year from the database
- Returns data for all months from the academic year's start_date to end_date

## Example Request
```
GET /api/student-attendances/report/generate
```

## Response Format
```json
{
    "message": "تم إنشاء تقرير الحضور بنجاح",
    "data": {
        "months": [
            {
                "year": 2024,
                "month": 9,
                "stats": {
                    "attendancePercentage": 85.0,
                    "absencePercentage": 10.0,
                    "justifiedAbsencePercentage": 3.0,
                    "latenessPercentage": 2.0,
                    "totalDays": 20,
                    "presentDays": 17,
                    "absentDays": 2,
                    "justifiedAbsentDays": 1,
                    "lateDays": 0
                },
                "days": [
                    {
                        "date": "2024-09-01",
                        "status": "present",
                        "sessions": [
                            {
                                "sessionNumber": 1,
                                "teacherName": "أستاذ عمر",
                                "subjectName": "رياضيات",
                                "status": "present"
                            }
                        ]
                    }
                ]
            },
            {
                "year": 2024,
                "month": 10,
                "stats": {
                    "attendancePercentage": 78.0,
                    "absencePercentage": 15.0,
                    "justifiedAbsencePercentage": 5.0,
                    "latenessPercentage": 2.0,
                    "totalDays": 22,
                    "presentDays": 17,
                    "absentDays": 3,
                    "justifiedAbsentDays": 1,
                    "lateDays": 1
                },
                "days": []
            },
            {
                "year": 2025,
                "month": 1,
                "stats": {
                    "attendancePercentage": 82.0,
                    "absencePercentage": 12.0,
                    "justifiedAbsencePercentage": 4.0,
                    "latenessPercentage": 2.0,
                    "totalDays": 18,
                    "presentDays": 15,
                    "absentDays": 2,
                    "justifiedAbsentDays": 1,
                    "lateDays": 0
                },
                "days": []
            }
        ]
    },
    "status_code": 200
}
```

**Note:** The response includes data for all months from the academic year's start_date to end_date, with each month containing its own statistics and daily breakdown.

## Status Values
- `present`: Student was present
- `absent`: Student was absent (unexcused)
- `justified_absent`: Student was absent (excused)
- `late`: Student was late
- `not_marked`: Attendance not recorded
- `holiday`: School holiday
- `no_sessions`: No sessions scheduled for this day

## Day Status Values
- `present`: All sessions for the day were attended
- `absent`: Student was absent for all sessions
- `late`: Student was late for at least one session
- `mixed`: Mixed attendance status for the day
- `holiday`: School holiday
- `no_sessions`: No sessions scheduled

## Authentication
This endpoint requires authentication. Include the Bearer token in the Authorization header:
```
Authorization: Bearer {your_token}
```

## Permissions
The user must have the `VIEW_STUDENT_ATTENDANCES` permission to access this endpoint.

## Error Responses
- `401 Unauthorized`: Missing or invalid authentication
- `403 Forbidden`: Insufficient permissions
- `404 Not Found`: Student not found for the authenticated user or current academic year not found
