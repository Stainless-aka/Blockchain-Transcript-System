<?php

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->db->row("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
    }

    public function findByUsername(string $username): ?array
    {
        return $this->db->row("SELECT * FROM users WHERE username = ? LIMIT 1", [$username]);
    }

    public function create(array $d): int
    {
        $hash = password_hash($d['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $this->db->run(
            "INSERT INTO users (username, email, password, full_name, role, created_at)
             VALUES (?,?,?,?,?,NOW())",
            [$d['username'], $d['email'], $hash, $d['full_name'], $d['role'] ?? 'admin']
        );
        return $this->db->lastId();
    }

    public function updateProfile(int $id, array $d): bool
    {
        $s = $this->db->run(
            "UPDATE users SET full_name=?, email=?, updated_at=NOW() WHERE id=?",
            [$d['full_name'], $d['email'], $id]
        );
        return $s->rowCount() > 0;
    }

    public function updatePassword(int $id, string $plain): bool
    {
        $s = $this->db->run(
            "UPDATE users SET password=?, updated_at=NOW() WHERE id=?",
            [password_hash($plain, PASSWORD_BCRYPT, ['cost'=>12]), $id]
        );
        return $s->rowCount() > 0;
    }

    public function touchLogin(int $id): void
    {
        $this->db->run("UPDATE users SET last_login=NOW() WHERE id=?", [$id]);
    }

    public function verify(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    /** Alias for verify() — used by AuthService and AuthController */
    public function verifyPassword(string $plain, string $hash): bool
    {
        return $this->verify($plain, $hash);
    }

    /** Alias for touchLogin() — used by AuthService */
    public function updateLastLogin(int $id): void
    {
        $this->touchLogin($id);
    }
}
