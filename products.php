<?php
$pageTitle = 'Product Management';
include __DIR__ . '/partials/header.php';
?>
<section class="actions-row">
    <button class="button primary">Add New Product</button>
    <button class="button secondary">Import Products</button>
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
            <tr>
                <td>MM-0012</td>
                <td>500ml Mineral Water</td>
                <td>Beverages</td>
                <td>120</td>
                <td>RM 1.70</td>
                <td><span class="status success">Active</span></td>
            </tr>
            <tr>
                <td>MM-0025</td>
                <td>Instant Noodles</td>
                <td>Snacks</td>
                <td>82</td>
                <td>RM 3.50</td>
                <td><span class="status success">Active</span></td>
            </tr>
            <tr>
                <td>MM-0041</td>
                <td>Hand Soap</td>
                <td>Personal Care</td>
                <td>45</td>
                <td>RM 6.90</td>
                <td><span class="status success">Active</span></td>
            </tr>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
