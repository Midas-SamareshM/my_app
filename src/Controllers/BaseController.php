<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Flash;
use App\Http\Request;

/**
 * Abstract base controller with shared rendering, redirect, and auth guards.
 * All controllers extend this class.
 */
abstract class BaseController
{
    public function __construct(protected readonly Request $request) {}

    /**
     * Render a view file, optionally wrapping it in the main layout.
     *
     * @param  string               $view    View path relative to views/ (e.g. 'shop/index').
     * @param  array<string, mixed> $data    Variables made available inside the view.
     * @param  bool                 $layout  Whether to wrap the view in the header/footer layout.
     *
     * @return void
     */
    protected function render(string $view, array $data = [], bool $layout = true): void
    {
        $data['currentUserId']   = Auth::userId();
        $data['currentUserRole'] = Auth::userRole();
        $data['isLoggedIn']      = Auth::isLoggedIn();
        $data['isAdmin']         = Auth::isAdmin();
        $data['flashMessage']    = Flash::get();

        if ($layout) {
            render('layouts/header', $data);
            render($view, $data);
            render('layouts/footer', $data);
        } else {
            render($view, $data);
        }
    }

    /**
     * Redirect to a path relative to BASE_URL and terminate execution.
     *
     * @param  string  $path  Relative path (e.g. '/login').
     *
     * @return never
     */
    protected function redirect(string $path): never
    {
        redirect($path);
    }

    /**
     * Redirect with a one-shot flash message.
     *
     * @param  string  $path     Redirect path.
     * @param  string  $type     Flash type: success | danger | warning | info.
     * @param  string  $message  Flash message text.
     *
     * @return never
     */
    protected function redirectWithMessage(string $path, string $type, string $message): never
    {
        Flash::set($type, $message);
        $this->redirect($path);
    }

    /**
     * Abort with an HTTP 403 if the current user is not authenticated.
     *
     * @return void
     */
    protected function requireAuth(): void
    {
        if (!Auth::isLoggedIn()) {
            Flash::set('warning', 'Please log in to continue.');
            redirect('/login');
        }
    }

    /**
     * Abort with an HTTP 403 if the current user is not an admin.
     *
     * @return void
     */
    protected function requireAdmin(): void
    {
        if (!Auth::isLoggedIn() || !Auth::isAdmin()) {
            http_response_code(403);
            Flash::set('danger', 'Access denied.');
            redirect('/');
        }
    }

    /**
     * Abort with a 403 if the submitted CSRF token is invalid.
     *
     * @return void
     */
    protected function requireValidCsrf(): void
    {
        $submittedToken = (string) $this->request->post('csrf_token', '');

        if (!verifyCsrf($submittedToken)) {
            http_response_code(403);
            die('Invalid CSRF token.');
        }
    }

    /**
     * Send a JSON response and terminate execution.
     *
     * @param  mixed  $data        Data to encode.
     * @param  int    $statusCode  HTTP status code.
     *
     * @return never
     */
    protected function json(mixed $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
