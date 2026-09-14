<?php

session_start();
header('Content-Type: application/json');

require 'database/config.php';

// Checkout requires a logged-in account so the order can be tied to a user.
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success'  => false,
        'message'  => 'Please log in to check out.',
        'redirect' => 'login.php',
    ]);
    exit;
}

$raw   = file_get_contents('php://input');
$input = json_decode($raw, true);
$items = $input['items'] ?? [];

$validPaymentMethods = ['cod', 'gcash', 'card'];
$paymentMethod = $input['payment_method'] ?? 'cod';
if (!in_array($paymentMethod, $validPaymentMethods, true)) {
    $paymentMethod = 'cod';
}

if (!is_array($items) || empty($items)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
    exit;
}

function parsePeso($value): float
{
    return (float) preg_replace('/[^0-9.]/', '', (string) $value);
}

// Builds a GCash-style reference number, e.g. GC-20260913-4F8B2C.
// This is a stand-in reference for the order (not a real GCash
// transaction ID from GCash's own systems) so the customer has
// something to write on their payment / quote when they send
// money, and staff have something to match it against.
function generatePaymentReference(PDO $pdo, string $paymentMethod): ?string
{
    if ($paymentMethod !== 'gcash') {
        return null;
    }

    $prefix = 'GC';

    do {
        $reference = sprintf(
            '%s-%s-%s',
            $prefix,
            date('Ymd'),
            strtoupper(bin2hex(random_bytes(3)))
        );

        $check = $pdo->prepare('SELECT 1 FROM orders WHERE payment_reference = :ref');
        $check->execute([':ref' => $reference]);
    } while ($check->fetchColumn() !== false); // extremely unlikely, but guarantee uniqueness

    return $reference;
}

try {
    $pdo = getConnection();

    // Look up the real name, price, and stock for every product from the
    // database instead of trusting whatever the browser sent. Without
    // this, a request could be crafted (e.g. via the browser console) to
    // check out at any price the client wants — the server has to be the
    // source of truth for money.
    $productLookup = [];

    foreach ($pdo->query('SELECT product_id, name, price, stock FROM products') as $row) {
        $productLookup[$row['product_id']] = [
            'source' => 'products',
            'name'   => $row['name'],
            'price'  => parsePeso($row['price']),
            'stock'  => (int) $row['stock'],
        ];
    }

    foreach ($pdo->query('SELECT product_id, product_name AS name, product_price AS price, stock FROM collections') as $row) {
        $productLookup[$row['product_id']] = [
            'source' => 'collections',
            'name'   => $row['name'],
            'price'  => parsePeso($row['price']),
            'stock'  => (int) $row['stock'],
        ];
    }

    $cleanItems = [];
    $total = 0;

    foreach ($items as $item) {
        $productId = trim((string) ($item['id'] ?? ''));
        $qty       = (int) ($item['qty'] ?? 0);

        if ($productId === '' || $qty <= 0 || !isset($productLookup[$productId])) {
            continue;
        }

        $product = $productLookup[$productId];

        if ($qty > $product['stock']) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => "Sorry, only {$product['stock']} of \"{$product['name']}\" left in stock.",
            ]);
            exit;
        }

        $subtotal = $product['price'] * $qty;
        $total += $subtotal;

        $cleanItems[] = [
            'product_id'   => $productId,
            'product_name' => $product['name'],
            'price'        => $product['price'],
            'quantity'     => $qty,
            'subtotal'     => $subtotal,
            'source'       => $product['source'],
        ];
    }

    if (empty($cleanItems)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
        exit;
    }

    $pdo->beginTransaction();

    // Only GCash orders get an automated reference number.
    $paymentReference = generatePaymentReference($pdo, $paymentMethod);

    $orderStmt = $pdo->prepare(
        "INSERT INTO orders (user_id, total, payment_method, payment_reference, status)
         VALUES (:user_id, :total, :payment_method, :payment_reference, 'pending')"
    );
    $orderStmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $orderStmt->bindValue(':total', $total);
    $orderStmt->bindValue(':payment_method', $paymentMethod);
    $orderStmt->bindValue(
        ':payment_reference',
        $paymentReference,
        $paymentReference === null ? PDO::PARAM_NULL : PDO::PARAM_STR
    );
    $orderStmt->execute();

    $orderId = (int) $pdo->lastInsertId();

    $itemStmt = $pdo->prepare(
        "INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal)
         VALUES (:order_id, :product_id, :product_name, :price, :quantity, :subtotal)"
    );

    $decrementProductStock    = $pdo->prepare('UPDATE products SET stock = stock - :qty WHERE product_id = :pid AND stock >= :qty2');
    $decrementCollectionStock = $pdo->prepare('UPDATE collections SET stock = stock - :qty WHERE product_id = :pid AND stock >= :qty2');

    foreach ($cleanItems as $item) {
        $itemStmt->execute([
            ':order_id'      => $orderId,
            ':product_id'    => $item['product_id'],
            ':product_name'  => $item['product_name'],
            ':price'         => $item['price'],
            ':quantity'      => $item['quantity'],
            ':subtotal'      => $item['subtotal'],
        ]);

        $decrementStmt = $item['source'] === 'products' ? $decrementProductStock : $decrementCollectionStock;
        $decrementStmt->execute([
            ':qty'  => $item['quantity'],
            ':pid'  => $item['product_id'],
            ':qty2' => $item['quantity'],
        ]);

        if ($decrementStmt->rowCount() === 0) {
            // Someone else bought the last of the stock between our check
            // above and this update — roll back the whole order rather
            // than oversell.
            throw new RuntimeException("Out of stock: {$item['product_name']}");
        }
    }

    $pdo->commit();

    $message = "Order #{$orderId} placed! Thank you for shopping with Empenado.";
    if ($paymentReference !== null) {
        $message .= " Your GCash reference number is {$paymentReference} — please include it in your payment note.";
    }

    echo json_encode([
        'success'           => true,
        'order_id'          => $orderId,
        'total'             => $total,
        'payment_method'    => $paymentMethod,
        'payment_reference' => $paymentReference,
        'message'           => $message,
    ]);
} catch (RuntimeException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => $e->getMessage() . '. Please update your cart and try again.']);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong placing your order. Please try again.']);
}
