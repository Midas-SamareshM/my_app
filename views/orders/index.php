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

<h4 class="mb-4">My Orders</h4>

<?php if (empty($userOrderList)): ?>
    <div class="text-center py-5">
        <i class="bi bi-bag display-3 text-muted"></i>
        <h5 class="mt-3">You haven't placed any orders yet.</h5>
        <a href="<?= url('/shop') ?>" class="btn btn-dark mt-2">Start Shopping</a>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userOrderList as $order): ?>
                    <tr>
                        <td class="fw-semibold">#<?= e($order['id']) ?></td>
                        <td><?= formatPrice((float) $order['total_amount']) ?></td>
                        <td>
                            <span class="badge bg-<?= $statusBadge($order['status']) ?>">
                                <?= $statusLabel($order['status']) ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= e(date('d M Y', strtotime($order['created_at']))) ?></td>
                        <td>
                            <a href="<?= url('/orders/' . $order['id']) ?>"
                               class="btn btn-sm btn-outline-secondary">Track</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
