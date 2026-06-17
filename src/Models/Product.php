<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Uploadable;

/**
 * Product model with category join and image upload support.
 */
class Product extends BaseModel
{
    use Uploadable;

    protected string $table = 'products';

    /**
     * Return all active products with their category names.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllWithCategory(): array
    {
        return $this->query(
            'SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON c.id = p.category_id
             ORDER BY p.created_at DESC'
        );
    }

    /**
     * Return only active (purchasable) products with category names.
     *
     * @param  int|null  $categoryId  Filter by category, or null for all categories.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getActiveProducts(?int $categoryId = null): array
    {
        if ($categoryId !== null) {
            return $this->query(
                'SELECT p.*, c.name AS category_name
                 FROM products p
                 JOIN categories c ON c.id = p.category_id
                 WHERE p.is_active = 1 AND p.category_id = ?
                 ORDER BY p.name ASC',
                [$categoryId]
            );
        }

        return $this->query(
            'SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.is_active = 1
             ORDER BY p.name ASC'
        );
    }

    /**
     * Find a product by its URL slug, including category name.
     *
     * @param  string  $slug  URL slug (e.g. 'wireless-headphones').
     *
     * @return array<string, mixed>|null  Product row, or null if not found.
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->queryOne(
            'SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.slug = ? LIMIT 1',
            [$slug]
        );
    }

    /**
     * Decrease stock for a product after a successful purchase.
     *
     * @param  int  $productId      Product primary key.
     * @param  int  $quantitySold   Units sold.
     *
     * @return bool  True on success.
     */
    public function decreaseStock(int $productId, int $quantitySold): bool
    {
        $statement = $this->db()->prepare(
            'UPDATE products
             SET stock_quantity = stock_quantity - ?
             WHERE id = ? AND stock_quantity >= ?'
        );

        return $statement->execute([$quantitySold, $productId, $quantitySold]);
    }
}
