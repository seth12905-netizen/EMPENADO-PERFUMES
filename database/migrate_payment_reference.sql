-- =========================================================
-- EMPENADO PERFUMES — GCash Reference Number Migration
--
-- Run this ONLY if you already imported empenado_db.sql
-- and have real data (users, orders, etc.) that you don't
-- want to lose. It adds the column needed to store an
-- auto-generated GCash payment reference number.
--
-- If you're starting fresh, just import empenado_db.sql
-- instead — it already includes this column.
--
-- Requires MySQL 8.0+ / MariaDB 10.5+ (for "ADD COLUMN IF
-- NOT EXISTS"). If your server is older, drop the
-- "IF NOT EXISTS" from the line below.
-- =========================================================

USE empenado_perfumes;

-- Stores the automatically-generated GCash reference number
-- (e.g. GC-20260913-4F8B2C) so both the customer and the
-- admin dashboard can look up a payment by it. NULL for
-- orders paid by Cash on Delivery or Card.
ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS payment_reference VARCHAR(30) NULL AFTER payment_method;
