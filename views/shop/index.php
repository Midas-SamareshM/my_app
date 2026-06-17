<div class="row g-3 mb-4">
    <div class="col">
        <h4 class="mb-0">Shop</h4>
    </div>
    <div class="col-auto">
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= url('/shop') ?>"
               class="btn btn-sm <?= $activeCategoryId === null ? 'btn-dark' : 'btn-outline-secondary' ?>">All</a>
            <?php foreach ($allCategoryList as $category): ?>
                <a href="<?= url('/shop?category=' . $category['id']) ?>"
                   class="btn btn-sm <?= (int)($activeCategoryId ?? 0) === (int)$category['id'] ? 'btn-dark' : 'btn-outline-secondary' ?>">
                    <?= e($category['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
    <?php foreach ($activeProductList as $product): ?>
    <div class="col">
        <div class="card h-100">
            <a href="<?= url('/shop/' . $product['slug']) ?>">
                <?php if ($product['product_image']): ?>
                    <img src="<?= uploadUrl($product['product_image']) ?>"
                         class="card-img-top"
                         style="height:200px;object-fit:cover;"
                         alt="<?= e($product['name']) ?>">
                <?php else: ?>
                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                         style="height:200px;">
                        <i class="bi bi-image fs-1 opacity-50"></i>
                    </div>
                <?php endif; ?>
            </a>
            <div class="card-body d-flex flex-column">
                <small class="text-muted"><?= e($product['category_name']) ?></small>
                <h6 class="card-title mt-1">
                    <a href="<?= url('/shop/' . $product['slug']) ?>" class="text-dark text-decoration-none">
                        <?= e($product['name']) ?>
                    </a>
                </h6>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><?= formatPrice((float) $product['price']) ?></span>
                    <?php if ((int)$product['stock_quantity'] > 0): ?>
                        <?php if ($isLoggedIn): ?>
                        <form method="POST" action="<?= url('/cart/add') ?>">
                            <?= csrfField() ?>
                            <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">
                            <button class="btn btn-sm btn-dark">Add to Cart</button>
                        </form>
                        <?php else: ?>
                        <a href="<?= url('/login') ?>" class="btn btn-sm btn-outline-dark">Log in to buy</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="badge bg-secondary">Out of stock</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($activeProductList)): ?>
        <div class="col-12">
            <p class="text-center text-muted py-5">No products available yet.</p>
        </div>
    <?php endif; ?>
</div>
