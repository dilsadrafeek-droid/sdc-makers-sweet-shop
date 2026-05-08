<?php
require_once __DIR__ . '/includes/header.php';

// Fetch categories for filter
$cats_stmt = $pdo->query("SELECT * FROM categories");
$categories = $cats_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle category filter
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

if ($category_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY id DESC");
    $stmt->execute([$category_id]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
}
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="text-align: center; margin-bottom: 2rem;">
    <h1>Our Sweet Collection</h1>
    <p>Discover our wide range of treats, carefully crafted to satisfy your sweetest cravings.</p>
</div>

<div class="category-filters" style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
    <a href="/sweet_shop/shop.php" class="btn <?= $category_id == 0 ? 'btn-primary' : 'btn-outline' ?>">All</a>
    <?php foreach ($categories as $cat): ?>
        <a href="/sweet_shop/shop.php?category=<?= $cat['id'] ?>" class="btn <?= $category_id == $cat['id'] ? 'btn-primary' : 'btn-outline' ?>">
            <?= htmlspecialchars($cat['name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="product-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <div class="product-img-wrapper">
                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img">
            </div>
            <div class="product-info">
                <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                <p class="product-description"><?= htmlspecialchars(substr($product['description'], 0, 80)) ?>...</p>
                <div class="product-footer">
                    <span class="product-price">Rs. <?= number_format($product['price'], 2) ?></span>
                    <a href="/sweet_shop/product.php?id=<?= $product['id'] ?>" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.9rem;">View Details</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($products)): ?>
        <p style="grid-column: 1 / -1; text-align: center;">No products found in this category.</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

