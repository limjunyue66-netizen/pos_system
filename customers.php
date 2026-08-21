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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_customer') {
    $result = addCustomer($_POST);
    if (isset($result['error'])) {
        setFlash('error', $result['error']);
    } else {
        setFlash('success', 'Customer "' . $result['name'] . '" added.');
    }
    redirectWith('customers.php');
}

$customers = getCustomers();
$flash = getFlash();
$pageTitle = 'Customers';
include __DIR__ . '/partials/header.php';
?>
<?php if ($flash): ?>
    <div class="flash <?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>">
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>
<section class="actions-row">
    <button class="button primary" type="button" onclick="openModal('addCustomerModal')">Add New Customer</button>
</section>
<div class="card">
    <div class="card-header">
        <h2>Customer List</h2>
    </div>
    <table class="table-list">
        <thead>
            <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Membership</th>
                <th>Points</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Walk-in</td>
                <td>—</td>
                <td>—</td>
                <td>—</td>
                <td>0</td>
            </tr>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer['name']); ?></td>
                    <td><?php echo htmlspecialchars($customer['phone'] ?: '—'); ?></td>
                    <td><?php echo htmlspecialchars($customer['email'] ?: '—'); ?></td>
                    <td><?php echo htmlspecialchars($customer['membership_no']); ?></td>
                    <td><?php echo (int) $customer['loyalty_points']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="addCustomerModal" class="modal">
    <div class="modal-card">
        <div class="card-header">
            <h2>Add New Customer</h2>
            <button class="button secondary mini" type="button" onclick="closeModal('addCustomerModal')">Close</button>
        </div>
        <form method="post">
            <input type="hidden" name="action" value="add_customer">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" required>
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
                    <label for="membership_no">Membership No</label>
                    <input id="membership_no" name="membership_no" type="text" placeholder="Leave blank to auto-generate">
                </div>
            </div>
            <div class="actions-row">
                <button class="button primary" type="submit">Save Customer</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
