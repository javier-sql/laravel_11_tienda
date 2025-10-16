document.addEventListener('DOMContentLoaded', function () {

    // ==== ADMIN ====
    if (document.body.id === 'admin-page') {

        // ==== MODAL IMAGEN ====
        if (document.getElementById('modal-image-input')) {
            window.PreviewModalImage = function () {
                const input = document.getElementById('modal-image-input');
                const preview = document.getElementById('preview-image');

                if (input && preview) {
                    input.addEventListener('change', function (event) {
                        try {
                            const files = event.target?.files;
                            if (files && files.length > 0) {
                                const file = files[0];
                                const reader = new FileReader();
                                reader.onload = function (e) {
                                    if (preview) preview.src = e.target?.result || '';
                                };
                                reader.readAsDataURL(file);
                            }
                        } catch (err) {
                            console.error('Error al previsualizar imagen:', err);
                        }
                    });
                }
            };
            PreviewModalImage();
        }

        // ==== FUNCIONES MODALES ====
        window.openModalWithData = function(id, name, description, price, stock, image, categoryId, brandId) {
            const form = document.getElementById('editForm');
            if(form) form.action = `/administrador/update/${id}`;
            const modal = document.getElementById('editModal');
            if(modal) modal.style.display = 'block';
            const preview = document.getElementById('preview-image');
            if(preview) preview.src = `/storage/${image}`;

            document.getElementById('modal-id').value = id;
            document.getElementById('modal-name').value = name;
            document.getElementById('modal-description').value = description;
            document.getElementById('modal-price').value = price;
            document.getElementById('modal-stock').value = stock;
            document.getElementById('modal-category').value = categoryId;
            document.getElementById('modal-brand').value = brandId;
        };

        window.closeModal = function() {
            const editModal = document.getElementById('editModal');
            if(editModal) editModal.style.display = 'none';
            const deleteProduct = document.getElementById('deleteProduct');
            if(deleteProduct) deleteProduct.style.display = 'none';
        };

        window.addEventListener('click', function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) modal.style.display = 'none';
        });

        // ==== CATEGORY MODALS ====
        window.categoryModal = function(){ document.getElementById('category-modal').style.display = 'block'; }
        window.closeModalCategory = function(){ document.getElementById('category-modal').style.display = 'none'; }
        window.categoryEditModal = function(){ document.getElementById('category-edit-modal').style.display = 'block'; }
        window.closeModalCategoryEdit = function(){ document.getElementById('category-edit-modal').style.display = 'none'; }
        window.modalDeleteCategory = function(){ document.getElementById('deleteCategoryModal').style.display = 'block'; }
        window.closeModalDeleteCategory = function(){
            document.getElementById('deleteCategoryModal').style.display = 'none';
            document.getElementById('categoryWarning').style.display = 'none';
        }

        window.loadCategoryName = function() {
            const select = document.getElementById('category_id_edit');
            const selectedOption = select?.options[select.selectedIndex];
            const id = selectedOption?.value;
            const name = selectedOption?.getAttribute('data-name');
            document.getElementById('edit_category_name').value = id ? name : '';
            document.getElementById('editCategoryForm').action = id ? '/categories/' + id : '';
        }

        window.DeleteCategory = function(event) {
            const select = document.getElementById('category_id_edit');
            const id = select?.options[select.selectedIndex]?.value;
            const warning = document.getElementById('categoryWarning');
            if(id){
                document.getElementById('deleteCategoryForm').action = '/categories_destroy/' + id;
                if(warning) warning.style.display = 'none';
            } else {
                event.preventDefault();
                if(warning) warning.style.display = 'block';
            }
        }

        window.openModalDeleteProduct = function(id){
            document.getElementById('deleteProduct').style.display = 'block';
            document.getElementById('deleteForm').action = `/administrador/delete/${id}`;
        }

        // ==== BRAND MODALS ====
        window.marcaModal = function(){ document.getElementById('marca-modal').style.display = 'block'; }
        window.closeModalMarca = function(){ document.getElementById('marca-modal').style.display = 'none'; }
        window.openModalBrandEdit = function(){ document.getElementById('brand-edit-modal').style.display = 'block'; }
        window.closeModalBrandEdit = function(){ document.getElementById('brand-edit-modal').style.display = 'none'; }
        window.loadBrandName = function() {
            const select = document.getElementById('brand_id_edit');
            const selectedOption = select?.options[select.selectedIndex];
            const id = selectedOption?.value;
            const name = selectedOption?.getAttribute('data-name');
            document.getElementById('edit_brand_name').value = id ? name : '';
            document.getElementById('editBrandForm').action = id ? '/brands/' + id : '';
        }

        window.DeleteBrand = function(event) {
            const select = document.getElementById('brand_id_edit');
            const id = select?.options[select.selectedIndex]?.value;
            const warning = document.getElementById('brandWarning');
            if(id){
                document.getElementById('deleteBrandForm').action = '/brands_destroy/' + id;
                if(warning) warning.style.display = 'none';
            } else {
                event.preventDefault();
                if(warning) warning.style.display = 'block';
            }
        }

        window.openModalDeleteBrand = function(){ document.getElementById('deleteBrandModal').style.display = 'block'; }
        window.closeModalDeleteBrand = function(){
            document.getElementById('deleteBrandModal').style.display = 'none';
            document.getElementById('brandWarning').style.display = 'none';
        }
    }

    // ==== TIENDA / USUARIO ====
    // ==== CARRITO ====
function updateCartUI(id, quantity, totalQuantity, price) {
    const quantityEl = document.getElementById('quantity-' + id);
    if (quantityEl) quantityEl.innerText = quantity;

    const subtotalEl = document.getElementById('subtotal-' + id);
    if (subtotalEl) subtotalEl.innerText = '$' + (price * quantity).toLocaleString('es-CL');

    document.querySelectorAll('.cart-total-quantity').forEach(span => {
        span.innerText = totalQuantity > 0 ? `(${totalQuantity})` : '0';
    });

    updateTotalCart();
}

function updateTotalCart() {
    let total = 0;
    document.querySelectorAll('[id^="subtotal-"]').forEach(el => {
        total += parseFloat(el.innerText.replace('$', '').replace(/\./g, '').replace(',', '.')) || 0;
    });

    const totalEl = document.getElementById('total-cart');
    const totalNav = document.querySelectorAll('.cart-total-price');

    if (totalEl) totalEl.innerText = 'Total: $' + total.toLocaleString('es-CL');
    totalNav.forEach(p => p.innerText = '$' + total.toLocaleString('es-CL'));
}

document.querySelectorAll('.decrease-btn, .increase-btn').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();

        const btn = this;
        const id = btn.dataset.id;
        const url = btn.dataset.url;

        // 🔹 Evitamos múltiples clicks mientras se procesa
        if (btn.disabled) return;
        btn.disabled = true;

        // 🔹 Obtener el precio del producto
        const priceEl = btn.closest('.cart-products-container')
            .querySelectorAll('.card-price')[Array.from(document.querySelectorAll('[id^="product-row-"]')).findIndex(el => el.id === 'product-row-' + id)];
        const priceText = priceEl.innerText.replace('$', '').replace(/\./g, '');
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
            if (data.success) {
                updateCartUI(id, data.quantity, data.totalQuantity, price);
            } else if(data.error) {
                alert(data.error);
            }
        })
        .catch(err => {
            console.error('Error en carrito:', err);
            alert('Error de servidor, intenta nuevamente');
        })
        .finally(() => {
            btn.disabled = false; // 🔹 Rehabilitamos el botón
        });
    });
});


document.querySelectorAll('.decrease-btn-detail, .increase-btn-detail').forEach(btn => {
    btn.addEventListener('click', async e => {
        e.preventDefault();

        const id = btn.dataset.id;
        const url = btn.dataset.url;
        const quantityEl = document.getElementById('quantity-' + id);

        if (btn.disabled) return; // previene múltiples clics
        btn.disabled = true;

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id })
            });

            const data = await res.json();

            if (data.success) {

                if (quantityEl) quantityEl.innerText = data.quantity; // cantidad del producto

                document.querySelectorAll('.cart-total-quantity').forEach(span => {
                    span.innerText = data.totalQuantity > 0 ? `(${data.totalQuantity})` : '0';
                });
                document.querySelectorAll('.cart-total-price').forEach(p => {
                    p.innerText = '$' + data.total_price.toLocaleString('es-CL');
                });
            } else if (data.message) {
                alert(data.message);
            }

        } catch (err) {
            console.error('Error AJAX:', err);
            alert('Error de servidor, intenta nuevamente.');
        } finally {
            btn.disabled = false;
        }
    });
});


    // ==== MENÚ RESPONSIVE ====
    const openBtn = document.getElementById('openMenu');
    const closeBtn = document.getElementById('closeMenu');
    const menu = document.getElementById('menu-hamburger');

    if (openBtn && closeBtn && menu) {
        openBtn.addEventListener('click', () => {
            menu.style.display = 'block';
            menu.offsetHeight;
            menu.classList.add('active');
        });

        closeBtn.addEventListener('click', () => {
            menu.classList.remove('active');
        });

        menu.addEventListener('transitionend', () => {
            if (!menu.classList.contains('active')) menu.style.display = 'none';
        });
    }

    // ==== RESET FORM AL VOLVER DESDE FLOW ====
    window.addEventListener('pageshow', () => {
        const form = document.getElementById('shipping-form');
        if (form) form.reset(); // limpia todos los inputs

        const shippingPriceEl = document.getElementById('shipping-price');
        if (shippingPriceEl) shippingPriceEl.innerText = '$0';

        const confirmBtn = document.getElementById('confirm-address');
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.classList.remove('btn-enabled'); // o la clase que uses para habilitado
        }

        const containerfrom = document.getElementById('checkout-container');
        const pagoSection = document.getElementById('formulario-pago');
        if (containerfrom) containerfrom.style.display = 'flex';
        if (pagoSection) pagoSection.style.display = 'none';
    });

    // ==== CHECKOUT DIRECCIÓN ====


    const containerFrom = document.getElementById('checkout-container');
    const form = document.getElementById('shipping-form');
    const confirmBtn = document.getElementById('confirm-address');
    const shippingPriceEl = document.getElementById('shipping-price');
    const pagoSection = document.getElementById('formulario-pago');
    const returnDiv = document.getElementById('return');
    const errorDiv = document.getElementById('error-message');

    const propertyTypeSelect = document.getElementById('property-type');
    const numberLabel = document.getElementById('property-number-label');
    const numberGroup = document.getElementById('property-number-group');
    const numberInput = document.getElementById('property-number');

    if (errorDiv) errorDiv.style.display = 'none';
    if (confirmBtn) confirmBtn.disabled = true;
    if (pagoSection) pagoSection.style.display = 'none';

    // Volver a checkout si se está en pago
    if (returnDiv) {
        returnDiv.addEventListener('click', () => {
            if (pagoSection) pagoSection.style.display = 'none';
            if (containerFrom) containerFrom.style.display = 'flex';
        });
    }

setTimeout(() => {
    if (typeof checkForm === 'function') checkForm();
}, 100);

    // ==== Función para validar si se puede habilitar el botón ====
function checkForm() {
    const commune = document.getElementById('commune')?.value;
    const street = document.getElementById('street')?.value.trim();
    const number = document.getElementById('number')?.value.trim();
    const phone = document.getElementById('phone')?.value.trim();
    const propertyType = propertyTypeSelect?.value;
    const propertyNumber = numberInput?.value.trim();

    let valid = commune && street && phone; // campos obligatorios

    // Número de propiedad solo si aplica
    if (['dpto', 'oficina', 'condominio'].includes(propertyType)) {
        valid = valid && propertyNumber;
    }

    if (confirmBtn) {
        confirmBtn.disabled = !valid;
        if (valid){
            confirmBtn.classList.add('btn-confirm-address');
        }
        else 
            confirmBtn.classList.remove('btn-confirm-address');
    }

    const selectedOption = document.querySelector('#commune option:checked');
    const price = selectedOption?.dataset.price || 0;
    if (shippingPriceEl) shippingPriceEl.innerText = '$' + parseInt(price).toLocaleString('es-CL');
}


    // ==== Función para mostrar/ocultar número según tipo ====
    function updatePropertyNumberInput() {
        if (!propertyTypeSelect || !numberLabel || !numberGroup || !numberInput) return;

        const type = propertyTypeSelect.value || '';
        if (['dpto', 'oficina', 'condominio'].includes(type)) {
            numberLabel.innerText = `Número de ${type.charAt(0).toUpperCase() + type.slice(1)}`;
            numberGroup.style.display = 'block';
            numberInput.required = true;
        } else {
            numberGroup.style.display = 'none';
            numberInput.value = '';
            numberInput.required = false;
        }
    }


    // Ejecutar al cargar para mostrar valores preseleccionados
    updatePropertyNumberInput();    
    checkForm();


    // Eventos de cambio/input
    if (form) {
        form.addEventListener('input', checkForm);
        form.addEventListener('change', checkForm);
    }
    if (propertyTypeSelect) {
        propertyTypeSelect.addEventListener('change', updatePropertyNumberInput);
    }

    // ==== Confirmar dirección ====
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            confirmBtn.disabled = true;
            if (errorDiv) errorDiv.style.display = 'none';

            confirmBtn.innerHTML = `
                <div class="loading">
                    <div>Confirmando...</div> 
                    <div class="loader"></div>
                </div>
            `;

            const commune_id = document.getElementById('commune').value;
            const street = document.getElementById('street').value.trim();
            const number = document.getElementById('number').value.trim();
            const propertyType = propertyTypeSelect?.value;
            const propertyNumber = numberInput?.value.trim();
            const phone = document.getElementById('phone').value.trim();
            const shipping = parseInt(document.querySelector('#commune option:checked').dataset.price);

            fetch('/checkout/save-address', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    commune_id, street, number, property_type: propertyType, property_number: propertyNumber, phone, shipping 
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    containerFrom.style.display = 'none';
                    if (pagoSection) pagoSection.style.display = 'flex';

                    const shippingEl = document.getElementById('shipping-total');
                    if (shippingEl) shippingEl.innerText = shipping.toLocaleString('es-CL');

                    const subtotalEl = document.getElementById('subtotal-products');
                    const totalEl = document.getElementById('total');
                    if (subtotalEl && totalEl) {
                        const subtotal = parseInt(subtotalEl.dataset.value);
                        totalEl.innerText = (subtotal + shipping).toLocaleString('es-CL');
                    }

                    const addressConfirm = document.getElementById('address-confirm');
                    if (addressConfirm) {
                        const communeName = document.querySelector('#commune option:checked')?.textContent || '';
                        addressConfirm.textContent = `${street} Nº ${number}, ${communeName}`;
                    }
                } else if (data.error) {
                    if (errorDiv) {
                        errorDiv.textContent = data.error;
                        errorDiv.style.display = 'block';
                    }
                }
            })
            .catch(err => {
                console.error('Error guardando dirección:', err);
                alert('Error de servidor, intenta nuevamente');
            })
            .finally(() => {
                confirmBtn.disabled = false;
                confirmBtn.innerText = "Confirmar dirección";
            });
        });
    }

    // ==== CHECKOUT TIPO PROPIEDAD ====
if (document.getElementById('checkout-container')) {
    const propertyTypeSelect = document.getElementById('property-type');
    const numberLabel = document.getElementById('property-number-label');
    const numberGroup = document.getElementById('property-number-group');
    const numberInput = document.getElementById('property-number');

    if (propertyTypeSelect && numberLabel && numberGroup && numberInput) {

        const togglePropertyNumber = () => {
            const type = propertyTypeSelect.value;
            if(['dpto','oficina','condominio'].includes(type)) {
                numberLabel.innerText = `Número de ${type.charAt(0).toUpperCase() + type.slice(1)}`;
                numberGroup.style.display = 'block';
                numberInput.required = true;
            } else {
                numberGroup.style.display = 'none';
                numberInput.value = '';
                numberInput.required = false;
            }
        };

        // Ejecutar al cargar la página
        togglePropertyNumber();

        // Ejecutar al cambiar el select
        propertyTypeSelect.addEventListener('change', togglePropertyNumber);
    }
}



// ==== CARRUSEL ====
const carouselInner = document.querySelector('.carousel-inner');
const items = document.querySelectorAll('.carousel-item');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

if (carouselInner && items.length > 0 && prevBtn && nextBtn) {
    let index = 1;
    let size = items[0]?.clientWidth || 0;

    // Clones para loop infinito
    const firstClone = items[0].cloneNode(true);
    const lastClone = items[items.length - 1].cloneNode(true);
    firstClone.id = "first-clone";
    lastClone.id = "last-clone";

    carouselInner.appendChild(firstClone);
    carouselInner.insertBefore(lastClone, items[0]);

    const allItems = document.querySelectorAll('.carousel-item');
    carouselInner.style.transform = `translateX(${-size * index}px)`;

    const moveToSlide = () => {
        carouselInner.style.transition = "transform 0.5s ease-in-out";
        carouselInner.style.transform = `translateX(${-size * index}px)`;
    };

    let autoplay = setInterval(() => {
        if (index >= allItems.length - 1) return;
        index++;
        moveToSlide();
    }, 5000);

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

    carouselInner.addEventListener('transitionend', () => {
        const currentItem = allItems[index];
        if (!currentItem) return;

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

    // Evitar errores en hover
    carouselInner.addEventListener('mouseenter', () => {
        if (autoplay) clearInterval(autoplay);
    });

    carouselInner.addEventListener('mouseleave', () => {
        autoplay = setInterval(() => {
            if (index >= allItems.length - 1) return;
            index++;
            moveToSlide();
        }, 5000);
    });

    // Ajuste al cambiar tamaño de ventana
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            size = items[0]?.clientWidth || size;
            carouselInner.style.transition = "none";
            carouselInner.style.transform = `translateX(${-size * index}px)`;
        }, 100);
    });
}

    // ====AJAX ====
    if (document.getElementById('products-list')) {
        const filtersForm = document.getElementById('filters-form');
        const productsList = document.getElementById('products-list');

        if (filtersForm && productsList) {
            function fetchProducts(url = '/productos') {
                const formData = new FormData(filtersForm);
                const params = new URLSearchParams(formData);

                fetch(`${url.split('?')[0]}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'text/html'
                    }
                })
                .then(res => {
                    if (!res.ok) throw new Error('HTTP error ' + res.status);
                    return res.text();
                })
                .then(html => {
                    productsList.innerHTML = html;
                })
                .catch(err => console.error('Error fetchProducts:', err));
            }

            filtersForm.addEventListener('change', () => fetchProducts());
            const searchInput = document.getElementById('search');
            if (searchInput) searchInput.addEventListener('input', () => fetchProducts());

            const resetLink = filtersForm.querySelector('a[href*="productos"]');
            if (resetLink) {
                resetLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    filtersForm.reset();
                    fetchProducts('/productos');
                });
            }

            productsList.addEventListener('click', function(e) {
                const button = e.target.closest('.add-to-cart-btn');
                if (button) {
                    e.preventDefault();
                    const productId = button.dataset.id;
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
                }
            });
        }
    }



// === ADD TO CART DETAIL ===
const buttondetail = document.querySelector('.add-to-cart-btn-detail');

if (buttondetail) {
    buttondetail.addEventListener('click', async function(e) {
        e.preventDefault();
        const btn = this;
        const productId = btn.dataset.id;

        // 🔹 Evitar múltiples clics
        if (btn.disabled) return;
        btn.disabled = true;

        try {
            const res = await fetch(`/add-to-cart/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: productId })
            });

            const data = await res.json();

            if (data.success) {
                // 🔹 Actualiza contador total del carrito
                document.querySelectorAll('.cart-total-quantity').forEach(span => {
                    span.innerText = data.cart_count > 0 ? `(${data.cart_count})` : '0';
                });

                // 🔹 Actualiza total del carrito
                document.querySelectorAll('.cart-total-price').forEach(p => {
                    p.innerText = '$' + data.total_price.toLocaleString('es-CL');
                });

                // 🔹 Actualiza cantidad en detalle del producto
                const quantityEl = document.getElementById('quantity-' + productId);
                if(quantityEl) {
                    quantityEl.innerText = data.product_quantity; // cantidad real del producto
                }

            } else {
                alert(data.error || 'No se pudo agregar al carrito');
            }

        } catch (err) {
            console.error('Error al agregar al carrito:', err);
            alert('Error de servidor, intenta nuevamente.');
        } finally {
            btn.disabled = false; // 🔹 Rehabilita botón aunque falle
        }
    });
}



// ==== PAGO ==== //

const formPago = document.getElementById('pago');
const btnPago = document.getElementById('btn-pago');
const errorDiv2 = document.getElementById('form-error'); // div para mostrar errores

if (btnPago && formPago) {
    formPago.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (btnPago.disabled) return;

        // Ocultar error previo
        errorDiv2.style.display = 'none';
        errorDiv2.textContent = '';

        btnPago.disabled = true;
        btnPago.querySelector('.btn-text').textContent = 'Procesando...';

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const payload = {
            name: formPago.name.value,
            email: formPago.email.value,
        };

        try {
            const response = await fetch(formPago.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            let data = {};

            try {
                data = await response.json();
            } catch {
                throw new Error('Ocurrió un error inesperado.');
            }
            
            if (data.errorstock) {
            // Guardar el mensaje en sessionStorage
            sessionStorage.setItem('errorstock', data.errorstock);
            
            // Redirigir al carrito
            window.location.href = data.redirect_url;
            return;
            }


            if (data.redirect_url) {
                // 🔹 NO se reactiva el botón ni se cambia el texto aquí
                // 🔹 Solo redirigimos, el botón sigue diciendo "Procesando..."
                window.location.href = data.redirect_url;
                return; // evitamos que se ejecute el finally
            }

            if (data.error) {
                let message = data.error;

                if (data.body) {
                    try {
                        const bodyObj = JSON.parse(data.body);
                        if (bodyObj.message && bodyObj.message.includes('userEmail')) {
                            const match = bodyObj.message.match(/The userEmail: (.+) is not valid/);
                            if (match) {
                                const email = match[1];
                                message = `El correo "${email}" no es válido.`;
                            } else {
                                message = bodyObj.message;
                            }
                        } else if (bodyObj.message) {
                            message = bodyObj.message;
                        }
                    } catch {}
                }

                // Mostrar mensaje de error
                errorDiv2.textContent = message;
                errorDiv2.style.display = 'block';
            }

        } catch (err) {
            errorDiv2.textContent = err.message || 'Error al procesar la compra.';
            errorDiv2.style.display = 'block';
        } finally {
            // 🔹 Solo reactivar el botón si hubo un error (no si hubo redirect)
            if (!errorDiv2.style.display || errorDiv2.style.display === 'none') {
                // Significa que no hubo error y ya se redirige → no hacemos nada
                return;
            }

            btnPago.disabled = false;
            btnPago.querySelector('.btn-text').textContent = 'Pagar';
        }
    });
}

    const errorStock = sessionStorage.getItem('errorstock');

    if (errorStock) {
        // Buscar todos los elementos con la clase errorstock-message
        const elements = document.querySelectorAll('.errorstock-message');
        elements.forEach(el => {
            const msgEl = el.querySelector('.error-message-stock');
            if (msgEl) {
                msgEl.textContent = errorStock;
                el.style.display = 'block';
            }
        });

        // Limpiar el mensaje después de mostrarlo
        sessionStorage.removeItem('errorstock');
    }

// ==== FIN PAGO ==== //

document.addEventListener('click', function (e) {
    const link = e.target.closest('.pagination a');
    if (!link) return;

    e.preventDefault();

    // ✅ Forzar HTTPS aunque el enlace venga con http
    let url = link.href;
    url = url.replace('http://', 'https://');

    const container = document.querySelector('#products-list');

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.text();
    })
    .then(html => {
        container.innerHTML = html;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    })
    .catch(err => console.error('Error cargando productos:', err));
});



// document.addEventListener('click', function(e) {
//     const link = e.target.closest('.cart-clear a');
//     if (!link) return;

//     e.preventDefault(); 
//     const url = link.href;
//     const cartContainer = document.querySelector('#cart-container');

//     fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success && cartContainer) {
//             document.querySelectorAll('.cart-total-quantity').forEach(span => {
//                 span.innerText = '(0)';
//             });
//             document.querySelectorAll('.cart-total-price').forEach(p => {
//                 p.innerText = '$0';
//             });

//             // Reemplaza solo la sección de productos del carrito
//             document.querySelector('.cart-products-container').innerHTML = `
//                 <div class="empty-cart">
//                     <p class="empty-title">Tu carrito está vacío.</p>
//                     <p>Vista nuestros productos. <a class="empty-link" href="/productos">Aquí</a></p>
//                 </div>
//             `;

//             } else {
//                 alert(data.message || 'Carrito vaciado');
//             }
//         })
//         .catch(err => console.error('Error vaciando carrito:', err));
// });






});
