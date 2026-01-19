<?php
include('includes/header.php');
require_once('../backend/functions/sale-functions.php');
require_once('../backend/functions/saleDetail-functions.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? '';

// Get sales based on role
if ($userRole === 'ADMIN' || $userRole === 'STAFF') {
    $sales = getAllSales();
} else {
    $sales = getSalesByCustomer($userId);
}
?>

<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-12">
            <div class="tm-bg-primary-dark tm-block tm-block-h-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="tm-block-title text-white">
                        <?php echo ($userRole === 'ADMIN' || $userRole === 'STAFF') ? 'All Invoices' : 'My Invoices'; ?>
                    </h2>
                </div>
                
                <?php if (empty($sales)): ?>
                    <div class="alert alert-info">
                        <p class="mb-0">No purchases found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-dark table-striped text-white">
                            <thead>
                                <tr>
                                    <th scope="col">Invoice #</th>
                                    <th scope="col">Date & Time</th>
                                    <th scope="col">Customer</th>
                                    <?php if ($userRole === 'ADMIN' || $userRole === 'STAFF'): ?>
                                        <th scope="col">Staff</th>
                                    <?php endif; ?>
                                    <th scope="col">Total Amount</th>
                                    <th scope="col">Payment Method</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sales as $sale): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($sale['SALEID']); ?></td>
                                        <td><?php echo htmlspecialchars($sale['SALEDATETIME']); ?></td>
                                        <td><?php echo htmlspecialchars($sale['CUSTOMER_NAME'] ?? 'N/A'); ?></td>
                                        <?php if ($userRole === 'ADMIN' || $userRole === 'STAFF'): ?>
                                            <td><?php echo htmlspecialchars($sale['STAFF_NAME'] ?? 'N/A'); ?></td>
                                        <?php endif; ?>
                                        <td>RM <?php echo number_format($sale['TOTALPRICE'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($sale['PAYMENTMETHOD'] ?? 'N/A'); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary view-details" 
                                                    data-sale-id="<?php echo $sale['SALEID']; ?>"
                                                    data-toggle="modal" 
                                                    data-target="#invoiceModal">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Details Modal -->
<div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceModalLabel">Invoice Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="invoiceDetailsContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">Print Invoice</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-details').forEach(button => {
        button.addEventListener('click', function() {
            const saleId = this.dataset.saleId;
            
            // Load invoice details via fetch
            fetch('get_invoice_details.php?sale_id=' + saleId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('invoiceDetailsContent').innerHTML = data;
                    const modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
                    modal.show();
                })
                .catch(error => {
                    document.getElementById('invoiceDetailsContent').innerHTML = '<div class="alert alert-danger">Failed to load invoice details.</div>';
                    console.error('Error:', error);
                });
        });
    });
});
</script>

<style>
    .tm-bg-primary-dark {
        background-color: rgba(0, 0, 0, 0.7);
        padding: 30px;
        border-radius: 10px;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .modal-content {
        background-color: #2c3e50 !important;
    }
    
    @media print {
        body * {
            visibility: hidden;
        }
        #invoiceDetailsContent, #invoiceDetailsContent * {
            visibility: visible;
        }
        #invoiceDetailsContent {
            position: absolute;
            left: 0;
            top: 0;
        }
    }
</style>

<?php include('includes/footer.php'); ?>