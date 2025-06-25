// toggle class active untuk navbar
const navbarNav = document.querySelector(".navbar-nav");
if (navbarNav) {
  const hamburgerButton = document.querySelector("#hamburger");
  if (hamburgerButton) {
    hamburgerButton.addEventListener("click", (e) => {
      console.log("Hamburger menu diklik.");
      navbarNav.classList.toggle("active");
      e.preventDefault();
    });
  } else {
    console.warn("Tombol #hamburger tidak ditemukan.");
  }
} else {
  console.warn("Elemen .navbar-nav tidak ditemukan.");
}

const hamburger = document.querySelector("#hamburger");
document.addEventListener("click", function (e) {
  if (navbarNav && hamburger) {
    if (!hamburger.contains(e.target) && !navbarNav.contains(e.target)) {
      if (navbarNav.classList.contains("active")) {
        navbarNav.classList.remove("active");
      }
    }
  }
});

function toggleDescription(cardElement) {
  if (cardElement) {
    cardElement.classList.toggle("active");
  } else {
    console.warn("toggleDescription dipanggil dengan cardElement null.");
  }
}

function formatDollar(amount) {
  const numAmount = parseFloat(amount);
  if (isNaN(numAmount)) {
    return "$ 0.00";
  }
  return "$" + numAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
}

function changeOrder(button, delta) {
  console.log(
    `--- menulogin.js: changeOrder dipanggil --- Tombol:`,
    button,
    `Delta: ${delta}`
  );
  if (!button) {
    console.error("changeOrder: Tombol tidak valid.");
    return;
  }
  const orderControls = button.parentElement;
  if (!orderControls) {
    console.error(
      "changeOrder: Elemen .order-controls (parent tombol) tidak ditemukan."
    );
    return;
  }
  const quantitySpan = orderControls.querySelector(".order-quantity");

  if (!quantitySpan) {
    console.error(
      "changeOrder: Elemen .order-quantity tidak ditemukan di dalam .order-controls."
    );
    return;
  }

  let currentQuantity = parseInt(quantitySpan.textContent);
  if (isNaN(currentQuantity)) {
    console.warn("changeOrder: Kuantitas awal NaN, direset ke 0.");
    currentQuantity = 0;
  }

  currentQuantity += delta;
  if (currentQuantity < 0) {
    currentQuantity = 0;
  }
  quantitySpan.textContent = currentQuantity;
  console.log(`changeOrder: Kuantitas baru di kartu menu: ${currentQuantity}`);

  updateCheckout();
}

function updateCheckout() {
  console.log("--- menulogin.js: updateCheckout dipanggil ---");
  const cards = document.querySelectorAll(".menu-card");
  const checkoutItemsDiv = document.getElementById("checkout-items");
  const checkoutCard = document.getElementById("checkout-card");
  const checkoutTotalDiv = document.getElementById("checkout-total");
  let grandTotal = 0;
  let itemsInCheckoutCount = 0;

  if (!checkoutItemsDiv || !checkoutCard || !checkoutTotalDiv) {
    console.error(
      "updateCheckout: Elemen DOM untuk checkout (checkout-items, checkout-card, atau checkout-total) tidak ditemukan."
    );
    return;
  }

  checkoutItemsDiv.innerHTML = "";

  cards.forEach((card, index) => {
    const quantitySpan = card.querySelector(".order-quantity");
    const menuTitleElem = card.querySelector(".menu-title");
    const menuPriceElem = card.querySelector(".menu-price");

    if (!quantitySpan || !menuTitleElem || !menuPriceElem) {
      console.warn(
        `updateCheckout: Card #${index + 1} tidak memiliki struktur lengkap.`
      );
      return;
    }

    const quantityText = quantitySpan.textContent;
    const quantity = parseInt(quantityText);

    if (isNaN(quantity) || quantity <= 0) {
      return;
    }

    itemsInCheckoutCount++;
    const title = menuTitleElem.textContent;
    const basePriceAttr = menuPriceElem.getAttribute("data-base-price");

    if (basePriceAttr === null) {
      console.warn(
        `updateCheckout: Card #${
          index + 1
        } (${title}) tidak memiliki atribut 'data-base-price'.`
      );
      return;
    }
    const basePrice = parseFloat(basePriceAttr);
    if (isNaN(basePrice)) {
      console.warn(
        `updateCheckout: Card #${
          index + 1
        } (${title}) nilai 'data-base-price' tidak valid: '${basePriceAttr}'.`
      );
      return;
    }

    const subtotal = basePrice * quantity;
    grandTotal += subtotal;

    const itemDiv = document.createElement("div");
    itemDiv.className = "checkout-item";
    itemDiv.innerHTML = `<span class="name">${title}</span><span class="qty">x${quantity}</span><span class="price">${formatDollar(
      subtotal
    )}</span>`;
    checkoutItemsDiv.appendChild(itemDiv);
  });

  if (itemsInCheckoutCount > 0) {
    if (checkoutCard) checkoutCard.style.display = "flex";
    if (checkoutTotalDiv)
      checkoutTotalDiv.textContent = "Total: " + formatDollar(grandTotal);
  } else {
    if (checkoutCard) checkoutCard.style.display = "none";
    if (checkoutTotalDiv)
      checkoutTotalDiv.textContent = "Total: " + formatDollar(0);
  }
}

function proceedToCheckout() {
  console.log("--- menulogin.js: proceedToCheckout ---");
  const cards = document.querySelectorAll(".menu-card");
  const orderData = [];
  let finalGrandTotal = 0;

  cards.forEach((card, index) => {
    const quantitySpan = card.querySelector(".order-quantity");
    const menuTitleElem = card.querySelector(".menu-title");
    const menuPriceElem = card.querySelector(".menu-price");
    const menuItemIdAttr = card.getAttribute("data-menu-item-id");

    if (!quantitySpan || !menuTitleElem || !menuPriceElem || !menuItemIdAttr) {
      console.warn(
        `proceedToCheckout: Card #${
          index + 1
        } struktur tidak lengkap atau tidak ada data-menu-item-id.`
      );
      return;
    }

    const quantity = parseInt(quantitySpan.textContent);
    if (isNaN(quantity) || quantity <= 0) {
      return;
    }

    const title = menuTitleElem.textContent;
    const basePriceAttr = menuPriceElem.getAttribute("data-base-price");
    const menuItemId = parseInt(menuItemIdAttr);

    if (basePriceAttr === null || isNaN(menuItemId) || menuItemId <= 0) {
      console.warn(
        `proceedToCheckout: Card #${
          index + 1
        } (${title}) 'data-base-price' atau 'data-menu-item-id' tidak valid.`
      );
      return;
    }
    const basePrice = parseFloat(basePriceAttr);
    if (isNaN(basePrice)) {
      console.warn(
        `proceedToCheckout: Card #${
          index + 1
        } (${title}) 'data-base-price' tidak valid: '${basePriceAttr}'.`
      );
      return;
    }

    const subtotal = basePrice * quantity;
    finalGrandTotal += subtotal;

    orderData.push({
      menu_item_id: menuItemId,
      name: title,
      quantity: quantity,
      price_per_unit: basePrice, // Mengirim harga satuan juga
      subtotal: subtotal,
    });
  });

  console.log("proceedToCheckout: Data Pesanan untuk POST:", orderData);

  if (orderData.length > 0) {
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "chackout.php";

    const orderDataInput = document.createElement("input");
    orderDataInput.type = "hidden";
    orderDataInput.name = "orderData";
    orderDataInput.value = JSON.stringify(orderData);
    form.appendChild(orderDataInput);

    const grandTotalInput = document.createElement("input");
    grandTotalInput.type = "hidden";
    grandTotalInput.name = "finalGrandTotal";
    grandTotalInput.value = finalGrandTotal.toString();
    form.appendChild(grandTotalInput);

    document.body.appendChild(form);
    form.submit();
  } else {
    alert("Your cart is empty! Please add items to your order.");
  }
}

function setDarkMode(isDark) {
  const moonBtn = document.getElementById("moon-btn");
  const sunBtn = document.getElementById("sun-btn");

  document.body.classList.toggle("dark-mode", isDark);
  if (moonBtn) moonBtn.style.display = isDark ? "none" : "inline-block";
  if (sunBtn) sunBtn.style.display = isDark ? "inline-block" : "none";
}

document.addEventListener("DOMContentLoaded", () => {
  console.log(
    "--- menulogin.js: DOMContentLoaded --- Inisialisasi Dimulai ---"
  );

  localStorage.removeItem("checkoutOrderData");
  localStorage.removeItem("checkoutGrandTotal");

  const quantitySpans = document.querySelectorAll(".menu-card .order-quantity");
  quantitySpans.forEach((span) => {
    span.textContent = "0";
  });
  console.log("menulogin.js: Kuantitas item direset.");

  updateCheckout();

  if (typeof feather !== "undefined") {
    feather.replace();
  } else {
    console.warn("menulogin.js: Library Feather icons tidak ditemukan.");
  }

  const moonBtn = document.getElementById("moon-btn");
  const sunBtn = document.getElementById("sun-btn");
  if (moonBtn) {
    moonBtn.addEventListener("click", (e) => {
      e.preventDefault();
      setDarkMode(true);
    });
  }
  if (sunBtn) {
    sunBtn.addEventListener("click", (e) => {
      e.preventDefault();
      setDarkMode(false);
    });
  }
  setDarkMode(false);

  const proceedButton = document.getElementById("proceed-to-checkout-button");
  if (proceedButton) {
    proceedButton.addEventListener("click", proceedToCheckout);
  } else {
    console.warn(
      'menulogin.js: Tombol "#proceed-to-checkout-button" tidak ditemukan.'
    );
  }

  document.querySelectorAll(".decrease-order").forEach((button) => {
    button.addEventListener("click", function () {
      changeOrder(this, -1);
    });
  });
  document.querySelectorAll(".increase-order").forEach((button) => {
    button.addEventListener("click", function () {
      changeOrder(this, 1);
    });
  });

  document.querySelectorAll(".menu-card").forEach((card) => {
    card.addEventListener("click", function (event) {
      if (!event.target.closest(".order-controls")) {
        toggleDescription(this);
      }
    });
  });

  console.log(
    "--- menulogin.js: DOMContentLoaded --- Inisialisasi Selesai ---"
  );
});
