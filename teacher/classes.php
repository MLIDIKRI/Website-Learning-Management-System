<?php
require '../includes/config.php';
$page_title = 'Kelola Kelas';

if (get_user_role() != 'teacher') {
    redirect('index.php');
}

$teacher_id = $_SESSION['user_id'];

// Handle Create Class
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_class'])) {
    $code = strtoupper(sanitize($_POST['class_code']));
    $name = sanitize($_POST['class_name']);
    $description = sanitize($_POST['description']);

    $check = $conn->query("SELECT id FROM classes WHERE code = '$code'");
    if ($check->num_rows > 0) {
        alert('Kode kelas sudah ada!', 'danger');
    } else {
        $query = "INSERT INTO classes (code, name, description, teacher_id) 
                  VALUES ('$code', '$name', '$description', $teacher_id)";
        if ($conn->query($query)) {
            alert('Kelas berhasil dibuat!', 'success');
            redirect('teacher/classes.php');
        }
    }
}

// Get teacher's classes
$classes_query = "SELECT * FROM classes WHERE teacher_id = $teacher_id ORDER BY created_at DESC";
$classes = $conn->query($classes_query);

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-chalkboard-user"></i> Teacher Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="classes.php" class="active"><i class="fas fa-book"></i> Kelas</a></li>
            <li><a href="assignments.php"><i class="fas fa-tasks"></i> Tugas</a></li>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-book"></i> Kelas Saya</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createClassModal">
            <i class="fas fa-plus"></i> Buat Kelas Baru
        </button>
    </div>

    <div class="row">
        <?php while ($class = $classes->fetch_assoc()):
            $student_count = $conn->query("SELECT COUNT(*) as count FROM class_members WHERE class_id = {$class['id']}")->fetch_assoc()['count'];
        ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <strong><?php echo $class['code']; ?></strong> - <?php echo $class['name']; ?>
                    </div>
                    <div class="card-body">
                        <p><?php echo substr($class['description'], 0, 100); ?></p>
                        <div class="mb-3">
                            <span class="badge badge-info"><i class="fas fa-users"></i> <?php echo $student_count; ?> Siswa</span>
                            <span class="badge badge-success">
                                <?php echo $class['status'] == 'active' ? 'Aktif' : 'Tidak Aktif'; ?>
                            </span>
                        </div>
                        <a href="class_detail.php?id=<?php echo $class['id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-right"></i> Buka Kelas
                        </a>
                        <a href="edit_class.php?id=<?php echo $class['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Create Class Modal -->
<div class="modal fade" id="createClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title">Buat Kelas Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="class_code" class="form-label">Kode Kelas</label>
                        <input type="text" class="form-control" id="class_code" name="class_code"
                            placeholder="Contoh: MTH101" required>
                    </div>
                    <div class="mb-3">
                        <label for="class_name" class="form-label">Nama Kelas</label>
                        <input type="text" class="form-control" id="class_name" name="class_name"
                            placeholder="Contoh: Matematika Dasar" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="create_class" class="btn btn-primary">Buat Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>