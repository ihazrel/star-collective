<?php include('includes/header.php'); ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <section id="overview">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold">Staff Dashboard</h1>
            <span class="badge border border-info text-info p-2">
                Shift: <?php echo date('l, d F Y'); ?>
            </span>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card stat-card p-3 border-danger">
                    <h6 class="text-secondary small text-uppercase">Low Stock Alerts</h6>
                    <h3 class="text-danger">12</h3>
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

    <div class="row g-4 mb-5">
        <div class="col-lg-12" id="inventory">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Update Stock Levels</h4>
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm" placeholder="Search Product...">
                        <button class="btn btn-outline-success btn-sm text-nowrap">Check Stock</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead class="text-secondary">
                            <tr>
                                <th>SKU</th>
                                <th>Product Name</th>
                                <th>Current Stock</th>
                                <th>Status</th>
                                <th>Update Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SKU-W-001</td>
                                <td>Oversized White T-Shirt</td>
                                <td>42 Units</td>
                                <td><span class="text-success small">Healthy</span></td>
                                <td><input type="number" class="form-control form-control-sm w-75" placeholder="Add/Sub"></td>
                                <td><button class="btn btn-accent btn-sm">Update</button></td>
                            </tr>
                            <tr class="table-danger">
                                <td>SKU-C-005</td>
                                <td>Black Cargo Pants</td>
                                <td>3 Units</td>
                                <td><span class="text-danger small fw-bold">LOW STOCK</span></td>
                                <td><input type="number" class="form-control form-control-sm w-75" placeholder="Add/Sub"></td>
                                <td><button class="btn btn-accent btn-sm">Update</button></td>
                            </tr>
                            <tr>
                                <td>SKU-H-012</td>
                                <td>Graphic Hoodie (Astro)</td>
                                <td>15 Units</td>
                                <td><span class="text-warning small">Moderate</span></td>
                                <td><input type="number" class="form-control form-control-sm w-75" placeholder="Add/Sub"></td>
                                <td><button class="btn btn-accent btn-sm">Update</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-5" id="procurement">
            <div class="card p-4 h-100">
                <h4 class="mb-4">Procurement & Vendors</h4>
                <div class="mb-3">
                    <label class="form-label small text-secondary">Select Vendor</label>
                    <select class="form-select">
                        <option selected disabled>Choose Vendor...</option>
                        <option>Pak Gembus Sdn Bhd</option>
                        <option>Adidas</option>
                        <option>SVG Worldwide</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-secondary">Comments / Instructions</label>
                    <textarea class="form-control" rows="2" placeholder="Restock request for..."></textarea>
                </div>
                <button class="btn btn-accent w-100 mb-3">Create Purchase Order</button>
                <hr class="border-secondary">
                <p class="small text-secondary mb-2">My Recent PO Requests</p>
                <div class="list-group list-group-flush bg-transparent">
                    <div class="list-group-item bg-transparent text-white border-secondary px-0 py-1">
                        <small>PO #8821 - SVG Worldwide <span class="float-end text-warning">Pending Approval</span></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7" id="sales">
            <div class="card p-4 h-100">
                <div class="row">
                    <div class="col-md-5 border-end border-secondary">
                        <h4 class="mb-4">Record New Sale</h4>
                        <form action="#" method="POST">
                            <div class="mb-3">
                                <label class="form-label small">Scan/Search SKU</label>
                                <input type="text" class="form-control" placeholder="Enter ID">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Payment Type</label>
                                <select class="form-select">
                                    <option>Cash</option>
                                    <option>Credit Card</option>
                                    <option>FPX Transfer</option>
                                    <option>E-Wallet</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-accent btn-sm w-100">Record Sale Detail</button>
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-2">Clear Sale Cart</button>
                        </form>
                    </div>
                    <div class="col-md-7 ps-md-4">
                        <h4 class="mb-4">Live Sales Log</h4>
                        <div class="table-responsive">
                            <table class="table table-dark table-sm">
                                <thead>
                                    <tr>
                                        <th>Inv #</th>
                                        <th>Total</th>
                                        <th>Payment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#STF-001</td>
                                        <td>RM120.00</td>
                                        <td>Cash</td>
                                        <td><button class="btn btn-link text-accent btn-sm p-0">Print</button></td>
                                    </tr>
                                    <tr>
                                        <td>#STF-002</td>
                                        <td>RM85.00</td>
                                        <td>E-Wallet</td>
                                        <td><button class="btn btn-link text-accent btn-sm p-0">Print</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>