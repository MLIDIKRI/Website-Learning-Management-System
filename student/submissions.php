<?php
require '../includes/config.php';
$page_title = 'Pengumpulan';

if (get_user_role() != 'student') {
    redirect('index.php');
}

$student_id = $_SESSION['user_id'];

// Get student's submissions
$submissions_query = "SELECT s.*, a.title as assignment_title, c.name as class_name 
                     FROM assignment_submissions s 
                     JOIN assignments a ON s.assignment_id = a.id 
                     JOIN classes c ON a.class_id = c.id 
                     WHERE s.student_id = $student_id 
                     ORDER BY s.created_at DESC";
$submissions = $conn->query($submissions_query);

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
            <li><a href="submissions.php" class="active"><i class="fas fa-file-upload"></i> Pengumpulan</a></li>
            <li><a href="grades.php"><i class="fas fa-chart-bar"></i> Nilai</a></li>
            <li><a href="materials.php"><i class="fas fa-book-open"></i> Materi</a></li>
            <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Pengumuman</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-file-upload"></i> Pengumpulan Tugas Saya</h2>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Daftar Pengumpulan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Tugas</th>
                            <th>Status</th>
                            <th>Tanggal Pengumpulan</th>
                            <th>Nilai</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($sub = $submissions->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $sub['class_name']; ?></td>
                                <td><?php echo $sub['assignment_title']; ?></td>
                                <td>
                                    <?php
                                    $status_badge = ['submitted' => 'warning', 'graded' => 'success', 'late' => 'danger'];
                                    $status_text = ['submitted' => 'Menunggu Review', 'graded' => 'Sudah Dinilai', 'late' => 'Terlambat'];
                                    ?>
                                    <span class="badge badge-<?php echo $status_badge[$sub['status']]; ?>">
                                        <?php echo $status_text[$sub['status']]; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y H:i', strtotime($sub['submitted_at'])); ?></td>
                                <td>
                                    <?php if ($sub['score'] !== null): ?>
                                        <strong><?php echo number_format($sub['score'], 2); ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="view_submission.php?id=<?php echo $sub['id']; ?>" class="btn btn-sm btn-info">
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