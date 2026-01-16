<?php
require_once __DIR__ . '/../backend/functions/item-functions.php';
require_once __DIR__ . '/../backend/functions/cartItem-functions.php';

session_start();

// Sample products
$products  = getAllItems();
$cart = getCartItemsByCustomer($_SESSION['user_id'] ?? 0);

function refresh_data_cart() {
    global $cart;

    $cart = getCartItemsByCustomer($_SESSION['user_id'] ?? 0);
}

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'] ?? 1;
        
        if (empty($_SESSION['user_id'])) {
            echo "Error: User ID is empty\n";
        } else {
            createCartItem($_SESSION['user_id'], $product_id, $quantity);
        }
    }
    
    if ($_POST['action'] === 'remove') {
        $product_id = (int)$_POST['product_id'];
        deleteCartItem($product_id);
    }
    
    if ($_POST['action'] === 'confirm') {
        // Process order
        $_SESSION['order_confirmed'] = true;
        $_SESSION['cart'] = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Cart</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .page-wrapper { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; align-items: start; }
        h1 { margin-bottom: 30px; color: #333; grid-column: 1 / -1; }
        .top-bar { display: flex; justify-content: flex-end; align-items: center; margin-bottom: 10px; color: #555; gap: 10px; }
        .top-bar .user-name { font-weight: bold; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; grid-column: 1 / -1; }
        .shop-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .product-card { background: white; border-radius: 8px; padding: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .product-card img { width: 100%; height: 200px; object-fit: cover; border-radius: 4px; margin-bottom: 10px; }
        .product-card h3 { color: #333; margin-bottom: 10px; }
        .product-card .price { font-size: 20px; color: #27ae60; font-weight: bold; margin-bottom: 10px; }
        .product-card form { display: flex; gap: 5px; }
        .product-card input { width: 60px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; }
        .product-card button { flex: 1; padding: 10px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .product-card button:hover { background: #2980b9; }
        .cart-section { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); position: sticky; top: 20px; max-height: calc(100vh - 40px); overflow-y: auto; }
        .cart-section h2 { margin-bottom: 20px; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee; gap: 10px; flex-wrap: wrap; }
        .cart-item-info { flex: 1; min-width: 150px; }
        .cart-item-total { font-weight: bold; color: #27ae60; white-space: nowrap; }
        .remove-btn { padding: 5px 10px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .remove-btn:hover { background: #c0392b; }
        .cart-total { margin-top: 20px; padding-top: 20px; border-top: 2px solid #333; font-size: 18px; font-weight: bold; text-align: right; }
        .confirm-btn { width: 100%; padding: 12px; background: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 20px; font-size: 16px; }
        .confirm-btn:hover { background: #229954; }
        .empty-cart { text-align: center; color: #999; padding: 20px; }
        @media (max-width: 768px) {
            .page-wrapper { grid-template-columns: 1fr; }
            .cart-section { position: static; max-height: none; }
        }
    </style>
</head>
<body>


    <div class="container">
        <div class="top-bar">
            <span>Welcome,</span>
            <span class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Guest') ?></span>
        </div>
        <h1>🛍️ Shop</h1>
        
        <?php if (isset($_SESSION['order_confirmed'])): ?>
            <div class="success">✅ Order confirmed! Thank you for your purchase.</div>
            <?php unset($_SESSION['order_confirmed']); ?>
        <?php endif; ?>

        <div class="page-wrapper">
            <div class="shop-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['IMAGE'] ?? '/frontend/src/assets/img/gallery-img-03.jpg') ?>" alt="<?= htmlspecialchars($product['NAME']) ?>">
                    <h3><?= htmlspecialchars($product['NAME']) ?></h3>
                    <div class="price">$<?= number_format($product['PRICE'], 2) ?></div>
                    <form method="POST">
                        <input type="number" name="quantity" value="1" min="1">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $product['ITEMID'] ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
            </div>

            <div class="cart-section">
            <h2>🛒 Shopping Cart</h2>
            <?php if (empty($cart)): ?>
                <div class="empty-cart">Your cart is empty</div>
            <?php else: ?>
                <?php foreach ($cart as $cart_items): ?>
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <strong><?= htmlspecialchars($cart_items['NAME']) ?></strong><br>
                            Qty: <?= $cart_items['QUANTITY'] ?> × $<?= number_format($cart_items['PRICE'], 2) ?>
                        </div>
                        <div class="cart-item-total">
                            $<?= number_format($cart_items['TOTAL_BY_ITEM'], 2) ?>
                        </div>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?= $cart_items['ITEMID'] ?>">
                            <button type="submit" class="remove-btn">Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                <div class="cart-total">Total: $<?= number_format($cart[0]['TOTAL_PRICE'] ?? 0, 2) ?></div>
                <form method="POST">
                    <input type="hidden" name="action" value="confirm">
                    <button type="submit" class="confirm-btn">✅ Confirm Order</button>
                </form>
            <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>