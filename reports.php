<?php
require_once __DIR__ . '/includes/store.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Reports';
include __DIR__ . '/partials/header.php';
requireAdminPage();

$products = getProducts();
$sales = getSales();
$today = date('Y-m-d');
$todayTotal = 0;
$stockValue = 0;
$topName = '—';
$topCounts = [];

foreach ($sales as $sale) {
    if (strpos($sale['created_at'] ?? '', $today) === 0) {
        $todayTotal += (float) $sale['total_amount'];
    }
    foreach ($sale['items'] as $item) {
        $topCounts[$item['name']] = ($topCounts[$item['name']] ?? 0) + (int) $item['quantity'];
    }
}
if ($topCounts) {
    arsort($topCounts);
    $topName = array_key_first($topCounts);
}
foreach ($products as $product) {
    $stockValue += ((float) $product['cost_price']) * ((int) $product['stock']);
}
?>
<section class="dashboard-grid">
    <article class="card stats-card">
        <h2>Daily Sales</h2>
        <p class="stat-value"><?php echo money($todayTotal); ?></p>
    </article>
    <article class="card stats-card">
        <h2>Top Product</h2>
        <p class="stat-value" style="font-size:1.3rem;"><?php echo htmlspecialchars($topName); ?></p>
    </article>
    <article class="card stats-card">
        <h2>Inventory Value</h2>
        <p class="stat-value"><?php echo money($stockValue); ?></p>
    </article>
</section>
<div class="card">
    <div class="card-header">
        <h2>Recent Receipts</h2>
    </div>
    <table class="table-list">
        <thead>
            <tr>
                <th>Receipt</th>
                <th>Date</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $recent = array_slice(array_reverse($sales), 0, 20); ?>
            <?php if (!$recent): ?>
                <tr><td colspan="4">No receipts yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($recent as $sale): ?>
                <tr>
                    <td><?php echo htmlspecialchars($sale['receipt_no']); ?></td>
                    <td><?php echo htmlspecialchars($sale['created_at']); ?></td>
                    <td><?php echo money($sale['total_amount']); ?></td>
                    <td><a class="link-button" href="receipt.php?no=<?php echo urlencode($sale['receipt_no']); ?>">View / Print</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
