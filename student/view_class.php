<?php
require '../includes/config.php';
$page_title = 'Detail Kelas';

if (get_user_role() != 'student') {
    redirect('index.php');
}

$student_id = $_SESSION['user_id'];
$class_id = (int)$_GET['id'];

// Check if student is in this class
$check = $conn->query("SELECT c.* FROM classes c 
                      JOIN class_members cm ON c.id = cm.class_id 
                      WHERE c.id = $class_id AND cm.student_id = $student_id");
if ($check->num_rows == 0) {
    redirect('student/classes.php');
}

$class = $check->fetch_assoc();
$teacher = $conn->query("SELECT full_name FROM users WHERE id = {$class['teacher_id']}")->fetch_assoc();

// Get announcements
$announcements_query = "SELECT * FROM announcements WHERE class_id = $class_id ORDER BY created_at DESC LIMIT 5";
$announcements = $conn->query($announcements_query);

// Get assignments
$assignments_query = "SELECT * FROM assignments WHERE class_id = $class_id ORDER BY due_date ASC";
$assignments = $conn->query($assignments_query);

// Get materials
$materials_query = "SELECT * FROM materials WHERE class_id = $class_id ORDER BY created_at DESC LIMIT 5";
$materials = $conn->query($materials_query);

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
    <div class="mb-4">
        <a href="classes.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <h2 class="mb-2"><?php echo $class['name']; ?> (<?php echo $class['code']; ?>)</h2>
    <p class="text-muted mb-4">Guru: <?php echo $teacher['full_name']; ?></p>

    <!-- Announcements -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-bullhorn"></i> Pengumuman Terbaru
        </div>
        <div class="card-body">
            <?php if ($announcements->num_rows > 0): ?>
                <?php while ($ann = $announcements->fetch_assoc()): ?>
                    <div class="mb-3 pb-3 border-bottom">
                        <h6><?php echo $ann['title']; ?></h6>
                        <p class="mb-2"><?php echo substr($ann['content'], 0, 150); ?></p>
                        <small class="text-muted"><?php echo date('d M Y H:i', strtotime($ann['created_at'])); ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted text-center">Belum ada pengumuman</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Assignments -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tasks"></i> Tugas
                </div>
                <div class="card-body">
                    <?php if ($assignments->num_rows > 0): ?>
                        <?php while ($assign = $assignments->fetch_assoc()):
                            $submitted = $conn->query("SELECT id FROM assignment_submissions WHERE assignment_id = {$assign['id']} AND student_id = $student_id")->num_rows;
                            $now = new DateTime();
                            $due = new DateTime($assign['due_date']);
                            $is_late = $now > $due;
                        ?>
                            <div class="mb-3">
                                <h6><?php echo $assign['title']; ?></h6>
                                <small class="text-muted">
                                    Tenggat: <?php echo date('d M Y H:i', strtotime($assign['due_date'])); ?>
                                    <?php if ($is_late) echo ' <span class="badge badge-danger">Terlambat</span>'; ?>
                                    <?php if ($submitted) echo ' <span class="badge badge-success">Sudah Dikumpul</span>'; ?>
                                </small>
                                <div class="mt-2">
                                    <a href="view_assignment.php?id=<?php echo $assign['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </div>
                            </div>
                            <hr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">Belum ada tugas</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Materials -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-file"></i> Materi
                </div>
                <div class="card-body">
                    <?php if ($materials->num_rows > 0): ?>
                        <?php while ($material = $materials->fetch_assoc()): ?>
                            <div class="mb-3">
                                <h6><?php echo $material['title']; ?></h6>
                                <small class="text-muted"><?php echo date('d M Y', strtotime($material['created_at'])); ?></small>
                                <p><?php echo substr($material['description'], 0, 50); ?></p>
                                <?php if ($material['file_path']): ?>
                                    <a href="../<?php echo $material['file_path']; ?>" class="btn btn-sm btn-info" download>
                                        <i class="fas fa-download"></i> Unduh
                                    </a>
                                <?php endif; ?>
                            </div>
                            <hr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">Belum ada materi</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>