# Teacher Attendance Report API

## Overview
This API provides detailed teacher attendance reports with monthly statistics and daily breakdowns for the current academic year.

**Monthly Logic:**
- Gets the current active academic year from the database
- Retrieves all semesters for the current academic year
- Breaks down each semester into individual months
- Returns data for each month with its own statistics and daily breakdown
- Handles academic years with multiple semesters (e.g., Fall, Spring, Summer)

## Endpoint
```
GET /api/teacher-attendances/report/generate
```

## Parameters
No parameters required. The API automatically:
- Gets the teacher_id from the authenticated user's token
- Gets the current active academic year from the database
- Returns data for all months in all semesters of the current academic year

## Example Request
```
GET /api/teacher-attendances/report/generate
```

## Response Format
```json
{
    "message": "تم إنشاء تقرير الحضور بنجاح",
    "data": {
        "months": [
            {
                "year": 2025,
                "month": 5,
                "stats": {
                    "attendancePercentage": 70.0,
                    "absencePercentage": 15.0,
                    "justifiedAbsencePercentage": 10.0,
                    "latenessPercentage": 5.0,
                    "totalDays": 22,
                    "presentDays": 15,
                    "absentDays": 3,
                    "justifiedAbsentDays": 2,
                    "lateDays": 1
                },
                "days": [
                    {
                        "date": "2025-05-01",
                        "status": "present",
                        "sessions": [
                            {
                                "sessionNumber": 1,
                                "teacherName": "أستاذ عمر",
                                "subjectName": "رياضيات",
                                "status": "present"
                            },
                            {
                                "sessionNumber": 2,
                                "teacherName": "أستاذة نور",
                                "subjectName": "علوم",
                                "status": "present"
                            }
                        ]
                    },
                    {
                        "date": "2025-05-02",
                        "status": "present",
                        "sessions": [
                            {
                                "sessionNumber": 1,
                                "teacherName": "أستاذة هبة",
                                "subjectName": "عربي",
                                "status": "present"
                            }
                        ]
                    },
                    {
                        "date": "2025-05-03",
                        "status": "holiday",
                        "sessions": []
                    }
                ]
            },
            {
                "year": 2025,
                "month": 6,
                "stats": {
                    "attendancePercentage": 70.0,
                    "absencePercentage": 15.0,
                    "justifiedAbsencePercentage": 10.0,
                    "latenessPercentage": 5.0,
                    "totalDays": 22,
                    "presentDays": 15,
                    "absentDays": 3,
                    "justifiedAbsentDays": 2,
                    "lateDays": 1
                },
                "days": [
                    {
                        "date": "2025-06-01",
                        "status": "present",
                        "sessions": [
                            {
                                "sessionNumber": 1,
                                "teacherName": "أستاذ عمر",
                                "subjectName": "رياضيات",
                                "status": "present"
                            },
                            {
                                "sessionNumber": 2,
                                "teacherName": "أستاذة نور",
                                "subjectName": "علوم",
                                "status": "present"
                            }
                        ]
                    },
                    {
                        "date": "2025-06-02",
                        "status": "present",
                        "sessions": [
                            {
                                "sessionNumber": 1,
                                "teacherName": "أستاذة هبة",
                                "subjectName": "عربي",
                                "status": "present"
                            }
                        ]
                    },
                    {
                        "date": "2025-06-03",
                        "status": "holiday",
                        "sessions": []
                    }
                ]
            }
        ]
    },
    "status_code": 200
}
```

**Note:** The response includes data for all months in all semesters of the current academic year, with each month containing its own statistics and daily breakdown.

## Status Values
- `present`: Teacher was present
- `absent`: Teacher was absent (unexcused)
- `justified_absent`: Teacher was absent (excused)
- `late`: Teacher was late
- `not_marked`: Attendance not recorded
- `holiday`: School holiday
- `no_sessions`: No sessions scheduled for this day

## Day Status Values
- `present`: All sessions for the day were attended
- `absent`: Teacher was absent for all sessions
- `late`: Teacher was late for at least one session
- `mixed`: Mixed attendance status for the day
- `holiday`: School holiday
- `no_sessions`: No sessions scheduled

## Authentication
This endpoint requires authentication. Include the Bearer token in the Authorization header:
```
Authorization: Bearer {your_token}
```

## Permissions
The user must have the `VIEW_TEACHER_ATTENDANCES` permission to access this endpoint.

## Error Responses
- `401 Unauthorized`: Missing or invalid authentication
- `403 Forbidden`: Insufficient permissions
- `404 Not Found`: Teacher not found for the authenticated user, current academic year not found, or no semesters found

