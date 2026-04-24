<?php
require '../includes/config.php';
$page_title = 'Teacher Dashboard';

if (get_user_role() != 'teacher') {
    redirect('index.php');
}

$teacher_id = $_SESSION['user_id'];

// Get teacher's classes
$classes_query = "SELECT * FROM classes WHERE teacher_id = $teacher_id ORDER BY created_at DESC";
$classes = $conn->query($classes_query);
$total_classes = $classes->num_rows;

// Get total students
$total_students_query = "SELECT COUNT(*) as count FROM class_members 
                        WHERE class_id IN (SELECT id FROM classes WHERE teacher_id = $teacher_id)";
$total_students = $conn->query($total_students_query)->fetch_assoc()['count'];

// Get total assignments
$total_assignments_query = "SELECT COUNT(*) as count FROM assignments 
                           WHERE class_id IN (SELECT id FROM classes WHERE teacher_id = $teacher_id)";
$total_assignments = $conn->query($total_assignments_query)->fetch_assoc()['count'];

// Get pending submissions
$pending_submissions_query = "SELECT COUNT(*) as count FROM assignment_submissions 
                             WHERE status = 'submitted' AND 
                             assignment_id IN (SELECT id FROM assignments 
                             WHERE class_id IN (SELECT id FROM classes WHERE teacher_id = $teacher_id))";
$pending_submissions = $conn->query($pending_submissions_query)->fetch_assoc()['count'];

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-chalkboard-user"></i> Teacher Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="classes.php"><i class="fas fa-book"></i> Kelas</a></li>
            <li><a href="assignments.php"><i class="fas fa-tasks"></i> Tugas</a></li>
            <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Pengumuman</a></li>
            <li><a href="discussions.php"><i class="fas fa-comments"></i> Diskusi</a></li>
            <li><a href="attendance.php"><i class="fas fa-list-check"></i> Kehadiran</a></li>
            <li><a href="grades.php"><i class="fas fa-chart-bar"></i> Nilai</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Dashboard Guru</h2>

    <!-- Statistics -->
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon stat-icon-1"><i class="fas fa-book"></i></div>
                </div>
                <div class="stat-content">
                    <h6>Kelas Aktif</h6>
                    <h3><?php echo $total_classes; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon stat-icon-2"><i class="fas fa-graduation-cap"></i></div>
                </div>
                <div class="stat-content">
                    <h6>Total Siswa</h6>
                    <h3><?php echo $total_students; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon stat-icon-3"><i class="fas fa-tasks"></i></div>
                </div>
                <div class="stat-content">
                    <h6>Total Tugas</h6>
                    <h3><?php echo $total_assignments; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon stat-icon-4"><i class="fas fa-hourglass-half"></i></div>
                </div>
                <div class="stat-content">
                    <h6>Pending Review</h6>
                    <h3><?php echo $pending_submissions; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Classes -->
    <div class="card mt-4">
        <div class="card-header">
            <i class="fas fa-book"></i> Kelas Terbaru
        </div>
        <div class="card-body">
            <?php if ($total_classes > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Kelas</th>
                                <th>Jumlah Siswa</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $classes->data_seek(0);
                            while ($class = $classes->fetch_assoc()):
                                $student_count = $conn->query("SELECT COUNT(*) as count FROM class_members WHERE class_id = {$class['id']}")->fetch_assoc()['count'];
                            ?>
                                <tr>
                                    <td><strong><?php echo $class['code']; ?></strong></td>
                                    <td><?php echo $class['name']; ?></td>
                                    <td><?php echo $student_count; ?> siswa</td>
                                    <td>
                                        <a href="class_detail.php?id=<?php echo $class['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">Anda belum memiliki kelas. <a href="classes.php">Buat kelas sekarang</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>