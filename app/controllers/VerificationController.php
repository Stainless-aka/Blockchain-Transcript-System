<?php

/**
 * VerificationController
 * Public-facing transcript verification system
 */
class VerificationController extends Controller {

    private Transcript $transcriptModel;
    private VerificationLog $verificationLog;
    private BlockchainService $blockchainSvc;

    public function __construct() {
        $this->transcriptModel = new Transcript();
        $this->verificationLog = new VerificationLog();
        $this->blockchainSvc   = new BlockchainService();
    }

    /**
     * GET /verify - Show verification form (public page)
     */
    public function index(array $params = []): void {
        $flash = $this->getFlash();

        $this->view('verification/index', [
            'title'      => 'Verify Transcript',
            'result'     => null,
            'queryValue' => '',
            'flash'      => $flash,
            'csrfToken'  => $this->generateCsrfToken(),
        ], 'public');
    }

    /**
     * POST /verify - Process verification request
     */
    public function verify(array $params = []): void {
        if (!$this->validateCsrf()) {
            $this->setFlash('danger', 'Invalid security token. Please try again.');
            $this->redirect(BASE_URL . '/verify');
        }

        $queryValue = trim($_POST['query_value'] ?? '');
        $queryType  = $this->input('query_type'); // 'transcript_id' or 'verification_code'

        if (empty($queryValue)) {
            $this->setFlash('danger', 'Please enter a Transcript ID or Verification Code.');
            $this->redirect(BASE_URL . '/verify');
        }

        // Sanitize
        $queryValue = htmlspecialchars(strip_tags($queryValue), ENT_QUOTES, 'UTF-8');

        // Look up the transcript
        $transcript = null;
        if ($queryType === 'transcript_id') {
            $transcript = $this->transcriptModel->findByTranscriptId($queryValue);
        } else {
            $transcript = $this->transcriptModel->findByVerificationCode($queryValue);
        }

        if (!$transcript) {
            // Log failed attempt
            $this->verificationLog->log([
                'transcript_id' => null,
                'query_value'   => $queryValue,
                'query_type'    => $queryType,
                'status'        => 'not_found',
            ]);

            logActivity('VERIFY_TRANSCRIPT', "Verification attempt: {$queryValue} — Not found.");

            $this->view('verification/index', [
                'title'      => 'Verify Transcript',
                'result'     => ['found' => false],
                'queryValue' => $queryValue,
                'flash'      => null,
                'csrfToken'  => $this->generateCsrfToken(),
            ], 'public');
            return;
        }

        // Run blockchain verification
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

        $status = $verificationResult['verified'] ? 'verified' : 'tampered';

        // Log verification
        $this->verificationLog->log([
            'transcript_id' => $transcript['id'],
            'query_value'   => $queryValue,
            'query_type'    => $queryType,
            'status'        => $status,
        ]);

        logActivity('VERIFY_TRANSCRIPT', "Transcript {$transcript['transcript_id']} verified — status: {$status}.");

        $this->view('verification/index', [
            'title'      => 'Verify Transcript',
            'result'     => array_merge($verificationResult, ['found' => true, 'transcript' => $transcript]),
            'queryValue' => $queryValue,
            'flash'      => null,
            'csrfToken'  => $this->generateCsrfToken(),
        ], 'public');
    }
}
