<?php
require '../includes/config.php';
$page_title = 'Nilai Siswa';

if (get_user_role() != 'teacher') {
    redirect('index.php');
}

$teacher_id = $_SESSION['user_id'];

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-chalkboard-user"></i> Teacher Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="classes.php"><i class="fas fa-book"></i> Kelas</a></li>
            <li><a href="assignments.php"><i class="fas fa-tasks"></i> Tugas</a></li>
            <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Pengumuman</a></li>
            <li><a href="discussions.php"><i class="fas fa-comments"></i> Diskusi</a></li>
            <li><a href="attendance.php"><i class="fas fa-list-check"></i> Kehadiran</a></li>
            <li><a href="grades.php" class="active"><i class="fas fa-chart-bar"></i> Nilai</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-chart-bar"></i> Nilai Siswa</h2>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Pilih Kelas untuk Melihat Nilai
        </div>
        <div class="card-body">
            <div class="row">
                <?php
                $classes = $conn->query("SELECT id, code, name FROM classes WHERE teacher_id = $teacher_id");
                while ($class = $classes->fetch_assoc()):
                ?>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5><?php echo $class['code']; ?></h5>
                                <p><?php echo $class['name']; ?></p>
                                <a href="view_grades.php?class_id=<?php echo $class['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-chart-bar"></i> Lihat Nilai
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>