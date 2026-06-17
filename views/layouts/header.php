<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'eCom Platform') ?> — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8f9fa; }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        .card { border: none; box-shadow: 0 1px 6px rgba(0,0,0,.08); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= url('/shop') ?>"><?= APP_NAME ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= url('/shop') ?>">Shop</a></li>
                <?php if ($isLoggedIn ?? false): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/cart') ?>"><i class="bi bi-cart3"></i> Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/orders') ?>">My Orders</a></li>
                    <?php if ($isAdmin ?? false): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Admin</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= url('/admin/dashboard') ?>">Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?= url('/admin/orders') ?>">Orders</a></li>
                                <li><a class="dropdown-item" href="<?= url('/admin/users') ?>">Users</a></li>
                                <li><a class="dropdown-item" href="<?= url('/admin/categories') ?>">Categories</a></li>
                                <li><a class="dropdown-item" href="<?= url('/admin/products') ?>">Products</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if ($isLoggedIn ?? false): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/profile') ?>"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/logout') ?>">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/login') ?>">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/register') ?>">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container pb-5">

<?php if (!empty($flashMessage)): ?>
    <div class="alert alert-<?= e($flashMessage['type']) ?> alert-dismissible fade show">
        <?= e($flashMessage['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
