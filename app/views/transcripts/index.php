<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-text-fill me-2 text-info"></i>Transcripts</h5>
        <p class="text-muted small mb-0">Manage and view all academic transcripts.</p>
    </div>
    <a href="<?= url('transcripts/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Create Transcript
    </a>
</div>

<!-- Search -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body px-4 py-3">
        <form method="GET" action="<?= url('transcripts') ?>" class="d-flex gap-2 align-items-center flex-wrap">
            <div class="input-group" style="max-width:380px;">
                <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control"
                       placeholder="Search by transcript ID, student name, degree..."
                       value="<?= e($search ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-outline-primary">Search</button>
            <?php if (!empty($search)): ?>
            <a href="<?= url('transcripts') ?>" class="btn btn-outline-secondary">Clear</a>
            <?php endif; ?>
            <span class="ms-auto text-muted small"><?= number_format($pagination['total']) ?> transcript(s)</span>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <?php if (empty($transcripts)): ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-file-earmark fs-1 d-block mb-3 opacity-50"></i>
            <p class="mb-0">No transcripts found.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">#</th>
                        <th>Transcript ID</th>
                        <th>Student</th>
                        <th>Department</th>
                        <th>Degree</th>
                        <th>CGPA</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th class="text-center px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($transcripts as $i => $t): ?>
                    <tr>
                        <td class="px-4 text-muted small">
                            <?= ($pagination['current_page'] - 1) * $pagination['per_page'] + $i + 1 ?>
                        </td>
                        <td class="font-monospace small"><?= e($t['transcript_id']) ?></td>
                        <td class="fw-semibold"><?= e($t['full_name']) ?></td>
                        <td class="small"><?= e(truncate($t['department'], 25)) ?></td>
                        <td class="small"><?= e(truncate($t['degree'], 30)) ?></td>
                        <td>
                            <span class="badge bg-<?= (float)$t['cgpa'] >= 3.5 ? 'success' : ((float)$t['cgpa'] >= 2.0 ? 'warning' : 'danger') ?>">
                                <?= e(number_format((float)$t['cgpa'], 2)) ?>
                            </span>
                        </td>
                        <td><?= e($t['graduation_year']) ?></td>
                        <td>
                            <span class="badge bg-<?= statusBadge($t['status']) ?>">
                                <i class="bi <?= $t['status'] === 'verified' ? 'bi-shield-check' : 'bi-clock' ?> me-1"></i>
                                <?= e(ucfirst($t['status'])) ?>
                            </span>
                        </td>
                        <td class="text-center px-4">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="<?= url('transcripts/view/' . $t['id']) ?>"
                                   class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-transcript-id="<?= $t['id'] ?>"
                                        data-transcript-code="<?= e($t['transcript_id']) ?>"
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
            <?= paginationLinks($pagination, url('transcripts') . (!empty($search) ? '?search=' . urlencode($search) : '')) ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-trash me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Delete transcript <strong id="deleteTranscriptCode"></strong>?</p>
                <p class="text-muted small mb-0">This is irreversible. The blockchain record will remain but the transcript will be removed from the database.</p>
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
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    const btn  = e.relatedTarget;
    const id   = btn.getAttribute('data-transcript-id');
    const code = btn.getAttribute('data-transcript-code');
    document.getElementById('deleteTranscriptCode').textContent = code;
    document.getElementById('deleteForm').action = '<?= url('transcripts/delete') ?>/' + id;
});
</script>
