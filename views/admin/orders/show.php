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

$pipeline = ['ordered', 'processed', 'dispatched', 'delivered'];
$currentIdx = array_search($order['status'], $pipeline, true);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?= url('/admin/orders') ?>" class="text-muted text-decoration-none small">
            ← All Orders
        </a>
        <h4 class="mb-0 mt-1">Order #<?= e($order['id']) ?></h4>
    </div>
    <span class="badge fs-6 bg-<?= $statusBadge($order['status']) ?>">
        <?= $statusLabel($order['status']) ?>
    </span>
</div>

<!-- Progress tracker -->
<?php if ($order['status'] !== 'cancelled'): ?>
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between position-relative">
            <div class="position-absolute top-50 start-0 end-0 translate-middle-y"
                 style="height:3px;background:#dee2e6;z-index:0;margin:0 60px;"></div>
            <?php foreach ($pipeline as $idx => $step): ?>
            <?php
                $done    = $currentIdx !== false && $idx <= $currentIdx;
                $active  = $idx === $currentIdx;
            ?>
            <div class="text-center position-relative" style="z-index:1;min-width:80px;">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                     style="width:36px;height:36px;font-size:.8rem;
                            background:<?= $done ? '#0d6efd' : '#dee2e6' ?>;
                            color:<?= $done ? '#fff' : '#6c757d' ?>;
                            border:2px solid <?= $active ? '#0d6efd' : ($done ? '#0d6efd' : '#dee2e6') ?>;">
                    <?php if ($done && !$active): ?>
                        <i class="bi bi-check-lg"></i>
                    <?php else: ?>
                        <?= $idx + 1 ?>
                    <?php endif; ?>
                </div>
                <div class="mt-1 small <?= $active ? 'fw-semibold text-primary' : ($done ? 'text-muted' : 'text-muted') ?>">
                    <?= $statusLabel($step) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Line items -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-semibold">Items</div>
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderLineItems as $lineItem): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($lineItem['product_image']): ?>
                                        <img src="<?= uploadUrl($lineItem['product_image']) ?>"
                                             style="width:40px;height:40px;object-fit:cover;border-radius:4px;" alt="">
                                    <?php endif; ?>
                                    <span><?= e($lineItem['product_name']) ?></span>
                                </div>
                            </td>
                            <td><?= formatPrice((float) $lineItem['unit_price']) ?></td>
                            <td><?= e($lineItem['quantity']) ?></td>
                            <td class="fw-semibold"><?= formatPrice((float) $lineItem['unit_price'] * $lineItem['quantity']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="fw-bold"><?= formatPrice((float) $order['total_amount']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Right panel -->
    <div class="col-lg-5 d-flex flex-column gap-4">

        <!-- Customer details -->
        <div class="card">
            <div class="card-header fw-semibold">Customer</div>
            <div class="card-body">
                <p class="mb-1"><i class="bi bi-person me-1 text-muted"></i><?= e($order['full_name']) ?></p>
                <p class="mb-1"><i class="bi bi-envelope me-1 text-muted"></i><?= e($order['email_address']) ?></p>
                <?php if (!empty($order['phone_number'])): ?>
                    <p class="mb-1"><i class="bi bi-telephone me-1 text-muted"></i><?= e($order['phone_number']) ?></p>
                <?php endif; ?>
                <p class="mb-0 mt-2"><strong>Ship to:</strong><br>
                    <span class="text-muted"><?= nl2br(e($order['shipping_address'])) ?></span>
                </p>
            </div>
        </div>

        <!-- Update status -->
        <div class="card border-primary">
            <div class="card-header fw-semibold text-primary">Update Status</div>
            <div class="card-body">
                <form method="POST" action="<?= url('/admin/orders/' . $order['id'] . '/status') ?>">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label small text-muted">New Status</label>
                        <select name="status" class="form-select">
                            <?php foreach ($allStatuses as $statusOption): ?>
                            <option value="<?= e($statusOption) ?>"
                                <?= $order['status'] === $statusOption ? 'selected' : '' ?>>
                                <?= $statusLabel($statusOption) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-arrow-repeat me-1"></i> Update Status
                    </button>
                </form>
            </div>
        </div>

        <div class="text-muted small">
            Placed: <?= e(date('d M Y, H:i', strtotime($order['created_at']))) ?><br>
            Last updated: <?= e(date('d M Y, H:i', strtotime($order['updated_at']))) ?>
        </div>
    </div>
</div>
