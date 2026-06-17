<?php

declare(strict_types=1);

/**
 * Global procedural helpers available throughout the application.
 */

/**
 * Render a view file with extracted variables.
 *
 * @param  string               $view  Dot-separated or slash-separated view path (e.g. 'auth/login').
 * @param  array<string, mixed> $data  Variables to extract into the view's scope.
 *
 * @return void
 *
 * @throws \RuntimeException  If the view file does not exist.
 */
function render(string $view, array $data = []): void
{
    $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';

    if (!file_exists($viewFile)) {
        throw new \RuntimeException("View not found: {$view}");
    }

    extract($data, EXTR_SKIP);
    require $viewFile;
}

/**
 * Issue an HTTP redirect and terminate execution.
 *
 * @param  string  $path  Path relative to BASE_URL (e.g. '/login').
 *
 * @return never
 */
function redirect(string $path): never
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

/**
 * Build an absolute URL relative to BASE_URL.
 *
 * @param  string  $path  Path starting with '/'.
 *
 * @return string  Full URL string.
 */
function url(string $path): string
{
    return BASE_URL . $path;
}

/**
 * Build the public URL for an uploaded file.
 *
 * @param  string|null  $filePath  Relative path stored in the database (e.g. 'products/image.jpg').
 * @param  string       $fallback  URL returned when $filePath is null or empty.
 *
 * @return string  Full upload URL.
 */
function uploadUrl(?string $filePath, string $fallback = ''): string
{
    if ($filePath === null || $filePath === '') {
        return $fallback;
    }

    return UPLOAD_URL . '/' . ltrim($filePath, '/');
}

/**
 * Escape a value for safe HTML output.
 *
 * @param  mixed  $value  Value to escape.
 *
 * @return string  HTML-safe string.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Generate or retrieve the CSRF token for the current session.
 *
 * @return string  32-byte hex CSRF token.
 */
function csrfToken(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf_token'];
}

/**
 * Render a hidden CSRF input field.
 *
 * @return string  HTML hidden input element.
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

/**
 * Verify a submitted CSRF token against the session value.
 *
 * @param  string  $submittedToken  Token from the form submission.
 *
 * @return bool  True when the token is valid.
 */
function verifyCsrf(string $submittedToken): bool
{
    return isset($_SESSION['_csrf_token'])
        && hash_equals($_SESSION['_csrf_token'], $submittedToken);
}

/**
 * Format a decimal price as a currency string.
 *
 * @param  float   $amount        Price amount.
 * @param  string  $currencyCode  ISO currency symbol prefix.
 *
 * @return string  Formatted price (e.g. '$ 29.99').
 */
function formatPrice(float $amount, string $currencyCode = '$'): string
{
    return $currencyCode . ' ' . number_format($amount, 2);
}

/**
 * Generate a URL-friendly slug from a string.
 *
 * @param  string  $text  Input text (e.g. product name).
 *
 * @return string  Lowercase, hyphen-separated slug.
 */
function slugify(string $text): string
{
    $slug = strtolower(trim($text));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);

    return trim($slug, '-');
}
