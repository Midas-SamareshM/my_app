<?php if (empty($cartItems)): ?>
    <div class="text-center py-5">
        <i class="bi bi-cart3 display-3 text-muted"></i>
        <h4 class="mt-3">Your cart is empty</h4>
        <a href="<?= url('/shop') ?>" class="btn btn-dark mt-2">Continue Shopping</a>
    </div>
<?php else: ?>
<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $cartItem): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if ($cartItem['image']): ?>
                                        <img src="<?= uploadUrl($cartItem['image']) ?>"
                                             style="width:50px;height:50px;object-fit:cover;border-radius:4px;"
                                             alt="<?= e($cartItem['name']) ?>">
                                    <?php endif; ?>
                                    <?= e($cartItem['name']) ?>
                                </div>
                            </td>
                            <td><?= formatPrice($cartItem['price']) ?></td>
                            <td>
                                <form method="POST" action="<?= url('/cart/update') ?>" class="d-flex gap-1">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="product_id" value="<?= e($cartItem['product_id']) ?>">
                                    <input type="number" name="quantity" value="<?= e($cartItem['quantity']) ?>"
                                           min="1" class="form-control form-control-sm" style="width:60px;">
                                    <button class="btn btn-sm btn-outline-secondary">✓</button>
                                </form>
                            </td>
                            <td><?= formatPrice($cartItem['price'] * $cartItem['quantity']) ?></td>
                            <td>
                                <form method="POST" action="<?= url('/cart/remove') ?>">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="product_id" value="<?= e($cartItem['product_id']) ?>">
                                    <button class="btn btn-sm btn-outline-danger">✕</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>Order Summary</h5>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total</span>
                    <strong><?= formatPrice($cartTotal) ?></strong>
                </div>
                <a href="<?= url('/checkout') ?>" class="btn btn-dark w-100">Proceed to Checkout</a>
                <a href="<?= url('/shop') ?>" class="btn btn-outline-secondary w-100 mt-2">Continue Shopping</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
