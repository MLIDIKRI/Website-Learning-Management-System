<?php
require '../includes/config.php';
$page_title = 'Lihat Pengumpulan';

if (get_user_role() != 'student') {
    redirect('index.php');
}

$student_id = $_SESSION['user_id'];
$submission_id = (int)$_GET['id'];

// Get submission details
$submission_query = $conn->query("SELECT s.*, a.title, a.max_score, c.name as class_name 
                                  FROM assignment_submissions s 
                                  JOIN assignments a ON s.assignment_id = a.id 
                                  JOIN classes c ON a.class_id = c.id 
                                  WHERE s.id = $submission_id AND s.student_id = $student_id");
if ($submission_query->num_rows == 0) {
    redirect('student/submissions.php');
}

$submission = $submission_query->fetch_assoc();

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
    <div class="mb-4">
        <a href="submissions.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <h2 class="mb-2"><?php echo $submission['title']; ?></h2>
    <p class="text-muted mb-4">Kelas: <?php echo $submission['class_name']; ?></p>

    <div class="row">
        <!-- Submission Content -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-file-alt"></i> Jawaban Anda
                </div>
                <div class="card-body">
                    <div class="bg-light p-4 rounded" style="border-left: 4px solid #667eea;">
                        <?php echo nl2br($submission['submission_text']); ?>
                    </div>
                </div>
            </div>

            <?php if ($submission['status'] == 'graded' && $submission['feedback']): ?>
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-comment"></i> Feedback Guru
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info" style="border-left: 4px solid #3b82f6;">
                            <?php echo nl2br($submission['feedback']); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Submission Status -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Detail Pengumpulan
                </div>
                <div class="card-body">
                    <p class="text-muted mb-1">Status</p>
                    <?php
                    $status_badge = ['submitted' => 'warning', 'graded' => 'success', 'late' => 'danger'];
                    $status_text = ['submitted' => 'Menunggu Review', 'graded' => 'Sudah Dinilai', 'late' => 'Terlambat'];
                    ?>
                    <h5 class="mb-3">
                        <span class="badge badge-<?php echo $status_badge[$submission['status']]; ?>" style="font-size: 14px; padding: 8px 12px;">
                            <?php echo $status_text[$submission['status']]; ?>
                        </span>
                    </h5>

                    <hr>

                    <p class="text-muted mb-1">Tanggal Pengumpulan</p>
                    <p class="mb-3"><?php echo date('d M Y, H:i', strtotime($submission['submitted_at'])); ?></p>

                    <?php if ($submission['status'] == 'graded'): ?>
                        <hr>
                        <p class="text-muted mb-1">Nilai Anda</p>
                        <h2 style="color: #667eea; margin-bottom: 10px;">
                            <?php echo number_format($submission['score'], 2); ?>
                        </h2>
                        <p style="color: #999; margin: 0;">
                            dari <?php echo number_format($submission['max_score'], 2); ?> poin
                        </p>

                        <div class="progress mt-3">
                            <?php
                            $percentage = ($submission['score'] / $submission['max_score']) * 100;
                            $color = $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger');
                            ?>
                            <div class="progress-bar bg-<?php echo $color; ?>" role="progressbar"
                                style="width: <?php echo $percentage; ?>%;"
                                aria-valuenow="<?php echo $percentage; ?>"
                                aria-valuemin="0" aria-valuemax="100">
                                <?php echo round($percentage); ?>%
                            </div>
                        </div>
                    <?php else: ?>
                        <hr>
                        <div class="alert alert-warning">
                            <i class="fas fa-hourglass-half"></i>
                            Tugas Anda masih dalam proses penilaian. Mohon ditunggu.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-check-circle"></i> Catatan
                </div>
                <div class="card-body">
                    <ul style="margin: 0; padding-left: 20px;">
                        <li>Periksa feedback guru dengan teliti</li>
                        <li>Gunakan nilai ini untuk pembelajaran</li>
                        <li>Jika ada yang belum jelas, tanya kepada guru</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>