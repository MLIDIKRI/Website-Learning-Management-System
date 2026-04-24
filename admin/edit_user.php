<?php
require '../includes/config.php';
$page_title = 'Edit Pengguna';

if (get_user_role() != 'admin') {
    redirect('index.php');
}

$user_id = (int)$_GET['id'];
$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
if ($user_query->num_rows == 0) {
    redirect('admin/users.php');
}

$user = $user_query->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $status = sanitize($_POST['status']);

    $query = "UPDATE users SET full_name = '$full_name', phone = '$phone', status = '$status' WHERE id = $user_id";
    if ($conn->query($query)) {
        alert('Pengguna berhasil diperbarui!', 'success');
        redirect('admin/users.php');
    }
}

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-cog"></i> Admin Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="users.php" class="active"><i class="fas fa-users"></i> Kelola Pengguna</a></li>
            <li><a href="classes.php"><i class="fas fa-book"></i> Kelola Kelas</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li><a href="logs.php"><i class="fas fa-history"></i> Activity Logs</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <div class="mb-4">
        <a href="users.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <h2 class="mb-4"><i class="fas fa-edit"></i> Edit Pengguna</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user"></i> Informasi Pengguna
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" value="<?php echo $user['username']; ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?php echo $user['email']; ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" class="form-control" value="<?php echo ucfirst($user['role']); ?>" disabled>
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
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>Aktif</option>
                                <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>Tidak Aktif</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Informasi Akun
                </div>
                <div class="card-body">
                    <p><strong>ID Pengguna:</strong> <?php echo $user['id']; ?></p>
                    <p><strong>Terdaftar:</strong> <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
                    <p><strong>Terakhir Diperbarui:</strong> <?php echo date('d M Y H:i', strtotime($user['updated_at'])); ?></p>
                    <p><strong>Status Saat Ini:</strong>
                        <span class="badge <?php echo $user['status'] == 'active' ? 'badge-success' : 'badge-danger'; ?>">
                            <?php echo $user['status'] == 'active' ? 'Aktif' : 'Tidak Aktif'; ?>
                        </span>
                    </p>

                    <?php if ($user['role'] == 'teacher'): ?>
                        <hr>
                        <h6>Kelas yang Diajar</h6>
                        <?php
                        $classes = $conn->query("SELECT code, name FROM classes WHERE teacher_id = {$user['id']}");
                        if ($classes->num_rows > 0) {
                            while ($class = $classes->fetch_assoc()) {
                                echo '<span class="badge badge-info">' . $class['code'] . ' - ' . $class['name'] . '</span> ';
                            }
                        } else {
                            echo '<p class="text-muted">Tidak ada kelas</p>';
                        }
                        ?>
                    <?php elseif ($user['role'] == 'student'): ?>
                        <hr>
                        <h6>Kelas yang Diambil</h6>
                        <?php
                        $classes = $conn->query("SELECT c.code, c.name FROM classes c 
                                           JOIN class_members cm ON c.id = cm.class_id 
                                           WHERE cm.student_id = {$user['id']}");
                        if ($classes->num_rows > 0) {
                            while ($class = $classes->fetch_assoc()) {
                                echo '<span class="badge badge-info">' . $class['code'] . ' - ' . $class['name'] . '</span> ';
                            }
                        } else {
                            echo '<p class="text-muted">Tidak ada kelas</p>';
                        }
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>