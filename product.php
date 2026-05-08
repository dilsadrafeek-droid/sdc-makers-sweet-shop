<?php
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<h2>Product not found</h2>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<div class="product-detail" style="display: flex; gap: 3rem; margin-top: 2rem; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 300px;">
        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 100%; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
    </div>
    <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; justify-content: center;">
        <span style="color: var(--secondary-color); font-weight: 600; margin-bottom: 0.5rem; display: block;"><?= htmlspecialchars($product['category_name']) ?></span>
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem;"><?= htmlspecialchars($product['name']) ?></h1>
        <p style="font-size: 1.1rem; color: #555; margin-bottom: 1.5rem; line-height: 1.8;"><?= htmlspecialchars($product['description']) ?></p>
        
        <div style="font-size: 2rem; color: var(--primary-color); font-weight: 700; margin-bottom: 2rem;">
            Rs. <?= number_format($product['price'], 2) ?>
            <span style="font-size: 1rem; color: #888; font-weight: normal; margin-left: 1rem;">
                <?= $product['stock'] > 0 ? "In Stock: {$product['stock']}" : "<span style='color:red;'>Out of Stock</span>" ?>
            </span>
        </div>
        
        <?php if ($product['stock'] > 0): ?>
            <form action="/sweet_shop/cart.php" method="POST" style="display: flex; gap: 1rem; align-items: center;">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                
                <div style="display: flex; align-items: center; border: 1px solid #ccc; border-radius: 8px; overflow: hidden;">
                    <button type="button" onclick="document.getElementById('qty').stepDown()" style="border: none; background: #eee; padding: 0.8rem 1rem; cursor: pointer; font-size: 1.2rem;">-</button>
                    <input type="number" id="qty" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" style="border: none; width: 60px; text-align: center; font-size: 1.2rem; appearance: none; -moz-appearance: textfield;" readonly>
                    <button type="button" onclick="document.getElementById('qty').stepUp()" style="border: none; background: #eee; padding: 0.8rem 1rem; cursor: pointer; font-size: 1.2rem;">+</button>
                </div>
                
                <button type="submit" class="btn btn-accent" style="padding: 0.8rem 2rem; font-size: 1.1rem;"><i class="fa-solid fa-cart-plus"></i> Add to Cart</button>
            </form>
        <?php else: ?>
            <button class="btn btn-outline" disabled>Out of Stock</button>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

