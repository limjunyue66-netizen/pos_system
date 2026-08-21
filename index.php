<?php
require_once __DIR__ . '/includes/store.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Dashboard';
include __DIR__ . '/partials/header.php';

$products = getProducts();
$sales = getSales();
$today = date('Y-m-d');
$todayTotal = 0;
$weekTotal = 0;
$monthTotal = 0;
$weekStart = date('Y-m-d', strtotime('-6 days'));
$monthPrefix = date('Y-m');
$topCounts = [];

foreach ($sales as $sale) {
    $date = substr($sale['created_at'] ?? '', 0, 10);
    $amount = (float) $sale['total_amount'];
    if ($date === $today) {
        $todayTotal += $amount;
    }
    if ($date >= $weekStart) {
        $weekTotal += $amount;
    }
    if (strpos($sale['created_at'] ?? '', $monthPrefix) === 0) {
        $monthTotal += $amount;
    }
    foreach ($sale['items'] as $item) {
        $name = $item['name'];
        $topCounts[$name] = ($topCounts[$name] ?? 0) + (int) $item['quantity'];
    }
}

arsort($topCounts);
$topProducts = array_slice(array_keys($topCounts), 0, 5);
$lowStock = 0;
foreach ($products as $product) {
    if ((int) $product['stock'] <= (int) ($product['reorder_level'] ?? 0)) {
        $lowStock++;
    }
}
$recent = array_slice(array_reverse($sales), 0, 8);
?>
<section class="dashboard-grid">
    <article class="card stats-card">
        <h2>Today's Sales</h2>
        <p class="stat-value"><?php echo money($todayTotal); ?></p>
    </article>
    <article class="card stats-card">
        <h2>Weekly Sales</h2>
        <p class="stat-value"><?php echo money($weekTotal); ?></p>
    </article>
    <article class="card stats-card">
        <h2>Monthly Revenue</h2>
        <p class="stat-value"><?php echo money($monthTotal); ?></p>
    </article>
    <article class="card stats-card">
        <h2>Total Orders</h2>
        <p class="stat-value"><?php echo count($sales); ?></p>
    </article>
    <article class="card stats-card">
        <h2>Total Products</h2>
        <p class="stat-value"><?php echo count($products); ?></p>
    </article>
    <article class="card stats-card low-stock">
        <h2>Low Stock Alerts</h2>
        <p><?php echo $lowStock; ?> items need restock</p>
    </article>
</section>

<section class="charts-section">
    <div class="card chart-card">
        <div class="card-header">
            <h2>Quick Actions</h2>
        </div>
        <div class="actions-row">
            <a class="button primary" href="pos.php">Open POS Counter</a>
            <?php if (isAdmin()): ?>
                <a class="button secondary" href="products.php">Add Product</a>
                <a class="button secondary" href="users.php">Add User</a>
                <a class="button secondary" href="sales.php">View Receipts</a>
            <?php else: ?>
                <a class="button secondary" href="history.php">My Receipts</a>
            <?php endif; ?>
        </div>
        <p class="muted-note">Pay at the POS counter to create a receipt. Then use View / Print to open it.</p>
    </div>
    <div class="card chart-card">
        <div class="card-header">
            <h2>Top Selling Products</h2>
        </div>
        <ul class="top-products-list">
            <?php if (!$topProducts): ?>
                <li>No sales yet</li>
            <?php endif; ?>
            <?php foreach ($topProducts as $index => $name): ?>
                <li><?php echo ($index + 1) . '. ' . htmlspecialchars($name); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<section class="recent-section">
    <div class="card">
        <div class="card-header">
            <h2>Recent Transactions</h2>
            <a class="link-button" href="<?php echo isAdmin() ? 'sales.php' : 'history.php'; ?>">View All</a>
        </div>
        <table class="table-list">
            <thead>
                <tr>
                    <th>Receipt</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$recent): ?>
                    <tr><td colspan="6">No transactions yet.</td></tr>
                <?php endif; ?>
                <?php foreach ($recent as $sale): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sale['receipt_no']); ?></td>
                        <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                        <td><?php echo money($sale['total_amount']); ?></td>
                        <td><?php echo htmlspecialchars(strtoupper($sale['payment_method'])); ?></td>
                        <td><span class="status success"><?php echo ucfirst($sale['status']); ?></span></td>
                        <td><a class="link-button" href="receipt.php?no=<?php echo urlencode($sale['receipt_no']); ?>">View / Print</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
