<div class="row g-5">
    <div class="col-md-5">
        <?php if ($product['product_image']): ?>
            <img src="<?= uploadUrl($product['product_image']) ?>"
                 class="img-fluid rounded shadow-sm"
                 style="max-height:400px;width:100%;object-fit:cover;"
                 alt="<?= e($product['name']) ?>">
        <?php else: ?>
            <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center"
                 style="height:300px;">
                <i class="bi bi-image fs-1 opacity-50"></i>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-7">
        <small class="text-muted text-uppercase"><?= e($product['category_name']) ?></small>
        <h2 class="mt-1"><?= e($product['name']) ?></h2>
        <h3 class="text-success my-3"><?= formatPrice((float) $product['price']) ?></h3>

        <p><?= nl2br(e($product['description'] ?: 'No description available.')) ?></p>

        <?php if ((int) $product['stock_quantity'] > 0): ?>
            <p class="text-success"><i class="bi bi-check-circle-fill"></i> In stock (<?= e($product['stock_quantity']) ?> available)</p>
            <?php if ($isLoggedIn): ?>
            <form method="POST" action="<?= url('/cart/add') ?>" class="d-flex align-items-center gap-3">
                <?= csrfField() ?>
                <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">
                <div style="width:80px;">
                    <input type="number" name="quantity" class="form-control" value="1" min="1"
                           max="<?= e($product['stock_quantity']) ?>">
                </div>
                <button class="btn btn-dark px-4">Add to Cart</button>
            </form>
            <?php else: ?>
            <a href="<?= url('/login') ?>" class="btn btn-dark">Log in to purchase</a>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-danger"><i class="bi bi-x-circle-fill"></i> Out of stock</p>
        <?php endif; ?>

        <a href="<?= url('/shop') ?>" class="btn btn-link ps-0 mt-3">← Back to shop</a>
    </div>
</div>
