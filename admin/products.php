<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Shop Products';
$activeNav = 'products';

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
$editProduct = null;

if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->bindValue(':id', $editId, PDO::PARAM_INT);
    $stmt->execute();
    $editProduct = $stmt->fetch(PDO::FETCH_ASSOC);
}

$products = $pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/includes/header.php';
?>

<section class="admin-panel">

    <div class="admin-panel-head">
        <h2><?= $editProduct ? 'Edit Product' : 'Add New Product' ?></h2>
        <?php if ($editProduct): ?>
            <a href="products.php" class="admin-link">Cancel edit</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="product-save.php" class="admin-form">
        <?= csrf_field() ?>
        <?php if ($editProduct): ?>
            <input type="hidden" name="id" value="<?= (int) $editProduct['id'] ?>">
        <?php endif; ?>

        <div class="admin-form-grid">

            <label>
                Product Slug (unique id)
                <input type="text" name="product_id" required maxlength="50"
                       pattern="[a-z0-9\-]+" title="Lowercase letters, numbers, and hyphens only"
                       placeholder="e.g. velvet-bloom"
                       value="<?= htmlspecialchars($editProduct['product_id'] ?? '') ?>">
            </label>

            <label>
                Name
                <input type="text" name="name" required maxlength="100"
                       value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>">
            </label>

            <label>
                Category
                <input type="text" name="category" required maxlength="50"
                       value="<?= htmlspecialchars($editProduct['category'] ?? '') ?>">
            </label>

            <label>
                Price (e.g. ₱2,500)
                <input type="text" name="price" required maxlength="20"
                       value="<?= htmlspecialchars($editProduct['price'] ?? '') ?>">
            </label>

            <label>
                Size (e.g. 50ml)
                <input type="text" name="size" required maxlength="20"
                       value="<?= htmlspecialchars($editProduct['size'] ?? '') ?>">
            </label>

            <label>
                Rating (1–5)
                <input type="number" name="rating" min="1" max="5" required
                       value="<?= htmlspecialchars((string) ($editProduct['rating'] ?? 5)) ?>">
            </label>

            <label>
                Stock
                <input type="number" name="stock" min="0" required
                       value="<?= htmlspecialchars((string) ($editProduct['stock'] ?? 0)) ?>">
            </label>

            <label>
                Image path
                <input type="text" name="image" required maxlength="255"
                       placeholder="images/products/product-1.jpg"
                       value="<?= htmlspecialchars($editProduct['image'] ?? '') ?>">
            </label>

        </div>

        <label>
            Description
            <textarea name="description" required maxlength="255" rows="2"><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea>
        </label>

        <button type="submit" class="btn btn-primary"><?= $editProduct ? 'Save Changes' : 'Add Product' ?></button>
    </form>

</section>

<section class="admin-panel">

    <div class="admin-panel-head">
        <h2>All Products (<?= count($products) ?>)</h2>
    </div>

    <?php if (empty($products)): ?>
        <p class="admin-empty">No products yet. Add your first one above.</p>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Rating</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><img src="../<?= htmlspecialchars($p['image']) ?>" alt="" class="admin-thumb"></td>
                            <td><?= htmlspecialchars($p['name']) ?><br><small class="admin-muted"><?= htmlspecialchars($p['product_id']) ?></small></td>
                            <td><?= htmlspecialchars($p['category']) ?></td>
                            <td><?= htmlspecialchars($p['price']) ?></td>
                            <td>
                                <?= (int) $p['stock'] ?>
                                <?php if ((int) $p['stock'] <= 5): ?>
                                    <span class="status-pill status-low">Low</span>
                                <?php endif; ?>
                            </td>
                            <td><?= (int) $p['rating'] ?>★</td>
                            <td class="admin-row-actions">
                                <a href="products.php?edit=<?= (int) $p['id'] ?>" class="admin-link">Edit</a>
                                <form method="POST" action="product-delete.php" onsubmit="return confirm('Delete this product? This cannot be undone.');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                    <button type="submit" class="admin-link admin-link-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
