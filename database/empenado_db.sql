-- =========================================================
-- EMPENADO PERFUMES — Database Schema
-- Import this in phpMyAdmin / MySQL to create the database
-- and all tables used by the site (shop, login, admin, etc).
--
-- This is the full, up-to-date schema. If you already have
-- an older copy of this database and don't want to lose your
-- data, use database/migrate_admin.sql instead, which only
-- ALTERs/adds the new admin-related pieces.
-- =========================================================

CREATE DATABASE IF NOT EXISTS empenado_perfumes
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE empenado_perfumes;

-- =========================================================
-- Users (shoppers + admins)
-- `role` distinguishes a normal shopper from an admin who can
-- access /admin. There is no separate "admin table" — an admin
-- is just a user row with role = 'admin'.
-- =========================================================
CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    phone         VARCHAR(20)  NOT NULL,
    password      VARCHAR(255) NOT NULL,
    role          ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- =========================================================
-- Contact Us messages (used by the contact form on index.php)
-- =========================================================
CREATE TABLE IF NOT EXISTS messages (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(100) NOT NULL,
    message       TEXT         NOT NULL,
    is_read       TINYINT(1)   NOT NULL DEFAULT 0,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- =========================================================
-- Shop Products (the "Fragrance Library" grid on index.php).
-- These used to be a hardcoded array in js/script.js — they
-- now live here so the admin dashboard can actually manage
-- them, and the shop grid loads them via get-products.php the
-- same way Special Collections load via get-collections.php.
-- =========================================================
CREATE TABLE IF NOT EXISTS products (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    product_id    VARCHAR(50)   NOT NULL UNIQUE,
    name          VARCHAR(100)  NOT NULL,
    category      VARCHAR(50)   NOT NULL,
    description   VARCHAR(255)  NOT NULL,
    price         VARCHAR(20)   NOT NULL,
    rating        TINYINT       NOT NULL DEFAULT 5,
    size          VARCHAR(20)   NOT NULL,
    image         VARCHAR(255)  NOT NULL,
    stock         INT           NOT NULL DEFAULT 0,
    created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products
    (product_id, name, category, description, price, rating, size, image, stock)
VALUES
    ('velvet-bloom', 'Velvet Bloom', 'Floral', 'Peony, rose absolute, soft musk.', '₱3,500', 5, '75ml', 'images/products/product-1.jpg', 25),
    ('cedar-smoke', 'Cedar & Smoke', 'Woody', 'Cedarwood, vetiver, black amber.', '₱3,000', 4, '50ml', 'images/products/product-2.jpg', 25),
    ('linen-air', 'Linen Air', 'Fresh', 'Sea salt, bergamot, white musk.', '₱2,200', 5, '50ml', 'images/products/product-3.jpg', 25),
    ('amber-dusk', 'Amber Dusk', 'Oriental', 'Amber resin, saffron, tonka bean.', '₱2,000', 5, '30ml', 'images/products/product-4.jpg', 25),
    ('citrus-grove', 'Citrus Grove', 'Citrus', 'Blood orange, neroli, green tea.', '₱2,600', 4, '50ml', 'images/products/product-5.jpg', 25),
    ('vanilla-noir', 'Vanilla Noir', 'Vanilla', 'Madagascar vanilla, oak, dark musk.', '₱4,300', 5, '100ml', 'images/products/product-6.jpg', 25);

-- =========================================================
-- Special Collections (used by the "View Details" cards
-- under the Special Collections section on index.php)
-- =========================================================
CREATE TABLE IF NOT EXISTS collections (
    id                    INT AUTO_INCREMENT PRIMARY KEY,
    name                  VARCHAR(100) NOT NULL,
    description           VARCHAR(255) NOT NULL,
    image                 VARCHAR(255) NOT NULL,
    product_id            VARCHAR(50)  NOT NULL UNIQUE,
    product_name          VARCHAR(100) NOT NULL,
    product_category      VARCHAR(50)  NOT NULL,
    product_description   VARCHAR(255) NOT NULL,
    product_price         VARCHAR(20)  NOT NULL,
    product_rating        TINYINT      NOT NULL DEFAULT 5,
    product_size          VARCHAR(20)  NOT NULL,
    product_image         VARCHAR(255) NOT NULL,
    stock                 INT          NOT NULL DEFAULT 0,
    created_at            TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO collections
    (name, description, image, product_id, product_name, product_category, product_description, product_price, product_rating, product_size, product_image, stock)
VALUES
    ('Signature Perfume', 'The scents that started it all.', 'images/collection/collection-1.jpg',
     'amber-royale', 'Amber Royale', 'Oriental', 'Warm amber, spiced saffron, golden musk.', '₱2,800', 5, '50ml', 'images/collection/collection-1.jpg', 15),

    ('Luxury Perfume', 'Rare ingredients, limited pours.', 'images/collection/collection-2.jpg',
     'velvet-oud', 'Velvet Oud', 'Woody', 'Rare oud, velvet rose, dark honey.', '₱5,500', 5, '50ml', 'images/collection/collection-2.jpg', 15),

    ('Everyday Perfume', 'Light, wearable, all day scent.', 'images/collection/collection-3.jpg',
     'rose-divine', 'Rose Divine', 'Floral', 'Rose petals, soft musk, light citrus.', '₱1,800', 4, '50ml', 'images/collection/collection-3.jpg', 15),

    ('Limited Perfume', 'Seasonal blends, once available.', 'images/collection/collection-4.jpg',
     'citrus-luxe', 'Citrus Luxe', 'Citrus', 'Yuzu, mandarin, white tea.', '₱3,200', 5, '30ml', 'images/collection/collection-4.jpg', 15);

-- =========================================================
-- Orders (used by the "Checkout" button in the cart modal)
-- =========================================================
CREATE TABLE IF NOT EXISTS orders (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT           NOT NULL,
    total         DECIMAL(10,2) NOT NULL,
    status        VARCHAR(20)   NOT NULL DEFAULT 'pending',
    created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    order_id      INT           NOT NULL,
    product_id    VARCHAR(50)   NOT NULL,
    product_name  VARCHAR(100)  NOT NULL,
    price         DECIMAL(10,2) NOT NULL,
    quantity      INT           NOT NULL,
    subtotal      DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- =========================================================
-- To make yourself an admin after registering a normal
-- account on the site, run (with your own email):
--
-- UPDATE users SET role = 'admin' WHERE email = 'you@example.com';
-- =========================================================
