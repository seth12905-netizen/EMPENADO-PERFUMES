<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Special Collections';
$activeNav = 'collections';

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
$editItem = null;

if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM collections WHERE id = :id');
    $stmt->bindValue(':id', $editId, PDO::PARAM_INT);
    $stmt->execute();
    $editItem = $stmt->fetch(PDO::FETCH_ASSOC);
}

$collections = $pdo->query('SELECT * FROM collections ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/includes/header.php';
?>

<section class="admin-panel">

    <div class="admin-panel-head">
        <h2><?= $editItem ? 'Edit Collection' : 'Add New Collection' ?></h2>
        <?php if ($editItem): ?>
            <a href="collections.php" class="admin-link">Cancel edit</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="collection-save.php" class="admin-form">
        <?= csrf_field() ?>
        <?php if ($editItem): ?>
            <input type="hidden" name="id" value="<?= (int) $editItem['id'] ?>">
        <?php endif; ?>

        <p class="admin-form-section-label">Collection Card</p>
        <div class="admin-form-grid">
            <label>
                Collection Name
                <input type="text" name="name" required maxlength="100"
                       placeholder="e.g. Signature Perfume"
                       value="<?= htmlspecialchars($editItem['name'] ?? '') ?>">
            </label>

            <label>
                Card Image path
                <input type="text" name="image" required maxlength="255"
                       placeholder="images/collection/collection-1.jpg"
                       value="<?= htmlspecialchars($editItem['image'] ?? '') ?>">
            </label>
        </div>
        <label>
            Card Description
            <textarea name="description" required maxlength="255" rows="2"><?= htmlspecialchars($editItem['description'] ?? '') ?></textarea>
        </label>

        <p class="admin-form-section-label">Underlying Product (shown in "View Details")</p>
        <div class="admin-form-grid">
            <label>
                Product Slug (unique id)
                <input type="text" name="product_id" required maxlength="50"
                       pattern="[a-z0-9\-]+" title="Lowercase letters, numbers, and hyphens only"
                       placeholder="e.g. amber-royale"
                       value="<?= htmlspecialchars($editItem['product_id'] ?? '') ?>">
            </label>

            <label>
                Product Name
                <input type="text" name="product_name" required maxlength="100"
                       value="<?= htmlspecialchars($editItem['product_name'] ?? '') ?>">
            </label>

            <label>
                Category
                <input type="text" name="product_category" required maxlength="50"
                       value="<?= htmlspecialchars($editItem['product_category'] ?? '') ?>">
            </label>

            <label>
                Price (e.g. ₱2,800)
                <input type="text" name="product_price" required maxlength="20"
                       value="<?= htmlspecialchars($editItem['product_price'] ?? '') ?>">
            </label>

            <label>
                Size (e.g. 50ml)
                <input type="text" name="product_size" required maxlength="20"
                       value="<?= htmlspecialchars($editItem['product_size'] ?? '') ?>">
            </label>

            <label>
                Rating (1–5)
                <input type="number" name="product_rating" min="1" max="5" required
                       value="<?= htmlspecialchars((string) ($editItem['product_rating'] ?? 5)) ?>">
            </label>

            <label>
                Stock
                <input type="number" name="stock" min="0" required
                       value="<?= htmlspecialchars((string) ($editItem['stock'] ?? 0)) ?>">
            </label>

            <label>
                Product Image path
                <input type="text" name="product_image" required maxlength="255"
                       value="<?= htmlspecialchars($editItem['product_image'] ?? '') ?>">
            </label>
        </div>
        <label>
            Product Description
            <textarea name="product_description" required maxlength="255" rows="2"><?= htmlspecialchars($editItem['product_description'] ?? '') ?></textarea>
        </label>

        <button type="submit" class="btn btn-primary"><?= $editItem ? 'Save Changes' : 'Add Collection' ?></button>
    </form>

</section>

<section class="admin-panel">

    <div class="admin-panel-head">
        <h2>All Collections (<?= count($collections) ?>)</h2>
    </div>

    <?php if (empty($collections)): ?>
        <p class="admin-empty">No collections yet. Add your first one above.</p>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Collection</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($collections as $c): ?>
                        <tr>
                            <td><img src="../<?= htmlspecialchars($c['image']) ?>" alt="" class="admin-thumb"></td>
                            <td><?= htmlspecialchars($c['name']) ?></td>
                            <td><?= htmlspecialchars($c['product_name']) ?><br><small class="admin-muted"><?= htmlspecialchars($c['product_id']) ?></small></td>
                            <td><?= htmlspecialchars($c['product_price']) ?></td>
                            <td>
                                <?= (int) $c['stock'] ?>
                                <?php if ((int) $c['stock'] <= 5): ?>
                                    <span class="status-pill status-low">Low</span>
                                <?php endif; ?>
                            </td>
                            <td class="admin-row-actions">
                                <a href="collections.php?edit=<?= (int) $c['id'] ?>" class="admin-link">Edit</a>
                                <form method="POST" action="collection-delete.php" onsubmit="return confirm('Delete this collection? This cannot be undone.');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
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
