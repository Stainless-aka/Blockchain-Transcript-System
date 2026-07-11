<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>Students</h5>
        <p class="text-muted small mb-0">Manage all registered students.</p>
    </div>
    <a href="<?= url('students/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Student
    </a>
</div>

<!-- Search + Filter -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body px-4 py-3">
        <form method="GET" action="<?= url('students') ?>" class="d-flex gap-2 align-items-center flex-wrap">
            <div class="input-group" style="max-width:380px;">
                <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control"
                       placeholder="Search by name, matric number, department..."
                       value="<?= e($search ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-outline-primary">Search</button>
            <?php if (!empty($search)): ?>
            <a href="<?= url('students') ?>" class="btn btn-outline-secondary">Clear</a>
            <?php endif; ?>
            <span class="ms-auto text-muted small">
                <?= number_format($pagination['total']) ?> student(s) found
            </span>
        </form>
    </div>
</div>

<!-- Students Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <?php if (empty($students)): ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-people fs-1 d-block mb-3 opacity-50"></i>
            <p class="mb-0">No students found.</p>
            <?php if (!empty($search)): ?>
            <a href="<?= url('students') ?>" class="btn btn-outline-primary btn-sm mt-3">View All Students</a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">#</th>
                        <th>Student ID</th>
                        <th>Matric Number</th>
                        <th>Full Name</th>
                        <th>Department</th>
                        <th>Level</th>
                        <th>Email</th>
                        <th class="text-center px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($students as $i => $student): ?>
                    <tr>
                        <td class="px-4 text-muted small">
                            <?= ($pagination['current_page'] - 1) * $pagination['per_page'] + $i + 1 ?>
                        </td>
                        <td class="font-monospace small"><?= e($student['student_id']) ?></td>
                        <td class="font-monospace small"><?= e($student['matric_number']) ?></td>
                        <td class="fw-semibold"><?= e($student['full_name']) ?></td>
                        <td><?= e($student['department']) ?></td>
                        <td><span class="badge bg-secondary"><?= e($student['level']) ?></span></td>
                        <td class="small"><?= e($student['email']) ?></td>
                        <td class="text-center px-4">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="<?= url('students/edit/' . $student['id']) ?>"
                                   class="btn btn-sm btn-outline-info" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <!-- Delete Button (triggers modal) -->
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-student-id="<?= $student['id'] ?>"
                                        data-student-name="<?= e($student['full_name']) ?>"
                                        title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['last_page'] > 1): ?>
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <small class="text-muted">
                Showing <?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?>–<?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?>
                of <?= number_format($pagination['total']) ?>
            </small>
            <?php
            $baseUrl = url('students') . (!empty($search) ? '?search=' . urlencode($search) . '&' : '?');
            $baseUrl = rtrim($baseUrl, '?&');
            echo paginationLinks($pagination, url('students') . (!empty($search) ? '?search=' . urlencode($search) : ''));
            ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel">
                    <i class="bi bi-trash me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteStudentName"></strong>?</p>
                <p class="text-muted small mb-0">This action cannot be undone. All transcripts linked to this student will also be affected.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="">
                    <?= csrfField() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Populate delete modal with student data
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (event) {
    const btn  = event.relatedTarget;
    const id   = btn.getAttribute('data-student-id');
    const name = btn.getAttribute('data-student-name');
    document.getElementById('deleteStudentName').textContent = name;
    document.getElementById('deleteForm').action = '<?= url('students/delete') ?>/' + id;
});
</script>
