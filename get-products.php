<?php

header('Content-Type: application/json');

require 'database/config.php';

try {
    $pdo = getConnection();

    $stmt = $pdo->query(
        "SELECT product_id, name, category, description, price, rating, size, image, stock
         FROM products
         ORDER BY id ASC"
    );

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $products = array_map(function ($row) {
        return [
            'id'    => $row['product_id'],
            'name'  => $row['name'],
            'cat'   => $row['category'],
            'desc'  => $row['description'],
            'price' => $row['price'],
            'rating'=> (int) $row['rating'],
            'size'  => $row['size'],
            'img'   => $row['image'],
            'stock' => (int) $row['stock'],
        ];
    }, $rows);

    echo json_encode($products);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to load products.']);
}
