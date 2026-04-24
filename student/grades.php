<?php
require '../includes/config.php';
$page_title = 'Nilai';

if (get_user_role() != 'student') {
    redirect('index.php');
}

$student_id = $_SESSION['user_id'];

// Get grades per class
$grades_query = "SELECT g.*, c.name as class_name FROM grades g 
                 JOIN classes c ON g.class_id = c.id 
                 WHERE g.student_id = $student_id";
$grades = $conn->query($grades_query);

// Get detailed scores from submissions
$scores_query = "SELECT s.score, a.max_score, a.title, c.name as class_name 
                 FROM assignment_submissions s 
                 JOIN assignments a ON s.assignment_id = a.id 
                 JOIN classes c ON a.class_id = c.id 
                 WHERE s.student_id = $student_id AND s.score IS NOT NULL 
                 ORDER BY s.graded_at DESC";
$scores = $conn->query($scores_query);

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
            <li><a href="submissions.php"><i class="fas fa-file-upload"></i> Pengumpulan</a></li>
            <li><a href="grades.php" class="active"><i class="fas fa-chart-bar"></i> Nilai</a></li>
            <li><a href="materials.php"><i class="fas fa-book-open"></i> Materi</a></li>
            <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Pengumuman</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-chart-bar"></i> Nilai Saya</h2>

    <!-- Class Grades -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-book"></i> Nilai Per Kelas
        </div>
        <div class="card-body">
            <div class="row">
                <?php
                $grades->data_seek(0);
                while ($grade = $grades->fetch_assoc()):
                ?>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5><?php echo $grade['class_name']; ?></h5>
                                <div class="mb-3">
                                    <small class="text-muted">Total Nilai</small>
                                    <h3><?php echo number_format($grade['total_score'], 2); ?></h3>
                                </div>
                                <p>
                                    <strong>Grade:</strong>
                                    <span class="badge badge-info"><?php echo $grade['grade_letter']; ?></span>
                                </p>
                                <p>
                                    <strong>Status:</strong>
                                    <?php
                                    $status_badge = ['pass' => 'success', 'fail' => 'danger', 'incomplete' => 'warning'];
                                    $status_text = ['pass' => 'Lulus', 'fail' => 'Tidak Lulus', 'incomplete' => 'Belum Lengkap'];
                                    ?>
                                    <span class="badge badge-<?php echo $status_badge[$grade['final_status']]; ?>">
                                        <?php echo $status_text[$grade['final_status']]; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Detailed Scores -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Detail Nilai Per Tugas
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Tugas</th>
                            <th>Nilai</th>
                            <th>Maksimal</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($score = $scores->fetch_assoc()):
                            $percentage = ($score['score'] / $score['max_score']) * 100;
                        ?>
                            <tr>
                                <td><?php echo $score['class_name']; ?></td>
                                <td><?php echo $score['title']; ?></td>
                                <td><strong><?php echo number_format($score['score'], 2); ?></strong></td>
                                <td><?php echo number_format($score['max_score'], 2); ?></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: <?php echo $percentage; ?>%;"
                                            aria-valuenow="<?php echo $percentage; ?>"
                                            aria-valuemin="0" aria-valuemax="100">
                                            <?php echo round($percentage); ?>%
                                        </div>
                                    </div>
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