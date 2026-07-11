<?php

/**
 * VerificationLog Model
 * Tracks all transcript verification attempts
 */
class VerificationLog extends Model
{
    protected string $table = 'verification_logs';
    protected string $pk    = 'id';

    /**
     * Log a verification attempt
     */
    public function log(array $data): int
    {
        $this->db->run(
            "INSERT INTO verification_logs (transcript_id, query_value, query_type, status, ip_address, verified_at)
             VALUES (?, ?, ?, ?, ?, NOW())",
            [
                $data['transcript_id'] ?? null,
                $data['query_value'],
                $data['query_type'],
                $data['status'],
                $data['ip_address'] ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'),
            ]
        );
        return $this->db->lastId();
    }

    /**
     * Get recent verification logs
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->db->rows(
            "SELECT vl.*, t.transcript_id as t_id, s.full_name
             FROM verification_logs vl
             LEFT JOIN transcripts t ON vl.transcript_id = t.id
             LEFT JOIN students s ON t.student_id = s.id
             ORDER BY vl.verified_at DESC
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get all verification logs paginated
     */
    public function getPaginated(int $page = 1, int $perPage = 20): array
    {
        $offset   = ($page - 1) * $perPage;
        $total    = (int) ($this->db->row("SELECT COUNT(*) as total FROM verification_logs")['total'] ?? 0);
        $records  = $this->db->rows(
            "SELECT vl.*, t.transcript_id as t_id, s.full_name
             FROM verification_logs vl
             LEFT JOIN transcripts t ON vl.transcript_id = t.id
             LEFT JOIN students s ON t.student_id = s.id
             ORDER BY vl.verified_at DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $lastPage = $total > 0 ? (int) ceil($total / $perPage) : 1;
        return [
            'data'         => $records,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => $lastPage,
        ];
    }
}
