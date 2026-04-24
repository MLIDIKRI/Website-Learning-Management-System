<?php
require '../includes/config.php';
$page_title = 'Pengumuman';

if (get_user_role() != 'teacher') {
    redirect('index.php');
}

$teacher_id = $_SESSION['user_id'];

// Handle Create Announcement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_announcement'])) {
    $class_id = (int)$_POST['class_id'];
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $priority = sanitize($_POST['priority']);

    $check = $conn->query("SELECT id FROM classes WHERE id = $class_id AND teacher_id = $teacher_id");
    if ($check->num_rows == 0) {
        alert('Kelas tidak ditemukan!', 'danger');
    } else {
        $query = "INSERT INTO announcements (class_id, title, content, created_by, priority) 
                  VALUES ($class_id, '$title', '$content', $teacher_id, '$priority')";
        if ($conn->query($query)) {
            alert('Pengumuman berhasil diposting!', 'success');
            redirect('teacher/announcements.php');
        }
    }
}

// Get teacher's announcements
$announcements_query = "SELECT a.*, c.name as class_name FROM announcements a 
                       JOIN classes c ON a.class_id = c.id 
                       WHERE c.teacher_id = $teacher_id ORDER BY a.created_at DESC";
$announcements = $conn->query($announcements_query);

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
            <li><a href="announcements.php" class="active"><i class="fas fa-bullhorn"></i> Pengumuman</a></li>
            <li><a href="discussions.php"><i class="fas fa-comments"></i> Diskusi</a></li>
            <li><a href="attendance.php"><i class="fas fa-list-check"></i> Kehadiran</a></li>
            <li><a href="grades.php"><i class="fas fa-chart-bar"></i> Nilai</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-bullhorn"></i> Pengumuman</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
            <i class="fas fa-plus"></i> Posting Pengumuman
        </button>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Daftar Pengumuman
        </div>
        <div class="card-body">
            <?php while ($ann = $announcements->fetch_assoc()): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5><?php echo $ann['title']; ?></h5>
                                <small class="text-muted"><?php echo $ann['class_name']; ?> -
                                    <?php echo date('d M Y H:i', strtotime($ann['created_at'])); ?>
                                </small>
                            </div>
                            <span class="badge badge-<?php
                                                        echo $ann['priority'] == 'high' ? 'danger' : ($ann['priority'] == 'normal' ? 'info' : 'secondary');
                                                        ?>">
                                <?php echo ucfirst($ann['priority']); ?>
                            </span>
                        </div>
                        <p><?php echo nl2br($ann['content']); ?></p>
                        <a href="edit_announcement.php?id=<?php echo $ann['id']; ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="?delete=<?php echo $ann['id']; ?>" class="btn btn-sm btn-danger"
                            onclick="return confirmDelete();">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- Create Announcement Modal -->
<div class="modal fade" id="createAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title">Posting Pengumuman</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="class_id" class="form-label">Kelas</label>
                        <select class="form-select" id="class_id" name="class_id" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php
                            $classes = $conn->query("SELECT id, name FROM classes WHERE teacher_id = $teacher_id");
                            while ($class = $classes->fetch_assoc()):
                            ?>
                                <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Pengumuman</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Isi Pengumuman</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="priority" class="form-label">Prioritas</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="normal">Normal</option>
                            <option value="high">Penting</option>
                            <option value="low">Rendah</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="post_announcement" class="btn btn-primary">Posting</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>