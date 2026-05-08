<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_msg'] = "Please log in to proceed to checkout.";
    header("Location: /sweet_shop/login.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: /sweet_shop/cart.php");
    exit;
}

// Calculate total and fetch products
$cart_items = [];
$total_price = 0;
$safe_ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));

if (!empty($safe_ids)) {
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($safe_ids)");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $qty = $_SESSION['cart'][$row['id']];
        $row['cart_qty'] = $qty;
        $total_price += $row['price'] * $qty;
        $cart_items[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = trim($_POST['address']);
    if (empty($address)) {
        $_SESSION['error_msg'] = "Shipping address is required.";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, shipping_address) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $total_price, $address]);
            $order_id = $pdo->lastInsertId();
            
            // Insert order items and deduct stock
            $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            
            foreach ($cart_items as $item) {
                $stmt_item->execute([$order_id, $item['id'], $item['cart_qty'], $item['price']]);
                $stmt_stock->execute([$item['cart_qty'], $item['id']]);
            }
            
            $pdo->commit();
            
            // Clear cart
            unset($_SESSION['cart']);
            $_SESSION['success_msg'] = "Your order was successfully placed! Order ID: #$order_id";
            header("Location: /sweet_shop/orders.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error_msg'] = "Failed to place order. " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="display: flex; gap: 3rem; flex-wrap: wrap-reverse; max-width: 1000px; margin: 0 auto;">
    <div style="flex: 2; min-width: 300px;">
        <div class="form-container" style="max-width: 100%; margin: 0;">
            <h2 style="margin-bottom: 2rem;">Shipping Details</h2>
            <form action="/sweet_shop/checkout.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" value="<?= htmlspecialchars($_SESSION['username']) ?>" class="form-control" readonly style="background: #f9f9f9;">
                </div>
                <div class="form-group">
                    <label for="address">Full Shipping Address</label>
                    <textarea id="address" name="address" class="form-control" rows="4" placeholder="123 Sweet Street, City, Country, ZIP" required></textarea>
                </div>
                <div class="form-group">
                    <label for="card">Payment Details (Demo)</label>
                    <input type="text" id="card" class="form-control" placeholder="**** **** **** ****" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.2rem; padding: 1rem;">Complete Order</button>
            </form>
        </div>
    </div>
    
    <div style="flex: 1; min-width: 300px;">
        <div style="background: #fff; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow); position: sticky; top: 100px;">
            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 1rem;">Order Summary</h3>
            <ul style="line-height: 2; margin-bottom: 1.5rem;">
                <?php foreach ($cart_items as $item): ?>
                    <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #eee; padding: 0.5rem 0;">
                        <span>
                            <span style="font-weight: 500;"><?= htmlspecialchars($item['name']) ?></span> <span style="color: #666; font-size: 0.9rem;">x <?= $item['cart_qty'] ?></span>
                        </span>
                        <span>Rs. <?= number_format($item['price'] * $item['cart_qty'], 2) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.3rem; margin-top: 1rem; border-top: 2px solid var(--bg-color); padding-top: 1rem;">
                <span>Total:</span>
                <span class="product-price">Rs. <?= number_format($total_price, 2) ?></span>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

