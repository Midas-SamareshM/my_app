<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\User;

/**
 * Authenticated user profile management.
 */
class ProfileController extends BaseController
{
    private User $userModel;

    public function __construct(\App\Http\Request $request)
    {
        parent::__construct($request);
        $this->userModel = new User();
    }

    /**
     * Display the profile edit form.
     *
     * @return void
     */
    public function index(): void
    {
        $this->requireAuth();

        $currentUser = $this->userModel->findById(Auth::userId());

        $this->render('profile/index', [
            'pageTitle'   => 'My Profile',
            'currentUser' => $currentUser,
        ]);
    }

    /**
     * Persist profile field updates (name, phone, address, avatar).
     *
     * @return void
     */
    public function update(): void
    {
        $this->requireAuth();
        $this->requireValidCsrf();

        $currentUserId = Auth::userId();
        $currentUser   = $this->userModel->findById($currentUserId);

        $updatedData = [
            'full_name'        => trim((string) $this->request->post('full_name', '')),
            'phone_number'     => trim((string) $this->request->post('phone_number', '')),
            'delivery_address' => trim((string) $this->request->post('delivery_address', '')),
        ];

        if ($updatedData['full_name'] === '') {
            $this->redirectWithMessage('/profile', 'danger', 'Full name is required.');
        }

        $avatarFile = $this->request->file('profile_image');

        if ($avatarFile !== null && $avatarFile['error'] === UPLOAD_ERR_OK) {
            try {
                $this->userModel->deleteUploadedFile($currentUser['profile_image']);
                $updatedData['profile_image'] = $this->userModel->uploadImage($avatarFile, 'avatars');
            } catch (\RuntimeException $uploadException) {
                $this->redirectWithMessage('/profile', 'danger', $uploadException->getMessage());
            }
        }

        $this->userModel->update($currentUserId, $updatedData);

        $this->redirectWithMessage('/profile', 'success', 'Profile updated successfully.');
    }

    /**
     * Update the account password.
     *
     * @return void
     */
    public function updatePassword(): void
    {
        $this->requireAuth();
        $this->requireValidCsrf();

        $currentUser       = $this->userModel->findById(Auth::userId());
        $currentPassword   = (string) $this->request->post('current_password', '');
        $newPassword       = (string) $this->request->post('new_password', '');
        $confirmNewPassword= (string) $this->request->post('confirm_password', '');

        if (!$this->userModel->verifyPassword($currentPassword, $currentUser['password_hash'])) {
            $this->redirectWithMessage('/profile', 'danger', 'Current password is incorrect.');
        }

        if (strlen($newPassword) < 8) {
            $this->redirectWithMessage('/profile', 'danger', 'New password must be at least 8 characters.');
        }

        if ($newPassword !== $confirmNewPassword) {
            $this->redirectWithMessage('/profile', 'danger', 'Passwords do not match.');
        }

        $this->userModel->update(Auth::userId(), [
            'password_hash' => $this->userModel->hashPassword($newPassword),
        ]);

        $this->redirectWithMessage('/profile', 'success', 'Password changed successfully.');
    }
}
