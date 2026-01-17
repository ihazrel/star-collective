<?php include('includes/header.php'); 

require_once __DIR__ . '/../../backend/functions/item-functions.php';
require_once __DIR__ . '/../../backend/functions/user-functions.php';
require_once __DIR__ . '/../../backend/functions/vendor-functions.php';
require_once __DIR__ . '/../../backend/functions/purchaseOrder-functions.php';

// List
$lowStockItems = getItemWithStockBelow(20);
$users = getAllStaffs();
$itemStockUpdate = getAllItems('CURRENTSTOCK', 'ASC');
$vendors = getAllVendors();
$latestPurchaseOrders = getLatestPurchaseOrder();


$roleFilter = isset($_POST['roleFilter']) ? trim($_POST['roleFilter']) : '';
$activeUsers = $roleFilter ? filterUsersByRole($roleFilter) : $users;

function refresh_data_users() {
    global $users, $activeUsers, $roleFilter;

    $roleFilter = isset($_POST['roleFilter']) ? trim($_POST['roleFilter']) : '';
    $users = getAllUsers();
    $activeUsers = $roleFilter ? filterUsersByRole($roleFilter) : $users;
}

function refresh_data_items() {
    global $itemStockUpdate, $lowStockItems;

    $lowStockItems = getItemWithStockBelow(20);
    $itemStockUpdate = getAllItems('CURRENTSTOCK', 'ASC');
}

function filterUsersByRole($role) {
    global $users;
    unset($_GET['usersPage']);

    return array_filter($users, fn($user) => strtolower($user['ROLE']) === strtolower($role));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'addStaff') {

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'Staff';

        $result = createUser($name, $email, $phone, $password, $role);
        refresh_data_users();

        if ($result['status']) {
            $_SESSION['flash_message'] = 'User has been added successfully.'; header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $errorMessage = $result['message'];
        }
    } elseif ($_POST['action'] === 'updateStock') {

        $itemId = $_POST['itemId'] ?? '';
        $quantity = (int)($_POST['quantity'] ?? 0);

        $result = updateStock($itemId, $quantity);
        refresh_data_items();

        if ($result['status']) {
            $_SESSION['flash_message'] = 'Stock has been updated successfully.'; header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $errorMessage = $result['message'];
        }
    } elseif ($_POST['action'] === 'createPO') {

        $vendorId = $_POST['vendorId'] ?? '';
        $totalPrice = $_POST['totalPrice'] ?? 0; // You may want to calculate the total price based on items
        $poItems = $_POST['poItems'] ?? [];

        $result = createPurchaseOrder($vendorId, $totalPrice, $poItems);
        if ($result['status']) {
            $_SESSION['flash_message'] = 'Purchase Order has been created successfully.'; header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $errorMessage = $result['message'];
        }
    }
}
?>

<?php if (isset($_SESSION['flash_message'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert" id="successBanner" style="position: fixed; top: 20px; right: 20px; z-index: 9999; width: 85%;">
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
<div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorBanner" style="position: fixed; top: 20px; right: 20px; z-index: 9999; width: 85%;">
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

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            
            <section id="overview">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 fw-bold">Admin Dashboard</h1>
                    <span class="badge border border-info text-info p-2">
                        <?php echo date('l, d F Y'); ?>
                    </span>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="card stat-card p-3 border-danger">
                            <h6 class="text-secondary small text-uppercase">Low Stock Alerts (under 20 items)</h6>
                            <h3 class="text-danger"><?php echo count($lowStockItems) . ' items'; ?></h3>
                            <small class="text-danger">Action Required: Procurement</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card p-3">
                            <h6 class="text-secondary small text-uppercase">Processed Sales (Today)</h6>
                            <h3 class="text-accent">RM3,450.67</h3>
                            <div class="progress mt-2" style="height: 4px; background-color: #333;">
                                <div class="progress-bar bg-accent" style="width: 45%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card p-3">
                            <h6 class="text-secondary small text-uppercase">Pending Orders</h6>
                            <h3>28</h3>
                            <small class="text-secondary">Awaiting packing</small>
                        </div>
                    </div>
                </div>
            </section>


            <section id="users" class="mb-5">
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">User Management (Staff & Customers)</h4>
                        <div class="d-flex gap-3 align-items-end">
                            <form method="POST" class="mb-3" id="roleFilterForm">
                                <label class="form-label small text-secondary">Select User Type</label>
                                <select class="form-select" name="roleFilter" id="roleFilterSelect">
                                    <option value="" <?php echo $roleFilter === '' ? 'selected' : ''; ?>>Select</option>
                                    <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="staff" <?php echo $roleFilter === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                </select>
                            </form>
                            <div class="mb-3">
                                <div class="btn-group">
                                    <button class="btn btn-accent btn-sm" data-bs-toggle="modal" data-bs-target="#userModal">+ Add Staff</button>
                                </div>

                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle">
                            <thead class="text-secondary">
                                <tr>
                                    <th>No</th>
                                    <th>User Info</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $usersPage = isset($_GET['usersPage']) ? (int)$_GET['usersPage'] : 1;
                                    $usersLimit = 10;
                                    $usersOffset = ($usersPage - 1) * $usersLimit;
                                    $totalUsers = count($activeUsers);
                                    $totalUserPages = max(1, ceil($totalUsers / $usersLimit));
                                    $paginatedUsers = array_slice($activeUsers, $usersOffset, $usersLimit);
                                    
                                    $i = 0;
                                    foreach ($paginatedUsers as $user): 
                                ?>
                                <tr>
                                    <td><?= $usersOffset + (++$i) ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($user['NAME']); ?></strong><br>
                                        <small class="text-secondary">Email: <?php echo htmlspecialchars($user['EMAIL']); ?></small><br>
                                    </td>
                                    <td>
                                        <?php
                                            $roleClass = match(strtolower($user['ROLE'])) {
                                                'admin' => 'bg-danger',
                                                'staff' => 'bg-primary',
                                                'customer' => 'bg-secondary',
                                                default => 'bg-dark'
                                            };
                                            $roleLabel = ucfirst($user['ROLE']);
                                        ?>
                                        <span class="badge <?php echo $roleClass; ?>"><?php echo htmlspecialchars($roleLabel); ?></span>
                                    </td>
                                    <td><small>Full (Manager)</small></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <nav class="mt-3">
                            <ul class="pagination">
                                <li class="page-item <?php echo $usersPage <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?usersPage=<?php echo $usersPage - 1; ?>">Previous</a>
                                </li>
                                <li class="page-item active">
                                    <span class="page-link">Page <?php echo $usersPage; ?> of <?php echo $totalUserPages; ?></span>
                                </li>
                                <li class="page-item <?php echo $usersPage >= $totalUserPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?usersPage=<?php echo $usersPage + 1; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </section>

            <div class="row g-4 mb-5">
                <div class="col-lg-7" id="inventory">
                    <div class="card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0">Inventory & Stock Update</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-dark table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Status</th>
                                        <th>Quick Add</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $inventoryPage = isset($_GET['inventoryPage']) ? max(1, (int)$_GET['inventoryPage']) : 1;
                                        $inventoryLimit = 10;
                                        $inventoryOffset = ($inventoryPage - 1) * $inventoryLimit;
                                        $totalInventory = count($itemStockUpdate);
                                        $totalInventoryPages = max(1, ceil($totalInventory / $inventoryLimit));
                                        $paginatedInventory = array_slice($itemStockUpdate, $inventoryOffset, $inventoryLimit);
                                        $currentUsersPage = isset($usersPage) ? $usersPage : 1;
                                    ?>
                                    <?php foreach ($paginatedInventory as $item): ?>
                                    <form action="" method="post">
                                        <input type="hidden" name="action" value="updateStock">
                                        <input type="hidden" name="itemId" value="<?php echo htmlspecialchars($item['ITEMID']); ?>">
                                        <tr <?php if ($item['CURRENTSTOCK'] < 20) echo 'class="table-danger"'; ?>>
                                        <td><?php echo htmlspecialchars($item['NAME']); ?></td>
                                        <td><?php echo htmlspecialchars($item['CURRENTSTOCK']); ?> Units</td>
                                        <td><span class="<?php if ($item['CURRENTSTOCK'] < 20) { echo 'text-danger'; } else { echo 'text-success'; } ?> small"><?php if ($item['CURRENTSTOCK'] < 20) { echo 'LOW STOCK'; } else { echo 'Healthy'; } ?></span></td>
                                        <td><input type="number" min="0" class="form-control form-control-sm w-50" name="quantity" value="0"></td>
                                        <td><button class="form-control btn btn-success btn-sm w-40" type="submit">Update Stock Table</button></td>
                                        </tr>
                                    </form>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <nav class="mt-3">
                                <ul class="pagination">
                                    <li class="page-item <?php echo $inventoryPage <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $currentUsersPage; ?>&inventoryPage=<?php echo $inventoryPage - 1; ?>">Previous</a>
                                    </li>
                                    <li class="page-item active">
                                        <span class="page-link">Page <?php echo $inventoryPage; ?> of <?php echo $totalInventoryPages; ?></span>
                                    </li>
                                    <li class="page-item <?php echo $inventoryPage >= $totalInventoryPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $currentUsersPage; ?>&inventoryPage=<?php echo $inventoryPage + 1; ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5" id="procurement">
                    <div class="card p-4 h-100">
                        <h4 class="mb-4">Procurement & Vendors</h4>
                        <form method="POST" id="poForm">
                            <input type="hidden" name="action" value="createPO">
                            <div class="mb-3">
                                <label class="form-label small text-secondary">Select Vendor</label>
                                <select class="form-select" id="vendorSelect" name="vendor">
                                    <option value="">Select Vendor</option>
                                    <?php foreach ($vendors as $vendor): ?>
                                        <option value="<?php echo htmlspecialchars($vendor['VENDORID']); ?>"><?php echo htmlspecialchars($vendor['COMPANYNAME']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-secondary">Item & Quantity</label>
                                <div class="input-group mb-3">
                                    <select class="form-select" id="itemSelect" name="item">
                                        <option value="">Select Item</option>
                                        <?php foreach ($itemStockUpdate as $item): ?>
                                            <option value="<?php echo htmlspecialchars($item['ITEMID']); ?>">
                                                <?php echo htmlspecialchars($item['NAME']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="number" class="form-control" id="itemQuantity" name="quantity" min="1" value="1" placeholder="Qty">
                                    <button class="btn btn-accent" type="button" id="addItemBtn">Add</button>
                                </div>
                                <div id="poItems" class="bg-secondary bg-opacity-10 rounded p-2">
                                    <!-- Items will be added here -->
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-secondary">Total Price</label>
                                <input type="number" class="form-control" name="totalPrice" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            <button type="button" class="btn btn-accent w-100 mb-3" id="submitPOBtn">Create Purchase Order</button>
                        </form>

                        <script>
                        document.getElementById('addItemBtn').addEventListener('click', function() {
                            const selectItem = document.getElementById('itemSelect');
                            const quantity = document.getElementById('itemQuantity');
                            const itemId = selectItem.value;
                            const itemText = selectItem.options[selectItem.selectedIndex].text;
                            
                            if (!itemId) return;
                            
                            const itemDiv = document.createElement('div');
                            itemDiv.className = 'input-group input-group-sm mb-2';
                            itemDiv.innerHTML = `
                                <input type="hidden" name="itemId" value="${itemId}">
                                <span class="input-group-text bg-transparent border-secondary text-white">${itemText}</span>
                                <span class="input-group-text bg-transparent border-secondary text-white">${quantity.value}</span>
                                <button class="btn btn-outline-danger btn-sm" type="button">−</button>
                            `;
                            itemDiv.querySelector('button').addEventListener('click', () => itemDiv.remove());
                            document.getElementById('poItems').appendChild(itemDiv);
                        });

                        document.getElementById('submitPOBtn').addEventListener('click', function() {
                            const poItems = document.getElementById('poItems');
                            const items = poItems.querySelectorAll('.input-group-sm');
                            const vendorId = document.getElementById('vendorSelect').value;
                            const totalPrice = document.getElementById('poForm').querySelector('input[name="totalPrice"]').value;
                            
                            if (items.length === 0) {
                                alert('Please add items to the purchase order');
                                return;
                            }

                            const vendorInput = document.createElement('input');
                            vendorInput.type = 'hidden';
                            vendorInput.name = 'vendorId';
                            vendorInput.value = vendorId;
                            document.getElementById('poForm').appendChild(vendorInput);
                            
                            const totalPriceInput = document.createElement('input');
                            totalPriceInput.type = 'hidden';
                            totalPriceInput.name = 'totalPrice';
                            totalPriceInput.value = totalPrice;
                            document.getElementById('poForm').appendChild(totalPriceInput);
                            
                            items.forEach((item, index) => {
                                const texts = item.querySelectorAll('.input-group-text');

                                const itemText = texts[0].textContent.trim();
                                const quantity  = texts[1].textContent.trim();

                                const itemId = item.querySelector('input[name="itemId"]').value;
                                const itemInput = document.createElement('input');
                                itemInput.type = 'hidden';
                                itemInput.name = `poItems[${index}][id]`;
                                itemInput.value = itemId;
                                document.getElementById('poForm').appendChild(itemInput);
                                
                                const qtyInput = document.createElement('input');
                                qtyInput.type = 'hidden';
                                qtyInput.name = `poItems[${index}][quantity]`;
                                qtyInput.value = quantity;
                                document.getElementById('poForm').appendChild(qtyInput);
                            });

                            document.getElementById('poForm').submit();
                        });
                        </script>
                        </script>
                        <hr class="border-secondary">
                        <p class="small text-secondary mb-2">Recent Purchase Orders</p>
                        <div class="list-group list-group-flush bg-transparent">
                            <?php foreach ($latestPurchaseOrders as $po): ?>
                            <div class="list-group-item bg-transparent text-white border-secondary px-0 py-1">
                                <small><?php echo htmlspecialchars($po['INVOICENO']); ?> - <?php echo htmlspecialchars($po['COMPANYNAME']); ?> <span class="float-end text-warning">Pending</span></small>
                            </div>
                            <?php endforeach; ?>
                            <div class="list-group-item bg-transparent text-white border-secondary px-0 py-1">
                                <small>PO #8819 - Adidas <span class="float-end text-success">Completed</span></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section id="sales">
                <div class="card p-4">
                    <div class="row g-4">
                        <div class="col-md-4 border-end border-secondary">
                            <h4 class="mb-4">Manual Sale Entry</h4>
                            <form action="#" method="POST">
                                <div class="mb-3">
                                    <label class="form-label small">Product ID / Search</label>
                                    <input type="text" class="form-control" placeholder="SKU-123">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">Payment Type</label>
                                    <select class="form-select">
                                        <option>Cash</option>
                                        <option>Credit Card</option>
                                        <option>FPX Online Transfer</option>
                                        <option>E-Wallet</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-accent btn-sm w-100">Record Sale Detail</button>
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-2">Clear POS Cart</button>
                            </form>
                        </div>
                        <div class="col-md-8">
                            <h4 class="mb-4">Generate Sales Reports</h4>
                            <div class="table-responsive">
                                <table class="table table-dark table-striped">
                                    <thead>
                                        <tr>
                                            <th>Invoice</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Payment Method</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>#INV-001</td>
                                            <td>16 Jan 2026</td>
                                            <td>RM45.00</td>
                                            <td>E-Wallet</td>
                                            <td><button class="btn btn-link text-accent btn-sm p-0">Download PDF</button></td>
                                        </tr>
                                        <tr>
                                            <td>#INV-002</td>
                                            <td>16 Jan 2026</td>
                                            <td>RM120.00</td>
                                            <td>Cash</td>
                                            <td><button class="btn btn-link text-accent btn-sm p-0">Download PDF</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button class="btn btn-outline-light btn-sm mt-3">Export Monthly Sales (CSV)</button>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark border-secondary">
      <div class="modal-header border-secondary">
        <h5 class="modal-title text-accent">User Administration</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="#" method="POST">
        <input type="hidden" name="action" value="addStaff">
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">User Type</label>
                <select class="form-select" name="role" required>
                    <option value="Staff">Staff Member</option>
                    <option value="Admin">Admin Partner</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
        </div>
        <div class="modal-footer border-secondary">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-accent">Register User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('roleFilterSelect');
    const roleForm = document.getElementById('roleFilterForm');
    if (roleSelect && roleForm) {
        roleSelect.addEventListener('change', function() {
            roleForm.submit();
        });
    }
});
</script>
</body>
</html>