<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Manage Users</h4>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customerList as $customer): ?>
                <tr>
                    <td><?= e($customer['id']) ?></td>
                    <td><?= e($customer['full_name']) ?></td>
                    <td><?= e($customer['email_address']) ?></td>
                    <td><?= e($customer['phone_number'] ?: '—') ?></td>
                    <td>
                        <span class="badge bg-<?= (int) $customer['is_active'] === 1 ? 'success' : 'secondary' ?>">
                            <?= (int) $customer['is_active'] === 1 ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td><?= e(date('d M Y', strtotime($customer['created_at']))) ?></td>
                    <td class="d-flex gap-1">
                        <form method="POST" action="<?= url('/admin/users/' . $customer['id'] . '/toggle') ?>">
                            <?= csrfField() ?>
                            <button class="btn btn-sm btn-outline-warning">
                                <?= (int) $customer['is_active'] === 1 ? 'Deactivate' : 'Activate' ?>
                            </button>
                        </form>
                        <form method="POST" action="<?= url('/admin/users/' . $customer['id'] . '/delete') ?>"
                              onsubmit="return confirm('Delete this user?')">
                            <?= csrfField() ?>
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($customerList)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">No customers yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
