<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Order #<?= e($order['id']) ?></h4>
    <span class="badge fs-6 bg-<?= match($order['status']) {
        'delivered' => 'success',
        'shipped'   => 'info',
        'confirmed' => 'primary',
        'cancelled' => 'danger',
        default     => 'secondary',
    } ?>"><?= e(ucfirst($order['status'])) ?></span>
</div>

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
                                             style="width:40px;height:40px;object-fit:cover;border-radius:3px;"
                                             alt="">
                                    <?php endif; ?>
                                    <?= e($lineItem['product_name']) ?>
                                </div>
                            </td>
                            <td><?= formatPrice((float)$lineItem['unit_price']) ?></td>
                            <td><?= e($lineItem['quantity']) ?></td>
                            <td><?= formatPrice((float)$lineItem['unit_price'] * $lineItem['quantity']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header fw-semibold">Details</div>
            <div class="card-body">
                <p class="mb-1"><strong>Placed:</strong> <?= e(date('d M Y H:i', strtotime($order['created_at']))) ?></p>
                <p class="mb-1"><strong>Total:</strong> <?= formatPrice((float) $order['total_amount']) ?></p>
                <p class="mb-0"><strong>Ship to:</strong><br><?= nl2br(e($order['shipping_address'])) ?></p>
            </div>
        </div>
        <a href="<?= url('/orders') ?>" class="btn btn-outline-secondary mt-3">← All Orders</a>
    </div>
</div>
