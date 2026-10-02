(() => {
    const config = window.kasirConfig;
    const cart = new Map();
    let paymentMethod = 'Tunai';
    let heldTransactionId = config.heldTransaction?.id ?? null;
    let searchTimer = null;

    const elements = {
        search: document.getElementById('searchBarang'),
        searchButton: document.getElementById('tombolCari'),
        searchResults: document.getElementById('hasilPencarian'),
        cart: document.getElementById('keranjang'),
        itemCount: document.getElementById('jumlahItem'),
        totalItems: document.getElementById('totalItem'),
        subtotal: document.getElementById('subtotalText'),
        discountPercent: document.getElementById('diskonPersen'),
        discountAmount: document.getElementById('diskonRupiah'),
        tax: document.getElementById('pajak'),
        otherFee: document.getElementById('biayaLain'),
        discountSummary: document.getElementById('rincianDiskon'),
        grandTotal: document.getElementById('totalAkhir'),
        paidAmount: document.getElementById('uangDibayar'),
        changeBox: document.getElementById('kembaliBox'),
        changeLabel: document.getElementById('labelKembalian'),
        change: document.getElementById('kembalian'),
        customer: document.getElementById('pelanggan'),
        customerNameWrap: document.getElementById('namaPelangganWrap'),
        customerName: document.getElementById('namaPelanggan'),
        customerPhone: document.getElementById('nomorPelanggan'),
        notice: document.getElementById('notifikasiKasir'),
        holdButton: document.getElementById('tahanTransaksi'),
        cancelButton: document.getElementById('batalTransaksi'),
        payButton: document.getElementById('bayarCetak'),
    };

    const rupiah = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 2,
    }).format(Number(value) || 0);

    const numericValue = (element) => Math.max(0, Number(element.value) || 0);

    function totals() {
        let subtotal = 0;
        let totalItems = 0;

        cart.forEach((product) => {
            subtotal += product.price * product.qty;
            totalItems += product.qty;
        });

        const discountPercent = Math.min(100, numericValue(elements.discountPercent));
        const discount = (subtotal * discountPercent / 100) + numericValue(elements.discountAmount);
        const tax = numericValue(elements.tax);
        const otherFee = numericValue(elements.otherFee);
        const grandTotal = Math.max(0, subtotal - discount + tax + otherFee);
        const paidAmount = numericValue(elements.paidAmount);

        return {
            subtotal,
            totalItems,
            discountPercent,
            discount,
            tax,
            otherFee,
            grandTotal,
            paidAmount,
            change: Math.max(0, paidAmount - grandTotal),
            sufficient: paidAmount >= grandTotal,
        };
    }

    function notify(message, type = 'danger') {
        elements.notice.className = `alert alert-${type} mt-3 mb-0`;
        elements.notice.textContent = message;
    }

    function hideNotice() {
        elements.notice.className = 'alert d-none mt-3 mb-0';
        elements.notice.textContent = '';
    }

    function renderSummary() {
        const summary = totals();
        elements.itemCount.textContent = `${summary.totalItems} item`;
        elements.totalItems.textContent = summary.totalItems.toLocaleString('id-ID');
        elements.subtotal.textContent = rupiah(summary.subtotal);
        elements.discountSummary.textContent = `Diskon ${rupiah(summary.discount)}`;
        elements.grandTotal.textContent = rupiah(summary.grandTotal);
        elements.change.textContent = summary.sufficient ? rupiah(summary.change) : rupiah(summary.grandTotal - summary.paidAmount);
        elements.changeLabel.textContent = summary.sufficient ? 'KEMBALIAN' : 'UANG KURANG';
        elements.changeBox.classList.toggle('sufficient', summary.sufficient);
        elements.changeBox.classList.toggle('insufficient', !summary.sufficient);
        elements.payButton.disabled = summary.totalItems === 0 || !summary.sufficient;
    }

    function createCell(text, className = '') {
        const cell = document.createElement('td');
        cell.textContent = text;
        if (className) {
            cell.className = className;
        }
        return cell;
    }

    function renderCart() {
        elements.cart.replaceChildren();

        if (cart.size === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            const icon = document.createElement('i');
            const message = document.createElement('span');

            cell.colSpan = 6;
            cell.className = 'cart-empty';
            icon.className = 'bi bi-basket';
            message.textContent = 'Keranjang masih kosong. Silakan cari atau scan barang.';
            cell.append(icon, message);
            row.append(cell);
            elements.cart.append(row);
            renderSummary();
            return;
        }

        [...cart.values()].forEach((product, index) => {
            const row = document.createElement('tr');
            const numberCell = createCell(String(index + 1), 'text-center text-secondary');
            const productCell = document.createElement('td');
            const name = document.createElement('span');
            const stock = document.createElement('small');
            const priceCell = createCell(rupiah(product.price), 'text-end text-nowrap');
            const quantityCell = document.createElement('td');
            const subtotalCell = createCell(rupiah(product.price * product.qty), 'text-end fw-semibold text-nowrap');
            const actionCell = document.createElement('td');
            const quantityControl = document.createElement('div');
            const decreaseButton = document.createElement('button');
            const quantityInput = document.createElement('input');
            const increaseButton = document.createElement('button');
            const removeButton = document.createElement('button');
            const removeIcon = document.createElement('i');

            name.className = 'cart-product-name';
            name.textContent = product.name;
            stock.className = 'cart-product-stock';
            stock.textContent = `Stok ${product.stock}`;
            productCell.append(name, stock);
            quantityControl.className = 'qty-control';

            decreaseButton.type = 'button';
            decreaseButton.setAttribute('aria-label', `Kurangi ${product.name}`);
            decreaseButton.textContent = '−';
            decreaseButton.disabled = product.qty <= 1;
            decreaseButton.addEventListener('click', () => updateQuantity(product.id, product.qty - 1));

            quantityInput.type = 'number';
            quantityInput.min = '1';
            quantityInput.max = String(product.stock);
            quantityInput.value = String(product.qty);
            quantityInput.setAttribute('aria-label', `Jumlah ${product.name}`);
            quantityInput.addEventListener('change', () => updateQuantity(product.id, Number(quantityInput.value)));

            increaseButton.type = 'button';
            increaseButton.setAttribute('aria-label', `Tambah ${product.name}`);
            increaseButton.textContent = '+';
            increaseButton.disabled = product.qty >= product.stock;
            increaseButton.addEventListener('click', () => updateQuantity(product.id, product.qty + 1));

            quantityControl.append(decreaseButton, quantityInput, increaseButton);
            quantityCell.className = 'text-center';
            quantityCell.append(quantityControl);

            removeButton.type = 'button';
            removeButton.className = 'btn btn-sm btn-outline-danger';
            removeButton.setAttribute('aria-label', `Hapus ${product.name}`);
            removeIcon.className = 'bi bi-trash';
            removeButton.append(removeIcon);
            removeButton.addEventListener('click', () => removeProduct(product.id));
            actionCell.className = 'text-center';
            actionCell.append(removeButton);

            row.append(numberCell, productCell, priceCell, quantityCell, subtotalCell, actionCell);
            elements.cart.append(row);
        });

        renderSummary();
    }

    function updateQuantity(productId, requestedQuantity) {
        const product = cart.get(productId);
        if (!product) {
            return;
        }

        if (!Number.isInteger(requestedQuantity) || requestedQuantity < 1) {
            notify('Jumlah barang minimal 1.', 'warning');
            renderCart();
            return;
        }

        if (requestedQuantity > product.stock) {
            notify(`Stok ${product.name} hanya ${product.stock} item.`, 'warning');
            renderCart();
            return;
        }

        product.qty = requestedQuantity;
        hideNotice();
        renderCart();
    }

    function removeProduct(productId) {
        cart.delete(productId);
        hideNotice();
        renderCart();
    }

    function addProduct(product) {
        if (product.stock < 1) {
            notify(`Stok ${product.name} sedang habis.`, 'warning');
            return;
        }

        const existing = cart.get(product.id);
        if (existing && existing.qty >= existing.stock) {
            notify(`Jumlah ${product.name} sudah mencapai batas stok.`, 'warning');
            return;
        }

        if (existing) {
            existing.qty += 1;
        } else {
            cart.set(product.id, {
                id: product.id,
                name: product.name,
                price: Number(product.price),
                stock: Number(product.stock),
                qty: 1,
            });
        }

        hideNotice();
        renderCart();
        elements.search.value = '';
        elements.searchResults.classList.add('d-none');
        elements.search.focus();
    }

    function showSearchMessage(message) {
        const empty = document.createElement('div');
        empty.className = 'search-empty';
        empty.textContent = message;
        elements.searchResults.replaceChildren(empty);
        elements.searchResults.classList.remove('d-none');
    }

    function renderSearchResults(products, term = '') {
        elements.searchResults.replaceChildren();
        if (products.length === 0) {
            showSearchMessage(term ? 'Produk tidak ditemukan.' : 'Belum ada produk yang tersedia.');
            return;
        }

        products.forEach((product) => {
            const button = document.createElement('button');
            const details = document.createElement('span');
            const name = document.createElement('span');
            const info = document.createElement('small');
            const price = document.createElement('span');

            button.type = 'button';
            button.className = 'search-result';
            button.disabled = Number(product.stock) < 1;
            if (product.image_url) {
                const image = document.createElement('img');
                image.className = 'search-result-image';
                image.src = product.image_url;
                image.alt = '';
                image.loading = 'lazy';
                button.append(image);
            }
            name.className = 'search-result-name';
            name.textContent = product.name;
            info.className = 'search-result-info';
            info.textContent = `Stok ${product.stock}`;
            if (product.code || product.barcode) {
                info.textContent += ` · ${product.code || product.barcode}`;
            }
            price.className = 'search-result-price';
            price.textContent = Number(product.stock) < 1 ? 'Stok habis' : rupiah(product.price);
            details.append(name, info);
            button.append(details, price);
            button.addEventListener('click', () => addProduct(product));
            elements.searchResults.append(button);
        });
        elements.searchResults.classList.remove('d-none');
    }

    async function searchProducts({ background = false } = {}) {
        const term = elements.search.value.trim();
        const shouldRender = !background || !elements.searchResults.classList.contains('d-none');

        if (background && document.activeElement === elements.search) {
            return;
        }

        if (!background) {
            elements.searchButton.disabled = true;
            showSearchMessage(term ? 'Sedang mencari produk...' : 'Memuat produk terbaru...');
        }

        try {
            const url = new URL(config.searchUrl, window.location.origin);
            if (term) {
                url.searchParams.set('q', term);
            }
            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                cache: 'no-store',
            });
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Pencarian produk gagal.');
            }
            if (
                shouldRender
                && (!background || !elements.searchResults.classList.contains('d-none'))
                && term === elements.search.value.trim()
            ) {
                renderSearchResults(data.products, term);
            }
        } catch (error) {
            const message = error.message || 'Sinkronisasi produk gagal. Coba lagi.';
            if (
                shouldRender
                && (!background || !elements.searchResults.classList.contains('d-none'))
                && term === elements.search.value.trim()
            ) {
                showSearchMessage(message);
            } else if (background) {
                notify(message);
            }
        } finally {
            if (!background) {
                elements.searchButton.disabled = false;
            }
        }
    }

    function requestPayload() {
        const customerName = elements.customer.value === 'Umum'
            ? 'Umum'
            : elements.customerName.value.trim();

        return {
            items: [...cart.values()].map((product) => ({
                product_id: product.id,
                qty: product.qty,
            })),
            discount_percent: Number(elements.discountPercent.value) || 0,
            discount_amount: Number(elements.discountAmount.value) || 0,
            tax: Number(elements.tax.value) || 0,
            other_fee: Number(elements.otherFee.value) || 0,
            paid_amount: Number(elements.paidAmount.value) || 0,
            payment_method: paymentMethod,
            customer_name: customerName || 'Pelanggan',
            customer_phone: elements.customerPhone.value.trim(),
            held_transaction_id: heldTransactionId,
        };
    }

    async function sendRequest(url, method, payload = null) {
        const options = {
            method,
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': config.csrfToken,
            },
        };
        if (payload !== null) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(payload);
        }

        const response = await fetch(url, options);
        const data = await response.json();
        if (!response.ok) {
            const validationMessages = Object.values(data.errors || {}).flat();
            throw new Error(validationMessages.join(' ') || data.message || 'Permintaan tidak dapat diproses.');
        }
        return data;
    }

    function resetTransaction() {
        cart.clear();
        elements.discountPercent.value = '0';
        elements.discountAmount.value = '0';
        elements.tax.value = '0';
        elements.otherFee.value = '0';
        elements.paidAmount.value = '0';
        elements.customer.value = 'Umum';
        elements.customerName.value = '';
        elements.customerNameWrap.classList.add('d-none');
        elements.customerPhone.value = '';
        heldTransactionId = null;
        paymentMethod = 'Tunai';
        document.querySelectorAll('.payment-method').forEach((button) => {
            button.classList.toggle('active', button.dataset.method === paymentMethod);
        });
        hideNotice();
        renderCart();
    }

    function hydrateHeldTransaction(transaction) {
        if (!transaction) {
            renderCart();
            return;
        }

        heldTransactionId = transaction.id;
        elements.customer.value = transaction.customer_name === 'Umum' ? 'Umum' : 'lainnya';
        if (transaction.customer_name !== 'Umum') {
            elements.customerNameWrap.classList.remove('d-none');
            elements.customerName.value = transaction.customer_name;
        }
        elements.customerPhone.value = transaction.customer_phone || '';
        elements.discountPercent.value = String(transaction.discount_percent || 0);
        elements.discountAmount.value = String(transaction.discount_amount || 0);
        elements.tax.value = String(transaction.tax || 0);
        elements.otherFee.value = String(transaction.other_fee || 0);
        elements.paidAmount.value = String(transaction.paid_amount || 0);
        paymentMethod = transaction.payment_method || 'Tunai';
        document.querySelectorAll('.payment-method').forEach((button) => {
            button.classList.toggle('active', button.dataset.method === paymentMethod);
        });

        transaction.items.forEach((product) => {
            cart.set(Number(product.id), {
                id: Number(product.id),
                name: product.name,
                price: Number(product.price),
                stock: Number(product.stock),
                qty: Number(product.qty),
            });
        });
        renderCart();
        notify(`Transaksi ${transaction.transaction_number} dilanjutkan.`, 'info');
    }

    function updateClock() {
        const clock = document.getElementById('currentDateTime');
        clock.textContent = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        }).format(new Date());
    }

    async function holdTransaction() {
        if (cart.size === 0) {
            notify('Keranjang masih kosong. Tambahkan produk sebelum menahan transaksi.', 'warning');
            return;
        }

        elements.holdButton.disabled = true;
        try {
            const result = await sendRequest(config.holdUrl, 'POST', requestPayload());
            resetTransaction();
            if (result.next_transaction_number) {
                document.getElementById('transactionNumber').textContent = result.next_transaction_number;
            }
            notify(`Transaksi ${result.transaction_number} berhasil ditahan.`, 'success');
        } catch (error) {
            notify(error.message);
        } finally {
            elements.holdButton.disabled = false;
        }
    }

    async function cancelTransaction() {
        if (!window.confirm('Batalkan transaksi ini dan kosongkan keranjang?')) {
            return;
        }

        if (heldTransactionId !== null) {
            try {
                await sendRequest(`${config.heldDeleteBaseUrl}/${heldTransactionId}`, 'DELETE');
            } catch (error) {
                notify(error.message);
                return;
            }
        }

        resetTransaction();
        notify('Transaksi dibatalkan.', 'secondary');
    }

    async function payAndPrint() {
        const summary = totals();
        if (cart.size === 0) {
            notify('Keranjang masih kosong. Tambahkan produk sebelum membayar.', 'warning');
            return;
        }
        if (!summary.sufficient) {
            notify('Uang dibayar belum mencukupi total transaksi.', 'warning');
            elements.paidAmount.focus();
            return;
        }
        if (elements.customer.value === 'lainnya' && !elements.customerName.value.trim()) {
            notify('Masukkan nama pelanggan atau pilih pelanggan Umum.', 'warning');
            elements.customerName.focus();
            return;
        }

        elements.payButton.disabled = true;
        try {
            const result = await sendRequest(config.storeUrl, 'POST', requestPayload());
            window.location.assign(result.receipt_url);
        } catch (error) {
            notify(error.message);
            elements.payButton.disabled = false;
        }
    }

    elements.searchButton.addEventListener('click', searchProducts);
    elements.search.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchProducts();
        }
    });
    elements.search.addEventListener('input', () => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(searchProducts, 300);
    });
    elements.search.addEventListener('click', () => {
        if (!elements.search.value.trim() && elements.searchResults.classList.contains('d-none')) {
            searchProducts();
        }
    });
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.search-row')) {
            elements.searchResults.classList.add('d-none');
        }
    });
    elements.discountPercent.addEventListener('input', renderSummary);
    [elements.discountAmount, elements.tax, elements.otherFee, elements.paidAmount].forEach((input) => {
        input.addEventListener('input', renderSummary);
    });
    document.querySelectorAll('[data-quick-amount]').forEach((button) => {
        button.addEventListener('click', () => {
            elements.paidAmount.value = button.dataset.quickAmount === 'exact'
                ? String(totals().grandTotal)
                : button.dataset.quickAmount;
            renderSummary();
            elements.paidAmount.focus();
        });
    });
    document.querySelectorAll('.payment-method').forEach((button) => {
        button.addEventListener('click', () => {
            paymentMethod = button.dataset.method;
            document.querySelectorAll('.payment-method').forEach((methodButton) => {
                methodButton.classList.toggle('active', methodButton === button);
            });
        });
    });
    elements.customer.addEventListener('change', () => {
        elements.customerNameWrap.classList.toggle('d-none', elements.customer.value !== 'lainnya');
        if (elements.customer.value === 'lainnya') {
            elements.customerName.focus();
        } else {
            elements.customerName.value = '';
        }
    });
    elements.holdButton.addEventListener('click', holdTransaction);
    elements.cancelButton.addEventListener('click', cancelTransaction);
    elements.payButton.addEventListener('click', payAndPrint);
    document.getElementById('kosongkanKeranjang').addEventListener('click', () => {
        cart.clear();
        hideNotice();
        renderCart();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'F2') {
            event.preventDefault();
            elements.search.focus();
        } else if (event.key === 'F4') {
            event.preventDefault();
            elements.paidAmount.focus();
        } else if (event.key === 'Escape') {
            event.preventDefault();
            cancelTransaction();
        }
    });

    hydrateHeldTransaction(config.heldTransaction);
    searchProducts();
    updateClock();
    window.setInterval(updateClock, 1000);
    window.setInterval(() => {
        searchProducts({ background: true });
    }, 30000);
})();
