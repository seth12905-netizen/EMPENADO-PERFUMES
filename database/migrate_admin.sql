-- =========================================================
-- EMPENADO PERFUMES — Admin Feature Migration
--
-- Run this ONLY if you already imported the old
-- empenado_db.sql and have real data (users, orders, etc.)
-- that you don't want to lose. It safely adds everything the
-- admin dashboard needs on top of what you already have.
--
-- If you're starting fresh, just import empenado_db.sql
-- instead — it already includes all of this.
--
-- Requires MySQL 8.0+ / MariaDB 10.5+ (for "ADD COLUMN IF
-- NOT EXISTS"). If your server is older, drop the
-- "IF NOT EXISTS" from each line below.
-- =========================================================

USE empenado_perfumes;

-- Give users a role so an account can be marked as admin
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer' AFTER password;

-- Add phone number to existing accounts. Defaults to '' since
-- existing users never entered one — have them update it from
-- their account page, or fill it in yourself in phpMyAdmin.
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS phone VARCHAR(20) NOT NULL DEFAULT '' AFTER email;

-- Track which contact messages the admin has already read
ALTER TABLE messages
    ADD COLUMN IF NOT EXISTS is_read TINYINT(1) NOT NULL DEFAULT 0 AFTER message;

-- Track stock on Special Collections products
ALTER TABLE collections
    ADD COLUMN IF NOT EXISTS stock INT NOT NULL DEFAULT 0 AFTER product_image;

-- Shop products used to be hardcoded in js/script.js. This
-- table lets the admin dashboard manage them like Collections.
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

INSERT IGNORE INTO products
    (product_id, name, category, description, price, rating, size, image, stock)
VALUES
    ('velvet-bloom', 'Velvet Bloom', 'Floral', 'Peony, rose absolute, soft musk.', '₱3,500', 5, '75ml', 'images/products/product-1.jpg', 25),
    ('cedar-smoke', 'Cedar & Smoke', 'Woody', 'Cedarwood, vetiver, black amber.', '₱3,000', 4, '50ml', 'images/products/product-2.jpg', 25),
    ('linen-air', 'Linen Air', 'Fresh', 'Sea salt, bergamot, white musk.', '₱2,200', 5, '50ml', 'images/products/product-3.jpg', 25),
    ('amber-dusk', 'Amber Dusk', 'Oriental', 'Amber resin, saffron, tonka bean.', '₱2,000', 5, '30ml', 'images/products/product-4.jpg', 25),
    ('citrus-grove', 'Citrus Grove', 'Citrus', 'Blood orange, neroli, green tea.', '₱2,600', 4, '50ml', 'images/products/product-5.jpg', 25),
    ('vanilla-noir', 'Vanilla Noir', 'Vanilla', 'Madagascar vanilla, oak, dark musk.', '₱4,300', 5, '100ml', 'images/products/product-6.jpg', 25);

-- =========================================================
-- Make yourself an admin (replace with your real email):
--
-- UPDATE users SET role = 'admin' WHERE email = 'you@example.com';
-- =========================================================
