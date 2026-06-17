<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Thin wrapper around the session-based authentication state.
 */
final class Auth
{
    private const SESSION_USER_ID   = '_auth_user_id';
    private const SESSION_USER_ROLE = '_auth_user_role';

    /**
     * Persist the authenticated user into the session.
     *
     * @param  int     $userId    Authenticated user's primary key.
     * @param  string  $userRole  Role string: 'admin' or 'customer'.
     *
     * @return void
     */
    public static function login(int $userId, string $userRole): void
    {
        session_regenerate_id(true);
        $_SESSION[self::SESSION_USER_ID]   = $userId;
        $_SESSION[self::SESSION_USER_ROLE] = $userRole;
    }

    /**
     * Destroy the authenticated session.
     *
     * @return void
     */
    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_USER_ID], $_SESSION[self::SESSION_USER_ROLE]);
        session_regenerate_id(true);
    }

    /**
     * Check whether a user is currently authenticated.
     *
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION[self::SESSION_USER_ID]);
    }

    /**
     * Return the authenticated user's ID.
     *
     * @return int|null  User ID, or null if not authenticated.
     */
    public static function userId(): ?int
    {
        return isset($_SESSION[self::SESSION_USER_ID])
            ? (int) $_SESSION[self::SESSION_USER_ID]
            : null;
    }

    /**
     * Return the authenticated user's role.
     *
     * @return string|null  Role string, or null if not authenticated.
     */
    public static function userRole(): ?string
    {
        return $_SESSION[self::SESSION_USER_ROLE] ?? null;
    }

    /**
     * Check whether the authenticated user has the admin role.
     *
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return self::userRole() === 'admin';
    }
}
