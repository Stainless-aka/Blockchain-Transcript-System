<?php

/**
 * BlockchainController
 * Displays blockchain information and validation status
 */
class BlockchainController extends Controller {

    private BlockchainService $blockchainSvc;
    private Block $blockModel;

    public function __construct() {
        $this->blockchainSvc = new BlockchainService();
        $this->blockModel    = new Block();
    }

    /**
     * GET /blockchain - Display blockchain explorer
     */
    public function index(array $params = []): void {
        $this->requireAuth();

        $page       = max(1, (int)($this->query('page') ?: 1));
        $perPage    = 10;
        $pagination = $this->blockModel->getPaginated($page, $perPage);
        $flash      = $this->getFlash();

        // Blockchain validation
        $validation = $this->blockchainSvc->validateChain();
        $stats      = $this->blockchainSvc->getStats();

        $this->view('blockchain/index', [
            'title'      => 'Blockchain Explorer',
            'blocks'     => $pagination['data'],
            'pagination' => $pagination,
            'validation' => $validation,
            'stats'      => $stats,
            'flash'      => $flash,
        ]);
    }

    /**
     * POST /blockchain/validate - Trigger blockchain validation
     */
    public function runValidation(array $params = []): void {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token.'], 400);
        }

        $result = $this->blockchainSvc->validateChain();

        logActivity('BLOCKCHAIN_VALIDATION', 'Ran blockchain integrity validation.');

        $this->json([
            'success' => true,
            'result'  => $result,
        ]);
    }
}
