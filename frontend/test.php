<?php
require_once __DIR__ . '/../backend/functions/item-functions.php';

$items = getAllItems();

$column_sort = [
    'NAME' => 'Name',
    'PRICE' => 'Price',
    'CURRENTSTOCK' => 'Current Stock',
    'LASTUPDATEDATETIME' => 'Updated On'
];

function refresh_data($column, $sort_direction) {
    global $items;

    if ($column && $sort_direction) {
        $items = getAllItems($column, $sort_direction);
        return;
    } else {
        $items = getAllItems();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['action'] ?? '';
    
    switch ($formType) {
        case 'addForm':
            $name = $_POST['name'] ?? '';
            $price = $_POST['price'] ?? '';
            $currentStock = $_POST['currentStock'] ?? '';

            $result = createItem($name, $price, $currentStock);
            refresh_data(null, null);

            if ($result['status']) {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?added=1');
                exit;
            } else {
                $errorMessage = $result['message'];
            }
            break;

        case 'editForm':
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';
            $price = $_POST['price'] ?? '';
            $currentStock = $_POST['currentStock'] ?? '';

            $result = editItem($id, $name, $price, $currentStock);
            refresh_data(null, null);

            if ($result['status']) {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?updated=1');
                exit;
            } else {
                $errorMessage = $result['message'];
            }
            break;

        case 'deleteForm':
            $id = $_POST['id'] ?? '';

            $result = deleteItem($id);
            refresh_data(null, null);

            if ($result['status']) {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?deleted=1');
                exit;
            } else {
                $errorMessage = $result['message'];
            }
            break;
    exit;}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table with Modal Actions</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .btn { padding: 8px 12px; margin: 5px; cursor: pointer; border: none; border-radius: 4px; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1; }
        .modal.active { display: flex; justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 30px; border-radius: 8px; width: 400px; }
        .modal-header { font-size: 20px; font-weight: bold; margin-bottom: 15px; }
        input[type="text"] { width: 100%; padding: 8px; margin: 10px 0; box-sizing: border-box; }
        .modal-footer { margin-top: 20px; text-align: right; }
        .clickable-cell { cursor: pointer; transition: background-color 0.2s; }
        .clickable-cell:hover { background-color: #e7f3ff; }
        .clickable-header { cursor: pointer; transition: background-color 0.2s; }
        .clickable-header:hover { background-color: #d0e0f0; }
    </style>
</head>
<body>
    <h1>Item Management</h1>
    <button class="btn btn-success" onclick="openModal('addModal')">+ Add New</button>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Current Stock</th>
                <th>Updated On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= $item['NAME'] ?></td>
                <td><?= $item['PRICE'] ?></td>
                <td><?= $item['CURRENTSTOCK'] ?></td>
                <td><?= $item['LASTUPDATEDATETIME'] ?></td>
                <td>
                    <button class="btn btn-primary" onclick="openEditModal(<?= $item['ITEMID'] ?>, '<?= $item['NAME'] ?>', '<?= $item['PRICE'] ?>', '<?= $item['CURRENTSTOCK'] ?>', '<?= $item['LASTUPDATEDATETIME'] ?>')">Edit</button>
                    <button class="btn btn-danger" onclick="openDeleteModal(<?= $item['ITEMID'] ?>)">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Add Modal -->
    <form id="addForm" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <input type="hidden" name="action" value="addForm">

        <div id="addModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">Add New Item</div>
                <input type="text" id="addName" name="name" placeholder="Name" required>
                <input type="text" id="addPrice" name="price" placeholder="Price" required>
                <input type="text" id="addCurrentStock" name="currentStock" placeholder="Current Stock" required>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn" onclick="closeModal('addModal')">Cancel</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Edit Modal -->
    <form id="editForm" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <input type="hidden" name="action" value="editForm">

        <div id="editModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">Edit Item</div>
                <input type="hidden" id="editId" name="id">
                <input type="text" id="editName" name="name" placeholder="Name">
                <input type="text" id="editPrice" name="price" placeholder="Price">
                <input type="text" id="editCurrentStock" name="currentStock" placeholder="Current Stock">
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn" onclick="closeModal('editModal')">Cancel</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Delete Modal -->
    <form id="deleteForm" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <input type="hidden" name="action" value="deleteForm">
    
        <div id="deleteModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">Confirm Delete</div>
                <p>Are you sure you want to delete this item?</p>
                <input type="hidden" id="deleteId" name="id">
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn" onclick="closeModal('deleteModal')">Cancel</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
        function openEditModal(id, name, price, currentStock, lastUpdateDateTime) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editPrice').value = price;
            document.getElementById('editCurrentStock').value = currentStock;
            openModal('editModal');
        }
        function openDeleteModal(id) {
            document.getElementById('deleteId').value = id;
            openModal('deleteModal');
        }
        function saveAdd() {
            alert('Add: ' + document.getElementById('addName').value);
            closeModal('addModal');
        }
        function saveEdit() {
            alert('Edit ID: ' + document.getElementById('editId').value);
            closeModal('editModal');
        }
        function confirmDelete() {
            alert('Deleted ID: ' + document.getElementById('deleteId').value);
            closeModal('deleteModal');
        }
    </script>
</body>
</html>