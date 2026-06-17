<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;

/**
 * Session-based shopping cart management.
 * Cart structure: $_SESSION['cart'] = [ product_id => ['product_id', 'name', 'price', 'quantity'] ]
 */
class CartController extends BaseController
{
    private const SESSION_KEY = 'cart';

    private Product $productModel;

    public function __construct(\App\Http\Request $request)
    {
        parent::__construct($request);
        $this->productModel = new Product();
    }

    /**
     * Display all items currently in the cart.
     *
     * @return void
     */
    public function index(): void
    {
        $this->requireAuth();

        $cartItems    = $this->getCartItems();
        $cartTotal    = $this->calculateCartTotal($cartItems);

        $this->render('cart/index', [
            'pageTitle' => 'My Cart',
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }

    /**
     * Add a product to the cart or increment its quantity.
     *
     * @return void
     */
    public function add(): void
    {
        $this->requireAuth();
        $this->requireValidCsrf();

        $productId     = (int) $this->request->post('product_id', 0);
        $requestedQty  = max(1, (int) $this->request->post('quantity', 1));

        $product = $this->productModel->findById($productId);

        if ($product === null || (int) $product['is_active'] === 0) {
            $this->redirectWithMessage('/shop', 'danger', 'Product not available.');
        }

        $cartItems = $this->getCartItems();

        if (isset($cartItems[$productId])) {
            $cartItems[$productId]['quantity'] += $requestedQty;
        } else {
            $cartItems[$productId] = [
                'product_id' => $productId,
                'name'       => $product['name'],
                'price'      => (float) $product['price'],
                'image'      => $product['product_image'],
                'quantity'   => $requestedQty,
            ];
        }

        $this->saveCartItems($cartItems);

        $this->redirectWithMessage('/cart', 'success', '"' . $product['name'] . '" added to cart.');
    }

    /**
     * Update the quantity of a specific cart item.
     *
     * @return void
     */
    public function update(): void
    {
        $this->requireAuth();
        $this->requireValidCsrf();

        $productId    = (int) $this->request->post('product_id', 0);
        $newQuantity  = (int) $this->request->post('quantity', 1);

        $cartItems = $this->getCartItems();

        if (isset($cartItems[$productId])) {
            if ($newQuantity <= 0) {
                unset($cartItems[$productId]);
            } else {
                $cartItems[$productId]['quantity'] = $newQuantity;
            }
        }

        $this->saveCartItems($cartItems);
        $this->redirect('/cart');
    }

    /**
     * Remove a single item from the cart.
     *
     * @return void
     */
    public function remove(): void
    {
        $this->requireAuth();
        $this->requireValidCsrf();

        $productId = (int) $this->request->post('product_id', 0);
        $cartItems = $this->getCartItems();

        unset($cartItems[$productId]);

        $this->saveCartItems($cartItems);
        $this->redirectWithMessage('/cart', 'success', 'Item removed from cart.');
    }

    /**
     * Empty the entire cart.
     *
     * @return void
     */
    public function clear(): void
    {
        $_SESSION[self::SESSION_KEY] = [];
        $this->redirect('/cart');
    }

    /**
     * Retrieve cart items from the session.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCartItems(): array
    {
        return $_SESSION[self::SESSION_KEY] ?? [];
    }

    /**
     * Calculate the cart grand total.
     *
     * @param  array<int, array<string, mixed>>  $cartItems  Items from the session cart.
     *
     * @return float  Sum of (price × quantity) for all items.
     */
    public function calculateCartTotal(array $cartItems): float
    {
        return array_reduce(
            $cartItems,
            static fn(float $runningTotal, array $cartItem): float
                => $runningTotal + ($cartItem['price'] * $cartItem['quantity']),
            0.0
        );
    }

    /**
     * Persist updated cart items back to the session.
     *
     * @param  array<int, array<string, mixed>>  $cartItems  Updated cart items.
     *
     * @return void
     */
    private function saveCartItems(array $cartItems): void
    {
        $_SESSION[self::SESSION_KEY] = $cartItems;
    }
}
