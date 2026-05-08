<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /sweet_shop/login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

<div style="max-width: 800px; margin: 0 auto; min-height: 50vh;">
    <h1 style="margin-bottom: 2rem;">My Orders</h1>
    <?php if (empty($orders)): ?>
        <p style="font-size: 1.1rem; color: #666;">You have no orders yet. <a href="/sweet_shop/shop.php" style="color: var(--accent-color); font-weight: 500; text-decoration: underline;">Start shopping!</a></p>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php foreach ($orders as $order): ?>
                <div style="border-radius: var(--border-radius); padding: 1.5rem; box-shadow: var(--box-shadow); background: #fff; border-left: 5px solid var(--primary-color);">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1rem; flex-wrap: wrap;">
                        <span style="font-weight: 700; font-size: 1.2rem;">Order #<?= $order['id'] ?></span>
                        <span style="color: #666; font-size: 0.95rem;"><i class="fa-regular fa-clock"></i> <?= date('F j, Y, g:i a', strtotime($order['created_at'])) ?></span>
                    </div>
                    
                    <?php
                        $item_stmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                        $item_stmt->execute([$order['id']]);
                        $items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <div style="margin-bottom: 1rem;">
                        <h4 style="margin-bottom: 0.5rem; font-size: 0.9rem; color: #888; text-transform: uppercase;">Items:</h4>
                        <ul style="list-style: inside circle; color: #555;">
                            <?php foreach ($items as $item): ?>
                                <li><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; background: var(--bg-color); padding: 1rem; border-radius: 8px;">
                        <div>
                            <span style="display: block; font-size: 0.85rem; color: #666; text-transform: uppercase;">Status</span>
                            <span style="font-weight: 600; color: <?= $order['status'] == 'Pending' ? '#b8860b' : '#28a745' ?>"><?= htmlspecialchars($order['status']) ?></span>
                        </div>
                        <div style="text-align: right;">
                            <span style="display: block; font-size: 0.85rem; color: #666; text-transform: uppercase;">Total Paid</span>
                            <span style="font-size: 1.2rem; font-weight: 700; color: var(--primary-color);">Rs. <?= number_format($order['total_price'], 2) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

