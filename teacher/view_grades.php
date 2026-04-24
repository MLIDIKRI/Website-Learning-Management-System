<?php
require '../includes/config.php';
$page_title = 'Nilai Siswa per Kelas';

if (get_user_role() != 'teacher') {
    redirect('index.php');
}

$teacher_id = $_SESSION['user_id'];
$class_id = sanitize($_GET['class_id'] ?? 0);

// Verify class belongs to teacher
$class_check = $conn->query("SELECT * FROM classes WHERE id = $class_id AND teacher_id = $teacher_id");
if ($class_check->num_rows == 0) {
    redirect('grades.php');
}
$class = $class_check->fetch_assoc();

// Get grades for this class
$grades_query = "SELECT g.*, u.username, u.full_name FROM grades g 
                 JOIN users u ON g.student_id = u.id 
                 WHERE g.class_id = $class_id 
                 ORDER BY u.full_name";
$grades = $conn->query($grades_query);

// Handle grade input
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_grade'])) {
    $student_id = sanitize($_POST['student_id']);
    $total_score = sanitize($_POST['total_score']);
    $grade_letter = sanitize($_POST['grade_letter']);
    $final_status = sanitize($_POST['final_status']);

    $update_query = "UPDATE grades SET total_score = '$total_score', grade_letter = '$grade_letter', 
                    final_status = '$final_status' WHERE student_id = $student_id AND class_id = $class_id";
    
    if ($conn->query($update_query)) {
        alert('Nilai berhasil diperbarui!', 'success');
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
    <h2 class="mb-4"><i class="fas fa-chart-bar"></i> Nilai Siswa - <?php echo $class['name']; ?></h2>
    
    <a href="grades.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Daftar Nilai Siswa
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Nilai Total</th>
                            <th>Grade</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($grade = $grades->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $grade['full_name']; ?></td>
                                <td><?php echo $grade['total_score'] ?? '-'; ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo $grade['grade_letter'] ?? '-'; ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $status_color = $grade['final_status'] == 'pass' ? 'success' : 'danger';
                                    echo '<span class="badge bg-' . $status_color . '">' . ucfirst($grade['final_status'] ?? 'pending') . '</span>';
                                    ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                            data-bs-target="#gradeModal" onclick="editGrade(<?php echo $grade['student_id']; ?>, '<?php echo $grade['full_name']; ?>', <?php echo $grade['total_score'] ?? ''; ?>, '<?php echo $grade['grade_letter'] ?? ''; ?>', '<?php echo $grade['final_status'] ?? ''; ?>')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Grade Modal -->
<div class="modal fade" id="gradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Nilai Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="view_grades.php?class_id=<?php echo $class_id; ?>">
                <div class="modal-body">
                    <input type="hidden" name="student_id" id="student_id">
                    <div class="mb-3">
                        <label class="form-label">Nama Siswa</label>
                        <input type="text" class="form-control" id="student_name" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="total_score" class="form-label">Nilai Total (0-100)</label>
                        <input type="number" class="form-control" id="total_score" name="total_score" min="0" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="grade_letter" class="form-label">Grade</label>
                        <select class="form-control" id="grade_letter" name="grade_letter" required>
                            <option value="A">A (90-100)</option>
                            <option value="B">B (80-89)</option>
                            <option value="C">C (70-79)</option>
                            <option value="D">D (60-69)</option>
                            <option value="F">F (<60)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="final_status" class="form-label">Status</label>
                        <select class="form-control" id="final_status" name="final_status" required>
                            <option value="pass">Pass</option>
                            <option value="fail">Fail</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="update_grade">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editGrade(studentId, studentName, totalScore, gradeLetter, finalStatus) {
    document.getElementById('student_id').value = studentId;
    document.getElementById('student_name').value = studentName;
    document.getElementById('total_score').value = totalScore || '';
    document.getElementById('grade_letter').value = gradeLetter || 'A';
    document.getElementById('final_status').value = finalStatus || 'pass';
}
</script>

<?php require '../includes/footer.php'; ?>
