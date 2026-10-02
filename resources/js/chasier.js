body {
    background: #f5f6fa;
}

.product-results {
    display: none;
    position: absolute;
    background: white;
    border: 1px solid #ddd;
    width: 100%;
    z-index: 1000;
    max-height: 250px;
    overflow-y: auto;
}

.product-item {
    padding: 12px;
    border-bottom: 1px solid #eee;
    cursor: pointer;

    display: flex;
    justify-content: space-between;
}

.product-item:hover {
    background: #f1f5ff;
}

.product-name {
    font-weight: bold;
}

.product-code {
    font-size: 12px;
    color: #777;
}

.product-price {
    font-weight: bold;
}

.search-input {
    position: relative;
}

.qty-control {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
}

.qty-control button {
    width: 32px;
    height: 32px;
    border: 1px solid #ddd;
    background: #f8f9fa;
    border-radius: 5px;
}

.qty-control input {
    width: 55px;
    height: 32px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.remove-btn {
    border: none;
    background: #dc3545;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 5px;
}

@media (max-width: 992px) {
    .container-fluid {
        padding: 10px !important;
    }