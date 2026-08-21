<?php
session_start();
require_once __DIR__ . '/includes/store.php';
require_once __DIR__ . '/includes/helpers.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_supplier') {
    $result = addSupplier($_POST);
    if (isset($result['error'])) {
        setFlash('error', $result['error']);
    } else {
        setFlash('success', 'Supplier "' . $result['name'] . '" added.');
    }
    redirectWith('suppliers.php');
}

$suppliers = getSuppliers();
$flash = getFlash();
$pageTitle = 'Suppliers';
include __DIR__ . '/partials/header.php';
?>
<?php if ($flash): ?>
    <div class="flash <?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>">
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>
<section class="actions-row">
    <button class="button primary" type="button" onclick="openModal('addSupplierModal')">Add New Supplier</button>
</section>
<div class="card">
    <div class="card-header">
        <h2>Supplier Directory</h2>
    </div>
    <table class="table-list">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suppliers as $supplier): ?>
                <tr>
                    <td><?php echo htmlspecialchars($supplier['name']); ?></td>
                    <td><?php echo htmlspecialchars($supplier['contact_person'] ?: '—'); ?></td>
                    <td><?php echo htmlspecialchars($supplier['phone'] ?: '—'); ?></td>
                    <td><?php echo htmlspecialchars($supplier['email'] ?: '—'); ?></td>
                    <td><?php echo htmlspecialchars($supplier['address'] ?: '—'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="addSupplierModal" class="modal">
    <div class="modal-card">
        <div class="card-header">
            <h2>Add New Supplier</h2>
            <button class="button secondary mini" type="button" onclick="closeModal('addSupplierModal')">Close</button>
        </div>
        <form method="post">
            <input type="hidden" name="action" value="add_supplier">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Supplier</label>
                    <input id="name" name="name" type="text" required>
                </div>
                <div class="form-group">
                    <label for="contact_person">Contact Person</label>
                    <input id="contact_person" name="contact_person" type="text">
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" type="text">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input id="address" name="address" type="text">
                </div>
            </div>
            <div class="actions-row">
                <button class="button primary" type="submit">Save Supplier</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
