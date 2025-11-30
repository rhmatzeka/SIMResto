

<!-- get_payment_status_view.php -->
<div class="container-fluid">
    <h3 class="mb-4"><i class="fas fa-credit-card text-warning"></i> Status Pembayaran per Meja</h3>
    <div id="payment-container">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3">Memuat data pembayaran...</p>
        </div>
    </div>
</div>

<script>



function renderPayment(data) {
    const container = document.getElementById('payment-container');
    
    if (!data || data.length === 0) {
        container.innerHTML = `
            <div class="alert alert-success text-center py-5">
                <i class="fas fa-check-circle fa-3x mb-3"></i>
                <h5>Semua meja sudah lunas!</h5>
            </div>`;
        return;
    }

    let html = `
    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th width="120">Meja</th>
                    <th width="180">Jumlah Pesanan</th>
                    <th width="200">Total Tagihan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>`;

    data.forEach(row => {
        html += `
            <tr class="table-warning">
                <td class="text-center fw-bold fs-5">Meja ${row.table_number}</td>
                <td class="text-center"><span class="badge bg-primary fs-6">${row.total_pending_orders} pesanan</span></td>
                <td class="text-end fw-bold fs-5">Rp ${parseFloat(row.total_unpaid_amount).toLocaleString('id-ID')}</td>
                <td class="text-center">
                    <button class="btn btn-success btn-lg" onclick="markAsPaid(${row.table_number})">
                        <i class="fas fa-check"></i> Sudah Dibayar
                    </button>
                </td>
            </tr>`;
    });

    html += `</tbody></table></div>`;
    container.innerHTML = html;
}

function loadPayment() {
    fetch('get_payment_status.php')
        .then(response => {
            if (!response.ok) throw new Error('Server error');
            return response.json();
        })
        .then(json => {
            // TERIMA APA PUN FORMATNYA — ARRAY LANGSUNG ATAU OBJECT
            let data = [];
            if (Array.isArray(json)) data = json;
            else if (json && json.data) data = json.data;
            else if (json && json.status === 'success') data = json.data || [];

            renderPayment(data);
        })
        .catch(err => {
            console.error(err);
            document.getElementById('payment-container').innerHTML = 
                '<div class="alert alert-danger">Gagal memuat data pembayaran. Cek console untuk detail.</div>';
        });
}

// Jalankan pertama kali + auto refresh tiap 8 detik
loadPayment();
setInterval(loadPayment, 8000);
</script>