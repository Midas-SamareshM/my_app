<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Order;
use App\Models\User;

/**
 * Checkout and order history for authenticated customers.
 */
class OrderController extends BaseController
{
    private Order $orderModel;
    private User  $userModel;

    public function __construct(\App\Http\Request $request)
    {
        parent::__construct($request);
        $this->orderModel = new Order();
        $this->userModel  = new User();
    }

    /**
     * Show the checkout confirmation page.
     *
     * @return void
     */
    public function showCheckout(): void
    {
        $this->requireAuth();

        $cartController = new CartController($this->request);
        $cartItems      = $cartController->getCartItems();

        if (empty($cartItems)) {
            $this->redirectWithMessage('/cart', 'warning', 'Your cart is empty.');
        }

        $currentUser = $this->userModel->findById(Auth::userId());
        $cartTotal   = $cartController->calculateCartTotal($cartItems);

        $this->render('orders/checkout', [
            'pageTitle'   => 'Checkout',
            'cartItems'   => $cartItems,
            'cartTotal'   => $cartTotal,
            'currentUser' => $currentUser,
        ]);
    }

    /**
     * Process the checkout and create the order.
     *
     * @return void
     */
    public function placeOrder(): void
    {
        $this->requireAuth();
        $this->requireValidCsrf();

        $shippingAddress = trim((string) $this->request->post('shipping_address', ''));

        if ($shippingAddress === '') {
            $this->redirectWithMessage('/checkout', 'danger', 'Shipping address is required.');
        }

        $cartController = new CartController($this->request);
        $cartItems      = $cartController->getCartItems();

        if (empty($cartItems)) {
            $this->redirectWithMessage('/cart', 'warning', 'Your cart is empty.');
        }

        $lineItems   = [];
        $totalAmount = 0.0;

        foreach ($cartItems as $cartItem) {
            $lineItems[] = [
                'product_id' => $cartItem['product_id'],
                'quantity'   => $cartItem['quantity'],
                'unit_price' => $cartItem['price'],
            ];
            $totalAmount += $cartItem['price'] * $cartItem['quantity'];
        }

        try {
            $newOrderId = $this->orderModel->createWithItems(
                Auth::userId(),
                $totalAmount,
                $shippingAddress,
                $lineItems
            );

            $cartController->clear();

            $this->redirectWithMessage(
                '/orders',
                'success',
                "Order #{$newOrderId} placed successfully! We'll confirm it shortly."
            );
        } catch (\RuntimeException $orderException) {
            $this->redirectWithMessage('/checkout', 'danger', $orderException->getMessage());
        }
    }

    /**
     * Display the current user's order history.
     *
     * @return void
     */
    public function index(): void
    {
        $this->requireAuth();

        $userOrderList = $this->orderModel->getByUser(Auth::userId());

        $this->render('orders/index', [
            'pageTitle'     => 'My Orders',
            'userOrderList' => $userOrderList,
        ]);
    }

    /**
     * Show detail for a single order (customer must own it).
     *
     * @param  string  $orderId  Order ID from the route parameter.
     *
     * @return void
     */
    public function show(string $orderId): void
    {
        $this->requireAuth();

        $order = $this->orderModel->findById((int) $orderId);

        if ($order === null || (int) $order['user_id'] !== Auth::userId()) {
            http_response_code(403);
            echo '<h1>Order not found.</h1>';
            return;
        }

        $orderLineItems = $this->orderModel->getOrderItems((int) $orderId);

        $this->render('orders/show', [
            'pageTitle'      => 'Order #' . $orderId,
            'order'          => $order,
            'orderLineItems' => $orderLineItems,
        ]);
    }
}
