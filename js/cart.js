// Cart Management JavaScript

// Helper function to safely parse JSON response (handles both string and already-parsed objects)
function parseJsonResponse(response) {
    if (typeof response === 'string') {
        try {
            return JSON.parse(response);
        } catch (e) {
            console.error('JSON parse error:', e);
            return null;
        }
    }
    return response; // Already an object
}

// Function to get client IP (for reference, but we'll use server-side)
function getClientIp() {
    // This is handled server-side, but kept for reference
    return '';
}

// Update quantity by increment/decrement
function updateQuantity(productId, change) {
    const quantityInput = document.querySelector(`input[data-product-id="${productId}"]`);
    if (!quantityInput) return;
    
    let currentQty = parseInt(quantityInput.value) || 1;
    let newQty = currentQty + change;
    
    if (newQty < 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Quantity',
            text: 'Quantity must be at least 1. Use Remove button to delete items.',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    quantityInput.value = newQty;
    saveQuantityChange(productId, newQty);
}

// Update quantity directly from input field
function updateQuantityDirect(productId, newQuantity) {
    const qty = parseInt(newQuantity);
    
    if (isNaN(qty) || qty < 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Quantity',
            text: 'Quantity must be at least 1.',
            confirmButtonText: 'OK'
        });
        // Reset to 1
        const quantityInput = document.querySelector(`input[data-product-id="${productId}"]`);
        if (quantityInput) {
            quantityInput.value = 1;
        }
        saveQuantityChange(productId, 1);
        return;
    }
    
    saveQuantityChange(productId, qty);
}

// Save quantity change to server
function saveQuantityChange(productId, quantity) {
    // Show loading state
    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
    if (row) {
        row.style.opacity = '0.6';
        row.style.pointerEvents = 'none';
    }
    
    $.ajax({
        url: '../functions/updateCart_action.php',
        method: 'POST',
        data: {
            update_cart: true,
            product_id: productId,
            quantity: quantity,
            increment: false
        },
        success: function(response) {
            try {
                const result = parseJsonResponse(response);
                if (!result) {
                    throw new Error('Invalid response format');
                }
                if (result.success) {
                    // Update subtotal and total
                    updateCartTotals(productId);
                    // Reload cart to get fresh data
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: result.message || 'Failed to update cart quantity.',
                        confirmButtonText: 'OK'
                    });
                    // Reload to reset
                    location.reload();
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the cart.',
                    confirmButtonText: 'OK'
                });
                location.reload();
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Failed to connect to server. Please try again.',
                confirmButtonText: 'OK'
            });
            location.reload();
        },
        complete: function() {
            // Restore row state
            if (row) {
                row.style.opacity = '1';
                row.style.pointerEvents = 'auto';
            }
        }
    });
}

// Remove item from cart
function removeFromCart(productId) {
    Swal.fire({
        title: 'Remove Item?',
        text: 'Are you sure you want to remove this item from your cart?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Remove',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            const row = document.querySelector(`tr[data-product-id="${productId}"]`);
            if (row) {
                row.style.opacity = '0.6';
                row.style.pointerEvents = 'none';
            }
            
            $.ajax({
                url: '../functions/removeFromCart_action.php',
                method: 'POST',
                data: {
                    remove_from_cart: true,
                    product_id: productId
                },
                success: function(response) {
                    try {
                        const result = parseJsonResponse(response);
                        if (!result) {
                            throw new Error('Invalid response format');
                        }
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Removed',
                                text: 'Item removed from cart successfully.',
                                confirmButtonText: 'OK',
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Remove Failed',
                                text: result.message || 'Failed to remove item from cart.',
                                confirmButtonText: 'OK'
                            });
                            if (row) {
                                row.style.opacity = '1';
                                row.style.pointerEvents = 'auto';
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while removing the item.',
                            confirmButtonText: 'OK'
                        });
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Failed to connect to server. Please try again.',
                        confirmButtonText: 'OK'
                    });
                    if (row) {
                        row.style.opacity = '1';
                        row.style.pointerEvents = 'auto';
                    }
                }
            });
        }
    });
}

// Empty entire cart
function emptyCart() {
    Swal.fire({
        title: 'Empty Cart?',
        text: 'Are you sure you want to remove all items from your cart? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Empty Cart',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Emptying Cart...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '../functions/emptyCart_action.php',
                method: 'POST',
                data: {
                    empty_cart: true
                },
                success: function(response) {
                    try {
                        const result = parseJsonResponse(response);
                        if (!result) {
                            throw new Error('Invalid response format');
                        }
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cart Emptied',
                                text: 'All items have been removed from your cart.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Empty Failed',
                                text: result.message || 'Failed to empty cart.',
                                confirmButtonText: 'OK'
                            });
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while emptying the cart.',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Failed to connect to server. Please try again.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

// Update cart totals (subtotal and total)
function updateCartTotals(productId) {
    // Get the row for this product
    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
    if (!row) return;
    
    // Get price and quantity
    const priceText = row.querySelector('.price').textContent;
    const price = parseFloat(priceText.replace('GH₵', '').replace(/,/g, ''));
    const quantity = parseInt(row.querySelector('.quantity-input').value) || 1;
    
    // Calculate subtotal
    const subtotal = price * quantity;
    
    // Update subtotal display
    const subtotalElement = row.querySelector(`span[data-subtotal="${productId}"]`);
    if (subtotalElement) {
        subtotalElement.textContent = 'GH₵' + subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    // Recalculate total
    recalculateTotal();
}

// Recalculate total from all subtotals
function recalculateTotal() {
    let total = 0;
    
    // Get all subtotal elements
    const subtotalElements = document.querySelectorAll('span[data-subtotal]');
    subtotalElements.forEach(element => {
        const subtotalText = element.textContent;
        const subtotal = parseFloat(subtotalText.replace('GH₵', '').replace(/,/g, ''));
        total += subtotal;
    });
    
    // Update total displays
    const cartSubtotal = document.getElementById('cartSubtotal');
    const cartTotal = document.getElementById('cartTotal');
    
    if (cartSubtotal) {
        cartSubtotal.textContent = 'GH₵' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    if (cartTotal) {
        cartTotal.textContent = 'GH₵' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
}

// Initialize cart on page load
$(document).ready(function() {
    // Add event listeners for quantity input changes
    $('.quantity-input').on('blur', function() {
        const productId = $(this).data('product-id');
        const quantity = parseInt($(this).val()) || 1;
        if (quantity < 1) {
            $(this).val(1);
            updateQuantityDirect(productId, 1);
        } else {
            updateQuantityDirect(productId, quantity);
        }
    });
    
    // Prevent form submission on Enter key in quantity inputs
    $('.quantity-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $(this).blur();
        }
    });
});

