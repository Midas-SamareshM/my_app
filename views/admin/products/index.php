<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Manage Products</h4>
    <a href="<?= url('/admin/products/create') ?>" class="btn btn-dark">+ Add Product</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productList as $product): ?>
                <tr>
                    <td>
                        <?php if ($product['product_image']): ?>
                            <img src="<?= uploadUrl($product['product_image']) ?>"
                                 alt="<?= e($product['name']) ?>"
                                 style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                        <?php else: ?>
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                                 style="width:50px;height:50px;border-radius:4px;font-size:10px;">No img</div>
                        <?php endif; ?>
                    </td>
                    <td><?= e($product['name']) ?></td>
                    <td><?= e($product['category_name']) ?></td>
                    <td><?= formatPrice((float) $product['price']) ?></td>
                    <td><?= e($product['stock_quantity']) ?></td>
                    <td>
                        <span class="badge bg-<?= (int) $product['is_active'] === 1 ? 'success' : 'secondary' ?>">
                            <?= (int) $product['is_active'] === 1 ? 'Active' : 'Hidden' ?>
                        </span>
                    </td>
                    <td class="d-flex gap-1">
                        <a href="<?= url('/admin/products/' . $product['id'] . '/edit') ?>"
                           class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="<?= url('/admin/products/' . $product['id'] . '/delete') ?>"
                              onsubmit="return confirm('Delete this product?')">
                            <?= csrfField() ?>
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($productList)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">No products yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
