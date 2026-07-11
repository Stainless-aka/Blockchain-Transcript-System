<?php

/**
 * TranscriptController
 * Manages transcript CRUD operations and blockchain anchoring
 */
class TranscriptController extends Controller {

    private Transcript $transcriptModel;
    private Student $studentModel;
    private BlockchainService $blockchainSvc;
    private HashService $hashService;

    public function __construct() {
        $this->transcriptModel = new Transcript();
        $this->studentModel    = new Student();
        $this->blockchainSvc   = new BlockchainService();
        $this->hashService     = new HashService();
    }

    /**
     * GET /transcripts - List all transcripts
     */
    public function index(array $params = []): void {
        $this->requireAuth();

        $page    = max(1, (int)($this->query('page') ?: 1));
        $perPage = 10;
        $search  = $this->query('search');
        $flash   = $this->getFlash();

        if (!empty($search)) {
            $pagination = $this->transcriptModel->search($search, $page, $perPage);
        } else {
            $pagination = $this->transcriptModel->getAllWithStudents($page, $perPage);
        }

        $this->view('transcripts/index', [
            'title'       => 'Transcripts',
            'transcripts' => $pagination['data'],
            'pagination'  => $pagination,
            'search'      => $search,
            'flash'       => $flash,
        ]);
    }

    /**
     * GET /transcripts/create - Show create form
     */
    public function create(array $params = []): void {
        $this->requireAuth();

        $students = $this->studentModel->allForDropdown();

        $this->view('transcripts/create', [
            'title'     => 'Create Transcript',
            'students'  => $students,
            'csrfToken' => $this->generateCsrfToken(),
            'flash'     => $this->getFlash(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    /**
     * POST /transcripts/store - Store new transcript + add to blockchain
     */
    public function store(array $params = []): void {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect(BASE_URL . '/transcripts/create');
        }

        $data = [
            'student_id'      => (int)($_POST['student_id'] ?? 0),
            'gpa'             => $this->input('gpa'),
            'cgpa'            => $this->input('cgpa'),
            'graduation_year' => $this->input('graduation_year'),
            'degree'          => $this->input('degree'),
            'status'          => 'pending',
        ];

        $errors = $this->validate([
            'gpa'             => $data['gpa'],
            'cgpa'            => $data['cgpa'],
            'graduation_year' => $data['graduation_year'],
            'degree'          => $data['degree'],
        ], [
            'gpa'             => 'required|numeric',
            'cgpa'            => 'required|numeric',
            'graduation_year' => 'required|numeric',
            'degree'          => 'required|min:2|max:150',
        ]);

        if (empty($data['student_id'])) {
            $errors['student_id'][] = 'Student is required.';
        }

        // GPA range validation
        if (!empty($data['gpa']) && ((float)$data['gpa'] < 0 || (float)$data['gpa'] > 5)) {
            $errors['gpa'][] = 'GPA must be between 0.00 and 5.00.';
        }
        if (!empty($data['cgpa']) && ((float)$data['cgpa'] < 0 || (float)$data['cgpa'] > 5)) {
            $errors['cgpa'][] = 'CGPA must be between 0.00 and 5.00.';
        }

        $students = $this->studentModel->allForDropdown();

        if (!empty($errors)) {
            $this->view('transcripts/create', [
                'title'     => 'Create Transcript',
                'students'  => $students,
                'csrfToken' => $this->generateCsrfToken(),
                'flash'     => null,
                'errors'    => $errors,
                'old'       => $data,
            ]);
            return;
        }

        // Generate unique IDs
        $data['transcript_id']     = $this->hashService->generateTranscriptId('TRX');
        $data['verification_code'] = $this->hashService->generateVerificationCode();

        // Generate SHA-256 hash
        $data['hash']    = $this->hashService->hashTranscript($data);
        $data['pdf_path'] = null;

        // Handle PDF upload if provided
        if (!empty($_FILES['pdf_file']['name'])) {
            $uploadResult = $this->handlePdfUpload($_FILES['pdf_file']);
            if ($uploadResult['success']) {
                $data['pdf_path'] = $uploadResult['path'];
            }
        }

        // Begin database transaction
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            // Save transcript to DB
            $transcriptDbId = $this->transcriptModel->create($data);

            // Add block to blockchain
            $blockData = [
                'transcript_id'   => $data['transcript_id'],
                'student_id'      => $data['student_id'],
                'hash'            => $data['hash'],
                'gpa'             => $data['gpa'],
                'cgpa'            => $data['cgpa'],
                'degree'          => $data['degree'],
                'graduation_year' => $data['graduation_year'],
                'timestamp'       => time(),
            ];

            $this->blockchainSvc->addBlock($blockData);

            // Mark transcript as verified (blockchain anchored)
            $this->transcriptModel->updateStatus($transcriptDbId, 'verified');

            $db->commit();

            logActivity('CREATE_TRANSCRIPT', "Created transcript {$data['transcript_id']} and anchored to blockchain.");
            $this->setFlash('success', "Transcript created and anchored to blockchain. ID: {$data['transcript_id']}");
            $this->redirect(BASE_URL . '/transcripts');

        } catch (Exception $e) {
            $db->rollback();
            error_log('Transcript creation failed: ' . $e->getMessage());
            $this->setFlash('danger', 'Failed to create transcript. Please try again.');
            $this->redirect(BASE_URL . '/transcripts/create');
        }
    }

    /**
     * GET /transcripts/view/{id} - View a single transcript
     */
    public function detail(array $params = []): void {
        $this->requireAuth();

        $transcript = $this->transcriptModel->findWithStudent((int)($params['id'] ?? 0));

        if (!$transcript) {
            $this->setFlash('danger', 'Transcript not found.');
            $this->redirect(BASE_URL . '/transcripts');
        }

        // Verify the transcript against blockchain
        $verificationResult = $this->blockchainSvc->verifyTranscript(
            [
                'transcript_id'   => $transcript['transcript_id'],
                'student_id'      => $transcript['student_id'],
                'gpa'             => $transcript['gpa'],
                'cgpa'            => $transcript['cgpa'],
                'graduation_year' => $transcript['graduation_year'],
                'degree'          => $transcript['degree'],
            ],
            $transcript['hash']
        );

        $this->view('transcripts/view', [
            'title'              => 'Transcript Details',
            'transcript'         => $transcript,
            'verificationResult' => $verificationResult,
            'flash'              => $this->getFlash(),
        ]);
    }

    /**
     * POST /transcripts/delete/{id} - Delete a transcript
     */
    public function delete(array $params = []): void {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect(BASE_URL . '/transcripts');
        }

        $id         = (int)($params['id'] ?? 0);
        $transcript = $this->transcriptModel->findById($id);

        if (!$transcript) {
            $this->setFlash('danger', 'Transcript not found.');
            $this->redirect(BASE_URL . '/transcripts');
        }

        $this->transcriptModel->delete($id);

        logActivity('DELETE_TRANSCRIPT', "Deleted transcript: {$transcript['transcript_id']}");
        $this->setFlash('success', 'Transcript deleted successfully.');
        $this->redirect(BASE_URL . '/transcripts');
    }

    /**
     * Handle PDF file upload
     */
    private function handlePdfUpload(array $file): array {
        $uploadDir  = BASE_PATH . '/public/uploads/transcripts/';
        $allowedExt = ['pdf'];
        $maxSize    = 5 * 1024 * 1024; // 5 MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload error.'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            return ['success' => false, 'message' => 'Only PDF files are allowed.'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File size exceeds 5 MB limit.'];
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = 'transcript_' . uniqid() . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return ['success' => true, 'path' => 'uploads/transcripts/' . $filename];
        }

        return ['success' => false, 'message' => 'Failed to move uploaded file.'];
    }
}
