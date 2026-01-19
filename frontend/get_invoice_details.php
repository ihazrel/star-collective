<?php
// get_invoice_details.php
session_start();
require_once('../backend/functions/sale-functions.php');
require_once('../backend/functions/saleDetail-functions.php');

if (!isset($_SESSION['user_id']) || !isset($_GET['sale_id'])) {
    echo '<div class="alert alert-danger">Unauthorized access.</div>';
    exit();
}

$saleId = $_GET['sale_id'];
$sale = getSaleById($saleId);

$paymentMethod = getPaymentMethodBySaleId($saleId);

if (!$sale) {
    echo '<div class="alert alert-danger">Invoice not found.</div>';
    exit();
}

// Get sale details (items purchased)
$saleDetails = getSaleDetailsBySaleId($saleId);
?>

<div class="invoice-container">
    <div class="text-center mb-4">
        <h3>Star Collective</h3>
        <p class="mb-1">Lifestyle & Apparel</p>
        <p class="mb-0"><small>Thank you for your purchase!</small></p>
    </div>
    
    <hr class="bg-white">
    
    <div class="row mb-3">
        <div class="col-6">
            <strong>Invoice #:</strong> <?php echo htmlspecialchars($saleId); ?><br>
            <strong>Date:</strong> <?php echo htmlspecialchars($sale['SALEDATETIME']); ?>
        </div>
        <div class="col-6 text-right">
            <strong>Customer ID:</strong> <?php echo htmlspecialchars($sale['CUSTOMERID']); ?><br>
            <strong>Payment Method:</strong> <?php echo htmlspecialchars($paymentMethod ?? 'N/A'); ?>
        </div>
    </div>
    
    <hr class="bg-white">
    
    <h5 class="mb-3">Items Purchased</h5>
    <table class="table table-bordered table-sm text-white">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($saleDetails as $detail): ?>
                <tr>
                    <td><?php echo htmlspecialchars($detail['ITEMNAME'] ?? 'Item #' . $detail['ITEMID']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($detail['QUANTITY']); ?></td>
                    <td class="text-right">RM <?php echo number_format($detail['UNITPRICE'], 2); ?></td>
                    <td class="text-right">RM <?php echo number_format($detail['FINALPRICE'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                <td class="text-right"><strong>RM <?php echo number_format($sale['TOTALPRICE'], 2); ?></strong></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="text-center mt-4">
        <p class="mb-0"><small>This is a computer-generated invoice. No signature required.</small></p>
    </div>
</div>

<style>
    .invoice-container {
        padding: 20px;
    }
    
    .table-bordered {
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .table-bordered th,
    .table-bordered td {
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
</style>