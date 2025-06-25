<h3 class="border-bottom pb-2 mb-3">Status Pembayaran Meja</h3>
<div id="payment-status-container" class="row">
    <p class="text-muted">Memuat status pembayaran...</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentStatusContainer = document.getElementById('payment-status-container');

    function renderPaymentStatus(statuses) {
        paymentStatusContainer.innerHTML = '';
        if (statuses.length === 0) {
            paymentStatusContainer.innerHTML = '<p class="text-muted">Tidak ada meja dengan pesanan pending.</p>';
            return;
        }

        statuses.forEach(status => {
            const cardHtml = `
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Meja ${status.table_number}</h5>
                            <p class="card-text">Total Pesanan Pending: <strong>${status.total_pending_orders}</strong></p>
                            <p class="card-text">Total Belum Dibayar: <strong>Rp ${parseFloat(status.total_unpaid_amount).toFixed(2)}</strong></p>
                            <p class="card-text text-muted-small">Order IDs: ${status.order_ids}</p>
                            <button class="btn btn-sm btn-info disabled" title="Fungsionalitas bayar belum diimplementasikan">Tandai Sudah Bayar</button>
                            </div>
                    </div>
                </div>
            `;
            paymentStatusContainer.innerHTML += cardHtml;
        });
    }

    if (typeof window.fetchPaymentStatus === 'function') {
        window.fetchPaymentStatus()
            .then(data => {
                if (data.status === 'error') {
                    window.showNotification(data.message, 'danger');
                    paymentStatusContainer.innerHTML = '<p class="text-danger">Gagal memuat status pembayaran.</p>';
                } else {
                    renderPaymentStatus(data);
                }
            })
            .catch(window.handleError);
    } else {
        paymentStatusContainer.innerHTML = '<p class="text-danger">Fungsi fetchPaymentStatus tidak tersedia.</p>';
    }
});
</script>