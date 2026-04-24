<?php
require '../includes/config.php';
$page_title = 'Detail Tugas';

if (get_user_role() != 'student') {
    redirect('index.php');
}

$student_id = $_SESSION['user_id'];
$assignment_id = (int)$_GET['id'];

// Get assignment details
$assignment_query = $conn->query("SELECT a.*, c.id as class_id, c.name as class_name FROM assignments a 
                                  JOIN classes c ON a.class_id = c.id 
                                  WHERE a.id = $assignment_id");
if ($assignment_query->num_rows == 0) {
    redirect('student/assignments.php');
}

$assignment = $assignment_query->fetch_assoc();

// Check if student is in the class
$check = $conn->query("SELECT id FROM class_members WHERE class_id = {$assignment['class_id']} AND student_id = $student_id");
if ($check->num_rows == 0) {
    redirect('student/assignments.php');
}

// Get student submission if exists
$submission_query = $conn->query("SELECT * FROM assignment_submissions WHERE assignment_id = $assignment_id AND student_id = $student_id");
$submission = $submission_query->num_rows > 0 ? $submission_query->fetch_assoc() : null;

// Handle submit assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_assignment'])) {
    $submission_text = sanitize($_POST['submission_text']);

    if (!$submission) {
        // New submission
        $now = date('Y-m-d H:i:s');
        $query = "INSERT INTO assignment_submissions (assignment_id, student_id, submission_text, submitted_at, status) 
                  VALUES ($assignment_id, $student_id, '$submission_text', '$now', 'submitted')";
    } else {
        // Update submission
        $now = date('Y-m-d H:i:s');
        $query = "UPDATE assignment_submissions SET submission_text = '$submission_text', submitted_at = '$now', status = 'submitted' 
                  WHERE id = {$submission['id']}";
    }

    if ($conn->query($query)) {
        alert('Tugas berhasil dikumpulkan!', 'success');
        redirect('student/view_assignment.php?id=' . $assignment_id);
    } else {
        alert('Gagal mengumpulkan tugas!', 'danger');
    }
}

$now = new DateTime();
$due = new DateTime($assignment['due_date']);
$is_late = $now > $due;

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-graduation-cap"></i> Student Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="classes.php"><i class="fas fa-book"></i> Kelas Saya</a></li>
            <li><a href="assignments.php" class="active"><i class="fas fa-tasks"></i> Tugas</a></li>
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
    <div class="mb-4">
        <a href="assignments.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <h2 class="mb-2"><?php echo $assignment['title']; ?></h2>
    <p class="text-muted mb-4">Kelas: <?php echo $assignment['class_name']; ?></p>

    <div class="row">
        <!-- Assignment Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Detail Tugas
                </div>
                <div class="card-body">
                    <h5>Deskripsi</h5>
                    <p><?php echo nl2br($assignment['description']); ?></p>

                    <?php if ($assignment['instructions']): ?>
                        <h5 class="mt-4">Instruksi Pengerjaan</h5>
                        <p><?php echo nl2br($assignment['instructions']); ?></p>
                    <?php endif; ?>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <strong>Nilai Maksimal:</strong><br>
                                <span class="badge badge-primary"><?php echo number_format($assignment['max_score'], 2); ?> Poin</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Tenggat Waktu:</strong><br>
                                <?php echo date('d M Y, H:i', strtotime($assignment['due_date'])); ?>
                                <?php if ($is_late): ?>
                                    <br><span class="badge badge-danger">SUDAH TERLAMBAT</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submission Form -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-file-upload"></i>
                    <?php echo $submission ? 'Edit Pengumpulan' : 'Kumpulkan Tugas'; ?>
                </div>
                <div class="card-body">
                    <?php if ($submission && $submission['status'] == 'graded'): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-check-circle"></i>
                            Tugas ini sudah dinilai. Anda tidak dapat mengubah pengumpulan lagi.
                        </div>
                    <?php endif; ?>

                    <form method="POST" <?php echo ($submission && $submission['status'] == 'graded') ? 'style="opacity: 0.5; pointer-events: none;"' : ''; ?>>
                        <div class="mb-3">
                            <label for="submission_text" class="form-label">Jawaban Anda</label>
                            <textarea class="form-control" id="submission_text" name="submission_text" rows="8"
                                required <?php echo ($submission && $submission['status'] == 'graded') ? 'disabled' : ''; ?>>
<?php echo $submission ? $submission['submission_text'] : ''; ?></textarea>
                        </div>

                        <?php if (!$submission || $submission['status'] != 'graded'): ?>
                            <button type="submit" name="submit_assignment" class="btn btn-primary">
                                <i class="fas fa-send"></i> <?php echo $submission ? 'Perbarui' : 'Kumpulkan'; ?> Tugas
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Submission Status -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fas fa-flag"></i> Status Pengumpulan
                </div>
                <div class="card-body">
                    <?php if ($submission): ?>
                        <div class="mb-3">
                            <p class="text-muted mb-1">Status</p>
                            <?php
                            $status_badge = ['submitted' => 'warning', 'graded' => 'success', 'late' => 'danger'];
                            $status_text = ['submitted' => 'Menunggu Review', 'graded' => 'Sudah Dinilai', 'late' => 'Terlambat'];
                            ?>
                            <h5 class="mb-2">
                                <span class="badge badge-<?php echo $status_badge[$submission['status']]; ?>">
                                    <?php echo $status_text[$submission['status']]; ?>
                                </span>
                            </h5>
                        </div>

                        <hr>

                        <p class="text-muted mb-1">Tanggal Pengumpulan</p>
                        <p><?php echo date('d M Y, H:i', strtotime($submission['submitted_at'])); ?></p>

                        <?php if ($submission['status'] == 'graded'): ?>
                            <hr>
                            <p class="text-muted mb-1">Nilai</p>
                            <h3 style="color: #667eea;">
                                <?php echo number_format($submission['score'], 2); ?> / <?php echo number_format($assignment['max_score'], 2); ?>
                            </h3>

                            <?php if ($submission['feedback']): ?>
                                <hr>
                                <p class="text-muted mb-1">Feedback Guru</p>
                                <div class="alert alert-info">
                                    <?php echo nl2br($submission['feedback']); ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted text-center mb-3">
                            <i class="fas fa-inbox"></i><br>
                            Belum dikumpulkan
                        </p>
                        <div class="alert alert-warning">
                            Silahkan isi form di samping dan kumpulkan tugas Anda.
                        </div>
                        <?php if ($is_late): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                Tenggat waktu sudah terlewat, jika Anda kumpulkan sekarang akan masuk kategori terlambat.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-calendar-alt"></i> Deadline
                </div>
                <div class="card-body text-center">
                    <p class="text-muted mb-2">Sisa Waktu</p>
                    <?php
                    $deadline = new DateTime($assignment['due_date']);
                    $now = new DateTime();
                    $interval = $now->diff($deadline);

                    if ($is_late) {
                        echo '<h6 style="color: #ef4444;">Sudah Terlewat</h6>';
                    } else {
                        echo '<h6 style="color: #10b981;">';
                        if ($interval->d > 0) echo $interval->d . ' hari ';
                        if ($interval->h > 0) echo $interval->h . ' jam ';
                        echo $interval->i . ' menit</h6>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>