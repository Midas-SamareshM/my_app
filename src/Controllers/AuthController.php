<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Flash;
use App\Models\User;

/**
 * Handles login, registration, and logout.
 */
class AuthController extends BaseController
{
    private User $userModel;

    public function __construct(\App\Http\Request $request)
    {
        parent::__construct($request);
        $this->userModel = new User();
    }

    /**
     * Display the customer login form.
     *
     * @return void
     */
    public function showLogin(): void
    {
        if (Auth::isLoggedIn()) {
            $this->redirect(Auth::isAdmin() ? '/admin/dashboard' : '/shop');
        }

        $this->render('auth/login', ['pageTitle' => 'Customer Login'], false);
    }

    /**
     * Process customer login — rejects admin accounts.
     *
     * @return void
     */
    public function login(): void
    {
        $this->requireValidCsrf();

        $emailAddress      = trim((string) $this->request->post('email', ''));
        $plainTextPassword = (string) $this->request->post('password', '');

        if ($emailAddress === '' || $plainTextPassword === '') {
            Flash::set('danger', 'Email and password are required.');
            $this->redirect('/login');
        }

        $user = $this->userModel->findByEmail($emailAddress);

        if ($user === null || !$this->userModel->verifyPassword($plainTextPassword, $user['password_hash'])) {
            Flash::set('danger', 'Invalid email address or password.');
            $this->redirect('/login');
        }

        if ($user['role'] === 'admin') {
            Flash::set('warning', 'Admin accounts must use the Admin Login page.');
            $this->redirect('/admin/login');
        }

        if ((int) $user['is_active'] === 0) {
            Flash::set('danger', 'Your account has been deactivated. Contact support.');
            $this->redirect('/login');
        }

        Auth::login((int) $user['id'], 'customer');

        $this->redirectWithMessage('/shop', 'success', 'Welcome back, ' . $user['full_name'] . '!');
    }

    /**
     * Display the admin-only login form.
     *
     * @return void
     */
    public function showAdminLogin(): void
    {
        if (Auth::isLoggedIn()) {
            $this->redirect(Auth::isAdmin() ? '/admin/dashboard' : '/shop');
        }

        $this->render('admin/login', ['pageTitle' => 'Admin Login'], false);
    }

    /**
     * Process admin login — rejects non-admin accounts.
     *
     * @return void
     */
    public function adminLogin(): void
    {
        $this->requireValidCsrf();

        $emailAddress      = trim((string) $this->request->post('email', ''));
        $plainTextPassword = (string) $this->request->post('password', '');

        if ($emailAddress === '' || $plainTextPassword === '') {
            Flash::set('danger', 'Email and password are required.');
            $this->redirect('/admin/login');
        }

        $user = $this->userModel->findByEmail($emailAddress);

        if ($user === null || !$this->userModel->verifyPassword($plainTextPassword, $user['password_hash'])) {
            Flash::set('danger', 'Invalid email address or password.');
            $this->redirect('/admin/login');
        }

        if ($user['role'] !== 'admin') {
            Flash::set('danger', 'Access denied. This login is for administrators only.');
            $this->redirect('/admin/login');
        }

        Auth::login((int) $user['id'], 'admin');

        $this->redirectWithMessage('/admin/dashboard', 'success', 'Welcome back, ' . $user['full_name'] . '!');
    }

    /**
     * Display the registration form.
     *
     * @return void
     */
    public function showRegister(): void
    {
        if (Auth::isLoggedIn()) {
            $this->redirect('/shop');
        }

        $this->render('auth/register', ['pageTitle' => 'Create Account'], false);
    }

    /**
     * Process registration form submission.
     *
     * @return void
     */
    public function register(): void
    {
        $this->requireValidCsrf();

        $fullName        = trim((string) $this->request->post('full_name', ''));
        $emailAddress    = trim((string) $this->request->post('email', ''));
        $plainTextPassword = (string) $this->request->post('password', '');
        $confirmPassword = (string) $this->request->post('password_confirm', '');

        if ($fullName === '' || $emailAddress === '' || $plainTextPassword === '') {
            Flash::set('danger', 'All fields are required.');
            $this->redirect('/register');
        }

        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            Flash::set('danger', 'Please enter a valid email address.');
            $this->redirect('/register');
        }

        if (strlen($plainTextPassword) < 8) {
            Flash::set('danger', 'Password must be at least 8 characters.');
            $this->redirect('/register');
        }

        if ($plainTextPassword !== $confirmPassword) {
            Flash::set('danger', 'Passwords do not match.');
            $this->redirect('/register');
        }

        if ($this->userModel->findByEmail($emailAddress) !== null) {
            Flash::set('danger', 'An account with this email already exists.');
            $this->redirect('/register');
        }

        $newUserId = $this->userModel->create([
            'full_name'     => $fullName,
            'email_address' => $emailAddress,
            'password_hash' => $this->userModel->hashPassword($plainTextPassword),
            'role'          => 'customer',
        ]);

        Auth::login($newUserId, 'customer');

        $this->redirectWithMessage('/shop', 'success', 'Account created! Welcome to eCom Platform.');
    }

    /**
     * Destroy the session and redirect to the appropriate login page.
     *
     * @return void
     */
    public function logout(): void
    {
        $wasAdmin = Auth::isAdmin();
        Auth::logout();

        $this->redirectWithMessage(
            $wasAdmin ? '/admin/login' : '/login',
            'success',
            'You have been logged out.'
        );
    }
}
