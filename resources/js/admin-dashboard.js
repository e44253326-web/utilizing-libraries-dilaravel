import './echo';

const notice = document.querySelector('[data-transaction-live-notice]');
const transactionRows = document.querySelector('[data-recent-transactions]');
const transactionCount = document.querySelector('[data-today-transactions]');
const revenue = document.querySelector('[data-today-revenue]');

if (notice && transactionRows && transactionCount && revenue && window.Echo) {
    window.Echo.private('admin.transactions').listen('.transaction.placed', (event) => {
        const transactionId = String(event.transaction_id);
        const alreadyDisplayed = Array.from(transactionRows.querySelectorAll('[data-transaction-id]'))
            .some((row) => row.dataset.transactionId === transactionId);

        if (alreadyDisplayed) {
            return;
        }

        notice.textContent = `Transaksi ${event.transaction_number} dari ${event.cashier_name} baru saja berhasil dibayar.`;
        notice.classList.remove('d-none');

        if (event.is_today) {
            const count = Number(transactionCount.dataset.value) + 1;
            const total = Number(revenue.dataset.value) + Number(event.grand_total);

            transactionCount.dataset.value = String(count);
            transactionCount.textContent = count.toLocaleString('id-ID');
            revenue.dataset.value = String(total);
            revenue.textContent = `Rp${total.toLocaleString('id-ID', { maximumFractionDigits: 0 })}`;
        }

        const row = document.createElement('tr');
        row.dataset.transactionId = transactionId;

        const cells = [
            { value: event.transaction_number, className: 'fw-semibold text-primary' },
            { value: event.cashier_name },
            { value: event.payment_method, badge: true },
            { value: event.created_at, className: 'text-secondary text-nowrap' },
            {
                value: `Rp${Number(event.grand_total).toLocaleString('id-ID', { maximumFractionDigits: 0 })}`,
                className: 'text-end fw-semibold text-nowrap',
            },
        ];

        for (const { value, className = '', badge = false } of cells) {
            const cell = document.createElement('td');

            if (className) {
                cell.className = className;
            }

            if (badge) {
                const paymentBadge = document.createElement('span');
                paymentBadge.className = 'admin-payment-badge';
                paymentBadge.textContent = value;
                cell.append(paymentBadge);
            } else {
                cell.textContent = value;
            }

            row.append(cell);
        }

        transactionRows.querySelector('[data-empty-transactions]')?.remove();
        transactionRows.prepend(row);
        transactionRows.querySelectorAll('[data-transaction-id]')
            .forEach((item, index) => {
                if (index >= 8) {
                    item.remove();
                }
            });
    });
}
