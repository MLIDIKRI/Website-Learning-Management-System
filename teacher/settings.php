<?php
require '../includes/config.php';
$page_title = 'Pengaturan Profil';

if (get_user_role() != 'teacher') {
    redirect('index.php');
}

$user_id = $_SESSION['user_id'];

// Get user info
$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

// Handle change password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!password_verify($old_password, $user['password'])) {
        alert('Password lama tidak sesuai!', 'danger');
    } else if ($new_password != $confirm_password) {
        alert('Password baru dan konfirmasi tidak sesuai!', 'danger');
    } else if (strlen($new_password) < 6) {
        alert('Password minimal 6 karakter!', 'danger');
    } else {
        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $query = "UPDATE users SET password = '$hashed' WHERE id = $user_id";
        if ($conn->query($query)) {
            alert('Password berhasil diubah!', 'success');
        }
    }
}

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-cog"></i> Settings</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
            <li><a href="settings.php" class="active"><i class="fas fa-lock"></i> Ganti Password</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-lock"></i> Ganti Password</h2>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-key"></i> Ubah Password
        </div>
        <div class="card-body">
            <form method="POST" action="settings.php">
                <div class="mb-3">
                    <label for="old_password" class="form-label">Password Lama</label>
                    <input type="password" class="form-control" id="old_password" name="old_password" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary" name="change_password">Ganti Password</button>
            </form>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
