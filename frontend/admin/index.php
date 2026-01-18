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
$purchaseOrdersList = getAllPurchaseOrders();
$latestPurchaseOrders = getLatestPurchaseOrder();

$poDetailsList;
$currPurchaseOrder;


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
    } elseif ($_POST['action'] === 'viewPODetails') {
        global $poDetailsList, $currPurchaseOrder;

        $purchaseOrderId = $_POST['purchaseOrderId'] ?? '';

        $poDetailsList = getPurchaseOrderDetails($purchaseOrderId);
        if ($poDetailsList['status']) {
            // Store details in session or a temporary variable to be used in the modal
            $_SESSION['po_details'] = $poDetailsList['data'];
        } else {
            $errorMessage = $poDetailsList['message'];
        }

        $currPurchaseOrder = getPurchaseOrderById($purchaseOrderId);
    } elseif ($_POST['action'] === 'updatePOStatus') {

        $purchaseOrderId = $_POST['purchaseOrderId'] ?? '';
        $newStatus = $_POST['newStatus'] ?? '';

        $result = updatePurchaseOrderStatus($purchaseOrderId, $newStatus);
        if ($result['status']) {
            $_SESSION['flash_message'] = 'Purchase Order status has been updated successfully.'; header('Location: ' . $_SERVER['PHP_SELF']);
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
<style>
    .tab-pane.fade:not(.show) {
        display: none !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }
    .tab-pane.fade.show {
        display: block !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }
</style>

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
                        
                        <!-- Nav Tabs -->
                        <ul class="nav nav-tabs mb-4" id="procurementTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="poListTab" data-bs-toggle="tab" data-bs-target="#poListContent" type="button" role="tab">All Purchase Orders</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="poFormTab" data-bs-toggle="tab" data-bs-target="#poFormContent" type="button" role="tab">Create PO</button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="procurementTabContent">
                            <!-- Tab 1: All Purchase Orders Listing -->
                            <div class="tab-pane fade show active" id="poListContent" role="tabpanel" style="display: block;">
                                <div class="table-responsive">
                                    <table class="table table-dark table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Invoice</th>
                                                <th>Vendor</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="poTableBody">
                                            <?php 
                                                $poPage = isset($_GET['poPage']) ? max(1, (int)$_GET['poPage']) : 1;
                                                $poLimit = 10;
                                                $poOffset = ($poPage - 1) * $poLimit;
                                                $totalPO = count($purchaseOrdersList);
                                                $totalPOPages = max(1, ceil($totalPO / $poLimit));
                                                $paginatedPO = array_slice($purchaseOrdersList, $poOffset, $poLimit);
                                            ?>
                                            <?php foreach ($paginatedPO as $po): ?>
                                            <tr class="po-row"
                                                role="button"
                                                tabindex="0"
                                                data-po-id="<?php echo htmlspecialchars($po['PURCHASEORDERID']); ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#poDetailsModal">

                                                <td class="p-1"><?php echo htmlspecialchars($po['INVOICENO']); ?></td>
                                                <td class="p-1"><?php echo htmlspecialchars($po['COMPANYNAME']); ?></td>
                                                <td class="p-1">RM<?php echo number_format($po['TOTALPRICE'], 2); ?></td>
                                                <td class="p-1">
                                                    <?php
                                                    $statusClass = match(strtolower($po['STATUS'])) {
                                                        'pending' => 'bg-warning text-dark',
                                                        'completed' => 'bg-success',
                                                        'cancelled' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?>">
                                                        <?php echo ucfirst($po['STATUS']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <nav class="mt-3">
                                    <ul class="pagination">
                                        <li class="page-item <?php echo $poPage <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?poPage=<?php echo $poPage - 1; ?>">Previous</a>
                                        </li>
                                        <li class="page-item active">
                                            <span class="page-link">Page <?php echo $poPage; ?> of <?php echo $totalPOPages; ?></span>
                                        </li>
                                        <li class="page-item <?php echo $poPage >= $totalPOPages ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?poPage=<?php echo $poPage + 1; ?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                                <?php if (empty($purchaseOrdersList)): ?>
                                <div class="alert alert-info text-center" role="alert">
                                    No purchase orders found. Create one to get started.
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Tab 2: Create Purchase Order Form -->
                            <div class="tab-pane fade" id="poFormContent" role="tabpanel" style="display: none; opacity: 0; pointer-events: none;">
                                <form method="POST" id="poForm">
                                    <input type="hidden" name="action" value="createPO">
                                    <div class="mb-3">
                                        <!-- Vendor Selection -->
                                        <label class="form-label small text-secondary">Select Vendor</label>
                                        <select class="form-select" id="vendorSelect" name="vendor" required>
                                            <option value="">Select Vendor</option>
                                            <?php foreach ($vendors as $vendor): ?>
                                                <option value="<?php echo htmlspecialchars($vendor['VENDORID']); ?>"><?php echo htmlspecialchars($vendor['COMPANYNAME']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <!-- Item Selection -->
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
                                            <input type="number" class="form-control" id="itemQuantity" name="quantity" min="1" value="1" placeholder="Qty" required>
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

<!-- Purchase Order Details Modal -->
<div class="modal fade" id="poDetailsModal" tabindex="-1">
    <input type="hidden" id="modalPurchaseOrderId" value="">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-accent">Purchase Order Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-secondary small">Invoice Number</p>
                        <p class="text-white" id="modalInvoiceNo">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary small">Vendor</p>
                        <p class="text-white" id="modalVendor">-</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-secondary small">Total Price</p>
                        <p class="text-white" id="modalTotalPrice">RM-</p>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-secondary small">Status</p>
                                <div>
                                    <p class="text-white"><span class="badge bg-warning text-dark" id="modalStatus">Pending</span></p>
                                </div>
                                <div>
                                    <form method="POST" id="updateStatusForm">
                                        <input type="hidden" name="action" value="updatePOStatus">
                                        <input type="hidden" name="purchaseOrderId" id="modalPurchaseOrderId" value="">

                                        <select class="form-select form-select-sm bg-dark"
                                                name="newStatus"
                                                id="modalNewStatus">
                                            <option value="" disabled selected>Change Status</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </form>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="border-secondary">
                <p class="text-secondary small mb-2">Items Ordered</p>
                <div class="table-responsive">
                    <table class="table table-dark table-sm">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="modalItemsTable">
                            <tr><td colspan="2" class="text-center text-secondary">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
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

// Procurement PO Item Addition
document.getElementById('addItemBtn').addEventListener('click', function() {
    const selectItem = document.getElementById('itemSelect');
    const quantity = document.getElementById('itemQuantity');
    const itemId = selectItem.value;
    const itemText = selectItem.options[selectItem.selectedIndex].text;
    
    if (!itemId) return;
    
    // Create item entry
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

// Submit Purchase Order Form
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

document.querySelectorAll('.po-row').forEach(row => {
    row.addEventListener('click', () => {
        document.getElementById('modalPurchaseOrderId').value = row.dataset.poId;

        loadPODetails(row.dataset.poId);
    });
});

function loadPODetails(poId) {

    // Reset modal
    document.getElementById('modalInvoiceNo').textContent = 'Loading...';
    document.getElementById('modalVendor').textContent = 'Loading...';
    document.getElementById('modalTotalPrice').textContent = 'Loading...';
    document.getElementById('modalStatus').textContent = 'Loading...';
    document.getElementById('modalItemsTable').innerHTML =
        '<tr><td colspan="2" class="text-center text-secondary">Loading...</td></tr>';

    fetch('po-details.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            purchaseOrderId: poId
        })
    })
    .then(res => res.json())
    .then(data => {

        document.getElementById('modalInvoiceNo').textContent = data.invoice;
        document.getElementById('modalVendor').textContent = data.vendor;
        document.getElementById('modalTotalPrice').textContent = 'RM' + data.total;
        document.getElementById('modalStatus').textContent = data.status;
        document.getElementById('modalStatus').className = 'badge ' + data.statusClass;
        document.getElementById('modalPurchaseOrderId').value = data.purchaseOrderId;

        let rows = '';
        data.items.forEach(item => {
            rows += `<tr>
                        <td>${item.name}</td>
                        <td>${item.qty}</td>
                    </tr>`;
        });

        document.getElementById('modalItemsTable').innerHTML = rows;
    });
}

// Change PO Status
document.getElementById('modalNewStatus').addEventListener('change', function() {
    const poId = document.getElementById('modalPurchaseOrderId').value;
    const newStatus = this.value;

    console.log('DEBUG: PO ID =', poId);
    console.log('DEBUG: New Status =', newStatus);

    if (!poId) {
        alert('Error: PO ID is empty!');
        return;
    }

    if (!confirm(`Change PO #${poId} to ${newStatus}?`)) {
        this.value = '';
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = ''; // current page

    // Add inputs
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'updatePOStatus';
    form.appendChild(actionInput);

    const poInput = document.createElement('input');
    poInput.type = 'hidden';
    poInput.name = 'purchaseOrderId';
    poInput.value = poId;
    form.appendChild(poInput);

    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'newStatus';
    statusInput.value = newStatus;
    form.appendChild(statusInput);

    document.body.appendChild(form);
    form.submit();
});


</script>
</body>
</html>