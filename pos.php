<?php
require_once __DIR__ . '/includes/store.php';

$pageTitle = 'POS Counter';
include __DIR__ . '/partials/header.php';

$posProducts = array_values(array_filter(getProducts(), function ($product) {
    return ($product['status'] ?? 'active') === 'active';
}));
$posCustomers = getCustomers();
$posPayload = [];
foreach ($posProducts as $product) {
    $posPayload[] = [
        'id' => (int) $product['id'],
        'name' => $product['name'],
        'sku' => $product['sku'],
        'category' => $product['category'],
        'price' => (float) $product['sell_price'],
        'stock' => (int) $product['stock'],
        'barcode' => (string) ($product['barcode'] ?? ''),
        'image' => $product['image'] ?? 'assets/images/default.svg',
    ];
}
?>
<script>
    window.currentUser = {
        name: <?php echo json_encode($user['name']); ?>,
        username: <?php echo json_encode($user['username']); ?>
    };
    window.posProducts = <?php echo json_encode($posPayload); ?>;
    window.posCustomers = <?php echo json_encode($posCustomers); ?>;
</script>
<div class="pos-grid">
    <section class="pos-panel">
        <div class="card">
            <div class="card-header">
                <h2>Scan or Search Product</h2>
            </div>
            <div class="form-group">
                <label>Scan Barcode here...</label>
                <input id="barcodeInput" type="text" placeholder="Scan barcode here...">
            </div>
            <div class="form-group">
                <label>Search product name, SKU or barcode...</label>
                <input id="searchInput" type="text" placeholder="Search product name, SKU or barcode...">
            </div>
            <div class="category-tabs"></div>
            <div class="product-grid"></div>
        </div>
        <div class="card">
            <div class="card-header">
                <h2>Customer</h2>
            </div>
            <div class="form-group">
                <label>Customer</label>
                <select id="customerSelect">
                    <option value="">Walk-in Customer</option>
                    <?php foreach ($posCustomers as $customer): ?>
                        <option value="<?php echo (int) $customer['id']; ?>">
                            <?php echo htmlspecialchars($customer['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="button secondary" type="button" id="createCustomerButton">Create New Customer</button>
        </div>
    </section>
    <section class="cart-panel">
        <div class="card">
            <div class="card-header">
                <h2>Shopping Cart</h2>
            </div>
            <table class="table-list cart-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="summary-row">
                <div>Subtotal</div>
                <div id="subtotalValue">RM 0.00</div>
            </div>
            <div class="summary-row">
                <div>Discount</div>
                <div id="discountValue">RM 0.00</div>
            </div>
            <div class="summary-row">
                <div>Tax (3%)</div>
                <div id="taxValue">RM 0.00</div>
            </div>
            <div class="summary-row total-row">
                <div>TOTAL:</div>
                <div id="totalValue">RM 0.00</div>
            </div>
            <div id="paymentMethodButtons" class="payment-method"></div>
            <div class="form-group">
                <label>Cash Received</label>
                <input id="cashReceived" type="number" min="0" placeholder="RM 0.00">
            </div>
            <div class="summary-row">
                <div>Change</div>
                <div id="changeValue">RM 0.00</div>
            </div>
            <div class="action-buttons">
                <button id="payButton" class="button primary full" type="button">Pay</button>
                <button id="printButton" class="button secondary full" type="button">Print Receipt</button>
                <button id="holdButton" class="button secondary full" type="button">Hold</button>
                <button id="drawerButton" class="button secondary full" type="button">Open Drawer</button>
                <button id="clearButton" class="button secondary full" type="button">Clear Cart</button>
            </div>
        </div>
    </section>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
