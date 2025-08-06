window.PreviewModalImage = function () {
    const input = document.getElementById('modal-image-input');
    const preview = document.getElementById('preview-image');

    if (input && preview) {
        input.addEventListener('change', function (event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
};


window.openModalWithData = function(id, name, description, price, stock, image, categoryId, brandId) {
    const form = document.getElementById('editForm');
    form.action = `/administrador/update/${id}`;

    // Mostrar el modal
    const modal = document.getElementById('editModal');
    modal.style.display = 'block';

    // Llenar los campos
    document.getElementById('modal-id').value = id;
    document.getElementById('modal-name').value = name;
    document.getElementById('modal-description').value = description;
    document.getElementById('modal-price').value = price;
    document.getElementById('modal-stock').value = stock;
    document.getElementById('modal-category').value = categoryId;
    document.getElementById('modal-brand').value = brandId;

    // Mostrar imagen actual
    const preview = document.getElementById('preview-image');
    preview.src = `/storage/${image}`;
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

document.addEventListener('DOMContentLoaded', function () {

    function updateCartUI(id, quantity, totalQuantity, price) {
        // Actualiza cantidad
        document.getElementById('quantity-' + id).innerText = quantity;

        // Actualiza subtotal
        const subtotalEl = document.getElementById('subtotal-' + id);
        subtotalEl.innerText = '$' + (price * quantity);

        // Actualiza total carrito en menú
        const totalQuantityNav = document.querySelector('.cart-total-quantity');


        if (totalQuantity > 0) {
            totalQuantityNav.innerText = `(${totalQuantity})`;
        } else {
            totalSpan.innerText = '0';
        }
        console.log("subtotal",price)
        updateTotalCart();
    }

    function updateTotalCart() {
        let total = 0;
        document.querySelectorAll('[id^="subtotal-"]').forEach(el => {
            const amount = parseFloat(el.innerText.replace('$', ''));
            total += amount;
        });

        const totalEl = document.getElementById('total-cart');
        const totalNav = document.querySelector('.cart-total-price');

        if (totalEl) {
            totalEl.innerText = 'Total: $' + total;
            totalNav.innerText = '$' + total.toLocaleString('es-CL');
        }
    }

    document.querySelectorAll('.decrease-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.dataset.id;
            const url = this.dataset.url;
            const priceText = document.querySelector(`#product-row-${id} td:nth-child(2)`).innerText.replace('$','').replace(/\./g, '');
            const price = parseFloat(priceText);
                        
            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
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
            const priceText = document.querySelector(`#product-row-${id} td:nth-child(2)`).innerText.replace('$','').replace(/\./g, '');
            const price = parseFloat(priceText);
            
            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
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

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector("form");
        if (form) {
            form.addEventListener("submit", function () {
                const submitBtn = this.querySelector("button[type='submit']");
                if (submitBtn) {
                    submitBtn.disabled = true;
                }
            });
        }
    });

    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;

            fetch(`/add-to-cart/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success){
                    // alert(data.success);
                    // alert(data.cart_count)
                    // Actualiza el contador del carrito
                    const totalQuantityElement = document.querySelector('.cart-total-quantity');
                    if (totalQuantityElement) {
                        totalQuantityElement.innerText = `(${data.cart_count})`;
                    }
                    // alert(data.total_price)

                    const totalPriceElement = document.querySelector('.cart-total-price');
                    if (totalPriceElement) {
                        totalPriceElement.innerText = `$${data.total_price.toLocaleString('es-CL')}`;
                    }

                } else if(data.error){
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al agregar al carrito');
            });
        });
    });

    const openBtn = document.getElementById('openMenu');
    const closeBtn = document.getElementById('closeMenu');
    const menu = document.getElementById('menu-hamburger');

    openBtn.addEventListener('click', () => {
    menu.style.display = 'block';
    menu.offsetHeight;  // fuerza repaint
    menu.classList.add('active');
    });

    closeBtn.addEventListener('click', () => {
    menu.classList.remove('active');
    });

    menu.addEventListener('transitionend', () => {
    if (!menu.classList.contains('active')) {
        menu.style.display = 'none';
    }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 917) {
            menu.classList.remove('active');
            menu.style.display = 'none'; // ← Esto es lo que faltaba
        }
    });

});

