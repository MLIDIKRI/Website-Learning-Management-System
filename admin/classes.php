<?php
require '../includes/config.php';
$page_title = 'Kelola Kelas';

if (get_user_role() != 'admin') {
    redirect('index.php');
}

// Get all classes
$classes_query = "SELECT c.*, u.full_name as teacher_name FROM classes c 
                  JOIN users u ON c.teacher_id = u.id ORDER BY c.created_at DESC";
$classes = $conn->query($classes_query);

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-cog"></i> Admin Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Kelola Pengguna</a></li>
            <li><a href="classes.php" class="active"><i class="fas fa-book"></i> Kelola Kelas</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li><a href="logs.php"><i class="fas fa-history"></i> Activity Logs</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-book"></i> Kelola Kelas</h2>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Daftar Kelas
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode Kelas</th>
                            <th>Nama Kelas</th>
                            <th>Guru</th>
                            <th>Status</th>
                            <th>Jumlah Siswa</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($class = $classes->fetch_assoc()):
                            // Get student count
                            $student_count = $conn->query("SELECT COUNT(*) as count FROM class_members WHERE class_id = {$class['id']}")->fetch_assoc()['count'];
                        ?>
                            <tr>
                                <td><strong><?php echo $class['code']; ?></strong></td>
                                <td><?php echo $class['name']; ?></td>
                                <td><?php echo $class['teacher_name']; ?></td>
                                <td>
                                    <?php if ($class['status'] == 'active'): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $student_count; ?> siswa</td>
                                <td>
                                    <a href="view_class.php?id=<?php echo $class['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>