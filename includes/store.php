<?php

function dataPath($file) {
    return dirname(__DIR__) . '/data/' . $file;
}

function readJsonFile($file, $default = []) {
    $path = dataPath($file);
    if (!file_exists($path)) {
        writeJsonFile($file, $default);
        return $default;
    }
    $fp = fopen($path, 'r');
    if (!$fp) {
        return $default;
    }
    flock($fp, LOCK_SH);
    $raw = stream_get_contents($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : $default;
}

function writeJsonFile($file, $data) {
    $path = dataPath($file);
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $fp = fopen($path, 'c+');
    if (!$fp) {
        throw new RuntimeException('Unable to write ' . $file);
    }
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, $json);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
}

function nextRecordId(array $items) {
    if (!$items) {
        return 1;
    }
    return (int) max(array_column($items, 'id')) + 1;
}

function categoryImage($category) {
    $map = [
        'Beverages' => 'beverage.svg',
        'Bakery' => 'bakery.svg',
        'Canned Goods' => 'canned.svg',
        'Produce' => 'produce.svg',
        'Meat' => 'meat.svg',
        'Cooking Essentials' => 'spices.svg',
        'Personal Care' => 'personal-care.svg',
        'Grains' => 'grains.svg',
        'Dairy' => 'dairy.svg',
        'Snacks' => 'snacks.svg',
    ];
    $file = $map[$category] ?? 'default.svg';
    return 'assets/images/' . $file;
}

function defaultProducts() {
    return [
        ['id' => 1, 'sku' => 'MM-0010', 'name' => 'Apple Juice 500ml', 'barcode' => '888001000001', 'category' => 'Beverages', 'cost_price' => 2.00, 'sell_price' => 3.20, 'stock' => 42, 'reorder_level' => 10, 'status' => 'active', 'image' => 'assets/images/beverage.svg'],
        ['id' => 2, 'sku' => 'MM-0011', 'name' => 'Baguette', 'barcode' => '888001000002', 'category' => 'Bakery', 'cost_price' => 0.80, 'sell_price' => 1.50, 'stock' => 87, 'reorder_level' => 15, 'status' => 'active', 'image' => 'assets/images/bakery.svg'],
        ['id' => 3, 'sku' => 'MM-0012', 'name' => 'Baked Beans 420g', 'barcode' => '888001000003', 'category' => 'Canned Goods', 'cost_price' => 1.10, 'sell_price' => 1.80, 'stock' => 36, 'reorder_level' => 8, 'status' => 'active', 'image' => 'assets/images/canned.svg'],
        ['id' => 4, 'sku' => 'MM-0013', 'name' => 'Bananas 1kg', 'barcode' => '888001000004', 'category' => 'Produce', 'cost_price' => 1.20, 'sell_price' => 1.99, 'stock' => 60, 'reorder_level' => 12, 'status' => 'active', 'image' => 'assets/images/produce.svg'],
        ['id' => 5, 'sku' => 'MM-0014', 'name' => 'Beef Ribeye 500g', 'barcode' => '888001000005', 'category' => 'Meat', 'cost_price' => 10.00, 'sell_price' => 14.99, 'stock' => 12, 'reorder_level' => 5, 'status' => 'active', 'image' => 'assets/images/meat.svg'],
        ['id' => 6, 'sku' => 'MM-0015', 'name' => 'Black Pepper 50g', 'barcode' => '888001000006', 'category' => 'Cooking Essentials', 'cost_price' => 1.20, 'sell_price' => 2.00, 'stock' => 33, 'reorder_level' => 8, 'status' => 'active', 'image' => 'assets/images/spices.svg'],
        ['id' => 7, 'sku' => 'MM-0016', 'name' => 'Body Wash 500ml', 'barcode' => '888001000007', 'category' => 'Personal Care', 'cost_price' => 3.00, 'sell_price' => 4.99, 'stock' => 20, 'reorder_level' => 6, 'status' => 'active', 'image' => 'assets/images/personal-care.svg'],
        ['id' => 8, 'sku' => 'MM-0017', 'name' => 'Brown Rice 2kg', 'barcode' => '888001000008', 'category' => 'Grains', 'cost_price' => 4.50, 'sell_price' => 6.20, 'stock' => 25, 'reorder_level' => 8, 'status' => 'active', 'image' => 'assets/images/grains.svg'],
        ['id' => 9, 'sku' => 'MM-0018', 'name' => 'Butter 250g', 'barcode' => '888001000009', 'category' => 'Dairy', 'cost_price' => 2.20, 'sell_price' => 3.50, 'stock' => 54, 'reorder_level' => 10, 'status' => 'active', 'image' => 'assets/images/dairy.svg'],
        ['id' => 10, 'sku' => 'MM-0019', 'name' => 'Canned Tuna 160g', 'barcode' => '888001000010', 'category' => 'Canned Goods', 'cost_price' => 1.30, 'sell_price' => 2.10, 'stock' => 40, 'reorder_level' => 8, 'status' => 'active', 'image' => 'assets/images/canned.svg'],
        ['id' => 11, 'sku' => 'MM-0020', 'name' => 'Chicken Breast 1kg', 'barcode' => '888001000011', 'category' => 'Meat', 'cost_price' => 6.50, 'sell_price' => 8.99, 'stock' => 18, 'reorder_level' => 6, 'status' => 'active', 'image' => 'assets/images/meat.svg'],
        ['id' => 12, 'sku' => 'MM-0021', 'name' => 'Chocolate Bar 100g', 'barcode' => '888001000012', 'category' => 'Snacks', 'cost_price' => 1.10, 'sell_price' => 1.99, 'stock' => 74, 'reorder_level' => 15, 'status' => 'active', 'image' => 'assets/images/snacks.svg'],
    ];
}

function defaultCustomers() {
    return [
        ['id' => 1, 'name' => 'Ahmad Hassan', 'phone' => '012-345 6789', 'email' => 'ahmad@mail.com', 'membership_no' => 'MM-0001', 'loyalty_points' => 120],
        ['id' => 2, 'name' => 'Siti Aisyah', 'phone' => '013-987 6543', 'email' => 'siti@mail.com', 'membership_no' => 'MM-0002', 'loyalty_points' => 85],
    ];
}

function defaultSuppliers() {
    return [
        ['id' => 1, 'name' => 'KK Food Trading', 'contact_person' => 'En. Rafi', 'phone' => '019-876 5432', 'email' => 'rafi@kkfood.my', 'address' => 'Shah Alam, Selangor'],
        ['id' => 2, 'name' => 'FreshCare Supplies', 'contact_person' => 'Puan Lina', 'phone' => '017-112 2334', 'email' => 'lina@freshcare.my', 'address' => 'Subang Jaya, Selangor'],
    ];
}

function getProducts() {
    return readJsonFile('products.json', defaultProducts());
}

function saveProducts(array $products) {
    writeJsonFile('products.json', array_values($products));
}

function getCustomers() {
    return readJsonFile('customers.json', defaultCustomers());
}

function saveCustomers(array $customers) {
    writeJsonFile('customers.json', array_values($customers));
}

function getSuppliers() {
    return readJsonFile('suppliers.json', defaultSuppliers());
}

function saveSuppliers(array $suppliers) {
    writeJsonFile('suppliers.json', array_values($suppliers));
}

function getSales() {
    return readJsonFile('sales.json', []);
}

function saveSales(array $sales) {
    writeJsonFile('sales.json', array_values($sales));
}

function findProductById($id) {
    foreach (getProducts() as $product) {
        if ((int) $product['id'] === (int) $id) {
            return $product;
        }
    }
    return null;
}

function findSaleByReceipt($receiptNo) {
    foreach (getSales() as $sale) {
        if (($sale['receipt_no'] ?? '') === $receiptNo) {
            return $sale;
        }
    }
    return null;
}

function addProduct(array $input) {
    $products = getProducts();
    $sku = strtoupper(trim($input['sku'] ?? ''));
    $name = trim($input['name'] ?? '');
    $barcode = trim($input['barcode'] ?? '');
    $category = trim($input['category'] ?? 'General');

    if ($sku === '' || $name === '') {
        return ['error' => 'SKU and product name are required.'];
    }

    foreach ($products as $product) {
        if (strcasecmp($product['sku'], $sku) === 0) {
            return ['error' => 'SKU already exists.'];
        }
        if ($barcode !== '' && strcasecmp((string) ($product['barcode'] ?? ''), $barcode) === 0) {
            return ['error' => 'Barcode already exists.'];
        }
    }

    $product = [
        'id' => nextRecordId($products),
        'sku' => $sku,
        'name' => $name,
        'barcode' => $barcode,
        'category' => $category !== '' ? $category : 'General',
        'cost_price' => round((float) ($input['cost_price'] ?? 0), 2),
        'sell_price' => round((float) ($input['sell_price'] ?? 0), 2),
        'stock' => max(0, (int) ($input['stock'] ?? 0)),
        'reorder_level' => max(0, (int) ($input['reorder_level'] ?? 0)),
        'status' => ($input['status'] ?? 'active') === 'archived' ? 'archived' : 'active',
        'image' => categoryImage($category),
    ];
    $products[] = $product;
    saveProducts($products);
    return $product;
}

function addCustomer(array $input) {
    $customers = getCustomers();
    $name = trim($input['name'] ?? '');
    if ($name === '') {
        return ['error' => 'Customer name is required.'];
    }
    $customer = [
        'id' => nextRecordId($customers),
        'name' => $name,
        'phone' => trim($input['phone'] ?? ''),
        'email' => trim($input['email'] ?? ''),
        'membership_no' => trim($input['membership_no'] ?? '') ?: ('MM-' . str_pad((string) (nextRecordId($customers)), 4, '0', STR_PAD_LEFT)),
        'loyalty_points' => max(0, (int) ($input['loyalty_points'] ?? 0)),
    ];
    $customers[] = $customer;
    saveCustomers($customers);
    return $customer;
}

function addSupplier(array $input) {
    $suppliers = getSuppliers();
    $name = trim($input['name'] ?? '');
    if ($name === '') {
        return ['error' => 'Supplier name is required.'];
    }
    $supplier = [
        'id' => nextRecordId($suppliers),
        'name' => $name,
        'contact_person' => trim($input['contact_person'] ?? ''),
        'phone' => trim($input['phone'] ?? ''),
        'email' => trim($input['email'] ?? ''),
        'address' => trim($input['address'] ?? ''),
    ];
    $suppliers[] = $supplier;
    saveSuppliers($suppliers);
    return $supplier;
}

function checkoutSale(array $payload, array $currentUser) {
    $items = $payload['items'] ?? [];
    if (!is_array($items) || !$items) {
        return ['error' => 'Cart is empty.'];
    }

    $products = getProducts();
    $byId = [];
    foreach ($products as $index => $product) {
        $byId[(int) $product['id']] = $index;
    }

    $saleItems = [];
    $subtotal = 0.0;

    foreach ($items as $item) {
        $productId = (int) ($item['product_id'] ?? 0);
        $qty = max(1, (int) ($item['quantity'] ?? 0));
        if (!isset($byId[$productId])) {
            return ['error' => 'One of the products no longer exists.'];
        }
        $index = $byId[$productId];
        $product = $products[$index];
        if (($product['status'] ?? 'active') !== 'active') {
            return ['error' => $product['name'] . ' is not available.'];
        }
        if ((int) $product['stock'] < $qty) {
            return ['error' => 'Not enough stock for ' . $product['name'] . '.'];
        }
        $unitPrice = (float) $product['sell_price'];
        $lineTotal = round($unitPrice * $qty, 2);
        $products[$index]['stock'] = (int) $product['stock'] - $qty;
        $saleItems[] = [
            'product_id' => $productId,
            'name' => $product['name'],
            'sku' => $product['sku'],
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'subtotal' => $lineTotal,
        ];
        $subtotal += $lineTotal;
    }

    $taxRate = 0.03;
    $discount = max(0, round((float) ($payload['discount'] ?? 0), 2));
    $tax = round($subtotal * $taxRate, 2);
    $total = round($subtotal + $tax - $discount, 2);
    $paid = round((float) ($payload['paid_amount'] ?? $total), 2);
    $method = strtolower(trim($payload['payment_method'] ?? 'cash'));
    if ($method === 'cash' && $paid < $total) {
        return ['error' => 'Cash received is less than total amount.'];
    }

    $customerId = $payload['customer_id'] ?? null;
    $customerName = 'Walk-in Customer';
    if ($customerId) {
        foreach (getCustomers() as $customer) {
            if ((int) $customer['id'] === (int) $customerId) {
                $customerName = $customer['name'];
                break;
            }
        }
    } else {
        $customerId = null;
    }

    $sales = getSales();
    $receiptNo = 'POS-' . date('ymd') . '-' . str_pad((string) (nextRecordId($sales)), 4, '0', STR_PAD_LEFT);
    $sale = [
        'id' => nextRecordId($sales),
        'receipt_no' => $receiptNo,
        'user_id' => (int) ($currentUser['id'] ?? 0),
        'cashier' => $currentUser['name'] ?? 'Cashier',
        'customer_id' => $customerId,
        'customer_name' => $customerName,
        'items' => $saleItems,
        'subtotal' => round($subtotal, 2),
        'tax' => $tax,
        'discount' => $discount,
        'total_amount' => $total,
        'paid_amount' => $method === 'cash' ? $paid : $total,
        'change_amount' => $method === 'cash' ? round(max(0, $paid - $total), 2) : 0,
        'payment_method' => $method,
        'status' => 'paid',
        'created_at' => date('Y-m-d H:i:s'),
    ];

    $sales[] = $sale;
    saveProducts($products);
    saveSales($sales);
    return $sale;
}

function money($value) {
    return 'RM ' . number_format((float) $value, 2);
}
