<?php
require_once __DIR__ . '/includes/store.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Sales History';
include __DIR__ . '/partials/header.php';

$user = $_SESSION['user'];
$sales = array_reverse(getSales());
if (($user['role'] ?? '') !== 'admin') {
    $sales = array_values(array_filter($sales, function ($sale) use ($user) {
        return (int) ($sale['user_id'] ?? 0) === (int) ($user['id'] ?? 0);
    }));
}
?>
<div class="card">
    <div class="card-header">
        <h2>My Transactions</h2>
    </div>
    <table class="table-list">
        <thead>
            <tr>
                <th>Receipt</th>
                <th>Date</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$sales): ?>
                <tr><td colspan="5">No receipts yet. Pay at the POS counter to create one.</td></tr>
            <?php endif; ?>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?php echo htmlspecialchars($sale['receipt_no']); ?></td>
                    <td><?php echo htmlspecialchars($sale['created_at']); ?></td>
                    <td><?php echo money($sale['total_amount']); ?></td>
                    <td><?php echo htmlspecialchars(strtoupper($sale['payment_method'])); ?></td>
                    <td>
                        <a class="link-button" href="receipt.php?no=<?php echo urlencode($sale['receipt_no']); ?>">View / Print</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
