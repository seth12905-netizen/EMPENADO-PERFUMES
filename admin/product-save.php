<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
    header('Location: products.php?status=error&message=' . urlencode('Invalid request. Please try again.'));
    exit;
}

$id         = isset($_POST['id']) ? (int) $_POST['id'] : null;
$productId  = trim($_POST['product_id'] ?? '');
$name       = trim($_POST['name'] ?? '');
$category   = trim($_POST['category'] ?? '');
$description = trim($_POST['description'] ?? '');
$price      = trim($_POST['price'] ?? '');
$rating     = (int) ($_POST['rating'] ?? 5);
$size       = trim($_POST['size'] ?? '');
$image      = trim($_POST['image'] ?? '');
$stock      = (int) ($_POST['stock'] ?? 0);

$errors = [];
if ($productId === '' || !preg_match('/^[a-z0-9\-]+$/', $productId)) {
    $errors[] = 'Product slug must contain only lowercase letters, numbers, and hyphens.';
}
if ($name === '')        $errors[] = 'Name is required.';
if ($category === '')    $errors[] = 'Category is required.';
if ($description === '') $errors[] = 'Description is required.';
if ($price === '')       $errors[] = 'Price is required.';
if ($size === '')        $errors[] = 'Size is required.';
if ($image === '')       $errors[] = 'Image path is required.';
if ($rating < 1 || $rating > 5) $errors[] = 'Rating must be between 1 and 5.';
if ($stock < 0)           $errors[] = 'Stock cannot be negative.';

if (!empty($errors)) {
    $redirect = $id ? "products.php?edit={$id}" : 'products.php';
    header('Location: ' . $redirect . '&status=error&message=' . urlencode(implode(' ', $errors)));
    exit;
}

try {
    if ($id) {
        $sql = "UPDATE products SET
                    product_id = :product_id, name = :name, category = :category,
                    description = :description, price = :price, rating = :rating,
                    size = :size, image = :image, stock = :stock
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    } else {
        $sql = "INSERT INTO products
                    (product_id, name, category, description, price, rating, size, image, stock)
                VALUES
                    (:product_id, :name, :category, :description, :price, :rating, :size, :image, :stock)";
        $stmt = $pdo->prepare($sql);
    }

    $stmt->bindValue(':product_id', $productId);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':category', $category);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':price', $price);
    $stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
    $stmt->bindValue(':size', $size);
    $stmt->bindValue(':image', $image);
    $stmt->bindValue(':stock', $stock, PDO::PARAM_INT);
    $stmt->execute();

    $verb = $id ? 'updated' : 'added';
    header('Location: products.php?status=success&message=' . urlencode("Product {$verb}."));
    exit;
} catch (PDOException $e) {
    $message = ((int) $e->getCode() === 23000)
        ? 'That product slug is already in use.'
        : 'Something went wrong saving the product.';
    $redirect = $id ? "products.php?edit={$id}" : 'products.php';
    header('Location: ' . $redirect . '&status=error&message=' . urlencode($message));
    exit;
}
