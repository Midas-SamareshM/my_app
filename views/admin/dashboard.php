<?php
$statusBadge = static fn(string $s): string => match($s) {
    'dispatched' => 'info',
    'delivered'  => 'success',
    'processed'  => 'primary',
    'cancelled'  => 'danger',
    default      => 'secondary',
};
$statusLabel = static fn(string $s): string => match($s) {
    'ordered'    => 'Ordered',
    'processed'  => 'Processed',
    'dispatched' => 'Dispatched',
    'delivered'  => 'Delivered',
    'cancelled'  => 'Cancelled',
    default      => ucfirst($s),
};
?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-0">Registered Users</h6>
                    <h2 class="mb-0"><?= e($totalUsers) ?></h2>
                </div>
                <i class="bi bi-people-fill fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-0">Products</h6>
                    <h2 class="mb-0"><?= e($totalProducts) ?></h2>
                </div>
                <i class="bi bi-box-seam-fill fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <a href="<?= url('/admin/orders') ?>" class="text-decoration-none">
            <div class="card text-white bg-warning">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Orders</h6>
                        <h2 class="mb-0"><?= e($totalOrders) ?></h2>
                    </div>
                    <i class="bi bi-bag-check-fill fs-1 opacity-50"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center fw-semibold">
        Recent Orders
        <a href="<?= url('/admin/orders') ?>" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $order): ?>
                <tr>
                    <td class="fw-semibold"><?= e($order['id']) ?></td>
                    <td><?= e($order['full_name']) ?></td>
                    <td><?= formatPrice((float) $order['total_amount']) ?></td>
                    <td>
                        <span class="badge bg-<?= $statusBadge($order['status']) ?>">
                            <?= $statusLabel($order['status']) ?>
                        </span>
                    </td>
                    <td class="text-muted small"><?= e(date('d M Y', strtotime($order['created_at']))) ?></td>
                    <td>
                        <a href="<?= url('/admin/orders/' . $order['id']) ?>"
                           class="btn btn-sm btn-outline-dark">Manage</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">No orders yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
