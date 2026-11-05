// chasier.js - Logika Kasir Toko Dian + Realtime Reverb
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('#search-product');
    const resultsBox = document.querySelector('.product-results');
    const cartTable = document.querySelector('#cart-table tbody');

    if (!searchInput) return; // kalau bukan di halaman kasir, stop

    console.log('Kasir Toko Dian siap!');

    // --- 1. SEARCH PRODUK ---
    let timeout = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        let query = this.value.trim();
        if (query.length < 2) {
            resultsBox.style.display = 'none';
            return;
        }
        timeout = setTimeout(() => {
            fetch(`/api/products/search?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    resultsBox.innerHTML = '';
                    if (data.length === 0) {
                        resultsBox.style.display = 'none';
                        return;
                    }
                    data.forEach(product => {
                        let div = document.createElement('div');
                        div.className = 'product-item';
                        div.innerHTML = `
                            <div>
                                <div class="product-name">${product.name}</div>
                                <div class="product-code">${product.code}</div>
                            </div>
                            <div class="product-price">Rp ${product.price}</div>
                        `;
                        div.addEventListener('click', () => addToCart(product));
                        resultsBox.appendChild(div);
                    });
                    resultsBox.style.display = 'block';
                });
        }, 300);
    });

    // --- 2. TAMBAH KE KERANJANG ---
    window.addToCart = function(product) {
        resultsBox.style.display = 'none';
        searchInput.value = '';
        
        let existing = document.querySelector(`tr[data-id="${product.id}"]`);
        if (existing) {
            let input = existing.querySelector('.qty-input');
            input.value = parseInt(input.value) + 1;
            updateTotal();
            return;
        }

        let tr = document.createElement('tr');
        tr.setAttribute('data-id', product.id);
        tr.innerHTML = `
            <td>${product.name}<br><small>${product.code}</small></td>
            <td>Rp ${product.price}</td>
            <td>
                <div class="qty-control">
                    <button onclick="changeQty(${product.id}, -1)">-</button>
                    <input type="number" class="qty-input" value="1" min="1">
                    <button onclick="changeQty(${product.id}, 1)">+</button>
                </div>
            </td>
            <td class="subtotal">Rp ${product.price}</td>
            <td><button class="remove-btn" onclick="removeItem(${product.id})">X</button></td>
        `;
        cartTable.appendChild(tr);
        updateTotal();
    }

    window.changeQty = function(id, delta) {
        let row = document.querySelector(`tr[data-id="${id}"]`);
        let input = row.querySelector('.qty-input');
        let newVal = parseInt(input.value) + delta;
        if (newVal < 1) newVal = 1;
        input.value = newVal;
        updateTotal();
    }

    window.removeItem = function(id) {
        document.querySelector(`tr[data-id="${id}"]`)?.remove();
        updateTotal();
    }

    function updateTotal() {
        // hitung total belanja
        let total = 0;
        document.querySelectorAll('#cart-table tbody tr').forEach(row => {
            // logic hitung, sesuaikan
        });
    }

    // --- 3. REALTIME ORDER DARI REVERB ---
    if (window.Echo) {
        console.log('Echo connected, listening order channel...');
        window.Echo.channel('orders')
            .listen('OrderPlaced', (e) => {
                console.log('Order baru masuk!', e);
                // Bunyi notif
                let audio = new Audio('/sounds/notif.mp3');
                audio.play().catch(()=>{});
                
                // Tampilkan toast
                alert(`Order baru! #${e.order.id} - Rp ${e.order.total}`);
                
                // Kalau ada tabel order ditahan, refresh
                if (window.location.pathname.includes('kasir')) {
                    // bisa fetch ulang data
                }
            });
    }
});