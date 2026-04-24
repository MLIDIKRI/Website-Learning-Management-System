<?php
require '../includes/config.php';
$page_title = 'Detail Kelas';

if (get_user_role() != 'teacher') {
    redirect('index.php');
}

$class_id = (int)$_GET['id'];
$teacher_id = $_SESSION['user_id'];

// Check if class belongs to teacher
$class_query = $conn->query("SELECT * FROM classes WHERE id = $class_id AND teacher_id = $teacher_id");
if ($class_query->num_rows == 0) {
    redirect('teacher/classes.php');
}

$class = $class_query->fetch_assoc();

// Get class members
$members_query = "SELECT u.* FROM users u 
                  JOIN class_members cm ON u.id = cm.student_id 
                  WHERE cm.class_id = $class_id ORDER BY u.full_name";
$members = $conn->query($members_query);

// Get recent announcements
$announcements_query = "SELECT * FROM announcements WHERE class_id = $class_id ORDER BY created_at DESC LIMIT 5";
$announcements = $conn->query($announcements_query);

// Get recent assignments
$assignments_query = "SELECT * FROM assignments WHERE class_id = $class_id ORDER BY created_at DESC LIMIT 5";
$assignments = $conn->query($assignments_query);

// Handle add student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $student_id = (int)$_POST['student_id'];

    $check = $conn->query("SELECT id FROM class_members WHERE class_id = $class_id AND student_id = $student_id");
    if ($check->num_rows > 0) {
        alert('Siswa sudah terdaftar di kelas ini!', 'warning');
    } else {
        $query = "INSERT INTO class_members (class_id, student_id) VALUES ($class_id, $student_id)";
        if ($conn->query($query)) {
            alert('Siswa berhasil ditambahkan!', 'success');
            redirect('teacher/class_detail.php?id=' . $class_id);
        }
    }
}

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
    <div class="mb-4">
        <a href="classes.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <h2 class="mb-2"><?php echo $class['name']; ?> (<?php echo $class['code']; ?>)</h2>
    <p class="text-muted mb-4"><?php echo $class['description']; ?></p>

    <div class="row">
        <!-- Student List -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-users"></i> Daftar Siswa</span>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php if ($members->num_rows > 0): ?>
                            <?php while ($member = $members->fetch_assoc()): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0"><?php echo $member['full_name']; ?></h6>
                                        <small class="text-muted">@<?php echo $member['username']; ?></small>
                                    </div>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="if(confirm('Hapus siswa ini?')) {
                                            fetch('?remove_student=' + <?php echo $member['id']; ?>).reload();
                                        }">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-3">Belum ada siswa terdaftar</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-lightning-bolt"></i> Aksi Cepat
                </div>
                <div class="card-body">
                    <a href="create_assignment.php?class_id=<?php echo $class_id; ?>" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-plus"></i> Buat Tugas Baru
                    </a>
                    <a href="post_announcement.php?class_id=<?php echo $class_id; ?>" class="btn btn-success w-100 mb-2">
                        <i class="fas fa-bullhorn"></i> Posting Pengumuman
                    </a>
                    <a href="upload_material.php?class_id=<?php echo $class_id; ?>" class="btn btn-info w-100 mb-2">
                        <i class="fas fa-file-upload"></i> Upload Materi
                    </a>
                    <a href="take_attendance.php?class_id=<?php echo $class_id; ?>" class="btn btn-warning w-100">
                        <i class="fas fa-clipboard-list"></i> Absen Siswa
                    </a>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Informasi Kelas
                </div>
                <div class="card-body">
                    <p><strong>Total Siswa:</strong> <?php echo $members->num_rows; ?></p>
                    <p><strong>Kode Kelas:</strong> <?php echo $class['code']; ?></p>
                    <p><strong>Status:</strong> <?php echo $class['status'] == 'active' ? 'Aktif' : 'Tidak Aktif'; ?></p>
                    <p><strong>Dibuat:</strong> <?php echo date('d M Y', strtotime($class['created_at'])); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements -->
    <div class="card mt-4">
        <div class="card-header">
            <i class="fas fa-bullhorn"></i> Pengumuman Terbaru
        </div>
        <div class="card-body">
            <?php if ($announcements->num_rows > 0): ?>
                <?php while ($announcement = $announcements->fetch_assoc()): ?>
                    <div class="mb-3 pb-3 border-bottom">
                        <h6><?php echo $announcement['title']; ?></h6>
                        <p class="mb-2"><?php echo substr($announcement['content'], 0, 150); ?></p>
                        <small class="text-muted"><?php echo date('d M Y H:i', strtotime($announcement['created_at'])); ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted text-center">Belum ada pengumuman</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title">Tambah Siswa ke Kelas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Pilih Siswa</label>
                        <select class="form-select" id="student_id" name="student_id" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php
                            $students = $conn->query("SELECT id, full_name, username FROM users WHERE role = 'student' ORDER BY full_name");
                            while ($student = $students->fetch_assoc()):
                            ?>
                                <option value="<?php echo $student['id']; ?>">
                                    <?php echo $student['full_name']; ?> (@<?php echo $student['username']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="add_student" class="btn btn-primary">Tambah Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>