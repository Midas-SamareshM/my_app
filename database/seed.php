<?php

declare(strict_types=1);

/**
 * Seed script — run once from browser: http://localhost/my_app/database/seed.php
 * Creates the default admin account and sample data.
 */

$dbConfig = require __DIR__ . '/../config/database.php';

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    $dbConfig['host'],
    $dbConfig['dbname'],
    $dbConfig['charset']
);

$pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

echo "<pre>\n";

// -------------------------------------------------------
// Admin user
// -------------------------------------------------------
$adminEmail    = 'admin@ecom.com';
$adminPassword = 'Admin@123';

$existingAdmin = $pdo->prepare('SELECT id FROM users WHERE email_address = ?');
$existingAdmin->execute([$adminEmail]);

if (!$existingAdmin->fetch()) {
    $passwordHash = password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 12]);

    $insertAdmin = $pdo->prepare(
        'INSERT INTO users (full_name, email_address, password_hash, role)
         VALUES (?, ?, ?, ?)'
    );
    $insertAdmin->execute(['Admin User', $adminEmail, $passwordHash, 'admin']);
    echo "Admin created — email: {$adminEmail}  password: {$adminPassword}\n";
} else {
    echo "Admin already exists, skipping.\n";
}

// -------------------------------------------------------
// Categories
// -------------------------------------------------------
$categoryList = [
    ['Electronics', 'electronics', 'Gadgets, devices, and accessories'],
    ['Clothing',    'clothing',    'Fashion for men, women, and kids'],
    ['Books',       'books',       'Fiction, non-fiction, and textbooks'],
];

foreach ($categoryList as [$categoryName, $categorySlug, $categoryDesc]) {
    $existingCategory = $pdo->prepare('SELECT id FROM categories WHERE slug = ?');
    $existingCategory->execute([$categorySlug]);

    if (!$existingCategory->fetch()) {
        $insertCategory = $pdo->prepare(
            'INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)'
        );
        $insertCategory->execute([$categoryName, $categorySlug, $categoryDesc]);
        echo "Category created: {$categoryName}\n";
    } else {
        echo "Category '{$categoryName}' already exists, skipping.\n";
    }
}

// -------------------------------------------------------
// Sample products
// -------------------------------------------------------
$electronicsId = $pdo->query("SELECT id FROM categories WHERE slug = 'electronics'")->fetchColumn();
$clothingId    = $pdo->query("SELECT id FROM categories WHERE slug = 'clothing'")->fetchColumn();
$booksId       = $pdo->query("SELECT id FROM categories WHERE slug = 'books'")->fetchColumn();

$sampleProductList = [
    [$electronicsId, 'Wireless Headphones', 'wireless-headphones', 'High-quality noise-cancelling headphones.', 79.99,  50],
    [$electronicsId, 'USB-C Hub',           'usb-c-hub',           '7-in-1 USB-C hub with HDMI and SD card.',  29.99, 100],
    [$clothingId,    'Classic T-Shirt',     'classic-t-shirt',     '100% cotton unisex t-shirt.',              14.99, 200],
    [$clothingId,    'Denim Jacket',        'denim-jacket',        'Slim fit denim jacket — all seasons.',     49.99,  75],
    [$booksId,       'Clean Code',          'clean-code',          'A handbook of agile software craftsmanship.', 34.99, 30],
];

foreach ($sampleProductList as [$categoryId, $productName, $productSlug, $productDesc, $productPrice, $stockQty]) {
    $existingProduct = $pdo->prepare('SELECT id FROM products WHERE slug = ?');
    $existingProduct->execute([$productSlug]);

    if (!$existingProduct->fetch()) {
        $insertProduct = $pdo->prepare(
            'INSERT INTO products (category_id, name, slug, description, price, stock_quantity)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $insertProduct->execute([$categoryId, $productName, $productSlug, $productDesc, $productPrice, $stockQty]);
        echo "Product created: {$productName}\n";
    } else {
        echo "Product '{$productName}' already exists, skipping.\n";
    }
}

echo "\nSeeding complete.\n</pre>";
