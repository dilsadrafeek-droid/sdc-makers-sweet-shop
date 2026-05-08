<?php
require_once __DIR__ . '/includes/db.php';

try {
    // Drop existing tables for fresh setup if needed
    $pdo->exec("DROP TABLE IF EXISTS order_items");
    $pdo->exec("DROP TABLE IF EXISTS orders");
    $pdo->exec("DROP TABLE IF EXISTS products");
    $pdo->exec("DROP TABLE IF EXISTS categories");
    $pdo->exec("DROP TABLE IF EXISTS users");

    // Create users table
    $pdo->exec("CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_admin INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Create categories table
    $pdo->exec("CREATE TABLE categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(100) NOT NULL UNIQUE,
        description TEXT
    )");

    // Create products table
    $pdo->exec("CREATE TABLE products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category_id INTEGER,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        stock INTEGER DEFAULT 0,
        image_url VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories (id)
    )");

    // Create orders table
    $pdo->exec("CREATE TABLE orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        total_price DECIMAL(10,2) NOT NULL,
        status VARCHAR(50) DEFAULT 'Pending',
        shipping_address TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users (id)
    )");

    // Create order_items table
    $pdo->exec("CREATE TABLE order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER,
        product_id INTEGER,
        quantity INTEGER NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders (id),
        FOREIGN KEY (product_id) REFERENCES products (id)
    )");

    // Insert Default Admin
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, 1)");
    $stmt->execute(['admin', $adminPassword]);

    // Insert Default User
    $userPassword = password_hash('user123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, 0)");
    $stmt->execute(['customer', $userPassword]);

    // Insert Categories
    $categories = [
        ['Traditional Kavum & Aluwa', 'Authentic traditional Sri Lankan sweets.'],
        ['Crispy & Crunchy', 'Delicious crispy treats like Kokis and Murukku.'],
        ['Toffees & Sweets', 'Rich Milky toffees, Jube Jubes and soft sweets.'],
        ['Festive Hampers', 'Assorted traditional sweet boxes perfect for Avurudu.'],
        ['Indian Sweets', 'Popular traditional Indian delicacies like Jalebi, Ladoo, and Gulab Jamun.']
    ];
    $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }

    // Insert Products
    $products = [
        [1, 'Konda Kavum', 'The most popular traditional oil cake made with rice flour and kithul treacle.', 85.00, 100, '/sweet_shop/images/konda_kavum.png'],
        [2, 'Kokis', 'A crispy, deep-fried Sri Lankan sweet made from rice flour and coconut milk.', 45.00, 200, '/sweet_shop/images/kokis.png'],
        [1, 'Cashew Aluwa', 'A flat, diamond-shaped sweet packed with roasted cashews and aromatic cardamom.', 120.00, 50, '/sweet_shop/images/cashew_aluwa.png'],
        [3, 'Milk Toffee', 'Rich, creamy and crumbly squares of local milk toffee infused with vanilla.', 60.00, 150, '/sweet_shop/images/milk_toffee.png'],
        [4, 'Avurudu Celebration Box', 'A premium assortment of Kokis, Kavum, Aluwa, and Mun Kavum perfect for the New Year.', 1500.00, 30, '/sweet_shop/images/indian_sweets.png'],
        [5, 'Jalebi & Ladoo Assortment', 'A vibrant box of fresh, syrupy Jalebi and soft Motichoor Ladoos.', 1200.00, 40, '/sweet_shop/images/indian_sweets.png'],
        [5, 'Gulab Jamun (10 Pcs)', 'Soft, melt-in-your-mouth fried dumplings soaked in rose-flavored sugar syrup.', 650.00, 60, '/sweet_shop/images/indian_sweets.png'],
        [2, 'Pani Walalu', 'Traditional sweet rings made with urad dal and soaked in thick treacle.', 110.00, 80, '/sweet_shop/images/indian_sweets.png']
    ];
    $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($products as $prod) {
        $stmt->execute($prod);
    }

    echo "Database setup completed successfully!\n";
    echo "Admin account: admin / admin123\n";
    echo "User account: customer / user123\n";

} catch (PDOException $e) {
    echo "Error setting up database: " . $e->getMessage() . "\n";
}
?>

