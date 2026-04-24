<?php
session_start();
require 'includes/config.php';

// If user is logged in, redirect to their dashboard
if (is_logged_in()) {
    $role = get_user_role();
    if ($role == 'admin') {
        redirect('admin/dashboard.php');
    } else if ($role == 'teacher') {
        redirect('teacher/dashboard.php');
    } else if ($role == 'student') {
        redirect('student/dashboard.php');
    }
}

// Login logic
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE (username = '$username' OR email = '$username') AND status = 'active'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['avatar'] = $user['avatar'];

            // Log user login event (admin, teacher, student)
            log_admin_action('login', 'Login user: ' . $user['username']);

            if ($user['role'] == 'admin') {
                redirect('admin/dashboard.php');
            } else if ($user['role'] == 'teacher') {
                redirect('teacher/dashboard.php');
            } else if ($user['role'] == 'student') {
                redirect('student/dashboard.php');
            }
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Username tidak ditemukan!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 28px;
            margin: 0;
            font-weight: bold;
        }

        .login-body {
            padding: 30px;
        }

        .form-control {
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .alert {
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .demo-info {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 13px;
        }

        .demo-info h6 {
            margin-bottom: 8px;
            color: #667eea;
            font-weight: bold;
        }

        .demo-info p {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1>📚 LMS</h1>
            <p style="margin-top: 10px; font-size: 14px;">Learning Management System</p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required placeholder="Masukkan username Anda">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan password Anda">
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>

            <div class="demo-info">
                <h6>📝 Akun Demo:</h6>
                <p><strong>Admin:</strong> admin / admin123</p>
                <p><strong>Teacher:</strong> teacher1 / teacher123</p>
                <p><strong>Student:</strong> student1 / student123</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>