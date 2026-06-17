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
        <div class="card text-white bg-warning">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-0">Total Orders</h6>
                    <h2 class="mb-0"><?= e($totalOrders) ?></h2>
                </div>
                <i class="bi bi-bag-check-fill fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header fw-semibold">Recent Orders</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $order): ?>
                <tr>
                    <td><?= e($order['id']) ?></td>
                    <td><?= e($order['full_name']) ?></td>
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
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">No orders yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
