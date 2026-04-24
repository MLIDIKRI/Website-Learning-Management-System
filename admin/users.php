<?php
require '../includes/config.php';
$page_title = 'Kelola Pengguna';

if (get_user_role() != 'admin') {
    redirect('index.php');
}

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $role = sanitize($_POST['role']);

    $check = $conn->query("SELECT id FROM users WHERE username = '$username' OR email = '$email'");
    if ($check->num_rows > 0) {
        alert('Username atau Email sudah terdaftar!', 'danger');
    } else {
        $query = "INSERT INTO users (username, email, password, full_name, phone, role) 
                  VALUES ('$username', '$email', '$password', '$full_name', '$phone', '$role')";
        if ($conn->query($query)) {
            log_admin_action('add_user', "Menambahkan user: $username ($role)");
            alert('Pengguna berhasil ditambahkan!', 'success');
            redirect('admin/users.php');
        } else {
            alert('Gagal menambahkan pengguna!', 'danger');
        }
    }
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $user_result = $conn->query("SELECT username, role FROM users WHERE id = $id");
    $user_data = $user_result->fetch_assoc();

    $query = "DELETE FROM users WHERE id = $id AND role != 'admin'";
    if ($conn->query($query)) {
        log_admin_action('delete_user', "Menghapus user: " . ($user_data['username'] ?? 'unknown'));
        alert('Pengguna berhasil dihapus!', 'success');
        redirect('admin/users.php');
    }
}

// Handle Reset Semua Password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_all_passwords'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        alert('Password baru dan konfirmasi tidak sesuai!', 'danger');
    } elseif (strlen($new_password) < 6) {
        alert('Password minimal 6 karakter!', 'danger');
    } else {
        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $query = "UPDATE users SET password = '$hashed'";
        if ($conn->query($query)) {
            log_admin_action('reset_all_passwords', 'Reset semua password ke password custom');
            alert('Semua password pengguna berhasil direset!', 'success');
            redirect('admin/users.php');
        } else {
            alert('Gagal mereset password semua pengguna.', 'danger');
        }
    }
}

// Handle Reset Semua Password Default
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_all_passwords_default'])) {
    if (reset_all_passwords_to_default()) {
        log_admin_action('reset_all_passwords_default', 'Reset semua password ke default: ' . DEFAULT_USER_PASSWORD);
        alert('Semua password pengguna telah direset ke: ' . DEFAULT_USER_PASSWORD, 'success');
        redirect('admin/users.php');
    } else {
        alert('Gagal mereset password semua pengguna.', 'danger');
    }
}

// Get All Users
$search = '';
if (isset($_GET['search'])) {
    $search = sanitize($_GET['search']);
    $users_query = "SELECT * FROM users WHERE username LIKE '%$search%' OR full_name LIKE '%$search%' ORDER BY created_at DESC";
} else {
    $users_query = "SELECT * FROM users ORDER BY created_at DESC";
}
$users = $conn->query($users_query);

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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users"></i> Kelola Pengguna</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus"></i> Tambah Pengguna
        </button>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Cari username atau nama..."
                        value="<?php echo $search; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reset Semua Password (Admin) -->
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning text-white">
            <i class="fas fa-key"></i> Reset Semua Password (Semua User)
        </div>
        <div class="card-body">
            <p>Gunakan fitur ini untuk mengatur ulang password seluruh pengguna ke password baru yang sama.</p>
            <form method="POST" class="row g-2">
                <div class="col-md-4">
                    <input type="password" name="new_password" class="form-control" placeholder="Password baru" required>
                </div>
                <div class="col-md-4">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi password" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="reset_all_passwords" class="btn btn-danger w-100">Reset Semua Password</button>
                </div>
            </form>
            <form method="POST" class="mt-3">
                <button type="submit" name="reset_all_passwords_default" class="btn btn-outline-danger w-100">Reset Semua ke Password Default: <?php echo DEFAULT_USER_PASSWORD; ?></button>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Daftar Pengguna
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['full_name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <?php
                                    $role_badge = ['admin' => 'danger', 'teacher' => 'warning', 'student' => 'info'];
                                    $role_text = ['admin' => 'Admin', 'teacher' => 'Guru', 'student' => 'Siswa'];
                                    ?>
                                    <span class="badge badge-<?php echo $role_badge[$user['role']]; ?>">
                                        <?php echo $role_text[$user['role']]; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['status'] == 'active'): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($user['role'] != 'admin'): ?>
                                        <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger"
                                            onclick="return confirmDelete();">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="teacher">Guru</option>
                            <option value="student">Siswa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="add_user" class="btn btn-primary">Tambah Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>