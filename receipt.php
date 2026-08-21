<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/includes/store.php';
require_once __DIR__ . '/includes/helpers.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$receiptNo = trim($_GET['no'] ?? '');
$sale = $receiptNo !== '' ? findSaleByReceipt($receiptNo) : null;
if (!$sale) {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $sale ? 'Receipt ' . htmlspecialchars($sale['receipt_no']) : 'Receipt not found'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="receipt-page">
    <div class="receipt-actions no-print">
        <a class="button secondary" href="javascript:history.back()">Back</a>
        <?php if ($sale): ?>
            <button class="button primary" type="button" onclick="window.print()">Print Receipt</button>
        <?php endif; ?>
    </div>
    <?php if (!$sale): ?>
        <div class="card">
            <h2>Receipt not found</h2>
            <p>Complete a payment at the POS counter first, then open the receipt from Sales or History.</p>
        </div>
    <?php else: ?>
        <pre class="receipt-paper"><?php
            echo "SUPERMARKET POS SYSTEM\n";
            echo "123 Supermarket Ave, Suite 100\n";
            echo "Kuala Lumpur, Malaysia\n";
            echo "Tel: +60 3-8888 9999\n";
            echo str_repeat('-', 40) . "\n";
            echo 'Receipt No: ' . $sale['receipt_no'] . "\n";
            echo 'Date: ' . $sale['created_at'] . "\n";
            echo 'Cashier: ' . $sale['cashier'] . "\n";
            echo 'Customer: ' . $sale['customer_name'] . "\n";
            echo 'Payment: ' . strtoupper($sale['payment_method']) . "\n";
            echo str_repeat('-', 40) . "\n";
            echo "Item                    Qty   Total\n";
            echo str_repeat('-', 40) . "\n";
            foreach ($sale['items'] as $item) {
                $name = str_pad(substr($item['name'], 0, 22), 22);
                $qty = str_pad((string) $item['quantity'], 5);
                $total = str_pad(money($item['subtotal']), 11, ' ', STR_PAD_LEFT);
                echo $name . $qty . $total . "\n";
            }
            echo str_repeat('-', 40) . "\n";
            echo 'Subtotal: ' . money($sale['subtotal']) . "\n";
            echo 'Tax (3%): ' . money($sale['tax']) . "\n";
            if ((float) $sale['discount'] > 0) {
                echo 'Discount: ' . money($sale['discount']) . "\n";
            }
            echo 'TOTAL: ' . money($sale['total_amount']) . "\n";
            if (($sale['payment_method'] ?? '') === 'cash') {
                echo 'Cash Tendered: ' . money($sale['paid_amount']) . "\n";
                echo 'Change Due: ' . money($sale['change_amount']) . "\n";
            }
            echo str_repeat('-', 40) . "\n";
            echo "Thank you for shopping with us!\n";
            echo "Goods sold are not returnable or refundable.\n";
        ?></pre>
    <?php endif; ?>
</body>
</html>
