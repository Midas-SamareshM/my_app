<?php

declare(strict_types=1);

namespace App\Http;

/**
 * PSR-7 inspired immutable request wrapper.
 * Encapsulates all incoming HTTP data — never use superglobals directly.
 */
final class Request
{
    private readonly array $queryParams;
    private readonly array $bodyParams;
    private readonly array $uploadedFiles;
    private readonly array $serverParams;
    private readonly array $cookieParams;

    public function __construct()
    {
        $this->queryParams   = $_GET    ?? [];
        $this->bodyParams    = $_POST   ?? [];
        $this->uploadedFiles = $_FILES  ?? [];
        $this->serverParams  = $_SERVER ?? [];
        $this->cookieParams  = $_COOKIE ?? [];
    }

    /**
     * Return the HTTP method (GET, POST, PUT, DELETE …).
     *
     * @return string  Uppercase HTTP method.
     */
    public function getMethod(): string
    {
        return strtoupper($this->serverParams['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Return the current URL path without the base segment.
     *
     * @return string  Path starting with '/'.
     */
    public function getPath(): string
    {
        $requestUri = $this->serverParams['REQUEST_URI'] ?? '/';
        $path       = parse_url($requestUri, PHP_URL_PATH) ?? '/';

        $base = rtrim(BASE_URL, '/');
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        return '/' . ltrim($path, '/');
    }

    /**
     * Return all query-string parameters.
     *
     * @return array<string, mixed>
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * Return a single query-string value.
     *
     * @param  string  $key      Parameter name.
     * @param  mixed   $default  Value returned when the key is absent.
     *
     * @return mixed
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $default;
    }

    /**
     * Return all parsed body parameters (from a POST form).
     *
     * @return array<string, mixed>
     */
    public function getParsedBody(): array
    {
        return $this->bodyParams;
    }

    /**
     * Return a single POST body value.
     *
     * @param  string  $key      Field name.
     * @param  mixed   $default  Value returned when the key is absent.
     *
     * @return mixed
     */
    public function post(string $key, mixed $default = null): mixed
    {
        return $this->bodyParams[$key] ?? $default;
    }

    /**
     * Return all uploaded file descriptors.
     *
     * @return array<string, mixed>
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * Return the descriptor for a single uploaded file.
     *
     * @param  string  $key  HTML input name.
     *
     * @return array<string, mixed>|null  File descriptor, or null if not present.
     */
    public function file(string $key): ?array
    {
        return isset($this->uploadedFiles[$key]) ? $this->uploadedFiles[$key] : null;
    }

    /**
     * Return the value of a named request header.
     *
     * @param  string  $headerName  Header name (e.g. 'Content-Type').
     *
     * @return string  Header value, or empty string if absent.
     */
    public function getHeaderLine(string $headerName): string
    {
        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $headerName));
        return $this->serverParams[$serverKey] ?? '';
    }

    /**
     * Check whether this is a POST request.
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * Check whether this is an XMLHttpRequest.
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return $this->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
}
