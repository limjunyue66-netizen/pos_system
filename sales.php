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

$sales = array_reverse(getSales());
$today = date('Y-m-d');
$todayTotal = 0;
$pending = 0;
$countToday = 0;
foreach ($sales as $sale) {
    if (strpos($sale['created_at'] ?? '', $today) === 0) {
        $todayTotal += (float) $sale['total_amount'];
        $countToday++;
    }
    if (($sale['status'] ?? '') === 'pending') {
        $pending++;
    }
}
$average = $countToday ? $todayTotal / $countToday : 0;
$pageTitle = 'Sales Management';
include __DIR__ . '/partials/header.php';
?>
<section class="dashboard-grid">
    <article class="card stats-card">
        <h2>Total Sales Today</h2>
        <p class="stat-value"><?php echo money($todayTotal); ?></p>
    </article>
    <article class="card stats-card">
        <h2>Pending Orders</h2>
        <p class="stat-value"><?php echo $pending; ?></p>
    </article>
    <article class="card stats-card">
        <h2>Average Transaction</h2>
        <p class="stat-value"><?php echo money($average); ?></p>
    </article>
</section>
<div class="card">
    <div class="card-header">
        <h2>Recent Sales</h2>
    </div>
    <table class="table-list">
        <thead>
            <tr>
                <th>Receipt</th>
                <th>Cashier</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$sales): ?>
                <tr><td colspan="6">No sales yet. Complete a payment at the POS counter.</td></tr>
            <?php endif; ?>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?php echo htmlspecialchars($sale['receipt_no']); ?></td>
                    <td><?php echo htmlspecialchars($sale['cashier']); ?></td>
                    <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                    <td><?php echo money($sale['total_amount']); ?></td>
                    <td><span class="status success"><?php echo ucfirst($sale['status']); ?></span></td>
                    <td>
                        <a class="link-button" href="receipt.php?no=<?php echo urlencode($sale['receipt_no']); ?>">View / Print</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
