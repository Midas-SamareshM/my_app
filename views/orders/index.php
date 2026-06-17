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
            <table class="table table-hover mb-0">
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
                        <td>#<?= e($order['id']) ?></td>
                        <td><?= formatPrice((float) $order['total_amount']) ?></td>
                        <td>
                            <span class="badge bg-<?= match($order['status']) {
                                'delivered' => 'success',
                                'shipped'   => 'info',
                                'confirmed' => 'primary',
                                'cancelled' => 'danger',
                                default     => 'secondary',
                            } ?>"><?= e(ucfirst($order['status'])) ?></span>
                        </td>
                        <td><?= e(date('d M Y', strtotime($order['created_at']))) ?></td>
                        <td>
                            <a href="<?= url('/orders/' . $order['id']) ?>"
                               class="btn btn-sm btn-outline-secondary">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
