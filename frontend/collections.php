<?php 
include('includes/header.php'); 

require_once __DIR__ . '/../backend/functions/item-functions.php';
require_once __DIR__ . '/../backend/functions/cartItem-functions.php';
require_once __DIR__ . '/../backend/functions/sale-functions.php';

session_start();

$itemList = getItemsForCollections();
$cart = getCartItemsByCustomer($_SESSION['user_id'] ?? 0);

function refreshCartData() {
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
            refreshCartData();
            $_SESSION['flash_message'] = "Item added to cart successfully.";
        }
    }
    
    if ($_POST['action'] === 'remove') {
        $cartId = (int)$_POST['cart_id'];
        deleteCartItem($cartId);
        refreshCartData();


        $_SESSION['flash_message'] = "Item removed from cart successfully.";
    }
    
    if ($_POST['action'] === 'confirm') {
        
        $customerId = $_SESSION['user_id'] ?? 0;
        $staffId = null; // Assuming no staff is involved in this frontend operation
         
        $result = createSalesFromCart($customerId, $staffId);

        if (isset($result['status']) && $result['status']) {
            $_SESSION['flash_message'] = "Order confirmed successfully.";
        } else {
            $errorMessage = $result['message'] ?? 'Failed to confirm order.';
    }
    
    // Refresh cart data
    $cart = getCartItemsByCustomer($_SESSION['user_id'] ?? 0);
    }
}
?>

<style>
    .collections-wrapper { display: grid; grid-template-columns: 1fr 300px; gap: 20px; align-items: start; padding: 20px; }
    .collections-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
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
    .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
    
    @media (max-width: 768px) {
        .collections-wrapper { grid-template-columns: 1fr; }
        .cart-section { position: static; max-height: none; }
    }
</style>

<!-- Success and Error Banners -->
<?php if (isset($_SESSION['flash_message'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert" id="successBanner" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; width: 90%; width: 85%;">
    <strong>Success!</strong> <?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<script>
    setTimeout(function() {
        const banner = document.getElementById('successBanner');
        if (banner) {
            banner.classList.remove('show');
            banner.addEventListener('transitionend', function() {
                banner.remove();
            });
        }
    }, 3000);
</script>
<?php endif; ?>
<?php if (isset($errorMessage)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorBanner" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; width: 90%; width: 85%;">
    <strong>Error!</strong> <?php echo htmlspecialchars($errorMessage); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<script>
    setTimeout(function() {
        const banner = document.getElementById('errorBanner');
        if (banner) {
            banner.classList.remove('show');
            banner.addEventListener('transitionend', function() {
                banner.remove();
            });
        }
    }, 3000);
</script>
<?php endif; ?>


<div class="container-fluid tm-content-container py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="tm-link-white">Our Collections</h2>
                <p>Curated apparel for the modern lifestyle.</p>
            </div>
        </div>

        <div class="collections-wrapper">
            <div class="collections-grid">
                <?php if (count($itemList) > 0): ?>
                    <?php foreach ($itemList as $item): ?>
                        <div class="product-card">
                            <img src="<?= htmlspecialchars($item['IMAGE'] ?? 'assets/img/gallery-img-03.jpg') ?>" alt="<?php echo htmlspecialchars($item['NAME']); ?>">
                            <h3><?php echo htmlspecialchars($item['NAME']); ?></h3>
                            <div class="price">$<?php echo number_format($item['PRICE'], 2); ?></div>
                            <form method="POST">
                                <input type="number" name="quantity" value="1" min="1">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $item['ID']; ?>">
                                <button type="submit">Add to Cart</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p>No products found.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cart-section">
                <h2 style="color: #000;">🛒 Shopping Cart</h2>
                <?php if (empty($cart)): ?>
                    <div class="empty-cart">Your cart is empty</div>
                <?php else: ?>
                    <?php foreach ($cart as $cart_items): ?>
                        <div class="cart-item">
                            <div class="cart-item-info" style="color: #000;">
                                <strong><?= htmlspecialchars($cart_items['NAME']) ?></strong><br>
                                Qty: <?= $cart_items['QUANTITY'] ?> × $<?= number_format($cart_items['PRICE'], 2) ?>
                            </div>
                            <div class="cart-item-total">
                                $<?= number_format($cart_items['TOTAL_BY_ITEM'], 2) ?>
                            </div>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="cart_id" value="<?= $cart_items['CARTID'] ?>">
                                <input type="hidden" name="product_id" value="<?= $cart_items['ITEMID'] ?>">
                                <button type="submit" class="remove-btn">Remove</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                    <div class="cart-total" style="color: #000;">Total: $<?= number_format($cart[0]['TOTAL_PRICE'] ?? 0, 2) ?></div>
                    <form method="POST">
                        <input type="hidden" name="action" value="confirm">
                        <button type="submit" class="confirm-btn">✅ Confirm Order</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>