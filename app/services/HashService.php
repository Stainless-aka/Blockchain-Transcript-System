<?php

/**
 * HashService
 * Handles SHA-256 hashing of transcript data
 */
class HashService {

    /**
     * Generate a SHA-256 hash for a transcript.
     * Note: 'status' is intentionally excluded — it is mutable metadata
     * and must not affect the content hash.
     */
    public function hashTranscript(array $transcriptData): string {
        // Normalize the data to ensure consistent hashing
        $normalized = [
            'transcript_id'   => (string)($transcriptData['transcript_id']   ?? ''),
            'student_id'      => (string)($transcriptData['student_id']       ?? ''),
            'gpa'             => (string)($transcriptData['gpa']              ?? ''),
            'cgpa'            => (string)($transcriptData['cgpa']             ?? ''),
            'graduation_year' => (string)($transcriptData['graduation_year']  ?? ''),
            'degree'          => (string)($transcriptData['degree']           ?? ''),
        ];

        // Sort by key to ensure consistent ordering
        ksort($normalized);

        return hash('sha256', json_encode($normalized));
    }

    /**
     * Generate a SHA-256 hash from a raw string
     */
    public function hash(string $data): string {
        return hash('sha256', $data);
    }

    /**
     * Verify a hash matches the given transcript data
     */
    public function verifyTranscriptHash(array $transcriptData, string $expectedHash): bool {
        $computedHash = $this->hashTranscript($transcriptData);
        return hash_equals($expectedHash, $computedHash);
    }

    /**
     * Generate a unique verification code
     */
    public function generateVerificationCode(): string {
        return strtoupper(bin2hex(random_bytes(8)));
    }

    /**
     * Generate a unique transcript ID with a prefix
     */
    public function generateTranscriptId(string $prefix = 'TRX'): string {
        return $prefix . '-' . strtoupper(bin2hex(random_bytes(4))) . '-' . date('Y');
    }
}
