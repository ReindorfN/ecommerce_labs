// checkout.js - Manage the simulated payment modal and handle checkout process

let isProcessing = false;

// Show the payment modal
function showPaymentModal() {
    if (isProcessing) {
        return;
    }
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.style.display = 'block';
    }
}

// Close the payment modal
function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('paymentModal');
    if (event.target === modal) {
        closePaymentModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePaymentModal();
    }
});

// Process payment - sends async request to process_checkout_action.php
function processPayment() {
    if (isProcessing) {
        return;
    }

    isProcessing = true;
    
    // Disable buttons
    const confirmBtn = document.querySelector('.modal-btn.confirm');
    const cancelBtn = document.querySelector('.modal-btn.cancel');
    const simulateBtn = document.getElementById('simulatePaymentBtn');
    
    if (confirmBtn) confirmBtn.disabled = true;
    if (cancelBtn) cancelBtn.disabled = true;
    if (simulateBtn) simulateBtn.disabled = true;
    
    // Show loading state
    const modalBody = document.querySelector('.modal-body');
    const originalContent = modalBody.innerHTML;
    modalBody.innerHTML = '<div class="loading"><p>Processing your order...</p><p>Please wait...</p></div>';

    // Send async request to process checkout
    $.ajax({
        url: '../functions/process_checkout_action.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            isProcessing = false;
            
            if (response.success) {
                // Show success message
                showSuccessMessage(response);
                
                // Close modal after a short delay
                setTimeout(function() {
                    closePaymentModal();
                }, 1500);
                
            } else {
                // Show error message
                showErrorMessage(response.message || 'Payment failed. Please try again.');
                
                // Restore modal content
                modalBody.innerHTML = originalContent;
                
                // Re-enable buttons
                if (confirmBtn) confirmBtn.disabled = false;
                if (cancelBtn) cancelBtn.disabled = false;
                if (simulateBtn) simulateBtn.disabled = false;
            }
        },
        error: function(xhr, status, error) {
            isProcessing = false;
            
            console.error('Checkout error:', error);
            
            // Show error message
            showErrorMessage('An error occurred while processing your order. Please try again.');
            
            // Restore modal content
            modalBody.innerHTML = originalContent;
            
            // Re-enable buttons
            if (confirmBtn) confirmBtn.disabled = false;
            if (cancelBtn) cancelBtn.disabled = false;
            if (simulateBtn) simulateBtn.disabled = false;
        }
    });
}

// Show success message with order confirmation
function showSuccessMessage(response) {
    const messagesDiv = document.getElementById('checkoutMessages');
    
    if (!messagesDiv) {
        return;
    }
    
    const invoiceNo = response.invoice_no || 'N/A';
    const orderId = response.order_id || 'N/A';
    const orderTotal = response.order_total || 0;
    
    const successHTML = `
        <div class="success-message">
            <h3>✅ Order Placed Successfully!</h3>
            <p><strong>Order Reference:</strong> ${invoiceNo}</p>
            <p><strong>Order ID:</strong> #${orderId}</p>
            <p><strong>Total Amount:</strong> GH₵${parseFloat(orderTotal).toFixed(2)}</p>
            <p>Thank you for your purchase! Your order has been confirmed.</p>
            <div style="margin-top: 20px;">
                <a href="../index.php" style="display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">Go Home</a>
                <a href="all_products.php" style="display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;">Continue Shopping</a>
            </div>
        </div>
    `;
    
    messagesDiv.innerHTML = successHTML;
    
    // Hide checkout content
    const checkoutContent = document.querySelector('.checkout-content');
    if (checkoutContent) {
        checkoutContent.style.display = 'none';
    }
    
    // Show success message with SweetAlert2 for better UX
    Swal.fire({
        icon: 'success',
        title: 'Order Confirmed!',
        html: `
            <p><strong>Order Reference:</strong> ${invoiceNo}</p>
            <p><strong>Order ID:</strong> #${orderId}</p>
            <p><strong>Total:</strong> GH₵${parseFloat(orderTotal).toFixed(2)}</p>
        `,
        confirmButtonText: 'Continue Shopping',
        confirmButtonColor: '#28a745',
        showCancelButton: true,
        cancelButtonText: 'Go Home',
        cancelButtonColor: '#667eea'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'all_products.php';
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            window.location.href = '../index.php';
        }
    });
}

// Show error message
function showErrorMessage(message) {
    const messagesDiv = document.getElementById('checkoutMessages');
    
    if (!messagesDiv) {
        return;
    }
    
    const errorHTML = `
        <div class="error-message">
            <h3>❌ Payment Failed</h3>
            <p>${message}</p>
        </div>
    `;
    
    messagesDiv.innerHTML = errorHTML;
    
    // Scroll to top to show error
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Show error with SweetAlert2
    Swal.fire({
        icon: 'error',
        title: 'Payment Failed',
        text: message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc3545'
    });
}

// Smooth transitions between cart → checkout → confirmation screens
function handlePageTransition(transitionType) {
    const container = document.querySelector('.container');
    
    if (container) {
        container.style.opacity = '0';
        container.style.transform = 'translateY(20px)';
        container.style.transition = 'all 0.3s ease';
        
        setTimeout(function() {
            container.style.opacity = '1';
            container.style.transform = 'translateY(0)';
        }, 100);
    }
}

// Initialize on page load
$(document).ready(function() {
    handlePageTransition();
    
    // Check if there are any URL parameters for success/error messages
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');
    
    if (status === 'success' && message) {
        showSuccessMessage({ message: decodeURIComponent(message) });
    } else if (status === 'error' && message) {
        showErrorMessage(decodeURIComponent(message));
    }
});

