<?php

declare(strict_types=1);

namespace App\Models;

use RuntimeException;

/**
 * Order model — manages orders and their line items atomically.
 */
class Order extends BaseModel
{
    protected string $table = 'orders';

    /**
     * Create a new order with its line items inside a transaction.
     *
     * @param  int                           $userId           Buyer's user ID.
     * @param  float                         $totalAmount      Pre-calculated order total.
     * @param  string                        $shippingAddress  Delivery address snapshot.
     * @param  array<int, array{product_id: int, quantity: int, unit_price: float}>  $lineItems  Items to purchase.
     *
     * @return int  The new order's primary key.
     *
     * @throws RuntimeException  If the transaction fails.
     */
    public function createWithItems(
        int $userId,
        float $totalAmount,
        string $shippingAddress,
        array $lineItems
    ): int {
        $pdo = $this->db();
        $pdo->beginTransaction();

        try {
            $insertOrder = $pdo->prepare(
                'INSERT INTO orders (user_id, total_amount, shipping_address)
                 VALUES (?, ?, ?)'
            );
            $insertOrder->execute([$userId, $totalAmount, $shippingAddress]);
            $orderId = (int) $pdo->lastInsertId();

            $insertItem = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, unit_price)
                 VALUES (?, ?, ?, ?)'
            );

            $decreaseStock = $pdo->prepare(
                'UPDATE products
                 SET stock_quantity = stock_quantity - ?
                 WHERE id = ? AND stock_quantity >= ?'
            );

            foreach ($lineItems as $lineItem) {
                $insertItem->execute([
                    $orderId,
                    $lineItem['product_id'],
                    $lineItem['quantity'],
                    $lineItem['unit_price'],
                ]);

                $decreaseStock->execute([
                    $lineItem['quantity'],
                    $lineItem['product_id'],
                    $lineItem['quantity'],
                ]);

                if ($decreaseStock->rowCount() === 0) {
                    throw new RuntimeException(
                        "Insufficient stock for product ID {$lineItem['product_id']}."
                    );
                }
            }

            $pdo->commit();

            return $orderId;
        } catch (\Throwable $throwable) {
            $pdo->rollBack();
            throw new RuntimeException('Order creation failed: ' . $throwable->getMessage(), 0, $throwable);
        }
    }

    /**
     * Return all orders for a specific customer, newest first.
     *
     * @param  int  $userId  Customer's user ID.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByUser(int $userId): array
    {
        return $this->query(
            'SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC',
            [$userId]
        );
    }

    /**
     * Return all orders with buyer names, newest first.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllWithUsers(): array
    {
        return $this->query(
            'SELECT o.*, u.full_name, u.email_address
             FROM orders o
             JOIN users u ON u.id = o.user_id
             ORDER BY o.created_at DESC'
        );
    }

    /**
     * Return the line items for an order, including product names.
     *
     * @param  int  $orderId  Order primary key.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getOrderItems(int $orderId): array
    {
        return $this->query(
            'SELECT oi.*, p.name AS product_name, p.product_image
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?',
            [$orderId]
        );
    }

    /**
     * Update the fulfillment status of an order.
     *
     * @param  int     $orderId    Order primary key.
     * @param  string  $newStatus  One of: pending | confirmed | shipped | delivered | cancelled.
     *
     * @return bool  True on success.
     */
    public function updateStatus(int $orderId, string $newStatus): bool
    {
        return $this->update($orderId, ['status' => $newStatus]);
    }
}
