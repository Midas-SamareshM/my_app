<div class="row g-4">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header fw-semibold">Shipping Details</div>
            <div class="card-body">
                <form method="POST" action="<?= url('/checkout') ?>">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label">Shipping Address</label>
                        <textarea name="shipping_address" class="form-control" rows="3" required><?= e($currentUser['delivery_address'] ?? '') ?></textarea>
                    </div>
                    <button class="btn btn-dark w-100">Place Order</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header fw-semibold">Order Summary</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($cartItems as $cartItem): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= e($cartItem['name']) ?> × <?= e($cartItem['quantity']) ?></span>
                        <span><?= formatPrice($cartItem['price'] * $cartItem['quantity']) ?></span>
                    </li>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span><?= formatPrice($cartTotal) ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
