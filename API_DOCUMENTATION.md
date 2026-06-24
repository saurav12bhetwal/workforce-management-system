markdown
# API Documentation - Attendance Module

## Base URL
http://localhost:8000

text

## Authentication
All API endpoints require authentication. Laravel Breeze handles authentication via session cookies.

---

## Employee Attendance APIs

### 1. Get Attendance Status
Get today's attendance status including check-in time and working hours.

**Endpoint:** `GET /employee/attendance/status`

**Response:**
```json
{
    "is_checked_in": true,
    "is_checked_out": false,
    "check_in_at": "2024-01-15T10:30:00+05:30",
    "check_in_time": "10:30 AM",
    "check_out_time": null,
    "working_minutes": 240,
    "working_hours": "04:00:00"
}
Response Fields:

Field	Type	Description
is_checked_in	boolean	True if checked in today
is_checked_out	boolean	True if checked out today
check_in_at	string	ISO-8601 timestamp of check-in
check_in_time	string	Formatted check-in time (h:i A)
check_out_time	string	Formatted check-out time (h:i A)
working_minutes	integer	Total working minutes
working_hours	string	Formatted working hours (HH:MM:00)
Status Codes:

200: Success

401: Unauthorized

2. Check In
Employee checks in with location capture.

Endpoint: POST /employee/attendance/check-in

Headers:

text
Content-Type: application/json
Request Body:

json
{
    "latitude": 28.6139,
    "longitude": 77.2090
}
Validation Rules:

Field	Rules
latitude	Required, Numeric, Between -90 and 90
longitude	Required, Numeric, Between -180 and 180
Success Response (200):

json
{
    "success": true,
    "message": "Checked in successfully!",
    "check_in_at": "2024-01-15T10:30:00+05:30",
    "check_in_time": "10:30 AM",
    "location": "Connaught Place, New Delhi"
}
Error Response (409 - Already Checked In):

json
{
    "success": false,
    "message": "You have already checked in today.",
    "already_checked_in": true
}
Error Response (422 - Validation Error):

json
{
    "message": "The latitude field is required.",
    "errors": {
        "latitude": ["The latitude field is required."]
    }
}
Status Codes:

200: Success

409: Already checked in

422: Validation error

401: Unauthorized

3. Check Out
Employee checks out with location capture and calculates working hours.

Endpoint: POST /employee/attendance/check-out

Headers:

text
Content-Type: application/json
Request Body:

json
{
    "latitude": 28.6139,
    "longitude": 77.2090
}
Validation Rules:

Field	Rules
latitude	Required, Numeric, Between -90 and 90
longitude	Required, Numeric, Between -180 and 180
Success Response (200):

json
{
    "success": true,
    "message": "Checked out successfully!",
    "working_hours": "08:00:00",
    "check_out_time": "06:30 PM",
    "location": "Connaught Place, New Delhi"
}
Error Response (409 - No Active Check-in):

json
{
    "success": false,
    "message": "No active check-in found. Please check in first."
}
Error Response (409 - Already Checked Out):

json
{
    "success": false,
    "message": "You have already checked out today."
}
Status Codes:

200: Success

409: No active check-in / Already checked out

422: Validation error

401: Unauthorized

4. Get Attendance History
Get employee's attendance history with filters.

Endpoint: GET /employee/attendance/history

Query Parameters:

Parameter	Type	Required	Description
from_date	date	No	Start date (YYYY-MM-DD)
to_date	date	No	End date (YYYY-MM-DD)
month	integer	No	Month (1-12)
year	integer	No	Year
Response:

json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "user_id": 3,
            "attendance_date": "2024-01-15",
            "check_in": "2024-01-15 10:30:00",
            "check_out": "2024-01-15 18:30:00",
            "check_in_lat": 28.6139,
            "check_in_lng": 77.2090,
            "check_out_lat": 28.6139,
            "check_out_lng": 77.2090,
            "check_in_location": "Connaught Place, New Delhi",
            "check_out_location": "Connaught Place, New Delhi",
            "working_minutes": 480,
            "created_at": "2024-01-15 10:30:00",
            "updated_at": "2024-01-15 18:30:00"
        }
    ],
    "first_page_url": "http://localhost:8000/employee/attendance/history?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "http://localhost:8000/employee/attendance/history?page=5",
    "next_page_url": "http://localhost:8000/employee/attendance/history?page=2",
    "path": "http://localhost:8000/employee/attendance/history",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 75
}
Summary Response (Included in View):
The history view also includes a summary:

json
{
    "total_days": 30,
    "present_days": 25,
    "absent_days": 5,
    "total_hours": "200:00:00",
    "avg_hours": "08:00:00"
}
Status Codes:

200: Success

401: Unauthorized

How Timer Works
Timer Logic (Important)
The attendance timer uses ISO-8601 timestamps from the database, NOT localStorage or session.

Flow:

Check In:

Server saves check_in_at as ISO-8601 timestamp

Returns check_in_at to frontend

Timer Calculation:

javascript
// Frontend calculates elapsed time
const checkInAt = new Date(response.check_in_at);
const elapsed = Date.now() - checkInAt.getTime();
const hours = Math.floor(elapsed / 3600000);
const minutes = Math.floor((elapsed % 3600000) / 60000);
const seconds = Math.floor((elapsed % 60000) / 1000);
Check Out:

Server calculates working minutes from check_in to check_out

Returns formatted working hours

Why this approach?

✅ Timer persists after page refresh (uses DB timestamp)

✅ Accurate across timezones (ISO-8601 format)

✅ No localStorage dependency

✅ Single source of truth (database)

Example JavaScript Usage
Check In
javascript
fetch('/employee/attendance/check-in', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        latitude: 28.6139,
        longitude: 77.2090
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        // Start timer using data.check_in_at
        const checkInAt = new Date(data.check_in_at);
        startTimer(checkInAt);
    }
});
Check Out
javascript
fetch('/employee/attendance/check-out', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        latitude: 28.6139,
        longitude: 77.2090
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log(`Working hours: ${data.working_hours}`);
    }
});
Get Status
javascript
fetch('/employee/attendance/status')
.then(response => response.json())
.then(data => {
    if (data.is_checked_in) {
        // Resume timer using data.check_in_at
        const checkInAt = new Date(data.check_in_at);
        startTimer(checkInAt);
    }
});
Error Responses
401 Unauthorized
json
{
    "message": "Unauthenticated."
}
422 Validation Error
json
{
    "message": "The latitude field is required.",
    "errors": {
        "latitude": ["The latitude field is required."],
        "longitude": ["The longitude field is required."]
    }
}
409 Conflict
json
{
    "success": false,
    "message": "You have already checked in today.",
    "already_checked_in": true
}
500 Internal Server Error
json
{
    "success": false,
    "message": "Something went wrong"
}
Status Codes Summary
Code	Description
200	Success
401	Unauthorized
409	Conflict (Already checked in/out)
422	Validation Error
500	Internal Server Error
Notes
Timer Accuracy: The timer uses the database check_in_at timestamp as the source of truth. This ensures accuracy even after page refresh.

Location: Both check-in and check-out capture latitude/longitude. The system also reverse-geocodes to get a readable location name.

Working Hours: Calculated as the difference between check-in and check-out times in minutes, formatted as HH:MM:00.

Filters: The history endpoint supports filtering by date range, month, and year.

Pagination: The history endpoint returns paginated results with 15 records per page.



