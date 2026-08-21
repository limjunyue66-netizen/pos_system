<?php
require_once __DIR__ . '/includes/store.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Inventory';
include __DIR__ . '/partials/header.php';
requireAdminPage();

$products = getProducts();
$stockValue = 0;
$lowStock = [];
foreach ($products as $product) {
    $stockValue += ((float) $product['cost_price']) * ((int) $product['stock']);
    if ((int) $product['stock'] <= (int) ($product['reorder_level'] ?? 0)) {
        $lowStock[] = $product;
    }
}
$sales = getSales();
$stockOutToday = 0;
$today = date('Y-m-d');
foreach ($sales as $sale) {
    if (strpos($sale['created_at'] ?? '', $today) === 0) {
        foreach ($sale['items'] as $item) {
            $stockOutToday += (int) $item['quantity'];
        }
    }
}
?>
<section class="dashboard-grid">
    <article class="card stats-card">
        <h2>Current Stock Value</h2>
        <p class="stat-value"><?php echo money($stockValue); ?></p>
    </article>
    <article class="card stats-card">
        <h2>Stock Out Today</h2>
        <p class="stat-value"><?php echo $stockOutToday; ?> items</p>
    </article>
    <article class="card stats-card low-stock">
        <h2>Low Stock Items</h2>
        <p><?php echo count($lowStock); ?> products below reorder level</p>
    </article>
</section>
<div class="card">
    <div class="card-header">
        <h2>Stock Levels</h2>
    </div>
    <table class="table-list">
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Stock</th>
                <th>Reorder Level</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <?php $isLow = (int) $product['stock'] <= (int) ($product['reorder_level'] ?? 0); ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['sku']); ?></td>
                    <td><?php echo (int) $product['stock']; ?></td>
                    <td><?php echo (int) ($product['reorder_level'] ?? 0); ?></td>
                    <td>
                        <span class="status <?php echo $isLow ? 'warning' : 'success'; ?>">
                            <?php echo $isLow ? 'Low stock' : 'OK'; ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
