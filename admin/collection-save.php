<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
    header('Location: collections.php?status=error&message=' . urlencode('Invalid request. Please try again.'));
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : null;

$name               = trim($_POST['name'] ?? '');
$description        = trim($_POST['description'] ?? '');
$image              = trim($_POST['image'] ?? '');
$productId          = trim($_POST['product_id'] ?? '');
$productName        = trim($_POST['product_name'] ?? '');
$productCategory    = trim($_POST['product_category'] ?? '');
$productDescription = trim($_POST['product_description'] ?? '');
$productPrice       = trim($_POST['product_price'] ?? '');
$productRating      = (int) ($_POST['product_rating'] ?? 5);
$productSize        = trim($_POST['product_size'] ?? '');
$productImage       = trim($_POST['product_image'] ?? '');
$stock              = (int) ($_POST['stock'] ?? 0);

$errors = [];
if ($name === '')               $errors[] = 'Collection name is required.';
if ($description === '')        $errors[] = 'Collection description is required.';
if ($image === '')              $errors[] = 'Card image path is required.';
if ($productId === '' || !preg_match('/^[a-z0-9\-]+$/', $productId)) {
    $errors[] = 'Product slug must contain only lowercase letters, numbers, and hyphens.';
}
if ($productName === '')        $errors[] = 'Product name is required.';
if ($productCategory === '')    $errors[] = 'Category is required.';
if ($productDescription === '') $errors[] = 'Product description is required.';
if ($productPrice === '')       $errors[] = 'Price is required.';
if ($productSize === '')        $errors[] = 'Size is required.';
if ($productImage === '')       $errors[] = 'Product image path is required.';
if ($productRating < 1 || $productRating > 5) $errors[] = 'Rating must be between 1 and 5.';
if ($stock < 0)                  $errors[] = 'Stock cannot be negative.';

if (!empty($errors)) {
    $redirect = $id ? "collections.php?edit={$id}" : 'collections.php';
    header('Location: ' . $redirect . '&status=error&message=' . urlencode(implode(' ', $errors)));
    exit;
}

try {
    if ($id) {
        $sql = "UPDATE collections SET
                    name = :name, description = :description, image = :image,
                    product_id = :product_id, product_name = :product_name,
                    product_category = :product_category, product_description = :product_description,
                    product_price = :product_price, product_rating = :product_rating,
                    product_size = :product_size, product_image = :product_image, stock = :stock
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    } else {
        $sql = "INSERT INTO collections
                    (name, description, image, product_id, product_name, product_category,
                     product_description, product_price, product_rating, product_size, product_image, stock)
                VALUES
                    (:name, :description, :image, :product_id, :product_name, :product_category,
                     :product_description, :product_price, :product_rating, :product_size, :product_image, :stock)";
        $stmt = $pdo->prepare($sql);
    }

    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':image', $image);
    $stmt->bindValue(':product_id', $productId);
    $stmt->bindValue(':product_name', $productName);
    $stmt->bindValue(':product_category', $productCategory);
    $stmt->bindValue(':product_description', $productDescription);
    $stmt->bindValue(':product_price', $productPrice);
    $stmt->bindValue(':product_rating', $productRating, PDO::PARAM_INT);
    $stmt->bindValue(':product_size', $productSize);
    $stmt->bindValue(':product_image', $productImage);
    $stmt->bindValue(':stock', $stock, PDO::PARAM_INT);
    $stmt->execute();

    $verb = $id ? 'updated' : 'added';
    header('Location: collections.php?status=success&message=' . urlencode("Collection {$verb}."));
    exit;
} catch (PDOException $e) {
    $message = ((int) $e->getCode() === 23000)
        ? 'That product slug is already in use.'
        : 'Something went wrong saving the collection.';
    $redirect = $id ? "collections.php?edit={$id}" : 'collections.php';
    header('Location: ' . $redirect . '&status=error&message=' . urlencode($message));
    exit;
}
