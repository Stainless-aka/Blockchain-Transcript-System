<?php

/**
 * Block Model
 * Handles all database operations related to blockchain blocks
 */
class Block extends Model
{
    protected string $table = 'blocks';
    protected string $pk    = 'id';

    /**
     * Create a new block
     */
    public function create(array $data): int
    {
        $this->db->run(
            "INSERT INTO blocks (block_index, previous_hash, current_hash, nonce, transcript_data, timestamp, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())",
            [
                $data['block_index'],
                $data['previous_hash'],
                $data['current_hash'],
                $data['nonce'],
                $data['transcript_data'],
                $data['timestamp'],
            ]
        );
        return $this->db->lastId();
    }

    /**
     * Get the latest block
     */
    public function getLatestBlock(): ?array
    {
        return $this->db->row("SELECT * FROM blocks ORDER BY block_index DESC LIMIT 1");
    }

    /**
     * Get all blocks ordered by index
     */
    public function getAllBlocks(): array
    {
        return $this->db->rows("SELECT * FROM blocks ORDER BY block_index ASC");
    }

    /**
     * Get block by index
     */
    public function getByIndex(int $index): ?array
    {
        return $this->db->row("SELECT * FROM blocks WHERE block_index = ?", [$index]);
    }

    /**
     * Get block containing specific transcript ID
     */
    public function getBlockByTranscriptId(string $transcriptId): ?array
    {
        return $this->db->row(
            "SELECT * FROM blocks WHERE transcript_data LIKE ?",
            ['%"transcript_id":"' . $transcriptId . '"%']
        );
    }

    /**
     * Get paginated blocks — used by BlockchainController
     */
    public function getPaginated(int $page = 1, int $perPage = 10): array
    {
        return $this->paginate($page, $perPage, '', [], 'block_index DESC');
    }

    /**
     * Get total count of blocks — used by DashboardController
     */
    public function getTotalCount(): int
    {
        return $this->count();
    }
}
