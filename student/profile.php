<?php
require '../includes/config.php';
$page_title = 'Profil Siswa';

if (get_user_role() != 'student') {
    redirect('index.php');
}

$user_id = $_SESSION['user_id'];

// Get user info
$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

// Handle update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $bio = sanitize($_POST['bio']);

    $query = "UPDATE users SET full_name = '$full_name', phone = '$phone', bio = '$bio' WHERE id = $user_id";
    if ($conn->query($query)) {
        $_SESSION['full_name'] = $full_name;
        alert('Profil berhasil diperbarui!', 'success');
        redirect('profile.php');
    }
}

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
        <h5><i class="fas fa-graduation-cap"></i> Student Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="classes.php"><i class="fas fa-book"></i> Kelas Saya</a></li>
            <li><a href="assignments.php"><i class="fas fa-tasks"></i> Tugas</a></li>
            <li><a href="submissions.php"><i class="fas fa-file-upload"></i> Pengumpulan</a></li>
            <li><a href="grades.php"><i class="fas fa-chart-bar"></i> Nilai</a></li>
            <li><a href="materials.php"><i class="fas fa-book-open"></i> Materi</a></li>
            <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Pengumuman</a></li>
            <li><a href="profile.php" class="active"><i class="fas fa-user"></i> Profil</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-user"></i> Profil Saya</h2>

    <div class="row">
        <!-- Profile Info -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user"></i> Informasi Pribadi
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" value="<?php echo $user['username']; ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?php echo $user['email']; ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                value="<?php echo $user['full_name']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="<?php echo $user['phone']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">Biografi</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo $user['bio']; ?></textarea>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-lock"></i> Ubah Password
                </div>
                <div class="card-body">
                    <form method="POST">
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
                        <button type="submit" name="change_password" class="btn btn-warning">
                            <i class="fas fa-key"></i> Ubah Password
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Informasi Akun
                </div>
                <div class="card-body">
                    <p><strong>Terdaftar sejak:</strong> <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
                    <p><strong>Status:</strong> <?php echo $user['status'] == 'active' ? 'Aktif' : 'Tidak Aktif'; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>