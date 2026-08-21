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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_product') {
    $result = addProduct($_POST);
    if (isset($result['error'])) {
        setFlash('error', $result['error']);
    } else {
        setFlash('success', 'Product "' . $result['name'] . '" added.');
    }
    redirectWith('products.php');
}

$products = getProducts();
$flash = getFlash();
$pageTitle = 'Product Management';
include __DIR__ . '/partials/header.php';
?>
<?php if ($flash): ?>
    <div class="flash <?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>">
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>
<section class="actions-row">
    <button class="button primary" type="button" onclick="openModal('addProductModal')">Add New Product</button>
</section>
<div class="card">
    <div class="card-header">
        <h2>Product Catalog</h2>
    </div>
    <table class="table-list">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Name</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Sell Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$products): ?>
                <tr><td colspan="6">No products yet. Click Add New Product.</td></tr>
            <?php endif; ?>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['sku']); ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td><?php echo (int) $product['stock']; ?></td>
                    <td><?php echo money($product['sell_price']); ?></td>
                    <td>
                        <span class="status <?php echo ($product['status'] ?? 'active') === 'active' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($product['status'] ?? 'active'); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="addProductModal" class="modal">
    <div class="modal-card">
        <div class="card-header">
            <h2>Add New Product</h2>
            <button class="button secondary mini" type="button" onclick="closeModal('addProductModal')">Close</button>
        </div>
        <form method="post">
            <input type="hidden" name="action" value="add_product">
            <div class="form-grid">
                <div class="form-group">
                    <label for="sku">SKU</label>
                    <input id="sku" name="sku" type="text" required placeholder="MM-0100">
                </div>
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input id="name" name="name" type="text" required placeholder="Mineral Water 500ml">
                </div>
                <div class="form-group">
                    <label for="barcode">Barcode</label>
                    <input id="barcode" name="barcode" type="text" placeholder="Optional">
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option>Beverages</option>
                        <option>Bakery</option>
                        <option>Canned Goods</option>
                        <option>Produce</option>
                        <option>Meat</option>
                        <option>Cooking Essentials</option>
                        <option>Personal Care</option>
                        <option>Grains</option>
                        <option>Dairy</option>
                        <option>Snacks</option>
                        <option>General</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cost_price">Cost Price (RM)</label>
                    <input id="cost_price" name="cost_price" type="number" step="0.01" min="0" value="0">
                </div>
                <div class="form-group">
                    <label for="sell_price">Sell Price (RM)</label>
                    <input id="sell_price" name="sell_price" type="number" step="0.01" min="0" required value="0">
                </div>
                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input id="stock" name="stock" type="number" min="0" value="0">
                </div>
                <div class="form-group">
                    <label for="reorder_level">Reorder Level</label>
                    <input id="reorder_level" name="reorder_level" type="number" min="0" value="5">
                </div>
            </div>
            <div class="actions-row">
                <button class="button primary" type="submit">Save Product</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
