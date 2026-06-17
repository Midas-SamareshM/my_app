<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * One-shot session flash messages (survive exactly one redirect).
 */
final class Flash
{
    private const SESSION_KEY = '_flash';

    /**
     * Store a flash message.
     *
     * @param  string  $type     Bootstrap alert type: success | danger | warning | info.
     * @param  string  $message  Human-readable message text.
     *
     * @return void
     */
    public static function set(string $type, string $message): void
    {
        $_SESSION[self::SESSION_KEY] = ['type' => $type, 'message' => $message];
    }

    /**
     * Retrieve and clear the stored flash message.
     *
     * @return array{type: string, message: string}|null  Message data, or null if none stored.
     */
    public static function get(): ?array
    {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            return null;
        }

        $flashData = $_SESSION[self::SESSION_KEY];
        unset($_SESSION[self::SESSION_KEY]);

        return $flashData;
    }

    /**
     * Check whether a flash message is waiting to be displayed.
     *
     * @return bool
     */
    public static function has(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]);
    }
}
