<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Category model for product grouping.
 */
class Category extends BaseModel
{
    protected string $table = 'categories';

    /**
     * Find a category by its URL slug.
     *
     * @param  string  $slug  URL slug (e.g. 'electronics').
     *
     * @return array<string, mixed>|null  Category row, or null if not found.
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->queryOne(
            'SELECT * FROM categories WHERE slug = ? LIMIT 1',
            [$slug]
        );
    }

    /**
     * Return all categories sorted alphabetically.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllSorted(): array
    {
        return $this->query('SELECT * FROM categories ORDER BY name ASC');
    }

    /**
     * Check whether a category can be safely deleted (no products attached).
     *
     * @param  int  $categoryId  Category primary key.
     *
     * @return bool  True when no products reference this category.
     */
    public function isDeletable(int $categoryId): bool
    {
        $statement = $this->db()->prepare(
            'SELECT COUNT(*) FROM products WHERE category_id = ?'
        );
        $statement->execute([$categoryId]);

        return (int) $statement->fetchColumn() === 0;
    }
}
