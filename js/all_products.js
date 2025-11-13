// Product Modal Functions
let currentProductId = null;

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

function addToCart(productId) {
    // Show loading state
    Swal.fire({
        title: 'Adding to Cart...',
        text: 'Please wait',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../functions/addToCart_action.php',
        method: 'POST',
        data: {
            add_to_cart: true,
            product_id: productId,
            quantity: 1
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
                        title: 'Added to Cart!',
                        text: 'Product added to cart successfully.',
                        showCancelButton: true,
                        confirmButtonText: 'View Cart',
                        cancelButtonText: 'Continue Shopping',
                        confirmButtonColor: '#667eea'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../views/cart.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Add Failed',
                        text: result.message || 'Failed to add product to cart.',
                        confirmButtonText: 'OK'
                    });
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while adding to cart.',
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

function openProductModal(event, product, categoryName, brandName) {
    // Prevent default link behavior if event is provided
    if (event) {
        event.preventDefault();
    }
    
    // Validate product data
    if (!product || !product.product_id) {
        console.error('Invalid product data provided to openProductModal');
        return;
    }
    
    // Get modal element
    const modal = document.getElementById('productModal');
    if (!modal) {
        console.error('Product modal element not found');
        return;
    }
    
    currentProductId = product.product_id;
    
    // Helper function to safely set text content
    function setTextContent(id, text) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = text || '';
        } else {
            console.warn('Element with id "' + id + '" not found');
        }
    }
    
    // Populate modal with product data
    setTextContent('modalProductTitle', product.product_title || 'Product Details');
    setTextContent('modalProductTitleText', product.product_title || 'Untitled Product');
    setTextContent('modalProductId', 'Product ID: ' + product.product_id);
    setTextContent('modalProductIdValue', product.product_id);
    setTextContent('modalProductPrice', 'GH₵' + parseFloat(product.product_price || 0).toFixed(2));
    setTextContent('modalProductDesc', product.product_desc || 'No description available.');
    setTextContent('modalProductCategory', categoryName || 'N/A');
    setTextContent('modalProductBrand', brandName || 'N/A');
    
    // Set product image
    const imageElement = document.getElementById('modalProductImage');
    if (imageElement) {
        if (product.product_image && product.product_image.trim() !== '') {
            imageElement.src = '../' + product.product_image;
        } else {
            imageElement.src = 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Crect fill=\'%23f8f9fa\' width=\'400\' height=\'400\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'16\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ENo Image Available%3C/text%3E%3C/svg%3E';
        }
    }
    
    // Populate keywords
    const keywordsContainer = document.getElementById('modalProductKeywords');
    if (keywordsContainer) {
        keywordsContainer.innerHTML = '';
        if (product.product_keywords && product.product_keywords.trim() !== '') {
            const keywords = product.product_keywords.split(',').map(k => k.trim()).filter(k => k);
            if (keywords.length > 0) {
                keywords.forEach(keyword => {
                    const tag = document.createElement('span');
                    tag.className = 'modal-keyword-tag';
                    tag.textContent = keyword;
                    keywordsContainer.appendChild(tag);
                });
            } else {
                const noKeywords = document.createElement('span');
                noKeywords.style.color = '#999';
                noKeywords.textContent = 'No keywords available';
                keywordsContainer.appendChild(noKeywords);
            }
        } else {
            const noKeywords = document.createElement('span');
            noKeywords.style.color = '#999';
            noKeywords.textContent = 'No keywords available';
            keywordsContainer.appendChild(noKeywords);
        }
    }
    
    // Show modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeProductModal() {
    const modal = document.getElementById('productModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
        currentProductId = null;
    }
}

function addToCartFromModal() {
    if (currentProductId) {
        addToCart(currentProductId);
        closeProductModal();
    } else {
        console.warn('No product ID available to add to cart');
    }
}

// Initialize modal event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('productModal');
    
    if (!modal) {
        console.warn('Product modal element not found. Modal functionality may not work.');
        return;
    }
    
    // Add event listeners to all product modal trigger buttons
    // Using event delegation to handle dynamically loaded products (pagination)
    document.addEventListener('click', function(e) {
        // Check if clicked element or its parent has the product-modal-trigger class
        const triggerButton = e.target.closest('.product-modal-trigger');
        
        if (triggerButton) {
            e.preventDefault();
            e.stopPropagation();
            
            try {
                // Get product data from data attributes (getAttribute always returns string or null)
                const productData = triggerButton.getAttribute('data-product');
                const categoryName = triggerButton.getAttribute('data-category');
                const brandName = triggerButton.getAttribute('data-brand');
                
                if (!productData) {
                    console.error('Product data not found in data-product attribute');
                    return;
                }
                
                // Parse JSON data from attributes
                const product = JSON.parse(productData);
                
                // Parse category and brand (they're JSON-encoded strings)
                let category = 'N/A';
                let brand = 'N/A';
                
                if (categoryName) {
                    try {
                        category = JSON.parse(categoryName);
                    } catch (e) {
                        // If parsing fails, use the raw value
                        category = categoryName;
                    }
                }
                
                if (brandName) {
                    try {
                        brand = JSON.parse(brandName);
                    } catch (e) {
                        // If parsing fails, use the raw value
                        brand = brandName;
                    }
                }
                
                // Open modal with parsed data
                openProductModal(e, product, category, brand);
            } catch (error) {
                console.error('Error parsing product data:', error);
                console.error('Product data string:', triggerButton.getAttribute('data-product'));
                console.error('Trigger button:', triggerButton);
            }
        }
    });
    
    // Close modal when clicking outside (on overlay)
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeProductModal();
        }
    });
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeProductModal();
        }
    });
});