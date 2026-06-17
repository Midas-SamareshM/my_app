<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-semibold">Profile Information</div>
            <div class="card-body">
                <form method="POST" action="<?= url('/profile/update') ?>" enctype="multipart/form-data">
                    <?= csrfField() ?>

                    <div class="text-center mb-3">
                        <?php if ($currentUser['profile_image']): ?>
                            <img src="<?= uploadUrl($currentUser['profile_image']) ?>"
                                 class="rounded-circle"
                                 style="width:90px;height:90px;object-fit:cover;"
                                 alt="Avatar">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center"
                                 style="width:90px;height:90px;font-size:2rem;">
                                <?= strtoupper(substr($currentUser['full_name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div class="mt-2">
                            <input type="file" name="profile_image" class="form-control form-control-sm" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required
                               value="<?= e($currentUser['full_name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" value="<?= e($currentUser['email_address']) ?>" disabled>
                        <small class="text-muted">Email cannot be changed.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone_number" class="form-control"
                               value="<?= e($currentUser['phone_number'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Default Delivery Address</label>
                        <textarea name="delivery_address" class="form-control" rows="3"><?= e($currentUser['delivery_address'] ?? '') ?></textarea>
                    </div>
                    <button class="btn btn-dark">Save Profile</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-semibold">Change Password</div>
            <div class="card-body">
                <form method="POST" action="<?= url('/profile/update-password') ?>">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button class="btn btn-outline-dark">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
