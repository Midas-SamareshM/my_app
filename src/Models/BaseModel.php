<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;
use PDOStatement;

/**
 * Abstract base model providing generic CRUD for a single DB table.
 * Extend this class and set $table (and optionally $primaryKey).
 */
abstract class BaseModel
{
    protected string $table;
    protected string $primaryKey = 'id';

    /**
     * Return the shared PDO connection.
     *
     * @return PDO
     */
    protected function db(): PDO
    {
        return Database::getInstance();
    }

    /**
     * Find a single row by primary key.
     *
     * @param  int  $recordId  Primary key value.
     *
     * @return array<string, mixed>|null  Row data, or null if not found.
     */
    public function findById(int $recordId): ?array
    {
        $statement = $this->db()->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1"
        );
        $statement->execute([$recordId]);

        $result = $statement->fetch();

        return $result !== false ? $result : null;
    }

    /**
     * Return all rows from the table.
     *
     * @param  string  $orderBy  Column and direction for ordering (e.g. 'created_at DESC').
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(string $orderBy = 'id ASC'): array
    {
        $statement = $this->db()->query("SELECT * FROM {$this->table} ORDER BY {$orderBy}");

        return $statement->fetchAll();
    }

    /**
     * Insert a new row and return its auto-increment ID.
     *
     * @param  array<string, mixed>  $columnData  Associative array of column => value.
     *
     * @return int  The new row's primary key value.
     */
    public function create(array $columnData): int
    {
        $columnNames   = implode(', ', array_keys($columnData));
        $placeholders  = implode(', ', array_fill(0, count($columnData), '?'));

        $statement = $this->db()->prepare(
            "INSERT INTO {$this->table} ({$columnNames}) VALUES ({$placeholders})"
        );
        $statement->execute(array_values($columnData));

        return (int) $this->db()->lastInsertId();
    }

    /**
     * Update a row by primary key.
     *
     * @param  int                   $recordId    Primary key value.
     * @param  array<string, mixed>  $columnData  Columns and their new values.
     *
     * @return bool  True on success.
     */
    public function update(int $recordId, array $columnData): bool
    {
        $setClauses = implode(', ', array_map(
            static fn(string $column): string => "{$column} = ?",
            array_keys($columnData)
        ));

        $statement = $this->db()->prepare(
            "UPDATE {$this->table} SET {$setClauses} WHERE {$this->primaryKey} = ?"
        );

        $bindValues   = array_values($columnData);
        $bindValues[] = $recordId;

        return $statement->execute($bindValues);
    }

    /**
     * Delete a row by primary key.
     *
     * @param  int  $recordId  Primary key value.
     *
     * @return bool  True on success.
     */
    public function delete(int $recordId): bool
    {
        $statement = $this->db()->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?"
        );

        return $statement->execute([$recordId]);
    }

    /**
     * Count all rows in the table.
     *
     * @return int  Total row count.
     */
    public function count(): int
    {
        $statement = $this->db()->query("SELECT COUNT(*) FROM {$this->table}");

        return (int) $statement->fetchColumn();
    }

    /**
     * Run a prepared SELECT query and return all matching rows.
     *
     * @param  string              $sql     Full SQL query with ? placeholders.
     * @param  array<int, mixed>   $params  Ordered bind values.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function query(string $sql, array $params = []): array
    {
        $statement = $this->db()->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    /**
     * Run a prepared SELECT query and return only the first row.
     *
     * @param  string             $sql     Full SQL query with ? placeholders.
     * @param  array<int, mixed>  $params  Ordered bind values.
     *
     * @return array<string, mixed>|null  First row, or null if none.
     */
    protected function queryOne(string $sql, array $params = []): ?array
    {
        $statement = $this->db()->prepare($sql);
        $statement->execute($params);

        $result = $statement->fetch();

        return $result !== false ? $result : null;
    }
}
