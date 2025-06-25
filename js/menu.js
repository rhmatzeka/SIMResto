// toggle class active
const navbarNav = document.querySelector(".navbar-nav");
// ketika hamburger menu di klik
document.querySelector("#hamburger").onclick = () => {
  navbarNav.classList.toggle("active");
};
// klik diluar sidebar untuk menghilangkan nav
const hamburger = document.querySelector("#hamburger");

document.addEventListener("click", function (e) {
  if (!hamburger.contains(e.target) && !navbarNav.contains(e.target)) {
    navbarNav.classList.remove("active");
  }
});

// Fungsi untuk menampilkan/menyembunyikan deskripsi item menu
function toggleDescription(card) {
  card.classList.toggle("active");
}

// Fungsi untuk memformat angka menjadi format mata uang Dollar AS
function formatDollar(amount) {
  return "$ " + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
}

// Fungsi untuk mengubah jumlah pesanan item
function changeOrder(button, delta) {
  const orderControls = button.parentElement;
  const quantitySpan = orderControls.querySelector(".order-quantity");
  const card = orderControls.closest(".menu-card");
  const priceDiv = card.querySelector(".menu-price");
  const basePrice = parseFloat(priceDiv.getAttribute("data-base-price"));
  let currentQuantity = parseInt(quantitySpan.textContent);
  currentQuantity += delta;
  if (currentQuantity < 0) currentQuantity = 0;
  quantitySpan.textContent = currentQuantity;

  // Update harga hanya jika kuantitas > 0, jika tidak, tampilkan harga dasar
  // Kode ini sudah benar di versi Anda sebelumnya, jadi saya pertahankan
  // const newPrice = basePrice * currentQuantity;
  // if (newPrice === 0 && currentQuantity === 0) { // Ditambahkan currentQuantity === 0 untuk kejelasan
  //     priceDiv.textContent = formatDollar(basePrice);
  // } else {
  //     priceDiv.textContent = formatDollar(newPrice);
  // }
  // Revisi: Logika harga di card item menu sebaiknya selalu menunjukkan total untuk item itu,
  // atau harga dasar jika kuantitas 0. Namun, untuk konsistensi dengan "Your Order",
  // kita bisa biarkan harga dasar jika 0.
  // Yang terpenting adalah updateCheckout() dipanggil.

  updateCheckout();
}

// Fungsi untuk memperbarui ringkasan checkout di halaman saat ini
function updateCheckout() {
  const cards = document.querySelectorAll(".menu-card");
  const checkoutItemsDiv = document.getElementById("checkout-items");
  const checkoutCard = document.getElementById("checkout-card");
  const checkoutTotalDiv = document.getElementById("checkout-total");
  let grandTotal = 0;
  checkoutItemsDiv.innerHTML = ""; // Kosongkan item sebelum memperbarui

  cards.forEach((card) => {
    const quantity = parseInt(
      card.querySelector(".order-quantity").textContent
    );
    if (quantity > 0) {
      const title = card.querySelector(".menu-title").textContent;
      const basePrice = parseFloat(
        card.querySelector(".menu-price").getAttribute("data-base-price")
      );
      const subtotal = basePrice * quantity;
      grandTotal += subtotal;

      const itemDiv = document.createElement("div");
      itemDiv.className = "checkout-item";

      const nameSpan = document.createElement("span");
      nameSpan.className = "name";
      nameSpan.textContent = title;

      const qtySpan = document.createElement("span");
      qtySpan.className = "qty";
      qtySpan.textContent = "x" + quantity;

      const priceSpan = document.createElement("span");
      priceSpan.className = "price";
      priceSpan.textContent = formatDollar(subtotal);

      itemDiv.appendChild(nameSpan);
      itemDiv.appendChild(qtySpan);
      itemDiv.appendChild(priceSpan);
      checkoutItemsDiv.appendChild(itemDiv);
    }
  });

  if (grandTotal > 0) {
    checkoutCard.style.display = "flex"; // Tampilkan kartu checkout
    checkoutTotalDiv.textContent = "Total: " + formatDollar(grandTotal);
  } else {
    checkoutCard.style.display = "none"; // Sembunyikan jika tidak ada item
  }
}

// Fungsi untuk mengumpulkan data pesanan, menyimpannya, dan pindah halaman
function proceedToCheckout() {
  const cards = document.querySelectorAll(".menu-card");
  const orderData = []; // Array untuk menyimpan item yang dipesan
  let finalGrandTotal = 0;

  cards.forEach((card) => {
    const quantity = parseInt(
      card.querySelector(".order-quantity").textContent
    );
    if (quantity > 0) {
      const title = card.querySelector(".menu-title").textContent;
      const basePrice = parseFloat(
        card.querySelector(".menu-price").getAttribute("data-base-price")
      );
      const subtotal = basePrice * quantity;
      finalGrandTotal += subtotal; // Akumulasi grand total

      orderData.push({
        name: title,
        quantity: quantity,
        price: basePrice, // Harga satuan
        subtotal: subtotal, // Subtotal per item
      });
    }
  });

  if (orderData.length > 0) {
    // Simpan array data pesanan dan total keseluruhan ke localStorage
    localStorage.setItem("checkoutOrderData", JSON.stringify(orderData));
    localStorage.setItem("checkoutGrandTotal", finalGrandTotal.toString());

    // Arahkan ke halaman checkout (pastikan nama file ini benar)
    window.location.href = "chackout.php"; // PERHATIKAN: Anda mungkin salah ketik, biasanya "checkout.html"
  } else {
    // Jika tidak ada item, mungkin tampilkan pesan
    // console.log("Tidak ada item untuk di-checkout.");
    alert("Your cart is empty!"); // Beri tahu pengguna keranjang kosong
  }
}
