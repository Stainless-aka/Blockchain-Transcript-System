<?php

/**
 * BlockchainService
 * Simulates a blockchain using SHA-256 hash chaining.
 * Each block references the hash of the previous block,
 * ensuring tamper-evidence across all transcript records.
 */
class BlockchainService {

    private Block $blockModel;
    private HashService $hashService;

    public function __construct() {
        $this->blockModel  = new Block();
        $this->hashService = new HashService();
    }

    /**
     * Create the genesis block (index 0) if no chain exists
     */
    public function createGenesisBlock(): array {
        $genesisData = [
            'block_index'     => 0,
            'previous_hash'   => str_repeat('0', 64),
            'timestamp'       => time(),
            'transcript_data' => json_encode(['message' => 'Genesis Block - Blockchain Initialized']),
            'nonce'           => 0,
        ];

        $genesisData['current_hash'] = $this->calculateHash(
            $genesisData['block_index'],
            $genesisData['previous_hash'],
            $genesisData['timestamp'],
            $genesisData['transcript_data'],
            $genesisData['nonce']
        );

        $this->blockModel->create($genesisData);

        return $genesisData;
    }

    /**
     * Add a new block to the blockchain with transcript data
     */
    public function addBlock(array $transcriptData): array {
        // Ensure the genesis block exists
        $latestBlock = $this->blockModel->getLatestBlock();

        if ($latestBlock === null) {
            $this->createGenesisBlock();
            $latestBlock = $this->blockModel->getLatestBlock();
        }

        $newIndex    = (int)$latestBlock['block_index'] + 1;
        $prevHash    = $latestBlock['current_hash'];
        $timestamp   = time();
        $dataJson    = json_encode($transcriptData);
        $nonce       = $this->mineBlock($newIndex, $prevHash, $timestamp, $dataJson);
        $currentHash = $this->calculateHash($newIndex, $prevHash, $timestamp, $dataJson, $nonce);

        $blockData = [
            'block_index'     => $newIndex,
            'previous_hash'   => $prevHash,
            'current_hash'    => $currentHash,
            'nonce'           => $nonce,
            'transcript_data' => $dataJson,
            'timestamp'       => $timestamp,
        ];

        $this->blockModel->create($blockData);

        return $blockData;
    }

    /**
     * Calculate SHA-256 hash for a block
     */
    public function calculateHash(int $index, string $previousHash, int $timestamp, string $data, int $nonce): string {
        return hash('sha256', $index . $previousHash . $timestamp . $data . $nonce);
    }

    /**
     * Simple proof-of-work: find nonce so hash starts with "00"
     * Lightweight enough for a simulation environment
     */
    private function mineBlock(int $index, string $previousHash, int $timestamp, string $data): int {
        $nonce  = 0;
        $target = '00'; // 2-zero prefix (adjustable difficulty)

        while (true) {
            $hash = $this->calculateHash($index, $previousHash, $timestamp, $data, $nonce);
            if (substr($hash, 0, 2) === $target) {
                break;
            }
            $nonce++;
            // Safety cap to prevent infinite loop in edge cases
            if ($nonce > 1000000) {
                break;
            }
        }

        return $nonce;
    }

    /**
     * Validate the entire blockchain integrity
     * Returns true if the chain is intact, false if tampered
     */
    public function validateChain(): array {
        $blocks = $this->blockModel->getAllBlocks();

        if (empty($blocks)) {
            return ['valid' => true, 'message' => 'Blockchain is empty.', 'tampered_blocks' => []];
        }

        $tamperedBlocks = [];

        for ($i = 1; $i < count($blocks); $i++) {
            $current  = $blocks[$i];
            $previous = $blocks[$i - 1];

            // Recalculate the current block's hash
            $expectedHash = $this->calculateHash(
                (int)$current['block_index'],
                $current['previous_hash'],
                (int)$current['timestamp'],
                $current['transcript_data'],
                (int)$current['nonce']
            );

            // Check current block's hash integrity
            if ($current['current_hash'] !== $expectedHash) {
                $tamperedBlocks[] = [
                    'index'   => $current['block_index'],
                    'reason'  => 'Hash mismatch – block data was modified.',
                ];
                continue;
            }

            // Check chain linkage (previous hash must match previous block's hash)
            if ($current['previous_hash'] !== $previous['current_hash']) {
                $tamperedBlocks[] = [
                    'index'  => $current['block_index'],
                    'reason' => 'Previous hash mismatch – chain broken.',
                ];
            }
        }

        return [
            'valid'           => empty($tamperedBlocks),
            'message'         => empty($tamperedBlocks)
                ? 'Blockchain is valid. All blocks are intact.'
                : 'Blockchain integrity compromised. Tampered blocks detected.',
            'tampered_blocks' => $tamperedBlocks,
            'total_blocks'    => count($blocks),
        ];
    }

    /**
     * Verify whether a transcript's hash is present and unmodified in the blockchain
     */
    public function verifyTranscript(array $transcriptData, string $storedHash): array {
        // Step 1: Recalculate hash from transcript fields
        $computedHash = $this->hashService->hashTranscript($transcriptData);
        $hashMatch    = hash_equals($storedHash, $computedHash);

        if (!$hashMatch) {
            return [
                'verified'       => false,
                'blockchain_valid' => false,
                'message'        => 'TAMPERED: Transcript data does not match its stored hash.',
                'computed_hash'  => $computedHash,
                'stored_hash'    => $storedHash,
            ];
        }

        // Step 2: Confirm the hash exists inside a blockchain block
        $block = $this->blockModel->getBlockByTranscriptId($transcriptData['transcript_id'] ?? '');

        if ($block === null) {
            return [
                'verified'        => false,
                'blockchain_valid' => false,
                'message'         => 'WARNING: Transcript hash not found in blockchain.',
                'computed_hash'   => $computedHash,
                'stored_hash'     => $storedHash,
            ];
        }

        // Step 3: Validate the chain integrity
        $chainValidation = $this->validateChain();

        return [
            'verified'          => true,
            'blockchain_valid'  => $chainValidation['valid'],
            'message'           => $chainValidation['valid']
                ? 'VERIFIED: Transcript is authentic and blockchain is intact.'
                : 'WARNING: Transcript found but blockchain chain integrity is compromised.',
            'block_index'       => $block['block_index'],
            'block_hash'        => $block['current_hash'],
            'computed_hash'     => $computedHash,
            'stored_hash'       => $storedHash,
            'chain_status'      => $chainValidation,
        ];
    }

    /**
     * Get blockchain statistics
     */
    public function getStats(): array {
        $blocks      = $this->blockModel->getAllBlocks();
        $validation  = $this->validateChain();
        $latestBlock = $this->blockModel->getLatestBlock();

        return [
            'total_blocks'    => count($blocks),
            'is_valid'        => $validation['valid'],
            'latest_block'    => $latestBlock,
            'validation'      => $validation,
        ];
    }
}
