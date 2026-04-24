<?php
require '../includes/config.php';
$page_title = 'Diskusi';

if (get_user_role() != 'teacher') {
    redirect('index.php');
}

$teacher_id = $_SESSION['user_id'];

// Get teacher's discussions
$discussions_query = "SELECT d.*, c.name as class_name, u.full_name FROM discussions d 
                     JOIN classes c ON d.class_id = c.id 
                     JOIN users u ON d.created_by = u.id
                     WHERE c.teacher_id = $teacher_id ORDER BY d.created_at DESC";
$discussions = $conn->query($discussions_query);

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
            <li><a href="discussions.php" class="active"><i class="fas fa-comments"></i> Diskusi</a></li>
            <li><a href="attendance.php"><i class="fas fa-list-check"></i> Kehadiran</a></li>
            <li><a href="grades.php"><i class="fas fa-chart-bar"></i> Nilai</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-comments"></i> Diskusi Kelas</h2>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Daftar Diskusi
        </div>
        <div class="card-body">
            <?php if ($discussions->num_rows > 0): ?>
                <?php while ($discussion = $discussions->fetch_assoc()):
                    $comments_count = $conn->query("SELECT COUNT(*) as count FROM discussion_comments WHERE discussion_id = {$discussion['id']}")->fetch_assoc()['count'];
                ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5><?php echo $discussion['title']; ?></h5>
                            <p class="text-muted mb-2">
                                <small><?php echo $discussion['class_name']; ?> -
                                    Dibuat oleh: <?php echo $discussion['full_name']; ?> -
                                    <?php echo date('d M Y H:i', strtotime($discussion['created_at'])); ?>
                                </small>
                            </p>
                            <p><?php echo substr($discussion['content'], 0, 150); ?></p>
                            <a href="view_discussion.php?id=<?php echo $discussion['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-arrow-right"></i> Lihat (<?php echo $comments_count; ?> komentar)
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted text-center">Belum ada diskusi</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>