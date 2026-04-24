<?php
require '../includes/config.php';
$page_title = 'Activity Logs';

if (get_user_role() != 'admin') {
    redirect('index.php');
}

// Get logs
$logs_query = "SELECT l.*, u.username FROM admin_logs l 
               LEFT JOIN users u ON l.admin_id = u.id 
               ORDER BY l.created_at DESC LIMIT 100";

// Handle clear logs request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_logs'])) {
    $delete_query = "DELETE FROM admin_logs";
    if ($conn->query($delete_query)) {
        alert('Semua activity logs berhasil dihapus.', 'success');
    } else {
        alert('Gagal menghapus activity logs.', 'danger');
    }
    redirect('logs.php');
}

try {
    $logs = $conn->query($logs_query);
} catch (mysqli_sql_exception $e) {
    if (strpos($e->getMessage(), "doesn't exist") !== false) {
        $create_logs_table = "CREATE TABLE IF NOT EXISTS admin_logs (
            id INT PRIMARY KEY AUTO_INCREMENT,
            admin_id INT NOT NULL,
            action VARCHAR(255) NOT NULL,
            description TEXT,
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB";
        $conn->query($create_logs_table);
        $logs = $conn->query($logs_query);
    } else {
        die('Database error: ' . $e->getMessage());
    }
}

$log_count = $logs ? $logs->num_rows : 0;

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
            <li><a href="logs.php" class="active"><i class="fas fa-history"></i> Activity Logs</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-history"></i> Activity Logs</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="mb-0"><strong>Total log:</strong> <?php echo $log_count; ?></p>
        <div>
            <form method="GET" class="d-inline">
                <button type="submit" class="btn btn-secondary me-2">Refresh</button>
            </form>
            <form method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus semua activity logs?');">
                <button type="submit" name="clear_logs" class="btn btn-danger">Hapus Semua Activity Logs</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Catatan Aktivitas Terbaru
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Admin</th>
                            <th>Aksi</th>
                            <th>Deskripsi</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($log_count > 0): ?>
                            <?php while ($log = $logs->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('d M Y H:i:s', strtotime($log['created_at'])); ?></td>
                                    <td><?php echo $log['username'] ?? 'Unknown'; ?></td>
                                    <td><span class="badge badge-info"><?php echo $log['action']; ?></span></td>
                                    <td><?php echo $log['description']; ?></td>
                                    <td><?php echo $log['ip_address']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada activity logs.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>