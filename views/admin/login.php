<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .admin-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            color: #e2e8f0;
        }
        .admin-card .form-control {
            background: #0f172a;
            border-color: #334155;
            color: #e2e8f0;
        }
        .admin-card .form-control:focus {
            background: #0f172a;
            border-color: #6366f1;
            color: #e2e8f0;
            box-shadow: 0 0 0 3px rgba(99,102,241,.25);
        }
        .admin-card .form-label {
            color: #94a3b8;
            font-size: .85rem;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .badge-admin {
            background: #312e81;
            color: #a5b4fc;
            font-size: .7rem;
            letter-spacing: .1em;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
        }
        .btn-admin {
            background: #6366f1;
            border: none;
            color: #fff;
            font-weight: 600;
            letter-spacing: .03em;
        }
        .btn-admin:hover {
            background: #4f46e5;
            color: #fff;
        }
        .divider {
            border-color: #334155;
        }
        .customer-link {
            color: #64748b;
            font-size: .85rem;
            text-decoration: none;
        }
        .customer-link:hover {
            color: #94a3b8;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <div class="text-center mb-4">
                <div class="badge-admin mb-3">Admin Portal</div>
                <h2 class="text-white fw-bold mb-1"><?= APP_NAME ?></h2>
                <p class="text-secondary small">Restricted access — authorised personnel only.</p>
            </div>

            <div class="admin-card p-4 shadow-lg">

                <?php if (!empty($flashMessage)): ?>
                    <div class="alert alert-<?= e($flashMessage['type']) ?> py-2 small">
                        <?= e($flashMessage['message']) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/admin/login') ?>">
                    <?= csrfField() ?>

                    <div class="mb-3">
                        <label class="form-label">Admin Email</label>
                        <input type="email" name="email" class="form-control" required autofocus
                               placeholder="admin@example.com">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required
                               placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn btn-admin w-100 py-2">
                        Sign In to Admin Panel
                    </button>
                </form>

                <hr class="divider my-4">

                <p class="text-center mb-0">
                    <a href="<?= url('/login') ?>" class="customer-link">
                        ← Customer login
                    </a>
                </p>

            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
