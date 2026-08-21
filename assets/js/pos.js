const posState = {
    products: Array.isArray(window.posProducts) ? window.posProducts : [],
    cart: [],
    paymentMethod: 'cash',
    cashReceived: 0,
    discount: 0,
    taxRate: 0.03,
    currentCategory: 'All Items',
    lastSale: null
};

const elements = {
    categories: document.querySelector('.category-tabs'),
    productGrid: document.querySelector('.product-grid'),
    searchInput: document.getElementById('searchInput'),
    barcodeInput: document.getElementById('barcodeInput'),
    cartTable: document.querySelector('.cart-table tbody'),
    subtotalValue: document.getElementById('subtotalValue'),
    discountValue: document.getElementById('discountValue'),
    taxValue: document.getElementById('taxValue'),
    totalValue: document.getElementById('totalValue'),
    cashInput: document.getElementById('cashReceived'),
    changeValue: document.getElementById('changeValue'),
    paymentContainer: document.getElementById('paymentMethodButtons'),
    payButton: document.getElementById('payButton'),
    holdButton: document.getElementById('holdButton'),
    drawerButton: document.getElementById('drawerButton'),
    printButton: document.getElementById('printButton'),
    clearButton: document.getElementById('clearButton'),
    customerSelect: document.getElementById('customerSelect'),
    createCustomerButton: document.getElementById('createCustomerButton')
};

function formatMoney(value) {
    return `RM ${Number(value).toFixed(2)}`;
}

function getFilteredProducts() {
    const text = (elements.searchInput?.value || '').trim().toLowerCase();
    return posState.products.filter(product => {
        const matchCategory = posState.currentCategory === 'All Items' || product.category === posState.currentCategory;
        const matchText = !text
            || product.name.toLowerCase().includes(text)
            || product.sku.toLowerCase().includes(text)
            || String(product.barcode || '').includes(text);
        return matchCategory && matchText;
    });
}

function renderCategories() {
    if (!elements.categories) return;
    const categories = ['All Items', ...new Set(posState.products.map(p => p.category))];
    elements.categories.innerHTML = categories.map(category => {
        const active = category === posState.currentCategory ? 'active' : '';
        return `<button type="button" class="category-button ${active}" data-category="${category}">${category}</button>`;
    }).join('');
    document.querySelectorAll('.category-button').forEach(button => {
        button.addEventListener('click', () => {
            posState.currentCategory = button.dataset.category;
            renderCategories();
            renderProducts();
        });
    });
}

function renderProducts() {
    if (!elements.productGrid) return;
    const products = getFilteredProducts();
    if (!products.length) {
        elements.productGrid.innerHTML = '<p class="muted-note">No products found. Add products in Product Management.</p>';
        return;
    }
    elements.productGrid.innerHTML = products.map(product => {
        const imageUrl = product.image || 'assets/images/default.svg';
        return `
            <article class="product-card" data-id="${product.id}">
                <div class="product-image" style="background-image: url('${imageUrl}'); background-size: cover; background-position: center;"></div>
                <div class="product-info">
                    <strong>${product.name}</strong>
                    <span>${product.sku}</span>
                </div>
                <div class="product-meta">
                    <div>${formatMoney(product.price)}</div>
                    <div>Stock ${product.stock}</div>
                </div>
                <button type="button" class="button secondary add-to-cart">Add</button>
            </article>
        `;
    }).join('');
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', () => {
            const productId = Number(button.closest('.product-card').dataset.id);
            addToCart(productId);
        });
    });
}

function addToCart(productId) {
    const product = posState.products.find(p => Number(p.id) === Number(productId));
    if (!product) return;
    const cartItem = posState.cart.find(item => Number(item.product.id) === Number(productId));
    const nextQty = cartItem ? cartItem.quantity + 1 : 1;
    if (nextQty > Number(product.stock)) {
        alert(`Only ${product.stock} left in stock for ${product.name}.`);
        return;
    }
    if (cartItem) {
        cartItem.quantity += 1;
    } else {
        posState.cart.push({ product, quantity: 1, discount: 0 });
    }
    posState.lastSale = null;
    renderCart();
}

function removeCartItem(index) {
    posState.cart.splice(index, 1);
    posState.lastSale = null;
    renderCart();
}

function updateCartQuantity(index, delta) {
    const item = posState.cart[index];
    if (!item) return;
    const nextQty = item.quantity + delta;
    if (nextQty < 1) return;
    if (nextQty > Number(item.product.stock)) {
        alert(`Only ${item.product.stock} left in stock for ${item.product.name}.`);
        return;
    }
    item.quantity = nextQty;
    posState.lastSale = null;
    renderCart();
}

function renderCart() {
    if (!elements.cartTable) return;
    elements.cartTable.innerHTML = posState.cart.map((item, index) => {
        const subtotal = item.quantity * item.product.price - item.discount;
        return `
            <tr>
                <td>${item.product.name}</td>
                <td>
                    <div class="qty-controls">
                        <button type="button" onclick="window.posActions.updateQty(${index}, -1)">-</button>
                        <span>${item.quantity}</span>
                        <button type="button" onclick="window.posActions.updateQty(${index}, 1)">+</button>
                    </div>
                </td>
                <td>${formatMoney(subtotal)}</td>
                <td><button type="button" class="button secondary mini" onclick="window.posActions.removeItem(${index})">Remove</button></td>
            </tr>
        `;
    }).join('');
    updateTotals();
}

function updateTotals() {
    const subtotal = posState.cart.reduce((sum, item) => sum + item.quantity * item.product.price - item.discount, 0);
    const tax = subtotal * posState.taxRate;
    const total = subtotal + tax - posState.discount;
    if (elements.subtotalValue) elements.subtotalValue.textContent = formatMoney(subtotal);
    if (elements.discountValue) elements.discountValue.textContent = formatMoney(posState.discount);
    if (elements.taxValue) elements.taxValue.textContent = formatMoney(tax);
    if (elements.totalValue) elements.totalValue.textContent = formatMoney(total);
    const cash = Number(elements.cashInput?.value) || 0;
    posState.cashReceived = cash;
    if (elements.changeValue) elements.changeValue.textContent = formatMoney(Math.max(0, cash - total));
}

function renderPaymentButtons() {
    if (!elements.paymentContainer) return;
    const methods = [
        { id: 'cash', label: 'Cash' },
        { id: 'card', label: 'Card' },
        { id: 'debit', label: 'Debit' },
        { id: 'dnpay', label: 'DuitNow' },
        { id: 'tng', label: 'TNG' },
        { id: 'grab', label: 'Grab' },
        { id: 'boost', label: 'Boost' }
    ];
    elements.paymentContainer.innerHTML = methods.map(method => {
        const active = method.id === posState.paymentMethod ? 'active' : '';
        return `<button type="button" class="button ${active}" data-method="${method.id}">${method.label}</button>`;
    }).join('');
    document.querySelectorAll('#paymentMethodButtons button').forEach(button => {
        button.addEventListener('click', () => selectPaymentMethod(button.dataset.method));
    });
}

function selectPaymentMethod(method) {
    posState.paymentMethod = method;
    document.querySelectorAll('#paymentMethodButtons button').forEach(button => {
        button.classList.toggle('active', button.dataset.method === method);
    });
}

function openSavedReceipt(receiptNo) {
    window.open(`receipt.php?no=${encodeURIComponent(receiptNo)}`, '_blank', 'width=480,height=720');
}

function openCashDrawer() {
    fetch('api/print.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'open_drawer' })
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              alert('Cash drawer trigger sent.');
          } else {
              alert('Unable to open drawer automatically.');
          }
      }).catch(() => {
          alert('Printer service unavailable. Please open drawer manually.');
      });
}

function getCartTotal() {
    const subtotal = posState.cart.reduce((sum, item) => sum + item.quantity * item.product.price, 0);
    return subtotal + subtotal * posState.taxRate - posState.discount;
}

function handlePayment() {
    if (!posState.cart.length) {
        alert('Please add items to cart before payment.');
        return;
    }
    const total = getCartTotal();
    if (posState.paymentMethod === 'cash' && posState.cashReceived < total) {
        alert('Cash received is less than total amount.');
        return;
    }

    const payload = {
        items: posState.cart.map(item => ({
            product_id: item.product.id,
            quantity: item.quantity
        })),
        payment_method: posState.paymentMethod,
        paid_amount: posState.paymentMethod === 'cash' ? posState.cashReceived : total,
        discount: posState.discount,
        customer_id: elements.customerSelect?.value || null
    };

    fetch('api/checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    }).then(async response => {
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Checkout failed.');
        }
        return data.sale;
    }).then(sale => {
        posState.lastSale = sale;
        posState.cart.forEach(item => {
            const product = posState.products.find(p => Number(p.id) === Number(item.product.id));
            if (product) {
                product.stock = Math.max(0, Number(product.stock) - item.quantity);
            }
        });
        alert(`Payment recorded.\nReceipt: ${sale.receipt_no}\nTotal: ${formatMoney(sale.total_amount)}`);
        openSavedReceipt(sale.receipt_no);
        if (posState.paymentMethod === 'cash') {
            openCashDrawer();
        }
        posState.cart = [];
        if (elements.cashInput) elements.cashInput.value = '';
        renderProducts();
        renderCart();
    }).catch(error => {
        alert(error.message || 'Unable to save this sale.');
    });
}

function holdOrder() {
    if (!posState.cart.length) {
        alert('Cannot hold empty cart.');
        return;
    }
    localStorage.setItem('heldOrder', JSON.stringify(posState.cart));
    alert('Order held successfully. You can resume later.');
}

function clearCart() {
    posState.cart = [];
    posState.lastSale = null;
    if (elements.cashInput) elements.cashInput.value = '';
    renderCart();
}

function createCustomerFromPos() {
    const name = prompt('Customer name');
    if (!name) return;
    const phone = prompt('Phone (optional)') || '';
    fetch('api/customer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, phone })
    }).then(async response => {
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Unable to add customer.');
        }
        return data.customer;
    }).then(customer => {
        const option = document.createElement('option');
        option.value = customer.id;
        option.textContent = customer.name;
        option.selected = true;
        elements.customerSelect.appendChild(option);
        alert('Customer added.');
    }).catch(error => {
        alert(error.message);
    });
}

function initPosPage() {
    if (!document.querySelector('.pos-grid')) return;
    renderCategories();
    renderProducts();
    selectPaymentMethod(posState.paymentMethod);
    renderPaymentButtons();
    elements.searchInput?.addEventListener('input', renderProducts);
    elements.barcodeInput?.addEventListener('keyup', event => {
        if (event.key === 'Enter') {
            const value = event.target.value.trim();
            const found = posState.products.find(p => p.barcode === value || p.sku === value);
            if (found) {
                addToCart(found.id);
                event.target.value = '';
            } else {
                alert('Product not found.');
            }
        }
    });
    elements.cashInput?.addEventListener('input', updateTotals);
    elements.payButton?.addEventListener('click', handlePayment);
    elements.holdButton?.addEventListener('click', holdOrder);
    elements.drawerButton?.addEventListener('click', openCashDrawer);
    elements.printButton?.addEventListener('click', () => {
        if (posState.lastSale) {
            openSavedReceipt(posState.lastSale.receipt_no);
            return;
        }
        if (!posState.cart.length) {
            alert('Cannot print receipt for an empty cart. Pay first, or add items.');
            return;
        }
        alert('Pay first to save the receipt, then you can print it.');
    });
    elements.clearButton?.addEventListener('click', clearCart);
    elements.createCustomerButton?.addEventListener('click', createCustomerFromPos);
    const held = localStorage.getItem('heldOrder');
    if (held) {
        try {
            posState.cart = JSON.parse(held);
            localStorage.removeItem('heldOrder');
        } catch (e) {
            localStorage.removeItem('heldOrder');
        }
    }
    renderCart();
}

window.posActions = { updateQty: updateCartQuantity, removeItem: removeCartItem };
window.addEventListener('DOMContentLoaded', initPosPage);
