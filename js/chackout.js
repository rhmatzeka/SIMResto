
        // Function to format numbers into US Dollar currency format
        function formatDollar(amount) {
            if (typeof amount !== 'number') {
                amount = parseFloat(amount);
            }
            if (isNaN(amount)) {
                return "$0.00"; // Default if not a number
            }
            return "$" + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const orderedItemsContainer = document.getElementById('ordered-items-container');
            const emptyCartMessage = document.getElementById('empty-cart-message');
            const grandTotalValueElement = document.getElementById('grand-total-value');
            const confirmOrderButton = document.getElementById('confirm-order-button');
            const paymentErrorElement = document.getElementById('payment-error-message');
            const paymentMethodRadios = document.querySelectorAll('input[name="paymentMethod"]');
            const paymentDetailsDivs = document.querySelectorAll('.payment-options .payment-details');
            const paymentMethodSection = document.getElementById('payment-method-section');


            // Retrieve order data from localStorage
            const orderDataString = localStorage.getItem('checkoutOrderData');
            const grandTotalString = localStorage.getItem('checkoutGrandTotal');
            
            let orderData = [];
            let grandTotal = 0;

            if (orderDataString && grandTotalString) {
                try {
                    orderData = JSON.parse(orderDataString);
                    grandTotal = parseFloat(grandTotalString);
                } catch (e) {
                    console.error("Error parsing order data from localStorage:", e);
                    handleEmptyCart();
                    return;
                }
            } else {
                handleEmptyCart();
                return; 
            }

            if (orderData.length > 0 && !isNaN(grandTotal)) {
                displayOrderDetails(orderData, grandTotal);
            } else {
                handleEmptyCart();
            }

            function displayOrderDetails(items, total) {
                const itemsHeader = orderedItemsContainer.querySelector('.items-header');
                if(itemsHeader) itemsHeader.style.display = 'flex'; // Ensure header is visible

                const existingItemRows = orderedItemsContainer.querySelectorAll('.item-details-row');
                existingItemRows.forEach(row => row.remove());
                emptyCartMessage.style.display = 'none';

                items.forEach(item => {
                    const itemRow = document.createElement('div');
                    itemRow.classList.add('item-details-row');

                    const itemNameSpan = document.createElement('span');
                    itemNameSpan.classList.add('item-name');
                    itemNameSpan.textContent = item.name;

                    const itemQtySpan = document.createElement('span');
                    itemQtySpan.classList.add('item-qty');
                    itemQtySpan.textContent = 'x' + item.quantity;

                    const itemPriceSpan = document.createElement('span');
                    itemPriceSpan.classList.add('item-price');
                    itemPriceSpan.textContent = formatDollar(item.subtotal); // Use formatDollar

                    itemRow.appendChild(itemNameSpan);
                    itemRow.appendChild(itemQtySpan);
                    itemRow.appendChild(itemPriceSpan);
                    orderedItemsContainer.appendChild(itemRow);
                });

                grandTotalValueElement.textContent = formatDollar(total); // Use formatDollar
                paymentMethodSection.style.display = 'block'; // Show payment methods
            }

            function handleEmptyCart() {
                const itemsHeader = orderedItemsContainer.querySelector('.items-header');
                 if(itemsHeader) itemsHeader.style.display = 'none'; 
                
                const existingItemRows = orderedItemsContainer.querySelectorAll('.item-details-row');
                existingItemRows.forEach(row => row.remove());

                emptyCartMessage.style.display = 'block';
                grandTotalValueElement.textContent = formatDollar(0); // Use formatDollar
                paymentMethodSection.style.display = 'none';
                confirmOrderButton.textContent = 'Cart is Empty';
                confirmOrderButton.disabled = true;
            }

            // Payment Method Logic
            paymentMethodRadios.forEach(radio => {
                radio.addEventListener('change', () => {
                    paymentDetailsDivs.forEach(div => div.style.display = 'none'); 
                    
                    const selectedDetails = radio.closest('div').querySelector('.payment-details');
                    if (selectedDetails) {
                        selectedDetails.style.display = 'block';
                    }
                    confirmOrderButton.disabled = false;
                    confirmOrderButton.textContent = 'Confirm Order';
                    paymentErrorElement.style.display = 'none';
                });
            });

            // Confirm Order Button Logic
            confirmOrderButton.addEventListener('click', () => {
                if (confirmOrderButton.disabled || orderData.length === 0) return; // Extra check

                const selectedPaymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
                if (!selectedPaymentMethod) {
                    paymentErrorElement.style.display = 'block';
                    return;
                }

                // Simulate order confirmation
                alert(`Order confirmed with payment method: ${selectedPaymentMethod.value}.\nTotal: ${formatDollar(grandTotal)}\nThank you for your order!`);

                // Clear localStorage to prevent resubmission
                localStorage.removeItem('checkoutOrderData');
                localStorage.removeItem('checkoutGrandTotal');

                // Redirect back to the menu page or a "thank you" page
                window.location.href = 'index.html'; // Change index.html to your menu file name if different
            });

            // Update year in footer
            document.getElementById('current-year').textContent = new Date().getFullYear();
        });