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

$pipeline   = ['ordered', 'processed', 'dispatched', 'delivered'];
$currentIdx = array_search($order['status'], $pipeline, true);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Order #<?= e($order['id']) ?></h4>
    <span class="badge fs-6 bg-<?= $statusBadge($order['status']) ?>">
        <?= $statusLabel($order['status']) ?>
    </span>
</div>

<!-- Order tracking timeline -->
<?php if ($order['status'] !== 'cancelled'): ?>
<div class="card mb-4">
    <div class="card-body py-4">
        <div class="d-flex justify-content-between position-relative">
            <div class="position-absolute top-50 start-0 end-0 translate-middle-y"
                 style="height:3px;background:#dee2e6;z-index:0;margin:0 70px;"></div>
            <?php foreach ($pipeline as $idx => $step): ?>
            <?php
                $done   = $currentIdx !== false && $idx <= $currentIdx;
                $active = $idx === $currentIdx;
                $icons  = ['bi-bag-check', 'bi-gear', 'bi-truck', 'bi-house-check'];
            ?>
            <div class="text-center position-relative" style="z-index:1;min-width:90px;">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto"
                     style="width:44px;height:44px;font-size:1.1rem;
                            background:<?= $done ? '#0d6efd' : '#f8f9fa' ?>;
                            color:<?= $done ? '#fff' : '#adb5bd' ?>;
                            border:2px solid <?= $active ? '#0d6efd' : ($done ? '#0d6efd' : '#dee2e6') ?>;">
                    <i class="bi <?= $icons[$idx] ?>"></i>
                </div>
                <div class="mt-2 small <?= $active ? 'fw-bold text-primary' : ($done ? 'text-muted' : 'text-muted') ?>">
                    <?= $statusLabel($step) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">
    <i class="bi bi-x-circle me-2"></i> This order has been <strong>cancelled</strong>.
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header fw-semibold">Items</div>
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderLineItems as $lineItem): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($lineItem['product_image']): ?>
                                        <img src="<?= uploadUrl($lineItem['product_image']) ?>"
                                             style="width:40px;height:40px;object-fit:cover;border-radius:3px;" alt="">
                                    <?php endif; ?>
                                    <?= e($lineItem['product_name']) ?>
                                </div>
                            </td>
                            <td><?= formatPrice((float) $lineItem['unit_price']) ?></td>
                            <td><?= e($lineItem['quantity']) ?></td>
                            <td><?= formatPrice((float) $lineItem['unit_price'] * $lineItem['quantity']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header fw-semibold">Order Details</div>
            <div class="card-body">
                <p class="mb-1"><strong>Placed:</strong> <?= e(date('d M Y, H:i', strtotime($order['created_at']))) ?></p>
                <p class="mb-1"><strong>Total:</strong> <?= formatPrice((float) $order['total_amount']) ?></p>
                <p class="mb-0"><strong>Ship to:</strong><br>
                    <span class="text-muted"><?= nl2br(e($order['shipping_address'])) ?></span>
                </p>
            </div>
        </div>
        <a href="<?= url('/orders') ?>" class="btn btn-outline-secondary mt-3 w-100">← My Orders</a>
    </div>
</div>
