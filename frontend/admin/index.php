<?php include('includes/header.php'); ?>
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


            <section id="users" class="mb-5">
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">User Management (Staff & Customers)</h4>
                        <div class="btn-group">
                            <button class="btn btn-accent btn-sm" data-bs-toggle="modal" data-bs-target="#userModal">+ Add Staff</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle">
                            <thead class="text-secondary">
                                <tr>
                                    <th>ID</th>
                                    <th>User Info</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#S001</td>
                                    <td>
                                        <strong>Hazrel</strong><br>
                                        <small class="text-secondary">Email: hazrel_bos@starcollective.com</small><br>
                                        <small class="text-secondary">Username: hazrel</small>
                                    </td>
                                    <td><span class="badge bg-primary">STAFF</span></td>
                                    <td><small>Full (Manager)</small></td>
                                </tr>
                                <tr>
                                    <td>#C001</td>
                                    <td>
                                        <strong>Zul</strong><br><small class="text-secondary">Email: zul@gmail.com</small><br>
                                        <small class="text-secondary">Username: zul</small>
                                    </td>
                                    <td><span class="badge bg-secondary">CUSTOMER</span></td>
                                    <td><small>Restricted</small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <div class="row g-4 mb-5">
                <div class="col-lg-7" id="inventory">
                    <div class="card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0">Inventory & Stock Update</h4>
                            <button class="btn btn-outline-success btn-sm">Update Stock Table</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-dark table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Status</th>
                                        <th>Quick Add</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Oversized White T-Shirt</td>
                                        <td>42 Units</td>
                                        <td><span class="text-success small">Healthy</span></td>
                                        <td><input type="number" class="form-control form-control-sm w-50" value="0"></td>
                                    </tr>
                                    <tr class="table-danger">
                                        <td>Black Cargo Pants</td>
                                        <td>3 Units</td>
                                        <td><span class="text-danger small">LOW STOCK</span></td>
                                        <td><input type="number" class="form-control form-control-sm w-50" value="0"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5" id="procurement">
                    <div class="card p-4 h-100">
                        <h4 class="mb-4">Procurement & Vendors</h4>
                        <div class="mb-3">
                            <label class="form-label small text-secondary">Select Vendor</label>
                            <select class="form-select">
                                <option>Pak Gembus Sdn Bhd</option>
                                <option>Adidas</option>
                                <option>SVG Worldwide</option>
                            </select>
                        </div>
                        <button class="btn btn-accent w-100 mb-3">Create Purchase Order</button>
                        <hr class="border-secondary">
                        <p class="small text-secondary mb-2">Recent Purchase Orders</p>
                        <div class="list-group list-group-flush bg-transparent">
                            <div class="list-group-item bg-transparent text-white border-secondary px-0 py-1">
                                <small>PO #8821 - SVG Worldwide <span class="float-end text-warning">Pending</span></small>
                            </div>
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
      <form>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">User Type</label>
                <select class="form-select">
                    <option value="staff">Staff Member</option>
                    <option value="admin">Admin Partner</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" required>
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
</body>
</html>