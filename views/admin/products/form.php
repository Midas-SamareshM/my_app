<?php $isEditing = $product !== null; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header fw-semibold">
                <?= $isEditing ? 'Edit: ' . e($product['name']) : 'Add New Product' ?>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="<?= $isEditing
                          ? url('/admin/products/' . $product['id'] . '/update')
                          : url('/admin/products/create') ?>"
                      enctype="multipart/form-data">
                    <?= csrfField() ?>

                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required
                               value="<?= e($product['name'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">— Select category —</option>
                            <?php foreach ($categoryList as $category): ?>
                                <option value="<?= e($category['id']) ?>"
                                    <?= isset($product['category_id']) && (int)$product['category_id'] === (int)$category['id'] ? 'selected' : '' ?>>
                                    <?= e($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Price ($)</label>
                            <input type="number" name="price" class="form-control"
                                   step="0.01" min="0" required
                                   value="<?= e($product['price'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control"
                                   min="0" required
                                   value="<?= e($product['stock_quantity'] ?? 0) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= e($product['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <?php if ($isEditing && $product['product_image']): ?>
                            <div class="mb-2">
                                <img src="<?= uploadUrl($product['product_image']) ?>"
                                     style="height:80px;object-fit:cover;border-radius:4px;" alt="Current image">
                                <small class="text-muted ms-2">Upload a new image to replace</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="product_image" class="form-control" accept="image/*">
                    </div>

                    <?php if ($isEditing): ?>
                    <div class="mb-3">
                        <label class="form-label">Visibility</label>
                        <select name="is_active" class="form-select">
                            <option value="1" <?= (int)($product['is_active'] ?? 1) === 1 ? 'selected' : '' ?>>Active (visible in shop)</option>
                            <option value="0" <?= (int)($product['is_active'] ?? 1) === 0 ? 'selected' : '' ?>>Hidden</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark">
                            <?= $isEditing ? 'Save Changes' : 'Create Product' ?>
                        </button>
                        <a href="<?= url('/admin/products') ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
