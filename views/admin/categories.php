<div class="row g-4">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header fw-semibold">Add Category</div>
            <div class="card-body">
                <form method="POST" action="<?= url('/admin/categories/create') ?>">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <button class="btn btn-dark">Add Category</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header fw-semibold">All Categories</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categoryList as $category): ?>
                        <tr>
                            <td><?= e($category['id']) ?></td>
                            <td><?= e($category['name']) ?></td>
                            <td><code><?= e($category['slug']) ?></code></td>
                            <td><?= e($category['description'] ?: '—') ?></td>
                            <td>
                                <form method="POST" action="<?= url('/admin/categories/' . $category['id'] . '/delete') ?>"
                                      onsubmit="return confirm('Delete this category?')">
                                    <?= csrfField() ?>
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($categoryList)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">No categories yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
