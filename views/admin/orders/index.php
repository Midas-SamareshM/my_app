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
$totalAll = array_sum($statusCounts);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Orders</h4>
    <span class="text-muted small"><?= $totalAll ?> total</span>
</div>

<!-- Status filter tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link <?= $activeFilter === null ? 'active' : '' ?>"
           href="<?= url('/admin/orders') ?>">
            All
            <span class="badge bg-secondary ms-1"><?= $totalAll ?></span>
        </a>
    </li>
    <?php foreach ($allStatuses as $status): ?>
    <li class="nav-item">
        <a class="nav-link <?= $activeFilter === $status ? 'active' : '' ?>"
           href="<?= url('/admin/orders?status=' . $status) ?>">
            <?= $statusLabel($status) ?>
            <?php if (!empty($statusCounts[$status])): ?>
                <span class="badge bg-<?= $statusBadge($status) ?> ms-1">
                    <?= $statusCounts[$status] ?>
                </span>
            <?php endif; ?>
        </a>
    </li>
    <?php endforeach; ?>
</ul>

<?php if (empty($orderList)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-bag display-3"></i>
        <p class="mt-3">No orders <?= $activeFilter ? 'with status "' . $statusLabel($activeFilter) . '"' : '' ?>.</p>
    </div>
<?php else: ?>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderList as $order): ?>
                <tr>
                    <td class="fw-semibold">#<?= e($order['id']) ?></td>
                    <td><?= e($order['full_name']) ?></td>
                    <td class="text-muted small"><?= e($order['email_address']) ?></td>
                    <td><?= formatPrice((float) $order['total_amount']) ?></td>
                    <td>
                        <span class="badge bg-<?= $statusBadge($order['status']) ?>">
                            <?= $statusLabel($order['status']) ?>
                        </span>
                    </td>
                    <td class="text-muted small"><?= e(date('d M Y, H:i', strtotime($order['created_at']))) ?></td>
                    <td>
                        <a href="<?= url('/admin/orders/' . $order['id']) ?>"
                           class="btn btn-sm btn-outline-dark">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
