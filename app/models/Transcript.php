<?php

class Transcript extends Model
{
    protected string $table = 'transcripts';

    public function create(array $d): int
    {
        $this->db->run(
            "INSERT INTO transcripts (transcript_id,student_id,gpa,cgpa,graduation_year,degree,pdf_path,status,hash,verification_code,created_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,NOW())",
            [
                $d['transcript_id'], $d['student_id'], $d['gpa'], $d['cgpa'],
                $d['graduation_year'], $d['degree'], $d['pdf_path'] ?? null,
                $d['status'] ?? 'pending', $d['hash'], $d['verification_code'],
            ]
        );
        return $this->db->lastId();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $s = $this->db->run(
            "UPDATE transcripts SET status=?,updated_at=NOW() WHERE id=?", [$status, $id]
        );
        return $s->rowCount() > 0;
    }

    /** Get transcript + student data by DB id — used by TranscriptController::view() */
    public function findWithStudent(int $id): ?array
    {
        return $this->withStudent($id);
    }

    public function withStudent(int $id): ?array
    {
        return $this->db->row(
            "SELECT t.*,s.full_name,s.matric_number,s.student_id AS student_code,s.department,s.faculty,s.email,s.level
             FROM transcripts t JOIN students s ON t.student_id=s.id WHERE t.id=?", [$id]
        );
    }

    /** Find by transcript_id string — used by VerificationController */
    public function findByTranscriptId(string $tid): ?array
    {
        return $this->byTranscriptId($tid);
    }

    public function byTranscriptId(string $tid): ?array
    {
        return $this->db->row(
            "SELECT t.*,s.full_name,s.matric_number,s.student_id AS student_code,s.department,s.faculty,s.email,s.level
             FROM transcripts t JOIN students s ON t.student_id=s.id WHERE t.transcript_id=?", [$tid]
        );
    }

    /** Find by verification code — used by VerificationController */
    public function findByVerificationCode(string $code): ?array
    {
        return $this->byVerificationCode($code);
    }

    public function byVerificationCode(string $code): ?array
    {
        return $this->db->row(
            "SELECT t.*,s.full_name,s.matric_number,s.student_id AS student_code,s.department,s.faculty,s.email,s.level
             FROM transcripts t JOIN students s ON t.student_id=s.id WHERE t.verification_code=?", [$code]
        );
    }

    /** Paginated list with student JOIN — used by TranscriptController::index() */
    public function getAllWithStudents(int $page = 1, int $pp = 10): array
    {
        return $this->listWithStudents($page, $pp);
    }

    public function listWithStudents(int $page = 1, int $pp = 10): array
    {
        $offset = ($page - 1) * $pp;
        $total  = (int) ($this->db->row("SELECT COUNT(*) c FROM transcripts")['c'] ?? 0);
        $data   = $this->db->rows(
            "SELECT t.*,s.full_name,s.matric_number,s.department
             FROM transcripts t JOIN students s ON t.student_id=s.id
             ORDER BY t.created_at DESC LIMIT {$pp} OFFSET {$offset}"
        );
        $lastPage = $total > 0 ? (int) ceil($total / $pp) : 1;
        return [
            'data'         => $data,
            'total'        => $total,
            'per_page'     => $pp,
            'current_page' => $page,
            'last_page'    => $lastPage,
        ];
    }

    /** Search with pagination — used by TranscriptController::index() */
    public function search(string $term, int $page = 1, int $pp = 10): array
    {
        $like  = '%' . $term . '%';
        $p     = [$like, $like, $like, $like];
        $where = "t.transcript_id LIKE ? OR s.full_name LIKE ? OR s.matric_number LIKE ? OR t.degree LIKE ?";
        $total = (int) ($this->db->row(
            "SELECT COUNT(*) c FROM transcripts t JOIN students s ON t.student_id=s.id WHERE {$where}", $p
        )['c'] ?? 0);
        $offset   = ($page - 1) * $pp;
        $data     = $this->db->rows(
            "SELECT t.*,s.full_name,s.matric_number,s.department
             FROM transcripts t JOIN students s ON t.student_id=s.id
             WHERE {$where} ORDER BY t.created_at DESC LIMIT {$pp} OFFSET {$offset}", $p
        );
        $lastPage = $total > 0 ? (int) ceil($total / $pp) : 1;
        return [
            'data'         => $data,
            'total'        => $total,
            'per_page'     => $pp,
            'current_page' => $page,
            'last_page'    => $lastPage,
        ];
    }

    public function countVerified(): int
    {
        return (int) ($this->db->row("SELECT COUNT(*) c FROM transcripts WHERE status='verified'")['c'] ?? 0);
    }

    public function tidExists(string $tid, int $exclude = 0): bool
    {
        return (bool) $this->db->row(
            "SELECT id FROM transcripts WHERE transcript_id=? AND id!=?", [$tid, $exclude]
        );
    }
}
