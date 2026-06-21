# SNISTOJ API Documentation

## Overview
SNISTOJ is a modern Online Judge System built with PHP 8.2 and following RESTful principles.

## Base URL
```
http://localhost:8080
```

## Authentication
All authenticated endpoints require user to be logged in via session.

---

## Endpoints

### Authentication Endpoints

#### Register User
```
POST /register
```
**Parameters:**
- `username` (string, required) - 3-20 alphanumeric characters
- `email` (string, required) - Valid email address
- `password` (string, required) - Minimum 8 characters
- `password_confirm` (string, required) - Must match password
- `csrf_token` (string, required) - CSRF token

**Response:**
```json
{
  "success": true,
  "message": "Registration successful"
}
```

#### Login User
```
POST /login
```
**Parameters:**
- `username` (string, required)
- `password` (string, required)
- `csrf_token` (string, required)

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "username": "john_doe",
    "email": "john@example.com"
  }
}
```

#### Logout
```
GET /logout
```
**Response:**
- Redirects to home page

---

### User Endpoints

#### Get User Profile
```
GET /user/profile
```
**Requires:** Authentication

**Response:**
```json
{
  "id": 1,
  "username": "john_doe",
  "email": "john@example.com",
  "full_name": "John Doe",
  "role": "user",
  "statistics": {
    "total_submissions": 42,
    "accepted_submissions": 25,
    "problems_solved": 20
  }
}
```

#### Update User Profile
```
POST /user/update
```
**Requires:** Authentication

**Parameters:**
- `full_name` (string, optional)
- `bio` (string, optional)
- `csrf_token` (string, required)

**Response:**
```json
{
  "success": true,
  "message": "Profile updated successfully"
}
```

---

### Problem Endpoints

#### Get All Problems
```
GET /problems?page=1&difficulty=easy&category=basic
```
**Requires:** Authentication

**Query Parameters:**
- `page` (integer) - Page number (default: 1)
- `difficulty` (string) - easy, medium, hard
- `category` (string) - Problem category

**Response:**
```json
{
  "problems": [
    {
      "id": 1,
      "title": "Hello World",
      "difficulty": "easy",
      "category": "basic",
      "time_limit": 1,
      "memory_limit": 256,
      "solved_by": 342,
      "submission_count": 567
    }
  ],
  "pagination": {
    "page": 1,
    "per_page": 10,
    "total": 128
  }
}
```

#### Get Problem Details
```
GET /problem/:id
```
**Requires:** Authentication

**Response:**
```json
{
  "id": 1,
  "title": "Hello World",
  "description": "Print Hello World",
  "difficulty": "easy",
  "category": "basic",
  "time_limit": 1,
  "memory_limit": 256,
  "input_format": "No input",
  "output_format": "Hello World",
  "examples": [
    {
      "input": "",
      "output": "Hello World"
    }
  ],
  "statistics": {
    "total_submissions": 567,
    "accepted": 342,
    "wrong_answer": 180,
    "runtime_error": 40,
    "timeout": 5
  }
}
```

#### Submit Problem Solution
```
POST /problem/submit
```
**Requires:** Authentication

**Parameters:**
- `problem_id` (integer, required)
- `code` (string, required)
- `language` (string, required) - c, cpp, java, python
- `csrf_token` (string, required)

**Response:**
```json
{
  "success": true,
  "submission_id": 12345,
  "status": "accepted",
  "execution_time": 0.023,
  "memory_used": 12
}
```

---

### Contest Endpoints

#### Get All Contests
```
GET /contests?status=running
```
**Requires:** Authentication

**Query Parameters:**
- `status` (string) - scheduled, running, ended

**Response:**
```json
{
  "contests": [
    {
      "id": 1,
      "title": "Weekly Contest #1",
      "status": "running",
      "start_time": "2026-06-21T15:00:00Z",
      "end_time": "2026-06-21T17:00:00Z",
      "duration": 120,
      "problems_count": 5,
      "participants": 342
    }
  ]
}
```

#### Get Contest Details
```
GET /contest/:id
```
**Requires:** Authentication

**Response:**
```json
{
  "id": 1,
  "title": "Weekly Contest #1",
  "description": "Weekly programming contest",
  "status": "running",
  "start_time": "2026-06-21T15:00:00Z",
  "end_time": "2026-06-21T17:00:00Z",
  "problems": [
    {
      "problem_id": 1,
      "title": "Problem A",
      "points": 100
    }
  ],
  "standings": [
    {
      "rank": 1,
      "username": "john_doe",
      "total_points": 500,
      "solved_problems": 5
    }
  ]
}
```

#### Register for Contest
```
POST /contest/register
```
**Requires:** Authentication

**Parameters:**
- `contest_id` (integer, required)
- `csrf_token` (string, required)

**Response:**
```json
{
  "success": true,
  "message": "Registered successfully"
}
```

---

### Compiler Endpoints

#### Run Code
```
POST /compiler/run
```
**Requires:** Authentication

**Parameters:**
- `code` (string, required)
- `language` (string, required) - c, cpp, java, python
- `input` (string, optional)
- `csrf_token` (string, required)

**Response:**
```json
{
  "success": true,
  "output": "Hello World",
  "execution_time": 0.023,
  "status": "OK"
}
```

---

### Admin Endpoints

#### Get Admin Dashboard
```
GET /admin/dashboard
```
**Requires:** Authentication + Admin Role

**Response:**
```json
{
  "users_count": 1234,
  "problems_count": 256,
  "submissions_count": 45678,
  "contests_count": 42,
  "recent_submissions": []
}
```

#### Create Problem
```
POST /admin/problem/create
```
**Requires:** Authentication + Admin Role

**Parameters:**
- `title` (string, required)
- `description` (string, required)
- `difficulty` (string, required) - easy, medium, hard
- `category` (string, required)
- `time_limit` (integer, required)
- `memory_limit` (integer, required)
- `csrf_token` (string, required)

**Response:**
```json
{
  "success": true,
  "problem_id": 128,
  "message": "Problem created successfully"
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "message": "Invalid input parameters"
}
```

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Authentication required"
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Access denied"
}
```

### 429 Too Many Requests
```json
{
  "success": false,
  "message": "Rate limit exceeded"
}
```

### 500 Server Error
```json
{
  "success": false,
  "message": "Internal server error"
}
```

---

## Status Codes

| Code | Description |
|------|-------------|
| 200 | OK |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 405 | Method Not Allowed |
| 422 | Unprocessable Entity |
| 429 | Too Many Requests |
| 500 | Internal Server Error |

---

## Rate Limiting

- **Login attempts**: 5 per 5 minutes per IP
- **General API calls**: 100 per minute per user
- **Compiler**: 30 per minute per user

---

## Supported Languages

- **C** - GCC compiler
- **C++** - G++ compiler
- **Java** - OpenJDK
- **Python** - Python 3.x

---

## Submission Status

| Status | Description |
|--------|-------------|
| Pending | Being compiled/executed |
| Accepted | Correct solution |
| Wrong Answer | Output doesn't match expected |
| Runtime Error | Program crashed |
| Time Limit Exceeded | Execution exceeded time limit |
| Memory Limit Exceeded | Execution exceeded memory limit |
| Compilation Error | Failed to compile |

---

## Best Practices

1. Always include CSRF token in POST requests
2. Use HTTPS in production
3. Implement proper error handling on client side
4. Respect rate limits
5. Validate input on client before sending

---

## Version

**API Version**: 2.0  
**Last Updated**: 2026-06-21
