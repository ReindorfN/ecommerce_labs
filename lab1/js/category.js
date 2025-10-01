function openModal() {
    document.getElementById('categoryModal').style.display = 'block';
    document.getElementById('category_name').focus();
}

function closeModal() {
    document.getElementById('categoryModal').style.display = 'none';
    document.getElementById('categoryForm').reset();
}

function openEditModal(catId, catName) {
    document.getElementById('editCategoryModal').style.display = 'block';
    document.getElementById('edit_cat_id').value = catId;
    document.getElementById('edit_category_name').value = catName;
    document.getElementById('edit_category_name').focus();
}

function closeEditModal() {
    document.getElementById('editCategoryModal').style.display = 'none';
    document.getElementById('editCategoryForm').reset();
}

// Function to show alerts
function showAlert(message, type) {
    const alertContainer = document.getElementById('alert-container');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass}`;
    alertDiv.textContent = message;
    
    // Insert at the top of the container
    alertContainer.insertBefore(alertDiv, alertContainer.firstChild);
    
    // Auto-hide after 5 seconds
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

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('categoryModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
        closeEditModal();
    }
});

// Function to confirm category deletion
function confirmDeleteCategory(catId, catName) {
    Swal.fire({
        title: `Delete Category "${catName}"?`,
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            deleteCategory(catId);
        }
    });
}

// Function to delete a category
function deleteCategory(catId) {
    const formData = new FormData();
    formData.append('cat_id', catId);
    formData.append('delete_category', '1');
    
    fetch('../functions/delete_category_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            refreshCategories();
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while deleting the category.', 'error');
    });
}

// Form validation and AJAX submission
document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const categoryName = document.getElementById('category_name').value.trim().toLowerCase();
    
    // field validations
    if (categoryName.length === 0) {
        showAlert('Please enter a category name.', 'error');
        return;
    }
    
    if (categoryName.length < 2) {
        showAlert('Category name must be at least 2 characters long.', 'error');
        return;
    }
    
    if (categoryName.length > 100) {
        showAlert('Category name must be less than 100 characters.', 'error');
        return;
    }
    
    // Disable submit button to prevent double submission
    const submitBtn = document.querySelector('button[name="add_category"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adding...';
    
    // Prepare form data
    const formData = new FormData();
    formData.append('category_name', categoryName);
    formData.append('add_category', '1');
    
    // AJAX request
    fetch('../functions/add_category_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeModal();
            // Refresh categories without page reload
            refreshCategories();
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Edit form validation and AJAX submission
document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const categoryName = document.getElementById('edit_category_name').value.trim();
    const catId = document.getElementById('edit_cat_id').value;
    
    // Client-side validation
    if (categoryName.length === 0) {
        showAlert('Please enter a category name.', 'error');
        return;
    }
    
    if (categoryName.length < 2) {
        showAlert('Category name must be at least 2 characters long.', 'error');
        return;
    }
    
    if (categoryName.length > 100) {
        showAlert('Category name must be less than 100 characters.', 'error');
        return;
    }
    
    // Disable submit button to prevent double submission
    const submitBtn = document.querySelector('button[name="update_category"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    // Prepare form data
    const formData = new FormData();
    formData.append('cat_id', catId);
    formData.append('category_name', categoryName);
    formData.append('update_category', '1');
    
    // Send AJAX request
    fetch('../functions/update_category_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeEditModal();
            refreshCategories();
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Function to refresh categories without page reload
function refreshCategories() {
    fetch('../functions/fetch_category_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: ''
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCategoriesDisplay(data.data);
        } else {
            console.error('Failed to fetch categories:', data.message);
        }
    })
    .catch(error => {
        console.error('Error fetching categories:', error);
    });
}

// Function to update the categories display
function updateCategoriesDisplay(categories) {
    const categoriesGrid = document.querySelector('.categories-grid');
    const emptyState = document.querySelector('.empty-state');
    
    if (!categoriesGrid) return;
    
    // Clear existing categories
    categoriesGrid.innerHTML = '';
    
    if (categories.length === 0) {
        // Show empty state
        if (emptyState) {
            emptyState.style.display = 'block';
        }
    } else {
        // Hide empty state
        if (emptyState) {
            emptyState.style.display = 'none';
        }
        
        // Add category cards
        categories.forEach(category => {
            const categoryCard = document.createElement('div');
            categoryCard.className = 'category-card';
            categoryCard.innerHTML = `
                <h3 class="category-name">${escapeHtml(category.cat_name)}</h3>
                <p class="category-id">ID: ${category.cat_id}</p>
                <div class="category-actions">
                    <button class="edit-category-btn" title="Edit" data-cat-id="${category.cat_id}" data-cat-name="${escapeHtml(category.cat_name)}">
                        <span aria-label="Edit">&#9998;</span>
                    </button>
                    <button class="delete-category-btn" title="Delete" data-cat-id="${category.cat_id}">
                        <span aria-label="Delete">&#128465;</span>
                    </button>
                </div>
            `;
            categoriesGrid.appendChild(categoryCard);

            // Add event listeners for edit and delete
            const editBtn = categoryCard.querySelector('.edit-category-btn');
            const deleteBtn = categoryCard.querySelector('.delete-category-btn');

            editBtn.addEventListener('click', function() {
                openEditModal(category.cat_id, category.cat_name);
            });

            deleteBtn.addEventListener('click', function() {
                confirmDeleteCategory(category.cat_id, category.cat_name);
            });
        });
    }
}

// preventing XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Add event listeners to existing category cards on page load
document.addEventListener('DOMContentLoaded', function() {
    addEventListenersToCategoryCards();
});

// Function to add event listeners to category cards
function addEventListenersToCategoryCards() {
    const editButtons = document.querySelectorAll('.edit-category-btn');
    const deleteButtons = document.querySelectorAll('.delete-category-btn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const catId = this.getAttribute('data-cat-id');
            const catName = this.getAttribute('data-cat-name');
            openEditModal(catId, catName);
        });
    });
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const catId = this.getAttribute('data-cat-id');
            const catName = this.closest('.category-card').querySelector('.category-name').textContent;
            confirmDeleteCategory(catId, catName);
        });
    });
}

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