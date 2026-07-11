<?php

/**
 * Base Model — all models extend this.
 */
abstract class Model
{
    protected Database $db;
    protected string $table = '';
    protected string $pk = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Find by primary key */
    public function find(int $id): ?array
    {
        return $this->db->row("SELECT * FROM {$this->table} WHERE {$this->pk} = ?", [$id]);
    }

    /** Alias for find() — used throughout controllers */
    public function findById(int $id): ?array
    {
        return $this->find($id);
    }

    /** Get all rows */
    public function all(string $order = 'id DESC'): array
    {
        return $this->db->rows("SELECT * FROM {$this->table} ORDER BY {$order}");
    }

    /** Count rows */
    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) as c FROM {$this->table}";
        if ($where) $sql .= " WHERE {$where}";
        $r = $this->db->row($sql, $params);
        return (int) ($r['c'] ?? 0);
    }

    /** Delete by PK */
    public function delete(int $id): bool
    {
        $stmt = $this->db->run("DELETE FROM {$this->table} WHERE {$this->pk} = ?", [$id]);
        return $stmt->rowCount() > 0;
    }

    /** Paginate */
    public function paginate(int $page, int $perPage, string $where = '', array $params = [], string $order = 'id DESC'): array
    {
        $offset = ($page - 1) * $perPage;
        $total  = $this->count($where, $params);
        $sql    = "SELECT * FROM {$this->table}";
        if ($where) $sql .= " WHERE {$where}";
        $sql .= " ORDER BY {$order} LIMIT {$perPage} OFFSET {$offset}";
        $data = $this->db->rows($sql, $params);
        $lastPage = (int) ceil($total / $perPage);
        return [
            'data'         => $data,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => $lastPage,
            // Legacy aliases
            'perPage'      => $perPage,
            'page'         => $page,
            'lastPage'     => $lastPage,
        ];
    }
}
