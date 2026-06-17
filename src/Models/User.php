<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Uploadable;

/**
 * User model — handles both admin and customer accounts.
 */
class User extends BaseModel
{
    use Uploadable;

    protected string $table = 'users';

    /**
     * Find a user by their email address.
     *
     * @param  string  $emailAddress  The email to search for.
     *
     * @return array<string, mixed>|null  User row, or null if not found.
     */
    public function findByEmail(string $emailAddress): ?array
    {
        return $this->queryOne(
            'SELECT * FROM users WHERE email_address = ? LIMIT 1',
            [$emailAddress]
        );
    }

    /**
     * Return all registered customers (excludes admins).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllCustomers(): array
    {
        return $this->query(
            "SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC"
        );
    }

    /**
     * Toggle a user's active status.
     *
     * @param  int  $userId  User's primary key.
     *
     * @return bool  True on success.
     */
    public function toggleActiveStatus(int $userId): bool
    {
        $statement = $this->db()->prepare(
            'UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?'
        );

        return $statement->execute([$userId]);
    }

    /**
     * Verify a plain-text password against the stored hash.
     *
     * @param  string  $plainTextPassword  Password submitted by the user.
     * @param  string  $storedHash         Bcrypt hash from the database.
     *
     * @return bool  True when the password matches.
     */
    public function verifyPassword(string $plainTextPassword, string $storedHash): bool
    {
        return password_verify($plainTextPassword, $storedHash);
    }

    /**
     * Hash a plain-text password for storage.
     *
     * @param  string  $plainTextPassword  Raw password.
     *
     * @return string  Bcrypt hash.
     */
    public function hashPassword(string $plainTextPassword): string
    {
        return password_hash($plainTextPassword, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
