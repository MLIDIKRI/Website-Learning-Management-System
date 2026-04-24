<?php
require '../includes/config.php';
$page_title = 'Pengumuman';

if (get_user_role() != 'student') {
    redirect('index.php');
}

$student_id = $_SESSION['user_id'];

// Get announcements from student's classes
$announcements_query = "SELECT a.*, c.name as class_name FROM announcements a 
                       JOIN classes c ON a.class_id = c.id 
                       JOIN class_members cm ON c.id = cm.class_id 
                       WHERE cm.student_id = $student_id 
                       ORDER BY a.created_at DESC";
$announcements = $conn->query($announcements_query);

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
            <li><a href="grades.php"><i class="fas fa-chart-bar"></i> Nilai</a></li>
            <li><a href="materials.php"><i class="fas fa-book-open"></i> Materi</a></li>
            <li><a href="announcements.php" class="active"><i class="fas fa-bullhorn"></i> Pengumuman</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-bullhorn"></i> Pengumuman</h2>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Daftar Pengumuman
        </div>
        <div class="card-body">
            <?php if ($announcements->num_rows > 0): ?>
                <?php while ($ann = $announcements->fetch_assoc()): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5><?php echo $ann['title']; ?></h5>
                                    <p class="text-muted mb-2">
                                        <small><?php echo $ann['class_name']; ?> -
                                            <?php echo date('d M Y H:i', strtotime($ann['created_at'])); ?></small>
                                    </p>
                                </div>
                                <span class="badge badge-<?php
                                                            echo $ann['priority'] == 'high' ? 'danger' : ($ann['priority'] == 'normal' ? 'info' : 'secondary');
                                                            ?>">
                                    <?php echo ucfirst($ann['priority']); ?>
                                </span>
                            </div>
                            <p><?php echo nl2br($ann['content']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted text-center">Belum ada pengumuman</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>