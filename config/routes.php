<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\OrderController;
use App\Controllers\ProfileController;
use App\Controllers\ShopController;

// -------------------------------------------------------
// Auth — customers
// -------------------------------------------------------
$router->get('/',          [AuthController::class, 'showLogin']);
$router->get('/login',     [AuthController::class, 'showLogin']);
$router->post('/login',    [AuthController::class, 'login']);
$router->get('/register',  [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout',    [AuthController::class, 'logout']);

// -------------------------------------------------------
// Auth — admin (separate login page)
// -------------------------------------------------------
$router->get('/admin/login',  [AuthController::class, 'showAdminLogin']);
$router->post('/admin/login', [AuthController::class, 'adminLogin']);

// -------------------------------------------------------
// Shop (public)
// -------------------------------------------------------
$router->get('/shop',               [ShopController::class, 'index']);
$router->get('/shop/{slug}',        [ShopController::class, 'show']);

// -------------------------------------------------------
// Cart
// -------------------------------------------------------
$router->get('/cart',           [CartController::class, 'index']);
$router->post('/cart/add',      [CartController::class, 'add']);
$router->post('/cart/update',   [CartController::class, 'update']);
$router->post('/cart/remove',   [CartController::class, 'remove']);

// -------------------------------------------------------
// Checkout & Orders
// -------------------------------------------------------
$router->get('/checkout',         [OrderController::class, 'showCheckout']);
$router->post('/checkout',        [OrderController::class, 'placeOrder']);
$router->get('/orders',           [OrderController::class, 'index']);
$router->get('/orders/{orderId}', [OrderController::class, 'show']);

// -------------------------------------------------------
// Profile
// -------------------------------------------------------
$router->get('/profile',                  [ProfileController::class, 'index']);
$router->post('/profile/update',          [ProfileController::class, 'update']);
$router->post('/profile/update-password', [ProfileController::class, 'updatePassword']);

// -------------------------------------------------------
// Admin
// -------------------------------------------------------
$router->get('/admin/dashboard',                     [AdminController::class, 'dashboard']);

$router->get('/admin/users',                         [AdminController::class, 'listUsers']);
$router->post('/admin/users/{userId}/toggle',        [AdminController::class, 'toggleUser']);
$router->post('/admin/users/{userId}/delete',        [AdminController::class, 'deleteUser']);

$router->get('/admin/categories',                    [AdminController::class, 'listCategories']);
$router->post('/admin/categories/create',            [AdminController::class, 'createCategory']);
$router->post('/admin/categories/{categoryId}/delete', [AdminController::class, 'deleteCategory']);

$router->get('/admin/orders',                            [AdminController::class, 'listOrders']);
$router->get('/admin/orders/{orderId}',                  [AdminController::class, 'viewOrder']);
$router->post('/admin/orders/{orderId}/status',          [AdminController::class, 'updateOrderStatus']);

$router->get('/admin/products',                      [AdminController::class, 'listProducts']);
$router->get('/admin/products/create',               [AdminController::class, 'showCreateProduct']);
$router->post('/admin/products/create',              [AdminController::class, 'createProduct']);
$router->get('/admin/products/{productId}/edit',     [AdminController::class, 'showEditProduct']);
$router->post('/admin/products/{productId}/update',  [AdminController::class, 'updateProduct']);
$router->post('/admin/products/{productId}/delete',  [AdminController::class, 'deleteProduct']);
