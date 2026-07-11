<?php

class Student extends Model
{
    protected string $table = 'students';

    public function create(array $d): int
    {
        $this->db->run(
            "INSERT INTO students (student_id,matric_number,full_name,department,faculty,level,email,created_at)
             VALUES (?,?,?,?,?,?,?,NOW())",
            [$d['student_id'],$d['matric_number'],$d['full_name'],$d['department'],$d['faculty'],$d['level'],$d['email']]
        );
        return $this->db->lastId();
    }

    public function update(int $id, array $d): bool
    {
        $s = $this->db->run(
            "UPDATE students SET student_id=?,matric_number=?,full_name=?,department=?,faculty=?,level=?,email=?,updated_at=NOW() WHERE id=?",
            [$d['student_id'],$d['matric_number'],$d['full_name'],$d['department'],$d['faculty'],$d['level'],$d['email'],$id]
        );
        return $s->rowCount() > 0;
    }

    /** Paginated list — used by StudentController::index() */
    public function getPaginated(int $page = 1, int $pp = 10): array
    {
        return $this->listPaginated($page, $pp);
    }

    public function listPaginated(int $page = 1, int $pp = 10): array
    {
        return $this->paginate($page, $pp, '', [], 'created_at DESC');
    }

    /** Full-text search with pagination */
    public function search(string $term, int $page = 1, int $pp = 10): array
    {
        $like  = '%' . $term . '%';
        $where = "full_name LIKE ? OR matric_number LIKE ? OR student_id LIKE ? OR department LIKE ?";
        $p     = [$like, $like, $like, $like];
        return $this->paginate($page, $pp, $where, $p, 'created_at DESC');
    }

    /** Check matric number uniqueness (exclude a given ID for updates) */
    public function matricNumberExists(string $m, int $exclude = 0): bool
    {
        return $this->matricExists($m, $exclude);
    }

    public function matricExists(string $m, int $exclude = 0): bool
    {
        return (bool) $this->db->row(
            "SELECT id FROM students WHERE matric_number=? AND id!=?", [$m, $exclude]
        );
    }

    /** Check student_id uniqueness (exclude a given ID for updates) */
    public function studentIdExists(string $s, int $exclude = 0): bool
    {
        return $this->sidExists($s, $exclude);
    }

    public function sidExists(string $s, int $exclude = 0): bool
    {
        return (bool) $this->db->row(
            "SELECT id FROM students WHERE student_id=? AND id!=?", [$s, $exclude]
        );
    }

    /** Return all students for a <select> dropdown */
    public function allForDropdown(): array
    {
        return $this->dropdown();
    }

    public function dropdown(): array
    {
        return $this->db->rows(
            "SELECT id,full_name,matric_number,student_id FROM students ORDER BY full_name ASC"
        );
    }
}
