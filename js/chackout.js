// js/chackout.js

function formatDollar(amount) {
  if (typeof amount !== "number") {
    amount = parseFloat(amount);
  }
  if (isNaN(amount)) {
    return "$0.00";
  }
  return "$" + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
}

document.addEventListener("DOMContentLoaded", () => {
  console.log("Chackout.js: DOMContentLoaded - Halaman checkout dimuat.");
  const orderedItemsContainer = document.getElementById(
    "ordered-items-container"
  );
  const emptyCartMessage = document.getElementById("empty-cart-message");
  const grandTotalValueElement = document.getElementById("grand-total-value");
  const confirmOrderButton = document.getElementById("confirm-order-button");
  const paymentErrorElement = document.getElementById("payment-error-message");
  const paymentMethodRadios = document.querySelectorAll(
    'input[name="paymentMethod"]'
  );
  const paymentDetailsDivs = document.querySelectorAll(
    ".payment-options .payment-details"
  );
  const paymentMethodSection = document.getElementById(
    "payment-method-section"
  );
  const itemsHeader = orderedItemsContainer
    ? orderedItemsContainer.querySelector(".items-header")
    : null;

  // --- MULAI KODE BARU UNTUK DISKON ---

  // Ambil elemen-elemen yang berhubungan dengan diskon dari DOM
  const applyDiscountButton = document.getElementById("apply-discount-button");
  const discountCodeInput = document.getElementById("discount-code-input");
  const discountMessageElement = document.getElementById("discount-message");
  const discountRow = document.getElementById("discount-row");
  const discountValueElement = document.getElementById("discount-value");
  const subtotalValueElement = document.getElementById("subtotal-value");

  // Inisialisasi variabel untuk menyimpan status diskon
  let initialSubtotal =
    typeof phpGrandTotal !== "undefined" ? phpGrandTotal : 0;
  let currentGrandTotal = initialSubtotal;
  let appliedDiscountAmount = 0;
  let appliedDiscountCode = null;

  // Saat halaman dimuat, atur nilai subtotal dan grand total awal
  if (subtotalValueElement)
    subtotalValueElement.textContent = formatDollar(initialSubtotal);
  // grandTotalValueElement akan diatur nanti di bawah

  // Tambahkan event listener untuk tombol "Apply"
  if (applyDiscountButton) {
    applyDiscountButton.addEventListener("click", async () => {
      const code = discountCodeInput.value.trim().toUpperCase();
      if (!code) {
        discountMessageElement.textContent = "Please enter a discount code.";
        discountMessageElement.style.color = "red";
        discountMessageElement.style.display = "block";
        return;
      }

      // Nonaktifkan tombol selama proses validasi
      applyDiscountButton.disabled = true;
      applyDiscountButton.textContent = "Validating...";

      try {
        // Kirim request ke server untuk validasi kode
        const response = await fetch("validate_discount.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            kode_diskon: code,
            subtotal: initialSubtotal, // Gunakan subtotal awal untuk perhitungan
          }),
        });

        const result = await response.json();

        if (result.success) {
          // Jika diskon valid
          appliedDiscountAmount = result.discount_amount;
          appliedDiscountCode = code; // Simpan kode diskon yang berhasil
          currentGrandTotal = initialSubtotal - appliedDiscountAmount;

          // Update UI
          discountMessageElement.textContent = result.message;
          discountMessageElement.style.color = "#27ae60"; // Warna hijau untuk sukses
          discountValueElement.textContent = `-${formatDollar(
            appliedDiscountAmount
          )}`;
          grandTotalValueElement.textContent = formatDollar(currentGrandTotal);

          discountRow.style.display = "flex";
          discountCodeInput.disabled = true;
          applyDiscountButton.textContent = "Applied!";
        } else {
          // Jika diskon tidak valid
          discountMessageElement.textContent = result.message;
          discountMessageElement.style.color = "red";
          applyDiscountButton.disabled = false; // Aktifkan kembali tombol
          applyDiscountButton.textContent = "Apply";
        }
      } catch (error) {
        console.error("Error validating discount:", error);
        discountMessageElement.textContent =
          "An error occurred. Please try again.";
        discountMessageElement.style.color = "red";
        applyDiscountButton.disabled = false;
        applyDiscountButton.textContent = "Apply";
      } finally {
        discountMessageElement.style.display = "block";
      }
    });
  }
  // --- AKHIR KODE BARU UNTUK DISKON ---

  // Variabel phpOrderItems, phpGrandTotal, dan phpCartIsEmpty sudah ada dari inline script di chackout.php
  console.log("Chackout.js: Data dari PHP:", {
    phpOrderItems,
    phpGrandTotal,
    phpCartIsEmpty,
  });

  if (typeof phpCartIsEmpty !== "undefined" && phpCartIsEmpty) {
    console.log("Chackout.js: Keranjang kosong menurut PHP.");
    handleEmptyCart();
  } else if (
    typeof phpOrderItems !== "undefined" &&
    phpOrderItems &&
    Array.isArray(phpOrderItems) &&
    phpOrderItems.length > 0 &&
    typeof phpGrandTotal !== "undefined"
  ) {
    console.log("Chackout.js: Menampilkan detail pesanan dari PHP.");
    displayOrderDetails(phpOrderItems, phpGrandTotal);
  } else {
    console.warn(
      "Chackout.js: Variabel PHP tidak valid atau keranjang kosong. Menjalankan handleEmptyCart."
    );
    handleEmptyCart();
  }

  function displayOrderDetails(items, total) {
    if (itemsHeader) itemsHeader.style.display = "flex";
    if (emptyCartMessage) emptyCartMessage.style.display = "none";
    if (!orderedItemsContainer) {
      console.error("Chackout.js: orderedItemsContainer tidak ditemukan!");
      return;
    }

    const existingItemRows =
      orderedItemsContainer.querySelectorAll(".item-details-row");
    existingItemRows.forEach((row) => row.remove());

    items.forEach((item) => {
      const itemRow = document.createElement("div");
      itemRow.classList.add("item-details-row");

      const itemNameSpan = document.createElement("span");
      itemNameSpan.classList.add("item-name");
      itemNameSpan.textContent = item.name || "Unknown Item";

      const itemQtySpan = document.createElement("span");
      itemQtySpan.classList.add("item-qty");
      itemQtySpan.textContent = "x" + (item.quantity || 0);

      const itemPriceSpan = document.createElement("span");
      itemPriceSpan.classList.add("item-price");
      itemPriceSpan.textContent = formatDollar(item.subtotal || 0);

      itemRow.appendChild(itemNameSpan);
      itemRow.appendChild(itemQtySpan);
      itemRow.appendChild(itemPriceSpan);

      if (itemsHeader) {
        itemsHeader.parentNode.insertBefore(itemRow, itemsHeader.nextSibling);
      } else {
        orderedItemsContainer.appendChild(itemRow);
      }
    });

    if (grandTotalValueElement)
      grandTotalValueElement.textContent = formatDollar(total);
    if (paymentMethodSection) paymentMethodSection.style.display = "block";
    if (confirmOrderButton) {
      confirmOrderButton.disabled = true;
      confirmOrderButton.textContent = "Select Payment First";
    }
    paymentMethodRadios.forEach((radio) => (radio.disabled = false));
  }

  function handleEmptyCart() {
    console.log("Chackout.js: handleEmptyCart dipanggil.");
    if (itemsHeader) itemsHeader.style.display = "none";
    if (emptyCartMessage) emptyCartMessage.style.display = "block";
    if (grandTotalValueElement)
      grandTotalValueElement.textContent = formatDollar(0);
    if (paymentMethodSection) paymentMethodSection.style.display = "none";

    // Nonaktifkan juga bagian diskon jika keranjang kosong
    if (document.getElementById("discount-section")) {
      document.getElementById("discount-section").style.display = "none";
    }

    if (confirmOrderButton) {
      confirmOrderButton.textContent = "Cart is Empty";
      confirmOrderButton.disabled = true;
    }
    paymentMethodRadios.forEach((radio) => {
      radio.disabled = true;
      radio.checked = false;
    });
    paymentDetailsDivs.forEach((div) => (div.style.display = "none"));
  }

  paymentMethodRadios.forEach((radio) => {
    radio.addEventListener("change", () => {
      console.log("Chackout.js: Metode pembayaran dipilih:", radio.value);
      paymentDetailsDivs.forEach((div) => (div.style.display = "none"));

      const selectedDiv = radio.closest("div");
      if (selectedDiv) {
        const selectedDetails = selectedDiv.querySelector(".payment-details");
        if (selectedDetails) {
          selectedDetails.style.display = "block";
        }
      }

      if (confirmOrderButton) {
        const cartActuallyEmpty =
          (typeof phpCartIsEmpty !== "undefined" && phpCartIsEmpty) ||
          typeof phpOrderItems === "undefined" ||
          !phpOrderItems ||
          phpOrderItems.length === 0;
        if (!cartActuallyEmpty) {
          confirmOrderButton.disabled = false;
          confirmOrderButton.textContent = "Confirm Order & Pay";
        } else {
          confirmOrderButton.textContent = "Cart is Empty";
          confirmOrderButton.disabled = true;
        }
      }
      if (paymentErrorElement) paymentErrorElement.style.display = "none";
    });
  });

  if (confirmOrderButton) {
    confirmOrderButton.addEventListener("click", async (event) => {
      // 1. Tambahkan 'event' di sini
      event.preventDefault(); // <-- 2. TAMBAHKAN BARIS INI DI PALING ATAS

      console.log(
        "Chackout.js: Tombol Confirm Order diklik dan default action dicegah."
      );
      console.log("Chackout.js: Tombol Confirm Order diklik.");
      const isCartEffectivelyEmpty =
        (typeof phpCartIsEmpty !== "undefined" && phpCartIsEmpty) ||
        typeof phpOrderItems === "undefined" ||
        !Array.isArray(phpOrderItems) ||
        phpOrderItems.length === 0;

      if (confirmOrderButton.disabled || isCartEffectivelyEmpty) {
        alert("Your cart is empty. Please add items from the menu.");
        console.log(
          "Chackout.js: Percobaan konfirmasi dengan keranjang kosong atau tombol disable."
        );
        return;
      }

      const selectedPaymentMethodInput = document.querySelector(
        'input[name="paymentMethod"]:checked'
      );
      if (!selectedPaymentMethodInput) {
        if (paymentErrorElement) paymentErrorElement.style.display = "block";
        console.log("Chackout.js: Metode pembayaran belum dipilih.");
        return;
      }
      const selectedPaymentMethod = selectedPaymentMethodInput.value;
      console.log(
        "Chackout.js: Metode pembayaran terkonfirmasi:",
        selectedPaymentMethod
      );

      const currentOrderItems =
        typeof phpOrderItems !== "undefined" && Array.isArray(phpOrderItems)
          ? phpOrderItems
          : [];

      if (currentOrderItems.length === 0) {
        alert("No items to order.");
        console.log(
          "Chackout.js: Tidak ada item untuk diorder (currentOrderItems kosong)."
        );
        return;
      }

      confirmOrderButton.disabled = true;
      confirmOrderButton.textContent = "Processing...";
      console.log("Chackout.js: Memproses pesanan...");

      // --- MULAI PERUBAHAN PAYLOAD ---
      const orderPayload = {
        items: currentOrderItems,
        grandTotal: currentGrandTotal, // Gunakan grand total yang sudah didiskon
        paymentMethod: selectedPaymentMethod,
        subtotal: initialSubtotal, // Kirim juga subtotal asli
        discountCode: appliedDiscountCode, // Kirim kode diskon yang dipakai
        discountAmount: appliedDiscountAmount, // Kirim jumlah potongannya
      };
      // --- AKHIR PERUBAHAN PAYLOAD ---

      console.log("Chackout.js: Payload ke process_order.php:", orderPayload);

      try {
        const response = await fetch("process_order.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(orderPayload),
        });

        const resultText = await response.text();
        console.log("Chackout.js: Respons mentah dari server:", resultText);

        let result;
        try {
          result = JSON.parse(resultText);
        } catch (e) {
          console.error(
            "Chackout.js: Gagal parse respons server sebagai JSON:",
            e
          );
          alert(
            `Order processing failed: Server returned an invalid response. (Status: ${
              response.status
            })\n\nServer Response:\n${resultText.substring(0, 500)}`
          );
          confirmOrderButton.disabled = false;
          confirmOrderButton.textContent = "Confirm Order & Pay";
          return;
        }

        console.log("Chackout.js: Respons JSON dari server:", result);

        if (response.ok && result.success) {
          console.log(
            "Chackout.js: Pesanan berhasil diproses oleh server. Order ID:",
            result.order_id
          );
          alert(
            `Order successfully placed!\nOrder ID: ${
              result.order_id
            }\nTotal: ${formatDollar(
              currentGrandTotal
            )}\nPayment Method: ${selectedPaymentMethod}\nThank you for your order!`
          );

          localStorage.removeItem("checkoutOrderData");
          localStorage.removeItem("checkoutGrandTotal");
          console.log(
            "Chackout.js: localStorage (checkoutOrderData, checkoutGrandTotal) dibersihkan setelah order sukses."
          );

          window.location.href =
            "order_confirmation.php?order_id=" + result.order_id;
        } else {
          let alertMessage = `Order processing failed: ${
            result.message || "Unknown server error"
          }`;
          if (result.debug_info) {
            alertMessage += `\n\nServer Debug Info:\n${result.debug_info}`;
          }
          console.error(
            "Chackout.js: Gagal memproses pesanan di server:",
            alertMessage
          );
          alert(alertMessage);
          confirmOrderButton.disabled = false;
          confirmOrderButton.textContent = "Confirm Order & Pay";
        }
      } catch (error) {
        console.error(
          "Chackout.js: Error saat mengirim pesanan (network error atau lainnya):",
          error
        );
        alert(
          "An error occurred while submitting your order. Please check your connection and try again."
        );
        confirmOrderButton.disabled = false;
        confirmOrderButton.textContent = "Confirm Order & Pay";
      }
    });
  }

  const currentYearElement = document.getElementById("current-year");
  if (currentYearElement) {
    currentYearElement.textContent = new Date().getFullYear();
  }

  let isPaymentSelectedOnLoad = false;
  paymentMethodRadios.forEach((radio) => {
    if (radio.checked) {
      isPaymentSelectedOnLoad = true;
      const selectedDiv = radio.closest("div");
      if (selectedDiv) {
        const selectedDetails = selectedDiv.querySelector(".payment-details");
        if (selectedDetails) {
          selectedDetails.style.display = "block";
        }
      }
    }
  });
  if (!isPaymentSelectedOnLoad) {
    paymentDetailsDivs.forEach((div) => (div.style.display = "none"));
  }

  const cartIsTrulyEmpty =
    (typeof phpCartIsEmpty !== "undefined" && phpCartIsEmpty) ||
    typeof phpOrderItems === "undefined" ||
    !phpOrderItems ||
    phpOrderItems.length === 0;
  if (confirmOrderButton) {
    if (cartIsTrulyEmpty) {
      confirmOrderButton.textContent = "Cart is Empty";
      confirmOrderButton.disabled = true;
    } else if (!document.querySelector('input[name="paymentMethod"]:checked')) {
      confirmOrderButton.textContent = "Select Payment First";
      confirmOrderButton.disabled = true;
    } else {
      confirmOrderButton.textContent = "Confirm Order & Pay";
      confirmOrderButton.disabled = false;
    }
  }
  console.log("Chackout.js: Inisialisasi Selesai.");
});
