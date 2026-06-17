<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

/**
 * Admin panel — dashboard, user management, category and product CRUD.
 */
class AdminController extends BaseController
{
    private User     $userModel;
    private Category $categoryModel;
    private Product  $productModel;
    private Order    $orderModel;

    public function __construct(\App\Http\Request $request)
    {
        parent::__construct($request);
        $this->userModel     = new User();
        $this->categoryModel = new Category();
        $this->productModel  = new Product();
        $this->orderModel    = new Order();
    }

    // -------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------

    /**
     * Display the admin dashboard with summary counters.
     *
     * @return void
     */
    public function dashboard(): void
    {
        $this->requireAdmin();

        $this->render('admin/dashboard', [
            'pageTitle'    => 'Admin Dashboard',
            'totalUsers'   => $this->userModel->count(),
            'totalProducts'=> $this->productModel->count(),
            'totalOrders'  => $this->orderModel->count(),
            'recentOrders' => $this->orderModel->getAllWithUsers(),
        ]);
    }

    // -------------------------------------------------------
    // User management
    // -------------------------------------------------------

    /**
     * List all registered customers.
     *
     * @return void
     */
    public function listUsers(): void
    {
        $this->requireAdmin();

        $this->render('admin/users', [
            'pageTitle'    => 'Manage Users',
            'customerList' => $this->userModel->getAllCustomers(),
        ]);
    }

    /**
     * Toggle a customer's active/inactive status.
     *
     * @param  string  $userId  User ID from the route parameter.
     *
     * @return void
     */
    public function toggleUser(string $userId): void
    {
        $this->requireAdmin();
        $this->requireValidCsrf();

        $this->userModel->toggleActiveStatus((int) $userId);
        $this->redirectWithMessage('/admin/users', 'success', 'User status updated.');
    }

    /**
     * Delete a customer account.
     *
     * @param  string  $userId  User ID from the route parameter.
     *
     * @return void
     */
    public function deleteUser(string $userId): void
    {
        $this->requireAdmin();
        $this->requireValidCsrf();

        $this->userModel->delete((int) $userId);
        $this->redirectWithMessage('/admin/users', 'success', 'User deleted.');
    }

    // -------------------------------------------------------
    // Category management
    // -------------------------------------------------------

    /**
     * List all product categories.
     *
     * @return void
     */
    public function listCategories(): void
    {
        $this->requireAdmin();

        $this->render('admin/categories', [
            'pageTitle'    => 'Manage Categories',
            'categoryList' => $this->categoryModel->getAllSorted(),
        ]);
    }

    /**
     * Create a new category from form submission.
     *
     * @return void
     */
    public function createCategory(): void
    {
        $this->requireAdmin();
        $this->requireValidCsrf();

        $categoryName = trim((string) $this->request->post('name', ''));

        if ($categoryName === '') {
            $this->redirectWithMessage('/admin/categories', 'danger', 'Category name is required.');
        }

        $this->categoryModel->create([
            'name'        => $categoryName,
            'slug'        => slugify($categoryName),
            'description' => trim((string) $this->request->post('description', '')),
        ]);

        $this->redirectWithMessage('/admin/categories', 'success', 'Category created.');
    }

    /**
     * Delete a category if it has no attached products.
     *
     * @param  string  $categoryId  Category ID from the route parameter.
     *
     * @return void
     */
    public function deleteCategory(string $categoryId): void
    {
        $this->requireAdmin();
        $this->requireValidCsrf();

        if (!$this->categoryModel->isDeletable((int) $categoryId)) {
            $this->redirectWithMessage('/admin/categories', 'danger', 'Cannot delete — category has products.');
        }

        $this->categoryModel->delete((int) $categoryId);
        $this->redirectWithMessage('/admin/categories', 'success', 'Category deleted.');
    }

    // -------------------------------------------------------
    // Product management
    // -------------------------------------------------------

    /**
     * List all products.
     *
     * @return void
     */
    public function listProducts(): void
    {
        $this->requireAdmin();

        $this->render('admin/products/index', [
            'pageTitle'   => 'Manage Products',
            'productList' => $this->productModel->getAllWithCategory(),
        ]);
    }

    /**
     * Show the create-product form.
     *
     * @return void
     */
    public function showCreateProduct(): void
    {
        $this->requireAdmin();

        $this->render('admin/products/form', [
            'pageTitle'    => 'Add Product',
            'categoryList' => $this->categoryModel->getAllSorted(),
            'product'      => null,
        ]);
    }

    /**
     * Persist a new product from form data.
     *
     * @return void
     */
    public function createProduct(): void
    {
        $this->requireAdmin();
        $this->requireValidCsrf();

        $productName  = trim((string) $this->request->post('name', ''));
        $categoryId   = (int) $this->request->post('category_id', 0);
        $priceValue   = (float) $this->request->post('price', 0);
        $stockQty     = (int) $this->request->post('stock_quantity', 0);
        $description  = trim((string) $this->request->post('description', ''));

        if ($productName === '' || $categoryId === 0 || $priceValue <= 0) {
            $this->redirectWithMessage('/admin/products/create', 'danger', 'Name, category, and price are required.');
        }

        $productImagePath = null;
        $uploadedFile     = $this->request->file('product_image');

        if ($uploadedFile !== null && $uploadedFile['error'] === UPLOAD_ERR_OK) {
            try {
                $productImagePath = $this->productModel->uploadImage($uploadedFile, 'products');
            } catch (\RuntimeException $uploadException) {
                $this->redirectWithMessage('/admin/products/create', 'danger', $uploadException->getMessage());
            }
        }

        $this->productModel->create([
            'category_id'    => $categoryId,
            'name'           => $productName,
            'slug'           => slugify($productName),
            'description'    => $description,
            'price'          => $priceValue,
            'stock_quantity' => $stockQty,
            'product_image'  => $productImagePath,
        ]);

        $this->redirectWithMessage('/admin/products', 'success', 'Product created.');
    }

    /**
     * Show the edit-product form.
     *
     * @param  string  $productId  Product ID from the route parameter.
     *
     * @return void
     */
    public function showEditProduct(string $productId): void
    {
        $this->requireAdmin();

        $product = $this->productModel->findById((int) $productId);

        if ($product === null) {
            $this->redirectWithMessage('/admin/products', 'danger', 'Product not found.');
        }

        $this->render('admin/products/form', [
            'pageTitle'    => 'Edit Product',
            'categoryList' => $this->categoryModel->getAllSorted(),
            'product'      => $product,
        ]);
    }

    /**
     * Persist product edits from form data.
     *
     * @param  string  $productId  Product ID from the route parameter.
     *
     * @return void
     */
    public function updateProduct(string $productId): void
    {
        $this->requireAdmin();
        $this->requireValidCsrf();

        $numericProductId = (int) $productId;
        $existingProduct  = $this->productModel->findById($numericProductId);

        if ($existingProduct === null) {
            $this->redirectWithMessage('/admin/products', 'danger', 'Product not found.');
        }

        $updatedData = [
            'category_id'    => (int) $this->request->post('category_id', 0),
            'name'           => trim((string) $this->request->post('name', '')),
            'slug'           => slugify((string) $this->request->post('name', '')),
            'description'    => trim((string) $this->request->post('description', '')),
            'price'          => (float) $this->request->post('price', 0),
            'stock_quantity' => (int) $this->request->post('stock_quantity', 0),
            'is_active'      => (int) $this->request->post('is_active', 1),
        ];

        $uploadedFile = $this->request->file('product_image');

        if ($uploadedFile !== null && $uploadedFile['error'] === UPLOAD_ERR_OK) {
            try {
                $this->productModel->deleteUploadedFile($existingProduct['product_image']);
                $updatedData['product_image'] = $this->productModel->uploadImage($uploadedFile, 'products');
            } catch (\RuntimeException $uploadException) {
                $this->redirectWithMessage("/admin/products/{$productId}/edit", 'danger', $uploadException->getMessage());
            }
        }

        $this->productModel->update($numericProductId, $updatedData);
        $this->redirectWithMessage('/admin/products', 'success', 'Product updated.');
    }

    // -------------------------------------------------------
    // Order management
    // -------------------------------------------------------

    /**
     * List all orders, optionally filtered by status.
     *
     * @return void
     */
    public function listOrders(): void
    {
        $this->requireAdmin();

        $statusFilter = $this->request->get('status', '');
        $statusFilter = in_array($statusFilter, \App\Models\Order::ALLOWED_STATUSES, true)
            ? $statusFilter
            : null;

        $this->render('admin/orders/index', [
            'pageTitle'     => 'Manage Orders',
            'orderList'     => $this->orderModel->getAllWithUsers($statusFilter),
            'statusCounts'  => $this->orderModel->countsByStatus(),
            'activeFilter'  => $statusFilter,
            'allStatuses'   => \App\Models\Order::ALLOWED_STATUSES,
        ]);
    }

    /**
     * Show the detail page for a single order (admin view).
     *
     * @param  string  $orderId  Order ID from the route parameter.
     *
     * @return void
     */
    public function viewOrder(string $orderId): void
    {
        $this->requireAdmin();

        $order = $this->orderModel->findWithUser((int) $orderId);

        if ($order === null) {
            $this->redirectWithMessage('/admin/orders', 'danger', 'Order not found.');
        }

        $this->render('admin/orders/show', [
            'pageTitle'      => 'Order #' . $orderId,
            'order'          => $order,
            'orderLineItems' => $this->orderModel->getOrderItems((int) $orderId),
            'allStatuses'    => \App\Models\Order::ALLOWED_STATUSES,
        ]);
    }

    /**
     * Update the status of an order via POST.
     *
     * @param  string  $orderId  Order ID from the route parameter.
     *
     * @return void
     */
    public function updateOrderStatus(string $orderId): void
    {
        $this->requireAdmin();
        $this->requireValidCsrf();

        $newStatus = (string) $this->request->post('status', '');

        try {
            $this->orderModel->updateStatus((int) $orderId, $newStatus);
            $this->redirectWithMessage(
                '/admin/orders/' . $orderId,
                'success',
                'Order status updated to "' . ucfirst($newStatus) . '".'
            );
        } catch (\InvalidArgumentException $statusException) {
            $this->redirectWithMessage('/admin/orders/' . $orderId, 'danger', $statusException->getMessage());
        }
    }

    // -------------------------------------------------------
    // Product management
    // -------------------------------------------------------

    /**
     * Delete a product and its uploaded image.
     *
     * @param  string  $productId  Product ID from the route parameter.
     *
     * @return void
     */
    public function deleteProduct(string $productId): void
    {
        $this->requireAdmin();
        $this->requireValidCsrf();

        $product = $this->productModel->findById((int) $productId);

        if ($product !== null) {
            $this->productModel->deleteUploadedFile($product['product_image']);
            $this->productModel->delete((int) $productId);
        }

        $this->redirectWithMessage('/admin/products', 'success', 'Product deleted.');
    }
}
