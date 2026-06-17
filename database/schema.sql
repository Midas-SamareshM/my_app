-- -------------------------------------------------------
-- ecom_platform database schema
-- Run this file once in phpMyAdmin or via MySQL CLI
-- -------------------------------------------------------

CREATE DATABASE IF NOT EXISTS ecom_platform
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ecom_platform;

-- -------------------------------------------------------
-- Users (both admin and customers)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    full_name        VARCHAR(100)     NOT NULL,
    email_address    VARCHAR(150)     NOT NULL UNIQUE,
    password_hash    VARCHAR(255)     NOT NULL,
    phone_number     VARCHAR(20)          NULL DEFAULT NULL,
    delivery_address TEXT                 NULL DEFAULT NULL,
    profile_image    VARCHAR(255)         NULL DEFAULT NULL,
    role             ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    is_active        TINYINT(1)       NOT NULL DEFAULT 1,
    created_at       TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Product categories
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100)  NOT NULL,
    slug        VARCHAR(100)  NOT NULL UNIQUE,
    description TEXT              NULL DEFAULT NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Products
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    category_id    INT UNSIGNED     NOT NULL,
    name           VARCHAR(200)     NOT NULL,
    slug           VARCHAR(200)     NOT NULL UNIQUE,
    description    TEXT                 NULL DEFAULT NULL,
    price          DECIMAL(10,2)    NOT NULL,
    stock_quantity INT UNSIGNED     NOT NULL DEFAULT 0,
    product_image  VARCHAR(255)         NULL DEFAULT NULL,
    is_active      TINYINT(1)       NOT NULL DEFAULT 1,
    created_at     TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Customer orders
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id          INT UNSIGNED NOT NULL,
    total_amount     DECIMAL(10,2) NOT NULL,
    status           ENUM('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
    shipping_address TEXT         NOT NULL,
    created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Line items per order
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
    id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    order_id   INT UNSIGNED  NOT NULL,
    product_id INT UNSIGNED  NOT NULL,
    quantity   INT UNSIGNED  NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
