<?php

/**
 * ActivityLog Model
 * Tracks all user actions within the system
 */
class ActivityLog extends Model
{
    protected string $table = 'activity_logs';
    protected string $pk    = 'id';

    /**
     * Log an activity
     */
    public function log(array $data): int
    {
        $this->db->run(
            "INSERT INTO activity_logs (user_id, action, description, ip_address, created_at)
             VALUES (?, ?, ?, ?, NOW())",
            [
                $data['user_id']    ?? null,
                $data['action'],
                $data['description'],
                $data['ip_address'] ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'),
            ]
        );
        return $this->db->lastId();
    }

    /**
     * Get recent activities with user info
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->db->rows(
            "SELECT al.*, u.full_name, u.username
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get paginated activity logs
     */
    public function getPaginated(int $page = 1, int $perPage = 20): array
    {
        $offset   = ($page - 1) * $perPage;
        $total    = (int) ($this->db->row("SELECT COUNT(*) as total FROM activity_logs")['total'] ?? 0);
        $records  = $this->db->rows(
            "SELECT al.*, u.full_name, u.username
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC
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
