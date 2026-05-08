<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $product_id = (int)$_POST['product_id'];
    
    if ($_POST['action'] == 'add') {
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        $_SESSION['success_msg'] = "Product added to cart.";
        header("Location: /sweet_shop/cart.php");
        exit;
    } elseif ($_POST['action'] == 'update') {
        $quantity = (int)$_POST['quantity'];
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        header("Location: /sweet_shop/cart.php");
        exit;
    } elseif ($_POST['action'] == 'remove') {
        unset($_SESSION['cart'][$product_id]);
        header("Location: /sweet_shop/cart.php");
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';

$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    // Be careful with SQL injection if ids array is from user, but keys from session should be fine.
    // Ensure all keys are integers to be safe:
    $safe_ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    
    if (!empty($safe_ids)) {
        $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($safe_ids)");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $qty = $_SESSION['cart'][$row['id']];
            $row['cart_qty'] = $qty;
            $row['subtotal'] = $row['price'] * $qty;
            $total_price += $row['subtotal'];
            $cart_items[] = $row;
        }
    }
}
?>

<div style="max-width: 800px; margin: 0 auto; min-height: 60vh;">
    <h1 style="margin-bottom: 2rem;">Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div style="text-align: center; padding: 3rem; background: #fff; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
            <i class="fa-solid fa-cart-shopping" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
            <p style="font-size: 1.2rem; margin-bottom: 1.5rem;">Your cart is empty.</p>
            <a href="/sweet_shop/shop.php" class="btn btn-primary">Discover our Sweets</a>
        </div>
    <?php else: ?>
        <div style="background: #fff; border-radius: var(--border-radius); box-shadow: var(--box-shadow); overflow-x: auto; margin-bottom: 2rem;">
            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--bg-color);">
                        <th style="padding: 1rem; text-align: left;">Product</th>
                        <th style="padding: 1rem; text-align: center;">Price</th>
                        <th style="padding: 1rem; text-align: center;">Quantity</th>
                        <th style="padding: 1rem; text-align: right;">Subtotal</th>
                        <th style="padding: 1rem; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td style="padding: 1rem; display: flex; align-items: center; gap: 1rem;">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="img" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                <span style="font-weight: 500;"><?= htmlspecialchars($item['name']) ?></span>
                            </td>
                            <td style="padding: 1rem; text-align: center;">Rs. <?= number_format($item['price'], 2) ?></td>
                            <td style="padding: 1rem; text-align: center;">
                                <form action="/sweet_shop/cart.php" method="POST" style="display: inline-flex; align-items: center;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['cart_qty'] ?>" min="1" max="<?= $item['stock'] ?>" style="width: 60px; text-align: center; padding: 0.4rem; border: 1px solid #ddd; border-radius: 4px;">
                                    <button type="submit" class="btn btn-outline" style="padding: 0.4rem 0.6rem; margin-left: 0.5rem; border-color: #ddd; color: #555;"><i class="fa-solid fa-rotate"></i></button>
                                </form>
                            </td>
                            <td style="padding: 1rem; text-align: right; font-weight: 600; color: var(--primary-color);">Rs. <?= number_format($item['subtotal'], 2) ?></td>
                            <td style="padding: 1rem; text-align: center;">
                                <form action="/sweet_shop/cart.php" method="POST">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <button type="submit" style="color: #dc3545; background: none; border: none; cursor: pointer; font-size: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 1rem; background: #fff; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
            <div style="font-size: 1.5rem;">
                Total: <span style="font-weight: 700; color: var(--primary-color);">Rs. <?= number_format($total_price, 2) ?></span>
            </div>
            <p style="color: #666; font-size: 0.9rem;">Shipping & taxes calculated at checkout.</p>
            <a href="/sweet_shop/checkout.php" class="btn btn-accent btn-lg" style="font-size: 1.1rem; padding: 1rem 3rem; margin-top: 1rem; display: inline-block;">Proceed to Checkout <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i></a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

