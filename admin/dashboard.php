<?php
require '../includes/config.php';
$page_title = 'Admin Dashboard';

// Check if user is admin
if (get_user_role() != 'admin') {
    redirect('index.php');
}

// Get Statistics
$stats = [];

// Total Users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$stats['total_users'] = $result->fetch_assoc()['count'];

// Total Teachers
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'teacher'");
$stats['total_teachers'] = $result->fetch_assoc()['count'];

// Total Students
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'");
$stats['total_students'] = $result->fetch_assoc()['count'];

// Total Classes
$result = $conn->query("SELECT COUNT(*) as count FROM classes");
$stats['total_classes'] = $result->fetch_assoc()['count'];

// Total Assignments
$result = $conn->query("SELECT COUNT(*) as count FROM assignments");
$stats['total_assignments'] = $result->fetch_assoc()['count'];

// Recent Users
$recent_users_query = "SELECT id, username, full_name, role, created_at FROM users ORDER BY created_at DESC LIMIT 5";
$recent_users = $conn->query($recent_users_query);

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-cog"></i> Admin Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Kelola Pengguna</a></li>
            <li><a href="classes.php"><i class="fas fa-book"></i> Kelola Kelas</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li><a href="logs.php"><i class="fas fa-history"></i> Activity Logs</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Dashboard Admin</h2>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon stat-icon-1"><i class="fas fa-users"></i></div>
                </div>
                <div class="stat-content">
                    <h6>Total Pengguna</h6>
                    <h3><?php echo $stats['total_users']; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon stat-icon-2"><i class="fas fa-chalkboard-user"></i></div>
                </div>
                <div class="stat-content">
                    <h6>Guru</h6>
                    <h3><?php echo $stats['total_teachers']; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon stat-icon-3"><i class="fas fa-graduation-cap"></i></div>
                </div>
                <div class="stat-content">
                    <h6>Siswa</h6>
                    <h3><?php echo $stats['total_students']; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon stat-icon-4"><i class="fas fa-book"></i></div>
                </div>
                <div class="stat-content">
                    <h6>Total Kelas</h6>
                    <h3><?php echo $stats['total_classes']; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="card mt-4">
        <div class="card-header">
            <i class="fas fa-history"></i> Pengguna Terbaru
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Role</th>
                            <th>Tanggal Bergabung</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $recent_users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['full_name']; ?></td>
                                <td>
                                    <?php
                                    $role_badge = ['admin' => 'danger', 'teacher' => 'warning', 'student' => 'info'];
                                    $role_text = ['admin' => 'Admin', 'teacher' => 'Guru', 'student' => 'Siswa'];
                                    ?>
                                    <span class="badge badge-<?php echo $role_badge[$user['role']]; ?>">
                                        <?php echo $role_text[$user['role']]; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>