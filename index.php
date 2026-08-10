<?php
// POS System Entry Point
// Minimal PHP bootstrap for a modern POS frontend

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'] ?? 'cashier';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Dashboard - MiniMarket POS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-logo">MiniMarket POS</div>
                <div class="brand-subtitle">Retail & Convenience</div>
            </div>

            <nav class="nav-menu">
                <a class="nav-item active" href="index.php">Dashboard</a>
                <?php if ($role === 'admin'): ?>
                    <a class="nav-item" href="sales.php">Sales Management</a>
                    <a class="nav-item" href="products.php">Product Management</a>
                    <a class="nav-item" href="inventory.php">Inventory</a>
                    <a class="nav-item" href="customers.php">Customers</a>
                    <a class="nav-item" href="suppliers.php">Suppliers</a>
                    <a class="nav-item" href="reports.php">Reports</a>
                    <a class="nav-item" href="users.php">Users</a>
                    <a class="nav-item" href="settings.php">Settings</a>
                <?php else: ?>
                    <a class="nav-item" href="pos.php">POS Counter</a>
                    <a class="nav-item" href="history.php">Sales History</a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <div class="profile-card">
                    <div class="profile-name"><?php echo htmlspecialchars($user['name'] ?? 'Cashier'); ?></div>
                    <div class="profile-role"><?php echo ucfirst(htmlspecialchars($role)); ?></div>
                </div>
                <a class="button secondary" href="logout.php">Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h1>Dashboard</h1>
                <div class="topbar-actions">
                    <button class="button icon-button" onclick="toggleTheme()">Toggle Theme</button>
                </div>
            </header>

            <section class="dashboard-grid">
                <article class="card stats-card info-card">
                    <h2>Admin Login Demo</h2>
                    <p class="stat-value">admin / admin123</p>
                    <p class="card-note">Admin role with full access for dashboard, product, inventory, and reports.</p>
                </article>
                <article class="card stats-card">
                    <h2>Today's Sales</h2>
                    <p class="stat-value">RM 2,150.00</p>
                </article>
                <article class="card stats-card">
                    <h2>Weekly Sales</h2>
                    <p class="stat-value">RM 14,780.00</p>
                </article>
                <article class="card stats-card">
                    <h2>Monthly Revenue</h2>
                    <p class="stat-value">RM 52,320.00</p>
                </article>
                <article class="card stats-card">
                    <h2>Total Orders</h2>
                    <p class="stat-value">214</p>
                </article>
                <article class="card stats-card">
                    <h2>Total Products</h2>
                    <p class="stat-value">1,248</p>
                </article>
                <article class="card stats-card low-stock">
                    <h2>Low Stock Alerts</h2>
                    <p>8 items need restock</p>
                </article>
            </section>

            <section class="charts-section">
                <div class="card chart-card">
                    <div class="card-header">
                        <h2>Sales Trend</h2>
                        <span>Last 7 days</span>
                    </div>
                    <div class="chart-placeholder">[Chart Placeholder]</div>
                </div>
                <div class="card chart-card">
                    <div class="card-header">
                        <h2>Top Selling Products</h2>
                        <span>Last 30 days</span>
                    </div>
                    <ul class="top-products-list">
                        <li>1. 500ml Mineral Water</li>
                        <li>2. Coffee 3-in-1</li>
                        <li>3. Rice 5kg</li>
                        <li>4. Instant Noodles</li>
                        <li>5. Bread</li>
                    </ul>
                </div>
            </section>

            <section class="recent-section">
                <div class="card">
                    <div class="card-header">
                        <h2>Recent Transactions</h2>
                        <a class="link-button" href="sales.php">View All</a>
                    </div>
                    <table class="table-list">
                        <thead>
                            <tr>
                                <th>Receipt</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#POS-2356</td>
                                <td>Walk-in</td>
                                <td>RM 28.90</td>
                                <td>Cash</td>
                                <td><span class="status success">Paid</span></td>
                            </tr>
                            <tr>
                                <td>#POS-2355</td>
                                <td>Ahmad</td>
                                <td>RM 62.50</td>
                                <td>DuitNow QR</td>
                                <td><span class="status success">Paid</span></td>
                            </tr>
                            <tr>
                                <td>#POS-2354</td>
                                <td>Walk-in</td>
                                <td>RM 15.20</td>
                                <td>Boost</td>
                                <td><span class="status success">Paid</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
