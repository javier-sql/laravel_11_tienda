window.openModalWithData = function(id, name, description, price, stock, image, categoryId, brandId) {
    const modal = document.getElementById('editModal');
    modal.style.display = 'block';

    document.getElementById('modal-id').value = id;
    document.getElementById('modal-name').value = name;
    document.getElementById('modal-description').value = description;
    document.getElementById('modal-price').value = price;
    document.getElementById('modal-stock').value = stock;
    document.getElementById('modal-image').value = image;
    document.getElementById('modal-category').value = categoryId;
    document.getElementById('modal-brand').value = brandId;

    const form = document.getElementById('editForm');
    form.action = `/administrador/update/${id}`;


};

window.closeModal = function() {
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('deleteProduct').style.display = 'none';
}

window.addEventListener('click', function(event) {
    const modal = document.getElementById('editModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});

window.categoryModal = function(){
    document.getElementById('category-modal').style.display = 'block';
}

window.closeModalCategory = function() {
    document.getElementById('category-modal').style.display = 'none';
}

window.marcaModal = function(){
    document.getElementById('marca-modal').style.display = 'block';
}

window.closeModalMarca = function() {
    document.getElementById('marca-modal').style.display = 'none';
}

window.categoryEditModal = function(){
    document.getElementById('category-edit-modal').style.display = 'block';
}

window.closeModalCategoryEdit = function() {
    document.getElementById('category-edit-modal').style.display = 'none';
}

window.loadCategoryName = function() {
    const select = document.getElementById('category_id_edit');
    const selectedOption = select.options[select.selectedIndex];
    const id = selectedOption.value;
    const name = selectedOption.getAttribute('data-name');

    if (id) {
        document.getElementById('edit_category_name').value = name;
        document.getElementById('editCategoryForm').action = '/categories/' + id;
    } else {
        document.getElementById('edit_category_name').value = '';
        document.getElementById('editCategoryForm').action = '';
    }
}

window.modalDeleteCategory = function(){
    document.getElementById('deleteCategoryModal').style.display = 'block';
}

window.closeModalDeleteCategory = function() {
    document.getElementById('deleteCategoryModal').style.display = 'none';
    document.getElementById('categoryWarning').style.display = 'none';
}

window.DeleteCategory = function() {
    const select = document.getElementById('category_id_edit');
    const selectedOption = select.options[select.selectedIndex];
    const id = selectedOption.value;
    const warning = document.getElementById('categoryWarning');

    if (id != null && id !== '') {
        document.getElementById('deleteCategoryForm').action = '/categories_destroy/' + id;
        warning.style.display = 'none';
    }else{
        event.preventDefault();
        warning.style.display = 'block';
    }
}

window.openModalDeleteProduct = function(id){
    document.getElementById('deleteProduct').style.display = 'block';

    let deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/administrador/delete/${id}`;
}

window.openModalBrandEdit = function() {
    document.getElementById('brand-edit-modal').style.display = 'block';
}

window.closeModalBrandEdit = function() {
    document.getElementById('brand-edit-modal').style.display = 'none';
}

window.loadBrandName = function() {
    const select = document.getElementById('brand_id_edit');
    const selectedOption = select.options[select.selectedIndex];
    const id = selectedOption.value;
    const name = selectedOption.getAttribute('data-name');

    if (id) {
        document.getElementById('edit_brand_name').value = name;
        document.getElementById('editBrandForm').action = '/brands/' + id;
    } else {
        document.getElementById('edit_brand_name').value = '';
        document.getElementById('editBrandForm').action = '';
    }
}

window.DeleteBrand = function() {
    const select = document.getElementById('brand_id_edit');
    const selectedOption = select.options[select.selectedIndex];
    const id = selectedOption.value;
    const warning = document.getElementById('brandWarning');

    if (id != null && id !== '') {
        document.getElementById('deleteBrandForm').action = '/brands_destroy/' + id;
        warning.style.display = 'none';
    } else {
        event.preventDefault(); // Evita que se envíe el formulario
        warning.style.display = 'block'; // Muestra advertencia
    }
}

window.openModalDeleteBrand = function() {
    document.getElementById('deleteBrandModal').style.display = 'block';
}

window.closeModalDeleteBrand = function() {
    document.getElementById('deleteBrandModal').style.display = 'none';
    document.getElementById('brandWarning').style.display = 'none';
}

///AJAX PRUEBA

document.addEventListener('DOMContentLoaded', function () {

    function updateCartUI(id, quantity, totalQuantity, price) {
        // Actualiza cantidad
        document.getElementById('quantity-' + id).innerText = quantity;

        // Actualiza subtotal
        const subtotalEl = document.getElementById('subtotal-' + id);
        subtotalEl.innerText = '$' + (price * quantity);

        // Actualiza total carrito en menú
        const totalSpan = document.getElementById('cart-total-quantity');
        if (totalQuantity > 0) {
            totalSpan.innerText = `(${totalQuantity})`;
        } else {
            totalSpan.innerText = '';
        }
        updateTotalCart();
    }

    function updateTotalCart() {
        let total = 0;
        document.querySelectorAll('[id^="subtotal-"]').forEach(el => {
            const amount = parseFloat(el.innerText.replace('$', ''));
            total += amount;
        });

        const totalEl = document.getElementById('total-cart');
        if (totalEl) {
            totalEl.innerText = 'Total: $' + total;
        }
    }

    document.querySelectorAll('.decrease-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.dataset.id;
            const url = this.dataset.url;
            const price = parseFloat(document.querySelector(`#product-row-${id} td:nth-child(2)`).innerText.replace('$',''));

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartUI(id, data.quantity, data.totalQuantity, price);
                }
            });
        });
    });

    document.querySelectorAll('.increase-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.dataset.id;
            const url = this.dataset.url;
            const price = parseFloat(document.querySelector(`#product-row-${id} td:nth-child(2)`).innerText.replace('$',''));

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartUI(id, data.quantity, data.totalQuantity, price);
                }
            });
        });
    });

});
