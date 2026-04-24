<?php
require '../includes/config.php';
$page_title = 'Student Dashboard';

if (get_user_role() != 'student') {
    redirect('index.php');
}

$student_id = $_SESSION['user_id'];

// Get student's classes
$classes_query = "SELECT c.* FROM classes c 
                  JOIN class_members cm ON c.id = cm.class_id 
                  WHERE cm.student_id = $student_id ORDER BY c.created_at DESC";
$classes = $conn->query($classes_query);
$total_classes = $classes->num_rows;

// Get pending assignments
$pending_assignments_query = "SELECT COUNT(*) as count FROM assignments a 
                             WHERE a.class_id IN (SELECT c.id FROM classes c 
                             JOIN class_members cm ON c.id = cm.class_id 
                             WHERE cm.student_id = $student_id)
                             AND a.due_date > NOW()";
$pending_assignments = $conn->query($pending_assignments_query)->fetch_assoc()['count'];

// Get total submissions
$total_submissions_query = "SELECT COUNT(*) as count FROM assignment_submissions 
                           WHERE student_id = $student_id";
$total_submissions = $conn->query($total_submissions_query)->fetch_assoc()['count'];

// Get average grade
$average_grade_query = "SELECT AVG(score) as avg_score FROM assignment_submissions 
                       WHERE student_id = $student_id AND score IS NOT NULL";
$avg_grade = $conn->query($average_grade_query)->fetch_assoc()['avg_score'];
$avg_grade = $avg_grade ? round($avg_grade, 2) : 0;

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-graduation-cap"></i> Student Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="classes.php"><i class="fas fa-book"></i> Kelas Saya</a></li>
            <li><a href="assignments.php"><i class="fas fa-tasks"></i> Tugas</a></li>
            <li><a href="submissions.php"><i class="fas fa-file-upload"></i> Pengumpulan</a></li>
            <li><a href="grades.php"><i class="fas fa-chart-bar"></i> Nilai</a></li>
            <li><a href="materials.php"><i class="fas fa-book-open"></i> Materi</a></li>
            <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Pengumuman</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Dashboard Siswa</h2>

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
                    <div class="stat-icon stat-icon-2"><i class="fas fa-tasks"></i></div>
                </div>
                <div class="stat-content">
                    <h6>Tugas Pending</h6>
                    <h3><?php echo $pending_assignments; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon stat-icon-3"><i class="fas fa-file-upload"></i></div>
                </div>
                <div class="stat-content">
                    <h6>Total Pengumpulan</h6>
                    <h3><?php echo $total_submissions; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon stat-icon-4"><i class="fas fa-star"></i></div>
                </div>
                <div class="stat-content">
                    <h6>Rata-rata Nilai</h6>
                    <h3><?php echo $avg_grade; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Classes -->
    <div class="card mt-4">
        <div class="card-header">
            <i class="fas fa-book"></i> Kelas Saya
        </div>
        <div class="card-body">
            <?php if ($total_classes > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Kelas</th>
                                <th>Guru</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $classes->data_seek(0);
                            while ($class = $classes->fetch_assoc()):
                                $teacher = $conn->query("SELECT full_name FROM users WHERE id = {$class['teacher_id']}")->fetch_assoc();
                            ?>
                                <tr>
                                    <td><strong><?php echo $class['code']; ?></strong></td>
                                    <td><?php echo $class['name']; ?></td>
                                    <td><?php echo $teacher['full_name']; ?></td>
                                    <td>
                                        <a href="view_class.php?id=<?php echo $class['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">Anda belum terdaftar di kelas manapun.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>