# 🔌 API & INTEGRATION GUIDE

Panduan untuk mengintegrasikan LMS dengan sistem eksternal atau membuat API custom.

---

## 📋 TABLE OF CONTENTS

1. [Current API Structure](#current-api-structure)
2. [Creating REST API](#creating-rest-api)
3. [Webhook Integrations](#webhook-integrations)
4. [Third-Party Service Integration](#third-party-service-integration)
5. [Mobile App Integration](#mobile-app-integration)
6. [Custom Data Export](#custom-data-export)

---

## 🏗️ CURRENT API STRUCTURE

### Existing Data Access Points

The LMS currently uses **form-based data access** rather than REST API. Here's how to add API functionality:

```
Current Architecture:
├─ User submits form → POST request
├─ PHP processes data
├─ Updates database
└─ Redirects to same page with alert

Proposed API Architecture:
├─ Client sends JSON request
├─ API endpoint processes data
├─ Returns JSON response
└─ Client handles response (JS or mobile app)
```

---

## 🚀 CREATING REST API

### Step 1: Create API Folder Structure

```
lms/
├─ api/
│  ├─ config.php
│  ├─ v1/
│  │  ├─ users.php
│  │  ├─ classes.php
│  │  ├─ assignments.php
│  │  ├─ submissions.php
│  │  ├─ grades.php
│  │  └─ auth.php
│  └─ v2/ (future versions)
```

### Step 2: Create API Config

```php
<?php
// api/config.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

include '../includes/config.php';

// API Response function
function api_response($success, $data = null, $error = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'error' => $error,
        'timestamp' => date('c')
    ]);
    exit;
}

// API authentication (token-based)
function api_auth() {
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? null;

    if (!$token || !verify_token($token)) {
        api_response(false, null, 'Unauthorized', 401);
    }
}

// Simple token verification (use JWT in production)
function verify_token($token) {
    $token = str_replace('Bearer ', '', $token);
    // Check if token is valid
    return strlen($token) > 20;  // Simplified
}
?>
```

### Step 3: Create User Endpoint

```php
<?php
// api/v1/users.php

include '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$user_id = $_GET['id'] ?? null;

switch ($method) {
    case 'GET':
        if ($user_id) {
            // Get single user
            $query = "SELECT id, username, email, full_name, role FROM users WHERE id = $user_id";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                api_response(true, $result->fetch_assoc());
            } else {
                api_response(false, null, 'User not found', 404);
            }
        } else {
            // Get all users (admin only)
            api_auth();

            $query = "SELECT id, username, email, full_name, role, created_at FROM users LIMIT 100";
            $result = $conn->query($query);
            $users = [];

            while ($user = $result->fetch_assoc()) {
                $users[] = $user;
            }

            api_response(true, ['users' => $users, 'total' => count($users)]);
        }
        break;

    case 'POST':
        // Create user (admin only)
        api_auth();

        $data = json_decode(file_get_contents('php://input'), true);

        // Validate
        if (empty($data['username']) || empty($data['password'])) {
            api_response(false, null, 'Username and password required', 400);
        }

        $username = sanitize($data['username']);
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $email = sanitize($data['email']);
        $full_name = sanitize($data['full_name']);
        $role = (int)$data['role'];  // 0=admin, 1=teacher, 2=student

        $query = "INSERT INTO users (username, password, email, full_name, role)
                  VALUES ('$username', '$password', '$email', '$full_name', $role)";

        if ($conn->query($query)) {
            api_response(true, ['id' => $conn->insert_id, 'username' => $username], null, 201);
        } else {
            api_response(false, null, $conn->error, 500);
        }
        break;

    case 'PUT':
        // Update user
        api_auth();

        if (!$user_id) {
            api_response(false, null, 'User ID required', 400);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $updates = [];

        if (isset($data['full_name'])) {
            $updates[] = "full_name = '" . sanitize($data['full_name']) . "'";
        }
        if (isset($data['email'])) {
            $updates[] = "email = '" . sanitize($data['email']) . "'";
        }

        if (empty($updates)) {
            api_response(false, null, 'No fields to update', 400);
        }

        $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = $user_id";

        if ($conn->query($query)) {
            api_response(true, ['id' => $user_id, 'updated' => true]);
        } else {
            api_response(false, null, $conn->error, 500);
        }
        break;

    case 'DELETE':
        // Delete user
        api_auth();

        if (!$user_id) {
            api_response(false, null, 'User ID required', 400);
        }

        $query = "DELETE FROM users WHERE id = $user_id";

        if ($conn->query($query)) {
            api_response(true, ['deleted' => true, 'id' => $user_id]);
        } else {
            api_response(false, null, $conn->error, 500);
        }
        break;

    default:
        api_response(false, null, 'Method not allowed', 405);
}
?>
```

### Step 4: Authentication Endpoint

```php
<?php
// api/v1/auth.php

include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_response(false, null, 'Only POST allowed', 405);
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['username']) || empty($data['password'])) {
    api_response(false, null, 'Username and password required', 400);
}

$username = sanitize($data['username']);
$password = $data['password'];

$query = "SELECT id, username, password, role, full_name FROM users WHERE username = '$username'";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    api_response(false, null, 'Invalid credentials', 401);
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    api_response(false, null, 'Invalid credentials', 401);
}

// Generate simple token (use JWT in production)
$token = bin2hex(random_bytes(32));

// Store token in database or session
// For now, return basic info
api_response(true, [
    'user_id' => $user['id'],
    'username' => $user['username'],
    'role' => $user['role'],
    'full_name' => $user['full_name'],
    'token' => $token
]);
?>
```

### Step 5: Usage Example

```javascript
// JavaScript client example

// Login
fetch("/lms/api/v1/auth.php", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify({
    username: "admin",
    password: "admin123",
  }),
})
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      token = data.data.token;
      console.log("Logged in as:", data.data.full_name);
    } else {
      console.error("Login failed:", data.error);
    }
  });

// Get users
fetch("/lms/api/v1/users.php", {
  headers: {
    Authorization: "Bearer " + token,
  },
})
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      console.log("Users:", data.data.users);
    }
  });

// Create user
fetch("/lms/api/v1/users.php", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    Authorization: "Bearer " + token,
  },
  body: JSON.stringify({
    username: "newuser",
    password: "newpass123",
    email: "user@example.com",
    full_name: "New User",
    role: 2,
  }),
})
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      console.log("User created:", data.data);
    }
  });
```

---

## 🔔 WEBHOOK INTEGRATIONS

### Email Notification Webhook

```php
<?php
// api/v1/webhooks/email.php

include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_response(false, null, 'Only POST allowed', 405);
}

$data = json_decode(file_get_contents('php://input'), true);

$to = $data['to'];
$subject = $data['subject'];
$message = $data['message'];
$template = $data['template'] ?? 'default';

// Send email
$headers = "Content-Type: text/html; charset=UTF-8\r\n";

if (mail($to, $subject, $message, $headers)) {
    api_response(true, ['email_sent' => true, 'to' => $to]);
} else {
    api_response(false, null, 'Failed to send email', 500);
}
?>
```

### Grade Notification

```php
<?php
// Webhook: Notify student when grade is posted

$student_email = "student@example.com";
$assignment_name = "Math Assignment 1";
$score = 85;
$max_score = 100;

$message = "
    <h2>Grade Posted</h2>
    <p>Your grade for <strong>$assignment_name</strong> has been posted:</p>
    <p><strong>Score: $score / $max_score</strong></p>
    <p>Log in to view detailed feedback.</p>
";

// Call webhook
$webhook_data = [
    'to' => $student_email,
    'subject' => 'Grade Posted: ' . $assignment_name,
    'message' => $message,
    'template' => 'grade'
];

curl_request('/lms/api/v1/webhooks/email.php', $webhook_data);
?>
```

---

## 🔗 THIRD-PARTY SERVICE INTEGRATION

### Google Classroom Integration

```php
<?php
// api/v1/integrations/google_classroom.php

// Requires Google API PHP Client
// composer require google/apiclient

require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setAuthorizationuri('https://accounts.google.com/o/oauth2/auth');
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/lms/api/v1/integrations/google_callback.php');
$client->addScope('https://www.googleapis.com/auth/classroom.courses');

// Get Google classes
function sync_google_classes($user_id) {
    global $conn, $client;

    $service = new Google_Service_Classroom($client);
    $google_courses = $service->courses->listCourses()->getCourses();

    foreach ($google_courses as $course) {
        // Insert into LMS
        $query = "INSERT INTO classes (name, code, teacher_id, google_id)
                  VALUES ('" . $course->getName() . "',
                          '" . $course->getId() . "',
                          $user_id,
                          '" . $course->getId() . "')";
        $conn->query($query);
    }
}
?>
```

### Zoom Integration

```php
<?php
// api/v1/integrations/zoom.php

// Requires Zoom SDK
class ZoomIntegration {
    private $api_key;
    private $api_secret;

    public function __construct($api_key, $api_secret) {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    // Create meeting for class
    public function create_class_meeting($class_id, $meeting_topic) {
        $url = 'https://api.zoom.us/v2/users/me/meetings';

        $payload = [
            'topic' => $meeting_topic,
            'type' => 2,  // Scheduled meeting
            'start_time' => date('c'),
            'duration' => 60,
            'timezone' => 'UTC',
            'settings' => [
                'host_video' => true,
                'participant_video' => true,
                'join_before_host' => false
            ]
        ];

        $response = $this->api_request('POST', $url, $payload);

        // Store in database
        global $conn;
        $query = "INSERT INTO class_meetings (class_id, zoom_meeting_id, meeting_url)
                  VALUES ($class_id, '" . $response['id'] . "', '" . $response['join_url'] . "')";
        $conn->query($query);

        return $response;
    }

    private function api_request($method, $url, $data) {
        $token = $this->generate_jwt();

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function generate_jwt() {
        // Generate JWT token for Zoom API
        // Implementation depends on Zoom
        return "YOUR_JWT_TOKEN";
    }
}

// Usage:
$zoom = new ZoomIntegration('your_api_key', 'your_api_secret');
$meeting = $zoom->create_class_meeting(1, 'Biology 101 - Live Class');
?>
```

---

## 📱 MOBILE APP INTEGRATION

### Mobile API Endpoints

```php
<?php
// api/v1/mobile/dashboard.php

include '../config.php';
$user_id = $_GET['user_id'];

// Return minimal data for mobile
$dashboard = [
    'user' => get_user_data($user_id),
    'classes' => get_user_classes($user_id),
    'pending_assignments' => get_pending_assignments($user_id),
    'recent_grades' => get_recent_grades($user_id)
];

api_response(true, $dashboard);

function get_user_data($user_id) {
    global $conn;
    $query = "SELECT id, username, full_name, email FROM users WHERE id = $user_id";
    return $conn->query($query)->fetch_assoc();
}

function get_user_classes($user_id) {
    global $conn;
    $query = "SELECT c.id, c.name, c.code, u.full_name as teacher
              FROM classes c
              INNER JOIN class_members cm ON c.id = cm.class_id
              LEFT JOIN users u ON c.teacher_id = u.id
              WHERE cm.user_id = $user_id";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_pending_assignments($user_id) {
    global $conn;
    $query = "SELECT a.id, a.title, a.due_date, c.name as class_name
              FROM assignments a
              INNER JOIN classes c ON a.class_id = c.id
              INNER JOIN class_members cm ON c.id = cm.class_id
              WHERE cm.user_id = $user_id
              AND a.due_date > NOW()
              LIMIT 5";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_recent_grades($user_id) {
    global $conn;
    $query = "SELECT g.*, a.title as assignment_name, c.name as class_name
              FROM grades g
              INNER JOIN assignments a ON g.assignment_id = a.id
              INNER JOIN classes c ON a.class_id = c.id
              WHERE g.user_id = $user_id
              ORDER BY g.updated_at DESC
              LIMIT 5";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>
```

### React Native Example

```javascript
// Mobile app component example (React Native)

import React, { useEffect, useState } from "react";
import { View, Text, FlatList } from "react-native";

export default function Dashboard({ userId, token }) {
  const [dashboard, setDashboard] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch(`/lms/api/v1/mobile/dashboard.php?user_id=${userId}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
      .then((response) => response.json())
      .then((data) => {
        setDashboard(data.data);
        setLoading(false);
      })
      .catch((error) => {
        console.error("Error:", error);
        setLoading(false);
      });
  }, [userId, token]);

  if (loading) return <Text>Loading...</Text>;

  return (
    <View>
      <Text>Welcome, {dashboard.user.full_name}</Text>

      <Text>Your Classes:</Text>
      <FlatList
        data={dashboard.classes}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => (
          <View>
            <Text>{item.name}</Text>
            <Text>Teacher: {item.teacher}</Text>
          </View>
        )}
      />

      <Text>Pending Assignments:</Text>
      <FlatList
        data={dashboard.pending_assignments}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => (
          <View>
            <Text>{item.title}</Text>
            <Text>Due: {item.due_date}</Text>
          </View>
        )}
      />
    </View>
  );
}
```

---

## 📊 CUSTOM DATA EXPORT

### CSV Export API

```php
<?php
// api/v1/export/grades.php

include '../config.php';

$class_id = $_GET['class_id'];
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="grades_'.$class_id.'.csv"');

$query = "SELECT u.username, u.full_name,
          GROUP_CONCAT(a.title SEPARATOR '|') as assignments,
          GROUP_CONCAT(g.total_score SEPARATOR '|') as scores,
          AVG(g.total_score) as average
          FROM users u
          INNER JOIN class_members cm ON u.id = cm.user_id
          INNER JOIN grades g ON u.id = g.user_id
          INNER JOIN assignments a ON g.assignment_id = a.id
          WHERE cm.class_id = $class_id
          GROUP BY u.id";

$result = $conn->query($query);

// Output header
$output = fopen('php://output', 'w');
fputcsv($output, ['Username', 'Full Name', 'Assignments', 'Scores', 'Average']);

// Output data
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['username'],
        $row['full_name'],
        $row['assignments'],
        $row['scores'],
        round($row['average'], 2)
    ]);
}

fclose($output);
?>
```

### PDF Report Export

```php
<?php
// api/v1/export/report.php

// Requires TCPDF or similar
// composer require tecnickcom/tcpdf

require_once('vendor/autoload.php');

$class_id = $_GET['class_id'];

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 16);

// Get class info
$query = "SELECT * FROM classes WHERE id = $class_id";
$class = $conn->query($query)->fetch_assoc();

$pdf->Cell(0, 10, 'Class Report: ' . $class['name'], 0, false, 'C');
$pdf->Ln(10);

// Get students and their grades
$query = "SELECT u.full_name, AVG(g.total_score) as avg_score
          FROM users u
          INNER JOIN class_members cm ON u.id = cm.user_id
          LEFT JOIN grades g ON u.id = g.user_id
          WHERE cm.class_id = $class_id
          GROUP BY u.id";

$result = $conn->query($query);

$pdf->SetFont('helvetica', '', 12);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(0, 10, $row['full_name'] . ': ' . round($row['avg_score'], 2), 0, false);
    $pdf->Ln(10);
}

// Output PDF
$pdf->Output('report_'.$class_id.'.pdf', 'D');
?>
```

---

## 🔐 API SECURITY BEST PRACTICES

```php
<?php
// Security measures for APIs

// 1. Rate limiting
function rate_limit($user_id, $max_requests = 100, $time_window = 3600) {
    // Use Redis or database to track requests
    // Limit requests per hour using storing timestamps
}

// 2. CORS (Cross-Origin Resource Sharing)
header('Access-Control-Allow-Origin: https://trusted-domain.com');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

// 3. Input validation
function validate_api_input($input, $type = 'string') {
    switch ($type) {
        case 'email':
            return filter_var($input, FILTER_VALIDATE_EMAIL);
        case 'integer':
            return filter_var($input, FILTER_VALIDATE_INT);
        case 'string':
            return is_string($input) && strlen($input) <= 255;
        default:
            return false;
    }
}

// 4. Use HTTPS only
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    api_response(false, null, 'HTTPS required', 403);
}

// 5. Implement JWT (JSON Web Tokens)
function generate_jwt($user_id) {
    $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64_encode(json_encode([
        'user_id' => $user_id,
        'exp' => time() + 3600
    ]));
    $signature = base64_encode(hash_hmac('sha256', "$header.$payload", 'SECRET_KEY', true));
    return "$header.$payload.$signature";
}
?>
```

---

## 🧪 TESTING API ENDPOINTS

### Using cURL

```bash
# Login
curl -X POST http://localhost/lms/api/v1/auth.php \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'

# Get users (with auth)
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost/lms/api/v1/users.php

# Create user
curl -X POST http://localhost/lms/api/v1/users.php \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"username":"newuser","password":"pass123","email":"user@example.com","full_name":"New User","role":2}'

# Update user
curl -X PUT http://localhost/lms/api/v1/users.php?id=1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"full_name":"Updated Name"}'

# Delete user
curl -X DELETE http://localhost/lms/api/v1/users.php?id=5 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Using Postman

1. Import the provided Postman collection (if available)
2. Set base URL: `http://localhost/lms/api/v1`
3. Get auth token from login endpoint
4. Use token in Authorization header for other requests
5. Test all CRUD operations

---

## 📚 API DOCUMENTATION TEMPLATE

```markdown
# LMS API v1 Documentation

## Base URL

`http://localhost/lms/api/v1`

## Authentication

Use Bearer token in Authorization header:
```

Authorization: Bearer YOUR_TOKEN

```

## Endpoints

### Login
- **POST** `/auth.php`
- **Body**: `{"username":"admin","password":"password"}`
- **Response**: `{"success":true,"data":{"token":"...","user_id":1,...}}`

### Get Users
- **GET** `/users.php`
- **Auth**: Required
- **Response**: `{"success":true,"data":{"users":[...],"total":10}}`

### Get User
- **GET** `/users.php?id=1`
- **Response**: `{"success":true,"data":{"id":1,"username":"admin",...}}`

... (document all endpoints similarly)
```

---

## 🚀 NEXT STEPS FOR API DEVELOPMENT

1. **Implement Authentication**
   - ✅ Create auth endpoint
   - [ ] Implement JWT tokens
   - [ ] Add refresh token support
   - [ ] Implement OAuth2

2. **Create Resource Endpoints**
   - ✅ Users API
   - [ ] Classes API
   - [ ] Assignments API
   - [ ] Submissions API
   - [ ] Grades API

3. **Add Advanced Features**
   - [ ] Pagination support
   - [ ] Filtering and sorting
   - [ ] Request validation middleware
   - [ ] Error handling middleware
   - [ ] CORS middleware

4. **Security Hardening**
   - [ ] Rate limiting
   - [ ] Input validation
   - [ ] Output encoding
   - [ ] CSRF protection
   - [ ] SQL injection prevention

5. **Documentation & Testing**
   - [ ] API documentation (Swagger/OpenAPI)
   - [ ] Postman collection
   - [ ] Unit tests
   - [ ] Integration tests

---

## 💡 API DESIGN BEST PRACTICES

```
✅ Use proper HTTP methods
   - GET for retrieving data
   - POST for creating data
   - PUT for updating data
   - DELETE for removing data

✅ Use proper HTTP status codes
   - 200: Success
   - 201: Created
   - 400: Bad request
   - 401: Unauthorized
   - 404: Not found
   - 500: Server error

✅ Return consistent JSON format
   {
       "success": boolean,
       "data": object or array,
       "error": string or null,
       "timestamp": ISO timestamp
   }

✅ Version your API
   - /api/v1/ for stable version
   - /api/v2/ for new features

✅ Document everything
   - Request/response examples
   - Authentication method
   - Error codes
   - Rate limits

✅ Use meaningful URLs
   - /users - for user list
   - /users/1 - for specific user
   - /classes/1/students - for class students
```

---

**Happy integrating!** 🔌✨

Version: 1.0
Last Updated: 2026
