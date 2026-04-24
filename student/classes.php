<?php
require '../includes/config.php';
$page_title = 'Kelas Saya';

if (get_user_role() != 'student') {
    redirect('index.php');
}

$student_id = $_SESSION['user_id'];

// Get student's classes
$classes_query = "SELECT c.* FROM classes c 
                  JOIN class_members cm ON c.id = cm.class_id 
                  WHERE cm.student_id = $student_id ORDER BY c.created_at DESC";
$classes = $conn->query($classes_query);

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-graduation-cap"></i> Student Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="classes.php" class="active"><i class="fas fa-book"></i> Kelas Saya</a></li>
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
    <h2 class="mb-4"><i class="fas fa-book"></i> Kelas Saya</h2>

    <div class="row">
        <?php while ($class = $classes->fetch_assoc()):
            $teacher = $conn->query("SELECT full_name FROM users WHERE id = {$class['teacher_id']}")->fetch_assoc();
        ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <strong><?php echo $class['code']; ?></strong>
                    </div>
                    <div class="card-body">
                        <h5><?php echo $class['name']; ?></h5>
                        <p class="text-muted">Guru: <?php echo $teacher['full_name']; ?></p>
                        <p><?php echo substr($class['description'], 0, 100); ?></p>

                        <a href="view_class.php?id=<?php echo $class['id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-right"></i> Buka Kelas
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require '../includes/footer.php'; ?>