<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-0"><i class="bi bi-link-45deg me-2 text-warning"></i>Blockchain Explorer</h5>
        <p class="text-muted small mb-0">View all blocks, hashes, and chain integrity status.</p>
    </div>
    <!-- Validate Chain Button -->
    <button id="validateBtn" class="btn btn-outline-warning">
        <i class="bi bi-shield-check me-1"></i> Validate Chain
    </button>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center py-3">
            <div class="fw-bold fs-3 text-warning"><?= number_format($stats['total_blocks']) ?></div>
            <div class="text-muted small">Total Blocks</div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center py-3">
            <div class="fw-bold fs-3 <?= $validation['valid'] ? 'text-success' : 'text-danger' ?>">
                <?= $validation['valid'] ? 'VALID' : 'INVALID' ?>
            </div>
            <div class="text-muted small">Chain Status</div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center py-3">
            <div class="fw-bold fs-3 text-danger"><?= count($validation['tampered_blocks'] ?? []) ?></div>
            <div class="text-muted small">Tampered Blocks</div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center py-3">
            <div class="fw-bold fs-3 text-info">
                <?= !empty($stats['latest_block']) ? '#' . e($stats['latest_block']['block_index']) : 'N/A' ?>
            </div>
            <div class="text-muted small">Latest Block</div>
        </div>
    </div>
</div>

<!-- Validation Alert -->
<div id="validationAlert" class="mb-4">
    <?php if ($validation['valid']): ?>
    <div class="alert alert-success d-flex align-items-center gap-2 rounded-4 border-0 shadow-sm mb-0">
        <i class="bi bi-shield-fill-check fs-4"></i>
        <div><strong>Blockchain is VALID.</strong> <?= e($validation['message']) ?></div>
    </div>
    <?php else: ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 rounded-4 border-0 shadow-sm mb-0">
        <i class="bi bi-shield-fill-exclamation fs-4"></i>
        <div>
            <strong>Blockchain INTEGRITY COMPROMISED!</strong> <?= e($validation['message']) ?>
            <?php if (!empty($validation['tampered_blocks'])): ?>
            <ul class="mb-0 mt-1">
                <?php foreach ($validation['tampered_blocks'] as $tb): ?>
                <li>Block #<?= e($tb['index']) ?> — <?= e($tb['reason']) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Blocks Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h6 class="fw-bold mb-0">All Blocks</h6>
    </div>
    <div class="card-body p-0">
        <?php if (empty($blocks)): ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-box fs-1 d-block mb-3 opacity-50"></i>
            <p>No blocks in the chain yet. Create a transcript to initialize the blockchain.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">Index</th>
                        <th>Timestamp</th>
                        <th>Previous Hash</th>
                        <th>Current Hash</th>
                        <th class="text-center">Nonce</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($blocks as $block): ?>
                <tr class="<?= $block['block_index'] == 0 ? 'table-secondary' : '' ?>">
                    <td class="px-4">
                        <span class="badge bg-warning text-dark">#<?= e($block['block_index']) ?></span>
                        <?php if ($block['block_index'] == 0): ?>
                        <span class="badge bg-secondary ms-1">Genesis</span>
                        <?php endif; ?>
                    </td>
                    <td><?= formatDate(date('Y-m-d H:i:s', (int)$block['timestamp'])) ?></td>
                    <td class="font-monospace" style="max-width:180px;">
                        <span class="text-truncate d-block" title="<?= e($block['previous_hash']) ?>">
                            <?= e(substr($block['previous_hash'], 0, 16)) ?>…
                        </span>
                    </td>
                    <td class="font-monospace" style="max-width:180px;">
                        <span class="text-truncate d-block" title="<?= e($block['current_hash']) ?>">
                            <?= e(substr($block['current_hash'], 0, 16)) ?>…
                        </span>
                    </td>
                    <td class="text-center"><?= e(number_format((int)$block['nonce'])) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pagination['last_page'] > 1): ?>
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <small class="text-muted"><?= number_format($pagination['total']) ?> blocks</small>
            <?= paginationLinks($pagination, url('blockchain')) ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Validate Chain Script -->
<script>
document.getElementById('validateBtn').addEventListener('click', async function () {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Validating...';

    try {
        const formData = new FormData();
        formData.append('csrf_token', '<?= csrfToken() ?>');

        const res  = await fetch('<?= url('blockchain/validate') ?>', { method: 'POST', body: formData });
        const data = await res.json();

        const alertDiv = document.getElementById('validationAlert');
        if (data.result.valid) {
            alertDiv.innerHTML = `<div class="alert alert-success d-flex align-items-center gap-2 rounded-4 border-0 shadow-sm mb-0">
                <i class="bi bi-shield-fill-check fs-4"></i>
                <div><strong>Blockchain is VALID.</strong> ${data.result.message}</div>
            </div>`;
        } else {
            alertDiv.innerHTML = `<div class="alert alert-danger d-flex align-items-center gap-2 rounded-4 border-0 shadow-sm mb-0">
                <i class="bi bi-shield-fill-exclamation fs-4"></i>
                <div><strong>INTEGRITY COMPROMISED!</strong> ${data.result.message}</div>
            </div>`;
        }
    } catch (err) {
        alert('Validation request failed. Please try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-shield-check me-1"></i> Validate Chain';
    }
});
</script>
