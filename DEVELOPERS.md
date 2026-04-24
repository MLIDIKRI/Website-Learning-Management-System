# 👨‍💻 DEVELOPERS GUIDE - LMS Architecture

Panduan untuk developers yang ingin memahami, extend, atau maintain sistem LMS.

---

## 🏗️ ARCHITECTURE OVERVIEW

### 3-Layer Architecture

```
┌─────────────────────────────────────────┐
│  PRESENTATION LAYER (UI)                │
│  - Bootstrap 5 HTML/CSS                 │
│  - JavaScript (Vanilla)                 │
│  - Responsive Design                    │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│  BUSINESS LOGIC LAYER (PHP)             │
│  - Page controllers                     │
│  - Form processing                      │
│  - Validation & sanitization            │
│  - Authorization checks                 │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│  DATA ACCESS LAYER (MySQL)              │
│  - Database queries                     │
│  - Data persistence                     │
│  - Relationships & constraints          │
└─────────────────────────────────────────┘
```

---

## 📁 PROJECT STRUCTURE EXPLAINED

### Root Level Files

```php
index.php
├── Purpose: Login entry point
├── Logic: Validates credentials, creates session
├── Functions: password_verify(), $_SESSION setup
└── Redirects: To dashboard based on role

logout.php
├── Purpose: End session
├── Logic: Destroys session, redirects to login
└── Clears: All session variables

db_schema.sql
├── Purpose: Create database tables
├── Contains: 14 CREATE TABLE statements
├── Indexes: Performance optimization
└── Foreign Keys: Data integrity

demo_data.sql
├── Purpose: Sample data for testing
├── Data: Users, classes, assignments, grades
├── Users: admin/admin123, teacher1/teacher123, etc.
└── Note: Run after db_schema.sql
```

### Includes Folder

```php
config.php
├── Database Connection (mysqli)
├── Define Constants (SITE_URL, etc.)
├── Helper Functions:
│   ├── is_logged_in() - Check session
│   ├── get_user_role() - Get user role
│   ├── sanitize() - Input sanitization
│   ├── redirect() - Redirect function
│   └── alert() - Session alerts
└── Used By: All pages (include in every file)

header.php
├── HTML <head> tag
├── Bootstrap & jQuery CDN
├── Navigation bar
├── User dropdown menu
└── Logout link

footer.php
├── Closes HTML tags
├── Script tags for JS libraries
├── Bootstrap Bundle JS
├── Font Awesome icons
├── Custom script.js load
└── Close </body></html>
```

### Assets Folder

```
css/style.css
├── Custom Bootstrap extensions
├── Navbar gradient styling
├── Sidebar navigation styles
├── Card components (.stat-card, .class-card)
├── Form styling
├── Badge colors (.badge-danger, .badge-success, etc.)
├── Utility classes
└── Responsive utilities

js/script.js
├── confirmDelete() function
├── formatDate() helper
├── formatDateTime() helper
├── showLoading() / hideLoading()
├── Tooltip initialization
└── Modal handling utilities

img/
└── Image assets

uploads/
└── File upload directory
    ├── Permissions: chmod 777
    └── Clear periodically
```

---

## 📊 DATABASE SCHEMA RELATIONSHIPS

### User-Centric Design

```
┌──────────────┐
│    USERS     │  (id, username, password, role, etc.)
└──────┬───────┘
       │
       ├─→ Classes (teacher_id)
       │   └─→ Class_Members (class_id, user_id)
       │       └─→ Assignments (class_id)
       │           └─→ Assignment_Submissions (assignment_id, user_id)
       │
       ├─→ Announcements (teacher_id)
       │
       ├─→ Attendance (user_id)
       │
       ├─→ Discussions (creator_id)
       │   └─→ Discussion_Comments (user_id)
       │
       └─→ Grades (user_id)
```

### Key Relationships

```sql
-- Student in Class
class_members: class_id (FK) + user_id (FK)

-- Assignment in Class
assignments: class_id (FK), teacher_id (FK)

-- Student Submission
assignment_submissions:
  assignment_id (FK),
  student_id (FK),
  graded_by (FK to users)

-- Records
attendance: class_id (FK), user_id (FK)
grades: class_id (FK), user_id (FK)
announcements: class_id (FK), teacher_id (FK)
```

---

## 🔄 REQUEST FLOW

### Step-by-Step Page Load

```
1. User requests: index.php
   ↓
2. Browser sends HTTP request
   ↓
3. PHP receives request
   ↓
4. include config.php (DB connection)
   ↓
5. Check: is_logged_in()? (session check)
   ↓
6. If Not Logged: Show login form
   ↓
7. If Logged: Run page logic
   ├─ Fetch data from database
   ├─ Process forms if POST
   ├─ Apply role-based filters
   └─ Store in $variables
   ↓
8. include header.php (navigation)
   ↓
9. Echo HTML (use $variables)
   ↓
10. include footer.php (scripts)
    ↓
11. Browser renders HTML
```

### Form Submission Flow

```
1. User clicks "Submit" button
   ↓
2. Browser sends POST request
   ↓
3. PHP receives $_POST data
   ↓
4. Sanitize input: sanitize($_POST['field'])
   ↓
5. Validate required fields
   ↓
6. Execute SQL INSERT/UPDATE
   ├─ Use prepared statements (sanitized)
   └─ Handle errors
   ↓
7. Set session alert: alert('Success', 'Message', 'success')
   ↓
8. Redirect: redirect('current_page.php')
   ↓
9. Browser loads page again
   ↓
10. Display alert message at top
```

---

## 👥 ROLE-BASED ACCESS CONTROL

### User Roles

```
ADMIN (role = 0)
├─ Can view everything
├─ Can manage users
├─ Can manage system
├─ Can view all classes
└─ Can generate reports

TEACHER (role = 1)
├─ Can create classes
├─ Can manage own classes
├─ Can grade assignments
├─ Can record attendance
└─ Cannot view other teachers' classes

STUDENT (role = 2)
├─ Can view enrolled classes
├─ Can submit assignments
├─ Can view own grades
└─ Cannot access other students' work
```

### Authorization Check

```php
// At top of every protected page:

include 'includes/config.php';

// Check if logged in
if (!is_logged_in()) {
    redirect('index.php');
}

// Check specific role
if (get_user_role() != 1) {  // 1 = teacher
    die('Access denied');
}
```

### Ownership Verification

```php
// Example: Teacher verifying ownership of class

$class_id = $_GET['class_id'];
$query = "SELECT * FROM classes WHERE id = $class_id AND teacher_id = " . $_SESSION['user_id'];
$result = $conn->query($query);

if ($result->num_rows == 0) {
    die('This class does not belong to you');
}
```

---

## 🗄️ COMMON DATABASE QUERIES

### Get User Data

```php
// Get current user
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($query);
$user = $result->fetch_assoc();
```

### Get Teacher's Classes

```php
$teacher_id = $_SESSION['user_id'];
$query = "SELECT * FROM classes WHERE teacher_id = $teacher_id ORDER BY created_at DESC";
$result = $conn->query($query);

while ($class = $result->fetch_assoc()) {
    echo $class['name'];
}
```

### Get Student's Enrolled Classes

```php
$student_id = $_SESSION['user_id'];
$query = "SELECT c.* FROM classes c
          INNER JOIN class_members cm ON c.id = cm.class_id
          WHERE cm.user_id = $student_id";
$result = $conn->query($query);
```

### Get Class Students

```php
$class_id = $_GET['class_id'];
$query = "SELECT u.* FROM users u
          INNER JOIN class_members cm ON u.id = cm.user_id
          WHERE cm.class_id = $class_id AND u.role = 2";
$result = $conn->query($query);
```

### Get Assignment with Student Submissions

```php
$assignment_id = $_GET['assignment_id'];
$query = "SELECT a.*,
                 GROUP_CONCAT(u.username) as submitted_by,
                 COUNT(DISTINCT asu.id) as submission_count
          FROM assignments a
          LEFT JOIN assignment_submissions asu ON a.id = asu.assignment_id
          LEFT JOIN users u ON asu.student_id = u.id
          WHERE a.id = $assignment_id
          GROUP BY a.id";
```

### Get Student's Grades

```php
$student_id = $_SESSION['user_id'];
$query = "SELECT * FROM grades
          WHERE user_id = $student_id
          ORDER BY created_at DESC";
$result = $conn->query($query);
```

---

## 📝 FORM HANDLING PATTERN

### Standard Form Processing

```php
<?php
include 'includes/config.php';

if (!is_logged_in()) redirect('index.php');

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get & sanitize input
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);

    // Validate
    if (empty($name) || strlen($name) < 3) {
        alert('Error', 'Name must be at least 3 characters', 'danger');
    } else {
        // Execute query
        $query = "INSERT INTO table_name (name, description)
                  VALUES ('$name', '$description')";

        if ($conn->query($query)) {
            alert('Success', 'Record created successfully', 'success');
            redirect('this_page.php');
        } else {
            alert('Error', 'Database error: ' . $conn->error, 'danger');
        }
    }
}

// Display form / items
?>

<!-- View -->
<!-- Show alerts -->
<?php get_alert(); ?>

<!-- Show form -->
<form method="POST">
    <input type="text" name="name" required>
    <textarea name="description"></textarea>
    <button type="submit">Submit</button>
</form>
```

---

## 🎨 UI COMPONENT PATTERNS

### Creating Stat Card

```php
<div class="row">
    <div class="col-md-3">
        <div class="stat-card stat-card-primary">
            <h5>Total Users</h5>
            <h2><?php echo $total_users; ?></h2>
            <small>Active users in system</small>
        </div>
    </div>
</div>
```

### Creating Bootstrap Modal Form

```html
<!-- Button to trigger -->
<button
  class="btn btn-primary"
  data-bs-toggle="modal"
  data-bs-target="#addModal"
>
  Add Item
</button>

<!-- Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Item</h5>
      </div>
      <form method="POST">
        <div class="modal-body">
          <input type="text" name="name" class="form-control" required />
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>
```

---

## 🔍 DEBUGGING & TROUBLESHOOTING

### Common Issues & Solutions

```php
// Issue 1: Database Connection Error
// Solution:
echo $conn->error;  // Shows actual error

// Issue 2: Data not saving
// Solution:
var_dump($_POST);  // Check what was sent
echo $query;       // Check SQL query

// Issue 3: Page shows blank
// Solution:
error_log("Debug: " . $variable);  // Use logs
// Check error_log in PHP folder

// Issue 4: Session lost
// Solution:
session_start();   // Must be first line
echo $_SESSION['user_id'];  // Check session var
```

### Debugging Functions

```php
// Log to file
function debug_log($msg) {
    $log = fopen('debug.log', 'a');
    fwrite($log, date('Y-m-d H:i:s') . " - " . $msg . "\n");
    fclose($log);
}

// Output debug info
function show_debug($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

// Usage:
debug_log("User login attempt: " . $_POST['username']);
show_debug($_SESSION);
```

---

## 🔐 SECURITY BEST PRACTICES

### Input Validation

```php
// Always sanitize user input
$username = sanitize($_POST['username']);

// Validate length
if (strlen($username) < 3 || strlen($username) > 50) {
    die('Invalid username');
}

// Validate format
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    die('Username contains invalid characters');
}
```

### SQL Safety

```php
// GOOD: Database escapes values
$username = sanitize($_POST['username']);
$query = "SELECT * FROM users WHERE username = '$username'";

// BETTER: Use prepared statements (when available)
// Currently using mysqli_real_escape_string via sanitize()
// Future: implement prepared statements for extra safety
```

### Password Security

```php
// Hashing password
$hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
$query = "INSERT INTO users (password) VALUES ('$hash')";

// Verifying password
if (password_verify($_POST['password'], $stored_hash)) {
    // Password is correct
} else {
    // Password is wrong
}
```

---

## 📚 EXTENDING THE SYSTEM

### Adding a New Featured (Example: Assignments Features)

#### Step 1: Database Changes

```sql
-- Add to existing table or create new
ALTER TABLE assignments ADD COLUMN rubric_enabled INT(1) DEFAULT 0;
ALTER TABLE assignments ADD COLUMN max_attempts INT(2) DEFAULT 1;

-- Or create new related table
CREATE TABLE rubrics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    assignment_id INT,
    criteria VARCHAR(255),
    points INT,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id)
);
```

#### Step 2: Create/Update Page

```php
// teacher/create_rubric.php
<?php
include '../includes/config.php';

if (!is_logged_in() || get_user_role() != 1) {
    redirect('../index.php');
}

$assignment_id = $_GET['assignment_id'];

// Verify teacher owns assignment
// ... authorization check ...

if ($_POST) {
    $criteria = sanitize($_POST['criteria']);
    $points = (int)$_POST['points'];

    $query = "INSERT INTO rubrics (assignment_id, criteria, points)
              VALUES ($assignment_id, '$criteria', $points)";

    if ($conn->query($query)) {
        alert('Success', 'Rubric added', 'success');
    }
}

include '../includes/header.php';
?>

<!-- Display form -->

<?php include '../includes/footer.php'; ?>
```

#### Step 3: Update Related Pages

```php
// teacher/view_submissions.php
// Add rubric info when grading:

$rubric_query = "SELECT * FROM rubrics WHERE assignment_id = $assignment_id";
$rubric_result = $conn->query($rubric_query);

while ($rubric = $rubric_result->fetch_assoc()) {
    echo $rubric['criteria'] . " (" . $rubric['points'] . " pts)";
}
```

#### Step 4: Update Frontend

```html
<!-- Add UI elements for new feature -->
<div class="alert alert-info">
  <strong>Grading Rubric:</strong>
  <!-- Display rubric items -->
</div>
```

---

## 🚀 PERFORMANCE OPTIMIZATION

### Database Optimization

```sql
-- Add indexes for frequently queried columns
ALTER TABLE classes ADD INDEX (teacher_id);
ALTER TABLE class_members ADD INDEX (user_id);
ALTER TABLE assignments ADD INDEX (class_id);
ALTER TABLE assignment_submissions ADD INDEX (student_id, assignment_id);
ALTER TABLE attendance ADD INDEX (user_id, class_id);

-- Check query performance
EXPLAIN SELECT * FROM assignments WHERE teacher_id = 1;
```

### Code Optimization

```php
// BAD: N+1 query problem
$classes = $conn->query("SELECT * FROM classes");
while ($class = $classes->fetch_assoc()) {
    $teacher = $conn->query("SELECT * FROM users WHERE id = " . $class['teacher_id']);
    // This runs query for each class!
}

// GOOD: Use JOIN
$query = "SELECT c.*, u.name as teacher_name
          FROM classes c
          JOIN users u ON c.teacher_id = u.id";
$result = $conn->query($query);
```

---

## 📖 CODE STYLE & CONVENTIONS

### File Organization

```php
<?php
// 1. Includes
include 'includes/config.php';

// 2. Session check & authorization
if (!is_logged_in()) redirect('index.php');
if (get_user_role() != 1) die('Access denied');

// 3. Get parameters
$id = $_GET['id'] ?? null;

// 4. Model: Get data from DB
$query = "SELECT * FROM table WHERE id = $id";
$result = $conn->query($query);
$item = $result->fetch_assoc();

// 5. Control: Handle form submission
if ($_POST) {
    // Process form
}

// 6. View: Include header
include 'includes/header.php';
?>

<!-- 7. HTML/Display -->

<?php include 'includes/footer.php'; ?>
```

### Naming Conventions

```php
// Variables: snake_case
$total_users = 10;
$class_name = "Biology 101";

// Functions: camelCase
function sanitize() {}
function redirectUser() {}

// Database columns: snake_case
user_id, class_name, created_at

// Classes (future): PascalCase
class UserManager {}
class AssignmentController {}

// Constants: UPPERCASE
define('SITE_NAME', 'LMS');
define('TIMEOUT', 3600);
```

---

## 🧪 TESTING CHECKLIST

### Manual Testing

```
☐ Login with each role (admin, teacher, student)
☐ Create new class as teacher
☐ Add students to class
☐ Create assignment with deadline
☐ Submit assignment as student
☐ Grade assignment as teacher
☐ View grades as student
☐ Test attendance recording
☐ Test form validation (empty fields)
☐ Test SQL injection prevention
☐ Check responsive design on mobile
☐ Test file upload
☐ Check session timeout
```

### Common Test Cases

```php
// Test 1: Access control
// Login as student, try to access /teacher/dashboard.php
// Result: Should deny access

// Test 2: Data validation
// Submit form with empty name field
// Result: Should show error, not create record

// Test 3: Ownership verification
// Login as teacher1, try to grade assignment from teacher2
// Result: Should deny access

// Test 4: Date handling
// Create assignment with past due date
// Result: Should show as overdue for students
```

---

## 📞 GETTING HELP

### Documentation Files

```
README.md           - Feature overview
INSTALLATION.md     - Setup instructions
QUICKSTART.md       - Fast setup
CHECKLIST.md        - What's implemented
DEVELOPERS.md       - This file
```

### Common Issues

```
Q: Database connection fails
A: Check config.php credentials and MySQL is running

Q: Pages show blank
A: Check error_log, enable error_reporting

Q: Forms not submitting
A: Check POST method and input names match

Q: Sessions not working
A: session_start() must be first line
```

---

## 🎯 Key Development Principles

1. **Separation of Concerns**
   - Database logic separate from UI
   - Each page handles one feature

2. **DRY (Don't Repeat Yourself)**
   - Use include files for common code
   - Create helper functions

3. **Security First**
   - Sanitize all input
   - Check authorization
   - Hash passwords (bcrypt)

4. **User Experience**
   - Clear error messages
   - Success confirmations
   - Responsive design

5. **Maintainability**
   - Clean code with comments
   - Consistent naming
   - Modular structure

---

## 🚀 Ready to Extend!

Now you understand the LMS architecture and can:

✅ Add new features
✅ Fix bugs
✅ Optimize performance
✅ Enhance security
✅ Customize design
✅ Integrate with other systems

**Happy developing!** 👨‍💻

---

**Version**: 1.0
**Last Updated**: 2026
