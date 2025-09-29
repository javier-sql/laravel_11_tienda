document.addEventListener('DOMContentLoaded', function () {

    // ==== MODALES ====
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

        const modal = document.getElementById('editModal');
        modal.style.display = 'block';

        document.getElementById('modal-id').value = id;
        document.getElementById('modal-name').value = name;
        document.getElementById('modal-description').value = description;
        document.getElementById('modal-price').value = price;
        document.getElementById('modal-stock').value = stock;
        document.getElementById('modal-category').value = categoryId;
        document.getElementById('modal-brand').value = brandId;

        const preview = document.getElementById('preview-image');
        preview.src = `/storage/${image}`;
    };

    window.closeModal = function() {
        document.getElementById('editModal').style.display = 'none';
        document.getElementById('deleteProduct').style.display = 'none';
    };

    window.addEventListener('click', function(event) {
        const modal = document.getElementById('editModal');
        if (event.target === modal) modal.style.display = 'none';
    });

    window.categoryModal = function(){ document.getElementById('category-modal').style.display = 'block'; }
    window.closeModalCategory = function(){ document.getElementById('category-modal').style.display = 'none'; }

    window.marcaModal = function(){ document.getElementById('marca-modal').style.display = 'block'; }
    window.closeModalMarca = function(){ document.getElementById('marca-modal').style.display = 'none'; }

    window.categoryEditModal = function(){ document.getElementById('category-edit-modal').style.display = 'block'; }
    window.closeModalCategoryEdit = function(){ document.getElementById('category-edit-modal').style.display = 'none'; }

    window.loadCategoryName = function() {
        const select = document.getElementById('category_id_edit');
        const selectedOption = select.options[select.selectedIndex];
        const id = selectedOption.value;
        const name = selectedOption.getAttribute('data-name');

        document.getElementById('edit_category_name').value = id ? name : '';
        document.getElementById('editCategoryForm').action = id ? '/categories/' + id : '';
    }

    window.modalDeleteCategory = function(){ document.getElementById('deleteCategoryModal').style.display = 'block'; }
    window.closeModalDeleteCategory = function(){
        document.getElementById('deleteCategoryModal').style.display = 'none';
        document.getElementById('categoryWarning').style.display = 'none';
    }

    window.DeleteCategory = function() {
        const select = document.getElementById('category_id_edit');
        const id = select.options[select.selectedIndex].value;
        const warning = document.getElementById('categoryWarning');

        if(id){
            document.getElementById('deleteCategoryForm').action = '/categories_destroy/' + id;
            warning.style.display = 'none';
        } else {
            event.preventDefault();
            warning.style.display = 'block';
        }
    }

    window.openModalDeleteProduct = function(id){
        document.getElementById('deleteProduct').style.display = 'block';
        document.getElementById('deleteForm').action = `/administrador/delete/${id}`;
    }

    window.openModalBrandEdit = function(){ document.getElementById('brand-edit-modal').style.display = 'block'; }
    window.closeModalBrandEdit = function(){ document.getElementById('brand-edit-modal').style.display = 'none'; }

    window.loadBrandName = function() {
        const select = document.getElementById('brand_id_edit');
        const selectedOption = select.options[select.selectedIndex];
        const id = selectedOption.value;
        const name = selectedOption.getAttribute('data-name');

        document.getElementById('edit_brand_name').value = id ? name : '';
        document.getElementById('editBrandForm').action = id ? '/brands/' + id : '';
    }

    window.DeleteBrand = function() {
        const select = document.getElementById('brand_id_edit');
        const id = select.options[select.selectedIndex].value;
        const warning = document.getElementById('brandWarning');

        if(id){
            document.getElementById('deleteBrandForm').action = '/brands_destroy/' + id;
            warning.style.display = 'none';
        } else {
            event.preventDefault();
            warning.style.display = 'block';
        }
    }

    window.openModalDeleteBrand = function(){ document.getElementById('deleteBrandModal').style.display = 'block'; }
    window.closeModalDeleteBrand = function(){
        document.getElementById('deleteBrandModal').style.display = 'none';
        document.getElementById('brandWarning').style.display = 'none';
    }

    // ==== CARRITO ====
    function updateCartUI(id, quantity, totalQuantity, price) {
        document.getElementById('quantity-' + id).innerText = quantity;
        const subtotalEl = document.getElementById('subtotal-' + id);
        subtotalEl.innerText = '$' + (price * quantity);

        document.querySelectorAll('.cart-total-quantity').forEach(span => {
            span.innerText = totalQuantity > 0 ? `(${totalQuantity})` : '0';
        });

        updateTotalCart();
    }

    function updateTotalCart() {
        let total = 0;
        document.querySelectorAll('[id^="subtotal-"]').forEach(el => {
            total += parseFloat(el.innerText.replace('$',''));
        });

        const totalEl = document.getElementById('total-cart');
        const totalNav = document.querySelectorAll('.cart-total-price');

        if(totalEl) totalEl.innerText = 'Total: $' + total;
        totalNav.forEach(p => p.innerText = '$' + total.toLocaleString('es-CL'));
    }

    document.querySelectorAll('.decrease-btn, .increase-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.dataset.id;
            const url = this.dataset.url;
            const priceText = document.querySelector(`#product-row-${id} td:nth-child(2)`).innerText.replace('$','').replace(/\./g,'');
            const price = parseFloat(priceText);

            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    updateCartUI(id, data.quantity, data.totalQuantity, price);
                }
            });
        });
    });

    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function(){
            const productId = this.dataset.id;
            fetch(`/add-to-cart/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    document.querySelectorAll('.cart-total-quantity').forEach(span => {
                        span.innerText = data.cart_count > 0 ? `(${data.cart_count})` : '0';
                    });
                    document.querySelectorAll('.cart-total-price').forEach(p => {
                        p.innerText = '$' + data.total_price.toLocaleString('es-CL');
                    });
                } else if(data.error){
                    alert(data.error);
                }
            })
            .catch(err => { console.error(err); alert('Error al agregar al carrito'); });
        });
    });

    // ==== MENÚ RESPONSIVE ====
    const openBtn = document.getElementById('openMenu');
    const closeBtn = document.getElementById('closeMenu');
    const menu = document.getElementById('menu-hamburger');

    if (!openBtn || !closeBtn || !menu) return; // no hay navbar, no hacer nada

    openBtn.addEventListener('click', () => {
        menu.style.display = 'block';
        menu.offsetHeight; // fuerza reflow
        menu.classList.add('active');
    });

    closeBtn.addEventListener('click', () => {
        menu.classList.remove('active');
    });

    menu.addEventListener('transitionend', () => {
        if (!menu.classList.contains('active')) menu.style.display = 'none';
    });
    // ==== ====

    const form = document.getElementById('shipping-form');
    const confirmBtn = document.getElementById('confirm-address');
    const shippingPriceEl = document.getElementById('shipping-price');

    function checkForm() {
        const commune = document.getElementById('commune').value;
        const street = document.getElementById('street').value.trim();
        const number = document.getElementById('number').value.trim();
        const phone = document.getElementById('phone').value.trim();

        if(commune && street && number && phone) {
            // Habilitar botón de confirmar
            confirmBtn.disabled = false;

            // Mostrar costo de envío
            const selectedOption = document.querySelector('#commune option:checked');
            const price = selectedOption.dataset.price || 0;
            shippingPriceEl.innerText = '$' + parseInt(price).toLocaleString('es-CL');
        } else {
            // Deshabilitar botón si no está todo lleno
            confirmBtn.disabled = true;
            shippingPriceEl.innerText = '$0';
        }
    }

    if (form) {
        form.addEventListener('input', checkForm);
        form.addEventListener('change', checkForm);
    }

const confirmAddressBtn = document.getElementById('confirm-address');

if (confirmAddressBtn) {
    confirmAddressBtn.addEventListener('click', function() {
        const commune_id = document.getElementById('commune').value;
        const street = document.getElementById('street').value.trim();
        const number = document.getElementById('number').value.trim();
        const propertyType = document.getElementById('property-type')?.value; 
        const propertyNumber = document.getElementById('property-number')?.value.trim();
        const phone = document.getElementById('phone').value.trim();
        const shipping = parseInt(document.querySelector('#commune option:checked').dataset.price);

        fetch('/checkout/save-address', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                commune_id, 
                street, 
                number, 
                property_type: propertyType,      
                property_number: propertyNumber,  
                phone, 
                shipping 
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const paymentForm = document.getElementById('pago');
                if(paymentForm) paymentForm.style.display = 'block';

                const shippingEl = document.getElementById('shipping-total');
                if(shippingEl) shippingEl.innerText = shipping.toLocaleString('es-CL');

                const subtotalEl = document.getElementById('subtotal-products');
                const totalEl = document.getElementById('total');
                if(subtotalEl && totalEl) {
                    const subtotal = parseInt(subtotalEl.dataset.value);
                    totalEl.innerText = (subtotal + shipping).toLocaleString('es-CL');
                }
            }
        })
        .catch(err => console.error('Error guardando dirección:', err));
    });
}


const propertyTypeSelect = document.getElementById('property-type');
    const numberLabel = document.getElementById('property-number-label');
    const numberGroup = document.getElementById('property-number-group');
    const numberInput = document.getElementById('property-number');

    if (propertyTypeSelect && numberLabel && numberGroup && numberInput) {
        propertyTypeSelect.addEventListener('change', function() {
            const type = this.value;

            if(type === 'dpto') {
                numberLabel.innerText = 'Número de Departamento';
                numberGroup.style.display = 'block';
                numberInput.required = true;
            } else if(type === 'oficina') {
                numberLabel.innerText = 'Número de Oficina';
                numberGroup.style.display = 'block';
                numberInput.required = true;
            } else if(type === 'condominio') {
                numberLabel.innerText = 'Número de Condominio';
                numberGroup.style.display = 'block';
                numberInput.required = true;
            } else {
                numberGroup.style.display = 'none';
                numberInput.value = '';
                numberInput.required = false;
            }
        });
    }



    /* Carrusel */
const carouselInner = document.querySelector('.carousel-inner');
const items = document.querySelectorAll('.carousel-item');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let index = 1;
let size = items[0].clientWidth;

// Clonar primera y última
const firstClone = items[0].cloneNode(true);
const lastClone = items[items.length - 1].cloneNode(true);
firstClone.id = "first-clone";
lastClone.id = "last-clone";

carouselInner.appendChild(firstClone);
carouselInner.insertBefore(lastClone, items[0]);

const allItems = document.querySelectorAll('.carousel-item');
carouselInner.style.transform = `translateX(${-size * index}px)`;

// Función mover slide
function moveToSlide() {
    carouselInner.style.transition = "transform 0.5s ease-in-out";
    carouselInner.style.transform = `translateX(${-size * index}px)`;
}

// Botones
nextBtn.addEventListener('click', () => {
    if (index >= allItems.length - 1) return;
    index++;
    moveToSlide();
});
prevBtn.addEventListener('click', () => {
    if (index <= 0) return;
    index--;
    moveToSlide();
});

// Resetear clones
carouselInner.addEventListener('transitionend', () => {
    const currentItem = allItems[index];
    if (currentItem.id === "first-clone") {
        carouselInner.style.transition = "none";
        index = 1;
        carouselInner.style.transform = `translateX(${-size * index}px)`;
    }
    if (currentItem.id === "last-clone") {
        carouselInner.style.transition = "none";
        index = allItems.length - 2;
        carouselInner.style.transform = `translateX(${-size * index}px)`;
    }
});

// 🔹 Autoplay con pausa al hover
let autoplay = setInterval(() => {
    if (index >= allItems.length - 1) return;
    index++;
    moveToSlide();
}, 5000);

carouselInner.addEventListener('mouseenter', () => clearInterval(autoplay));
carouselInner.addEventListener('mouseleave', () => {
    autoplay = setInterval(() => {
        if (index >= allItems.length - 1) return;
        index++;
        moveToSlide();
    }, 5000);
});

// 🔹 Responsive al resize
let resizeTimeout;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        size = items[0].clientWidth;
        carouselInner.style.transition = "none";
        carouselInner.style.transform = `translateX(${-size * index}px)`;
    }, 100);
});



});
