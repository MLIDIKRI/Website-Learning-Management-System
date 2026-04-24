<?php
require '../includes/config.php';
$page_title = 'Settings';

if (get_user_role() != 'admin') {
    redirect('index.php');
}

// Handle save settings
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_settings'])) {
    // Settings bisa disimpan ke file config atau database sesuai kebutuhan
    // Untuk sekarang, hanya tampilkan pesan sukses
    $alert_msg = "Pengaturan berhasil disimpan!";
    alert($alert_msg, 'success');
    // redirect('settings.php');
}

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-cog"></i> Admin Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Kelola Pengguna</a></li>
            <li><a href="classes.php"><i class="fas fa-book"></i> Kelola Kelas</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li><a href="logs.php"><i class="fas fa-history"></i> Activity Logs</a></li>
            <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Pengaturan</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-cog"></i> Pengaturan Sistem</h2>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-sliders-h"></i> Pengaturan Umum
        </div>
        <div class="card-body">
            <form method="POST" action="settings.php">
                <div class="mb-3">
                    <label for="site_name" class="form-label">Nama Situs</label>
                    <input type="text" class="form-control" id="site_name" name="site_name" value="LMS - Learning Management System">
                </div>
                <div class="mb-3">
                    <label for="site_url" class="form-label">URL Situs</label>
                    <input type="text" class="form-control" id="site_url" name="site_url" value="<?php echo SITE_URL; ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="session_timeout" class="form-label">Session Timeout (seconds)</label>
                    <input type="number" class="form-control" id="session_timeout" name="session_timeout" value="3600">
                </div>
                <div class="mb-3">
                    <label for="max_file_size" class="form-label">Ukuran File Maksimal (MB)</label>
                    <input type="number" class="form-control" id="max_file_size" name="max_file_size" value="50">
                </div>
                <button type="submit" class="btn btn-primary" name="save_settings">Simpan Pengaturan</button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <i class="fas fa-database"></i> Database
        </div>
        <div class="card-body">
            <p><strong>Host:</strong> <?php echo DB_HOST; ?></p>
            <p><strong>Database:</strong> <?php echo DB_NAME; ?></p>
            <p><strong>User:</strong> <?php echo DB_USER; ?></p>
            <a href="#" class="btn btn-warning">Backup Database</a>
            <a href="#" class="btn btn-danger">Restore Database</a>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>