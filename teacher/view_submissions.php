<?php
require '../includes/config.php';
$page_title = 'Lihat Pengumpulan';

if (get_user_role() != 'teacher') {
    redirect('index.php');
}

$teacher_id = $_SESSION['user_id'];
$assignment_id = (int)$_GET['assignment_id'];

// Get assignment details
$assignment_query = $conn->query("SELECT a.*, c.id as class_id, c.name as class_name FROM assignments a 
                                  JOIN classes c ON a.class_id = c.id 
                                  WHERE a.id = $assignment_id AND c.teacher_id = $teacher_id");
if ($assignment_query->num_rows == 0) {
    redirect('teacher/assignments.php');
}

$assignment = $assignment_query->fetch_assoc();

// Handle grading
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['grade_submission'])) {
    $submission_id = (int)$_POST['submission_id'];
    $score = (float)$_POST['score'];
    $feedback = sanitize($_POST['feedback']);

    $query = "UPDATE assignment_submissions SET score = $score, feedback = '$feedback', 
              graded_by = $teacher_id, graded_at = NOW(), status = 'graded' 
              WHERE id = $submission_id";
    if ($conn->query($query)) {
        alert('Pengumpulan berhasil dinilai!', 'success');
        redirect('teacher/view_submissions.php?assignment_id=' . $assignment_id);
    }
}

// Get submissions
$submissions_query = "SELECT s.*, u.full_name, u.username FROM assignment_submissions s 
                     JOIN users u ON s.student_id = u.id 
                     WHERE s.assignment_id = $assignment_id 
                     ORDER BY s.submitted_at DESC";
$submissions = $conn->query($submissions_query);

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-chalkboard-user"></i> Teacher Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="classes.php"><i class="fas fa-book"></i> Kelas</a></li>
            <li><a href="assignments.php" class="active"><i class="fas fa-tasks"></i> Tugas</a></li>
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
    <div class="mb-4">
        <a href="assignments.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <h2 class="mb-2"><?php echo $assignment['title']; ?></h2>
    <p class="text-muted mb-4">Kelas: <?php echo $assignment['class_name']; ?></p>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Daftar Pengumpulan (<?php echo $submissions->num_rows; ?>)
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Status</th>
                            <th>Tanggal Pengumpulan</th>
                            <th>Nilai</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($sub = $submissions->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $sub['full_name']; ?></strong><br>
                                    <small class="text-muted">@<?php echo $sub['username']; ?></small>
                                </td>
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
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                        data-bs-target="#gradeModal<?php echo $sub['id']; ?>">
                                        <i class="fas fa-eye"></i> Lihat & Nilai
                                    </button>
                                </td>
                            </tr>

                            <!-- Grade Modal -->
                            <div class="modal fade" id="gradeModal<?php echo $sub['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                            <h5 class="modal-title">Nilai Pengumpulan - <?php echo $sub['full_name']; ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-4">
                                                <h6>Jawaban Siswa</h6>
                                                <div class="alert alert-light border">
                                                    <?php echo nl2br($sub['submission_text']); ?>
                                                </div>
                                            </div>

                                            <form method="POST">
                                                <input type="hidden" name="submission_id" value="<?php echo $sub['id']; ?>">

                                                <div class="mb-3">
                                                    <label for="score<?php echo $sub['id']; ?>" class="form-label">Nilai (Max: <?php echo $assignment['max_score']; ?>)</label>
                                                    <input type="number" class="form-control" id="score<?php echo $sub['id']; ?>"
                                                        name="score" step="0.01" max="<?php echo $assignment['max_score']; ?>"
                                                        value="<?php echo $sub['score'] ?? ''; ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="feedback<?php echo $sub['id']; ?>" class="form-label">Feedback/Komentar</label>
                                                    <textarea class="form-control" id="feedback<?php echo $sub['id']; ?>"
                                                        name="feedback" rows="4"><?php echo $sub['feedback'] ?? ''; ?></textarea>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" name="grade_submission" class="btn btn-primary">
                                                        <i class="fas fa-save"></i> Simpan Nilai
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>