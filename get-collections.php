<?php

header('Content-Type: application/json');

require 'database/config.php';

try {
    $pdo = getConnection();

    $stmt = $pdo->query(
        "SELECT id, name, description, image,
                product_id, product_name, product_category,
                product_description, product_price, product_rating,
                product_size, product_image
         FROM collections
         ORDER BY id ASC"
    );

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $collections = array_map(function ($row) {
        return [
            'id'   => (int) $row['id'],
            'name' => $row['name'],
            'desc' => $row['description'],
            'img'  => $row['image'],
            'product' => [
                'id'     => $row['product_id'],
                'name'   => $row['product_name'],
                'cat'    => $row['product_category'],
                'desc'   => $row['product_description'],
                'price'  => $row['product_price'],
                'rating' => (int) $row['product_rating'],
                'size'   => $row['product_size'],
                'img'    => $row['product_image'],
            ],
        ];
    }, $rows);

    echo json_encode($collections);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to load collections.']);
}
