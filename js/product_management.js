// Product Management JavaScript

// Modal Functions
function openProductModal() {
    document.getElementById('productModal').style.display = 'block';
    document.getElementById('product_title').focus();
    
    // Reset image preview
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('previewImg').src = '';
}

function closeProductModal() {
    // Reset image preview
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('previewImg').src = '';
    
    document.getElementById('productModal').style.display = 'none';
    document.getElementById('productForm').reset();
}

// Image preview functionality
document.addEventListener('DOMContentLoaded', function() {
    // Image preview for add product form
    const imageInput = document.getElementById('product_image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImg = document.getElementById('previewImg');
                    const imagePreview = document.getElementById('imagePreview');
                    if (previewImg && imagePreview) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Image preview for edit product form
    const editImageInput = document.getElementById('edit_product_image');
    if (editImageInput) {
        editImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const editPreviewImg = document.getElementById('editPreviewImg');
                    const editImagePreview = document.getElementById('editImagePreview');
                    if (editPreviewImg && editImagePreview) {
                        editPreviewImg.src = e.target.result;
                        editImagePreview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            } else {
                const editImagePreview = document.getElementById('editImagePreview');
                const editPreviewImg = document.getElementById('editPreviewImg');
                if (editImagePreview) editImagePreview.style.display = 'none';
                if (editPreviewImg) editPreviewImg.src = '';
            }
        });
    }
});

function openEditProductModal(productId) {
    // Fetch product details and populate form
    fetchProductDetails(productId);
}

function closeEditProductModal() {
    document.getElementById('editProductModal').style.display = 'none';
    document.getElementById('editProductForm').reset();
}

// Function to fetch product details
function fetchProductDetails(productId) {
    // Fetch product data from the server
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('get_product', '1');
    
    fetch('../functions/get_product_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.product) {
            populateEditProductModal(data.product);
            document.getElementById('editProductModal').style.display = 'block';
        } else {
            showAlert('Failed to fetch product details.', 'error');
        }
    })
    .catch(error => {
        console.error('Error fetching product:', error);
        showAlert('Error fetching product details.', 'error');
    });
}

// Fill the edit product form with current product details
function populateEditProductModal(product) {
    document.getElementById('edit_product_id').value = product.product_id;
    document.getElementById('edit_product_title').value = product.product_title;
    document.getElementById('edit_product_cat').value = product.product_cat;
    document.getElementById('edit_product_brand').value = product.product_brand;
    document.getElementById('edit_product_price').value = product.product_price;
    document.getElementById('edit_product_desc').value = product.product_desc;
    document.getElementById('edit_product_keywords').value = product.product_keywords;

    // Handle image
    const currentImageElement = document.getElementById('edit_current_product_image');
    const deleteImageBtn = document.getElementById('deleteImageBtn');
    const oldImageInput = document.getElementById('edit_product_image_old');
    
    if (product.product_image && currentImageElement) {
        currentImageElement.src = '../uploads/' + product.product_image;
        currentImageElement.style.display = 'block';
        if (deleteImageBtn) deleteImageBtn.style.display = 'block';
        if (oldImageInput) oldImageInput.value = product.product_image;
    } else {
        if (currentImageElement) currentImageElement.style.display = 'none';
        if (deleteImageBtn) deleteImageBtn.style.display = 'none';
        if (oldImageInput) oldImageInput.value = '';
    }
    
    // Reset file input and preview
    const fileInput = document.getElementById('edit_product_image');
    const imagePreview = document.getElementById('editImagePreview');
    const previewImg = document.getElementById('editPreviewImg');
    
    if (fileInput) fileInput.value = '';
    if (imagePreview) imagePreview.style.display = 'none';
    if (previewImg) previewImg.src = '';
}

// Delete current image in edit form
function deleteEditProductImage() {
    const currentImageElement = document.getElementById('edit_current_product_image');
    const deleteImageBtn = document.getElementById('deleteImageBtn');
    const oldImageInput = document.getElementById('edit_product_image_old');
    
    if (currentImageElement) currentImageElement.style.display = 'none';
    if (deleteImageBtn) deleteImageBtn.style.display = 'none';
    if (oldImageInput) oldImageInput.value = ''; // Indicate deletion on server/save
}

// Function to show alerts
function showAlert(message, type) {
    const alertContainer = document.getElementById('alert-container');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass}`;
    alertDiv.textContent = message;
    
    alertContainer.insertBefore(alertDiv, alertContainer.firstChild);
    
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 300);
    }, 5000);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const productModal = document.getElementById('productModal');
    const editProductModal = document.getElementById('editProductModal');
    
    if (event.target == productModal) {
        closeProductModal();
    }
    if (event.target == editProductModal) {
        closeEditProductModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeProductModal();
        closeEditProductModal();
    }
});

// Delete Product Functions
function confirmDeleteProduct(productId, productTitle) {
    Swal.fire({
        title: `Delete Product "${productTitle}"?`,
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            deleteProduct(productId);
        }
    });
}

function deleteProduct(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('delete_product', '1');
    
    fetch('../functions/delete_product_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while deleting the product.', 'error');
    });
}

// Add Product Form submission
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const productTitle = document.getElementById('product_title').value.trim();
    const productCat = document.getElementById('product_cat').value;
    const productBrand = document.getElementById('product_brand').value;
    const productPrice = document.getElementById('product_price').value;
    const productDesc = document.getElementById('product_desc').value.trim();
    const productKeywords = document.getElementById('product_keywords').value.trim();
    const productImage = document.getElementById('product_image').files[0];
    
    // Validation
    if (!productTitle || productTitle.length < 2) {
        showAlert('Product title must be at least 2 characters.', 'error');
        return;
    }
    if (!productCat || productCat === '') {
        showAlert('Please select a category.', 'error');
        return;
    }
    if (!productBrand || productBrand === '') {
        showAlert('Please select a brand.', 'error');
        return;
    }
    if (!productPrice || parseFloat(productPrice) <= 0) {
        showAlert('Please enter a valid price.', 'error');
        return;
    }
    if (!productDesc || productDesc.length < 5) {
        showAlert('Product description must be at least 5 characters.', 'error');
        return;
    }
    if (!productKeywords || productKeywords.length < 2) {
        showAlert('Please enter keywords for the product.', 'error');
        return;
    }
    if (!productImage) {
        showAlert('Please select an image file.', 'error');
        return;
    }
    
    // Validate file size
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (productImage.size > maxSize) {
        showAlert('Image size must be less than 5MB.', 'error');
        return;
    }
    
    const submitBtn = document.querySelector('button[name="add_product"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Uploading...';
    
    // Step 1: Upload image first
    const uploadFormData = new FormData();
    uploadFormData.append('product_image', productImage);
    uploadFormData.append('product_id', 0); // New product, no ID yet
    uploadFormData.append('cat_id', productCat);
    uploadFormData.append('brand_id', productBrand);
    
    fetch('../functions/upload_product_image_action.php', {
        method: 'POST',
        body: uploadFormData
    })
    .then(response => response.json())
    .then(uploadData => {
        if (!uploadData.success) {
            throw new Error(uploadData.message || 'Image upload failed');
        }
        
        // Step 2: Add product with image path
        const productFormData = new FormData();
        productFormData.append('product_cat', productCat);
        productFormData.append('product_brand', productBrand);
        productFormData.append('product_title', productTitle);
        productFormData.append('product_price', productPrice);
        productFormData.append('product_desc', productDesc);
        productFormData.append('product_keywords', productKeywords);
        productFormData.append('product_image', uploadData.image_path);
        productFormData.append('add_product', '1');
        
        return fetch('../functions/add_product_action.php', {
            method: 'POST',
            body: productFormData
        });
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeProductModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert(error.message || 'An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Edit Product Form submission
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const productId = document.getElementById('edit_product_id').value;
    const productTitle = document.getElementById('edit_product_title').value.trim();
    const productCat = document.getElementById('edit_product_cat').value;
    const productBrand = document.getElementById('edit_product_brand').value;
    const productPrice = document.getElementById('edit_product_price').value;
    const productDesc = document.getElementById('edit_product_desc').value.trim();
    const productKeywords = document.getElementById('edit_product_keywords').value.trim();
    const productImageFile = document.getElementById('edit_product_image').files[0];
    const oldImagePath = document.getElementById('edit_product_image_old').value;
    
    // Validation
    if (!productTitle || productTitle.length < 2) {
        showAlert('Product title must be at least 2 characters.', 'error');
        return;
    }
    if (!productCat || productCat === '') {
        showAlert('Please select a category.', 'error');
        return;
    }
    if (!productBrand || productBrand === '') {
        showAlert('Please select a brand.', 'error');
        return;
    }
    if (!productPrice || parseFloat(productPrice) <= 0) {
        showAlert('Please enter a valid price.', 'error');
        return;
    }
    if (!productDesc || productDesc.length < 5) {
        showAlert('Product description must be at least 5 characters.', 'error');
        return;
    }
    if (!productKeywords || productKeywords.length < 2) {
        showAlert('Please enter keywords for the product.', 'error');
        return;
    }
    
    const submitBtn = document.querySelector('button[name="update_product"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    // If no new image file is selected, use the old image path
    let imagePath = oldImagePath;
    
    if (productImageFile) {
        // Validate file size
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (productImageFile.size > maxSize) {
            showAlert('Image size must be less than 5MB.', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            return;
        }
        
        // Upload new image first
        const uploadFormData = new FormData();
        uploadFormData.append('product_image', productImageFile);
        uploadFormData.append('product_id', productId);
        uploadFormData.append('cat_id', productCat);
        uploadFormData.append('brand_id', productBrand);
        
        fetch('../functions/upload_product_image_action.php', {
            method: 'POST',
            body: uploadFormData
        })
        .then(response => response.json())
        .then(uploadData => {
            if (!uploadData.success) {
                throw new Error(uploadData.message || 'Image upload failed');
            }
            
            // Update product with new image path
            imagePath = uploadData.image_path;
            return updateProduct();
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert(error.message || 'An error occurred uploading the image.', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    } else {
        // No new image, just update the product
        updateProduct();
    }
    
    function updateProduct() {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('product_cat', productCat);
        formData.append('product_brand', productBrand);
        formData.append('product_title', productTitle);
        formData.append('product_price', productPrice);
        formData.append('product_desc', productDesc);
        formData.append('product_keywords', productKeywords);
        formData.append('product_image', imagePath);
        formData.append('update_product', '1');
        
        return fetch('../functions/update_product_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                closeEditProductModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred. Please try again.', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    }
});

// Add event listeners for edit and delete buttons
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-product-btn');
    const deleteButtons = document.querySelectorAll('.delete-product-btn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productTitle = this.getAttribute('data-product-title');
            openEditProductModal(productId);
        });
    });
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productTitle = this.closest('.product-card').querySelector('.product-title').textContent;
            confirmDeleteProduct(productId, productTitle);
        });
    });
});

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';
        setTimeout(function() {
            alert.remove();
        }, 300);
    });
}, 5000);



