<?php
require '../includes/config.php';
$page_title = 'Kelola Tugas';

if (get_user_role() != 'teacher') {
    redirect('index.php');
}

$teacher_id = $_SESSION['user_id'];

// Handle Create Assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_assignment'])) {
    $class_id = (int)$_POST['class_id'];
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $instructions = sanitize($_POST['instructions']);
    $due_date = $conn->real_escape_string($_POST['due_date']);
    $max_score = (float)$_POST['max_score'];

    // Check if class belongs to teacher
    $check = $conn->query("SELECT id FROM classes WHERE id = $class_id AND teacher_id = $teacher_id");
    if ($check->num_rows == 0) {
        alert('Kelas tidak ditemukan!', 'danger');
    } else {
        $query = "INSERT INTO assignments (class_id, title, description, instructions, due_date, max_score, created_by) 
                  VALUES ($class_id, '$title', '$description', '$instructions', '$due_date', $max_score, $teacher_id)";
        if ($conn->query($query)) {
            alert('Tugas berhasil dibuat!', 'success');
            redirect('teacher/assignments.php');
        } else {
            alert('Gagal membuat tugas!', 'danger');
        }
    }
}

// Get teacher's assignments
$assignments_query = "SELECT a.*, c.name as class_name FROM assignments a 
                     JOIN classes c ON a.class_id = c.id 
                     WHERE c.teacher_id = $teacher_id ORDER BY a.created_at DESC";
$assignments = $conn->query($assignments_query);

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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tasks"></i> Kelola Tugas</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
            <i class="fas fa-plus"></i> Buat Tugas Baru
        </button>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Daftar Tugas
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Judul Tugas</th>
                            <th>Kelas</th>
                            <th>Tenggat Waktu</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($assignment = $assignments->fetch_assoc()):
                            $now = new DateTime();
                            $due = new DateTime($assignment['due_date']);
                            $status = $now > $due ? 'Selesai' : 'Aktif';
                            $status_badge = $now > $due ? 'danger' : 'success';
                        ?>
                            <tr>
                                <td><?php echo $assignment['title']; ?></td>
                                <td><?php echo $assignment['class_name']; ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($assignment['due_date'])); ?></td>
                                <td><span class="badge badge-<?php echo $status_badge; ?>"><?php echo $status; ?></span></td>
                                <td>
                                    <a href="view_submissions.php?assignment_id=<?php echo $assignment['id']; ?>"
                                        class="btn btn-sm btn-info">
                                        <i class="fas fa-file-alt"></i> Lihat Pengumpulan
                                    </a>
                                    <a href="edit_assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Assignment Modal -->
<div class="modal fade" id="createAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title">Buat Tugas Baru</h5>
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
                        <label for="title" class="form-label">Judul Tugas</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="instructions" class="form-label">Instruksi Pengerjaan</label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Tenggat Waktu</label>
                                <input type="datetime-local" class="form-control" id="due_date" name="due_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_score" class="form-label">Nilai Maksimal</label>
                                <input type="number" class="form-control" id="max_score" name="max_score" value="100" step="0.01">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="create_assignment" class="btn btn-primary">Buat Tugas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>