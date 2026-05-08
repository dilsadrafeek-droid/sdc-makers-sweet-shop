<?php
require_once __DIR__ . '/includes/header.php';

// Fetch top products (let's say limit 4)
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 4");
$featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="hero">
    <h1>Welcome to SDC Makers</h1>
    <p>Indulge in our exquisite collection of authentic Sri Lankan traditional sweets and festive treats crafted with love and passion.</p>
    <a href="/sweet_shop/shop.php" class="btn btn-accent">Shop Now</a>
</section>

<section>
    <h2 style="text-align: center; margin-bottom: 1rem;">Featured Delights</h2>
    <div class="product-grid">
        <?php foreach ($featured_products as $product): ?>
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
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

