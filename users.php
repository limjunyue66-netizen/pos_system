<?php
session_start();
require_once __DIR__ . '/api/auth.php';
require_once __DIR__ . '/includes/helpers.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_user') {
    $result = createManagedUser(
        $_POST['name'] ?? '',
        $_POST['username'] ?? '',
        $_POST['email'] ?? '',
        $_POST['password'] ?? '',
        $_POST['role'] ?? 'cashier'
    );
    if (isset($result['error'])) {
        setFlash('error', $result['error']);
    } else {
        setFlash('success', 'User "' . $result['username'] . '" created.');
    }
    redirectWith('users.php');
}

$users = readUsers();
$flash = getFlash();
$pageTitle = 'Users';
include __DIR__ . '/partials/header.php';
?>
<?php if ($flash): ?>
    <div class="flash <?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>">
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>
<section class="actions-row">
    <button class="button primary" type="button" onclick="openModal('addUserModal')">Add New User</button>
</section>
<div class="card">
    <div class="card-header">
        <h2>User Accounts</h2>
    </div>
    <table class="table-list">
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $account): ?>
                <tr>
                    <td><?php echo htmlspecialchars($account['name']); ?></td>
                    <td><?php echo htmlspecialchars($account['username']); ?></td>
                    <td><?php echo htmlspecialchars($account['email']); ?></td>
                    <td><?php echo ucfirst(htmlspecialchars($account['role'])); ?></td>
                    <td>
                        <span class="status <?php echo !empty($account['locked']) ? 'danger' : 'success'; ?>">
                            <?php echo !empty($account['locked']) ? 'Locked' : 'Active'; ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="addUserModal" class="modal">
    <div class="modal-card">
        <div class="card-header">
            <h2>Add New User</h2>
            <button class="button secondary mini" type="button" onclick="closeModal('addUserModal')">Close</button>
        </div>
        <form method="post">
            <input type="hidden" name="action" value="add_user">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input id="name" name="name" type="text" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required minlength="4">
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role">
                        <option value="cashier">Cashier</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <div class="actions-row">
                <button class="button primary" type="submit">Save User</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
