<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm p-4">
                <h3 class="mb-4 text-center fw-bold">Create Account</h3>

                <?php if (!empty($flashMessage)): ?>
                    <div class="alert alert-<?= e($flashMessage['type']) ?>">
                        <?= e($flashMessage['message']) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/register') ?>">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <small class="text-muted">(min 8 characters)</small></label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirm" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Create Account</button>
                </form>

                <hr>
                <p class="text-center mb-0">
                    Already have an account? <a href="<?= url('/login') ?>">Log in</a>
                </p>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
