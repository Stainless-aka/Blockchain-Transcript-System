<?php

/**
 * DashboardController
 * Displays the main dashboard with system statistics and recent activity
 */
class DashboardController extends Controller {

    /**
     * GET /dashboard
     */
    public function index(array $params = []): void {
        $this->requireAuth();

        $studentModel    = new Student();
        $transcriptModel = new Transcript();
        $blockModel      = new Block();
        $activityLog     = new ActivityLog();
        $blockchainSvc   = new BlockchainService();

        // Gather statistics
        $stats = [
            'total_students'     => $studentModel->count(),
            'total_transcripts'  => $transcriptModel->count(),
            'verified_transcripts' => $transcriptModel->countVerified(),
            'total_blocks'       => $blockModel->getTotalCount(),
        ];

        // Recent activities
        $recentActivities = $activityLog->getRecent(10);

        // Recent transcripts
        $recentTranscripts = $transcriptModel->getAllWithStudents(1, 5)['data'];

        // Blockchain validation status
        $chainStatus = $blockchainSvc->validateChain();

        $flash = $this->getFlash();

        $this->view('dashboard/index', [
            'title'             => 'Dashboard',
            'stats'             => $stats,
            'recentActivities'  => $recentActivities,
            'recentTranscripts' => $recentTranscripts,
            'chainStatus'       => $chainStatus,
            'flash'             => $flash,
        ]);
    }
}
