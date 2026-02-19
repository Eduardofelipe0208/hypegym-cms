// --- CONFIGURACIÓN ---
const CONFIG = {
    whatsappNumber: "584120936783", // Tu número de WhatsApp
    currency: (window.HYPE_CONFIG && window.HYPE_CONFIG.currency) || "$",
    manualRate: 450.50 // TASA DE RESPALDO (Por si falla la API)
};

// --- ESTADO ---
let cart = JSON.parse(localStorage.getItem('hypeCart')) || [];
let currentBcvRate = CONFIG.manualRate; // Iniciamos con manual

// --- BASE DE DATOS DE PRODUCTOS (ÚNICA FUENTE DE VERDAD) ---
// --- BASE DE DATOS DE PRODUCTOS (DINÁMICA) ---
let PRODUCTS_DB = [];

// --- API FETCH PRODUCTS ---
async function fetchProducts() {
    try {
        const response = await fetch('api/products.php');
        if (!response.ok) throw new Error('Error al cargar productos');

        const data = await response.json();
        PRODUCTS_DB = data;

        // Renderizar componentes una vez cargados los datos
        // Renderizar componentes una vez cargados los datos
        renderFeaturedProducts();
        renderProductDetail();

        // Inicializar la tienda (si estamos en shop.php)
        initShopRender();

        console.log("Productos cargados:", PRODUCTS_DB.length);

    } catch (error) {
        console.error("Error fetching products:", error);
        showToast("Error cargando el catálogo. Intenta recargar.", "error");
    }
}

// --- ELEMENTOS DOM ---
const els = {
    cartToggle: document.getElementById('cartToggle'),
    cartOverlay: document.getElementById('cartOverlay'),
    cartDrawer: document.getElementById('cartDrawer'),
    closeCart: document.getElementById('closeCart'),
    cartItems: document.getElementById('cartItems'),
    cartCount: document.getElementById('cartCount'),
    cartSubtotal: document.getElementById('cartSubtotal'),
    cartTotal: document.getElementById('cartTotal'),
    checkoutBtn: document.getElementById('checkoutBtn'),

    // Modal Elements
    checkoutOverlay: document.getElementById('checkoutOverlay'),
    closeCheckout: document.getElementById('closeCheckout'),
    checkoutForm: document.getElementById('checkoutForm'),
    displayRate: document.getElementById('displayRate'),
    displayTotalUSD: document.getElementById('displayTotalUSD'),
    displayTotalBS: document.getElementById('displayTotalBS'),

    shopContainer: document.getElementById('shopContainer'),
    filterBtns: document.querySelectorAll('.filter-btn')
};

// --- VARIABLES GLOBALES ---
let paymentMethods = []; // Se llena desde API

// --- API CONFIG (Tasa + Métodos) ---
async function fetchConfig() {
    try {
        const response = await fetch('api/config.php');
        const data = await response.json();

        if (data.exchange_rate) {
            currentBcvRate = parseFloat(data.exchange_rate);
            console.log("Tasa Interna Actualizada:", currentBcvRate);
        }

        if (data.payment_methods) {
            paymentMethods = data.payment_methods;
            fillPaymentMethodsSelect();
        }

        // Actualizar UI si modal está abierto
        updateCheckoutTotals();

    } catch (error) {
        console.error("Error cargando configuración:", error);
    }
}

function fillPaymentMethodsSelect() {
    const select = document.getElementById('paymentMethod');
    if (!select) return;

    select.innerHTML = '<option value="" disabled selected>Selecciona una opción</option>';
    paymentMethods.forEach(method => {
        select.innerHTML += `<option value="${method.id}">${method.name}</option>`;
    });
}

function updateCheckoutTotals() {
    if (!els.displayRate) return;

    const totalUSD = cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
    const totalBS = totalUSD * currentBcvRate;

    els.displayRate.innerText = `Bs. ${currentBcvRate.toFixed(2)}`;
    els.displayTotalUSD.innerText = `$${totalUSD.toFixed(2)}`;
    els.displayTotalBS.innerText = `Bs. ${totalBS.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

// --- LOGICA UI CHECKOUT ---
window.updatePaymentInstructions = function () {
    const methodId = document.getElementById('paymentMethod').value;
    const container = document.getElementById('paymentInstructions');
    const refContainer = document.getElementById('referenceContainer');
    const refInput = document.getElementById('paymentReference');

    const method = paymentMethods.find(m => m.id == methodId);

    // 1. Mostrar instrucciones
    if (method && method.instructions) {
        container.style.display = 'block';
        container.innerHTML = `<strong>Datos para el pago:</strong><br>${method.instructions.replace(/\n/g, '<br>')}`;
    } else {
        container.style.display = 'none';
    }

    // 2. Ocultar referencia si es Efectivo
    if (method && method.name.toLowerCase().includes('efectivo')) {
        refContainer.style.display = 'none';
        refInput.removeAttribute('required');
        refInput.value = 'Efectivo'; // Valor por defecto para pasar validación simple
    } else {
        refContainer.style.display = 'block';
        refInput.setAttribute('required', 'true');
        if (refInput.value === 'Efectivo') refInput.value = '';
    }
}

// --- LOGICA DE CHECKOUT (ENVÍO PEDIDO) ---
function openCheckoutModal() {
    if (cart.length === 0) return;
    updateCheckoutTotals();
    closeCart(); // Cierra el carrito
    els.checkoutOverlay.classList.add('open'); // Abre el modal
}

function closeCheckoutModal() {
    els.checkoutOverlay.classList.remove('open');
}

async function submitOrder(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitOrder');
    const statusMsg = document.getElementById('orderStatus');

    // Bloquear botón
    btn.disabled = true;
    btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Procesando...';
    statusMsg.innerText = '';

    const name = document.getElementById('clientName').value;
    const phone = document.getElementById('clientPhone').value;
    const email = document.getElementById('clientEmail').value;
    const methodId = document.getElementById('paymentMethod').value;
    const methodName = paymentMethods.find(m => m.id == methodId)?.name || 'Desconocido';
    const reference = document.getElementById('paymentReference').value;
    const address = document.getElementById('clientAddress').value;

    const orderData = {
        name,
        phone,
        email,
        address,
        payment_method_id: methodId,
        payment_method_name: methodName,
        reference,
        cart: cart.map(item => ({ id: item.id, quantity: item.quantity, size: item.size }))
    };

    try {
        const response = await fetch('api/checkout.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orderData)
        });
        const result = await response.json();

        if (result.success) {
            // ÉXITO
            showToast('¡Pedido recibido! Redirigiendo a WhatsApp...', 'success');

            // Construir mensaje de WhatsApp
            const cartText = cart.map(i => `• ${i.name} (${i.size}) x${i.quantity}`).join('\n');
            const totalUSD = els.displayTotalUSD.innerText;
            const totalBS = els.displayTotalBS.innerText;

            let message = `*HOLA! 👋 Vengo de hypesportswear.com*\n\n` +
                `📦 *NUEVO PEDIDO #${result.order_id}*\n` +
                `👤 *Cliente:* ${name}\n` +
                `📱 *Tel:* ${phone}\n` +
                `💳 *Pago:* ${methodName} (Ref: ${reference})\n` +
                `📍 *Envío:* ${address}\n\n` +
                `🛒 *RESUMEN:*\n${cartText}\n\n` +
                `💰 *TOTAL:* ${totalUSD} / ${totalBS}`;

            // Codificar el mensaje para URL
            const encodedMessage = encodeURIComponent(message);
            const waLink = `https://wa.me/584120936783?text=${encodedMessage}`;

            // Limpiar todo
            cart = [];
            renderCart();
            els.checkoutForm.reset();
            closeCheckoutModal();

            // Redirigir a WhatsApp (Estándar para móviles y desktop sin bloqueos)
            window.location.href = waLink;

        } else {
            throw new Error(result.error || 'Error desconocido');
        }

    } catch (error) {
        showToast('Error al procesar pedido: ' + error.message, 'error');
        statusMsg.innerText = 'Error: ' + error.message;
        statusMsg.style.color = '#ff6b6b';
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'CONFIRMAR Y PAGAR <i class="ph ph-check-circle"></i>';
    }
}

// Reemplazo de init para usar fetchConfig

// --- FUNCIONES CARRITO & TIENDA ---
function renderCart() {
    els.cartItems.innerHTML = '';
    let total = 0;
    let count = 0;

    if (cart.length === 0) {
        els.cartItems.innerHTML = `<div class="empty-state"><i class="ph ph-shopping-bag"></i><p>Tu carrito está vacío.</p></div>`;
        els.checkoutBtn.disabled = true;
        els.checkoutBtn.style.opacity = "0.5";
    } else {
        els.checkoutBtn.disabled = false;
        els.checkoutBtn.style.opacity = "1";
        cart.forEach((item, index) => {
            total += item.price * item.quantity;
            count += item.quantity;
            els.cartItems.innerHTML += `
                <div class="cart-item">
                    <img src="${item.image}" alt="${item.name}">
                    <div class="cart-item__info">
                        <h5>${item.name}</h5>
                        <div class="cart-item__meta">${item.size} | $${item.price}</div>
                        <div class="cart-item__actions">
                            <span>x${item.quantity}</span>
                            <button class="btn-remove" onclick="removeFromCart(${index})">Eliminar</button>
                        </div>
                    </div>
                </div>`;
        });
    }

    if (els.cartSubtotal) els.cartSubtotal.innerText = `$${total.toFixed(2)}`;
    els.cartTotal.innerText = `$${total.toFixed(2)}`;
    els.cartCount.innerText = count;
    els.cartCount.style.display = count > 0 ? 'flex' : 'none';
    localStorage.setItem('hypeCart', JSON.stringify(cart));
}

window.addToCart = function (productId) {
    const productData = PRODUCTS_DB.find(p => p.id === productId);
    if (!productData) return;
    const size = document.getElementById(`size-${productId}`)?.value || 'M';
    const existing = cart.find(i => i.id === productId && i.size === size);
    if (existing) existing.quantity++;
    else cart.push({ ...productData, size, quantity: 1 });
    renderCart();
    openCart();

    // Notificación de éxito
    showToast(`${productData.name} agregado al carrito`, 'success');
};

window.removeFromCart = (index) => { cart.splice(index, 1); renderCart(); };
function openCart() { els.cartOverlay.classList.add('open'); els.cartDrawer.classList.add('open'); }
function closeCart() { els.cartOverlay.classList.remove('open'); els.cartDrawer.classList.remove('open'); }

// --- RENDER FEATURED PRODUCTS (HOME) ---
function renderFeaturedProducts() {
    const container = document.getElementById('featuredContainer');
    if (!container) return; // No estamos en el Home

    const featured = PRODUCTS_DB.filter(p => p.featured === true).slice(0, 3);
    container.innerHTML = '';

    featured.forEach(p => {
        const badge = p.badge ? `<span class="badge ${p.badge === 'NEW' ? 'badge--neon' : ''}">${p.badge}</span>` : '';
        const stockClass = p.inStock ? '' : ' out-of-stock';
        const btnDisabled = p.inStock ? '' : ' disabled';
        const btnText = p.inStock ? 'AGREGAR' : 'AGOTADO';

        const sizesOptions = p.sizes.map((s, i) =>
            `<option value="${s}"${i === 1 ? ' selected' : ''}>${s}</option>`
        ).join('');

        container.innerHTML += `
            <article class="product-card${stockClass}">
                <div class="product-image">
                    ${badge}
                    <a href="index.php?page=product&id=${p.id}">
                        <img src="${p.image}" alt="${p.name}">
                    </a>
                </div>
                <div class="product-info">
                    <h4>${p.name}</h4>
                    <p class="price">$${p.price.toFixed(2)}</p>
                    <div class="product-controls">
                        <select id="size-${p.id}" class="size-selector"${btnDisabled}>
                            ${sizesOptions}
                        </select>
                        <button class="btn btn--primary btn--icon" onclick="addToCart('${p.id}')"${btnDisabled}>
                            <i class="ph ph-plus"></i> ${btnText}
                        </button>
                    </div>
                </div>
            </article>`;
    });
}


// --- RENDER SHOP PRODUCTS (CATÁLOGO) ---
let currentCategory = 'all';
let currentSearchQuery = '';

function renderShop(category = 'all', searchQuery = '') {
    if (!els.shopContainer) return;
    els.shopContainer.innerHTML = '';

    // Guardar estado actual
    currentCategory = category;
    currentSearchQuery = searchQuery;

    // Filtrar por categoría
    let filtered = category === 'all' ? PRODUCTS_DB : PRODUCTS_DB.filter(p => p.category === category);

    // Filtrar por búsqueda
    if (searchQuery.trim()) {
        filtered = filtered.filter(p =>
            p.name.toLowerCase().includes(searchQuery.toLowerCase())
        );
    }

    // Mensaje si no hay resultados
    if (filtered.length === 0) {
        els.shopContainer.innerHTML = '<p style="color:#888; text-align:center; padding:2rem;">No se encontraron productos.</p>';
        return;
    }

    filtered.forEach(p => {
        const badge = p.badge ? `<span class="badge ${p.badge === 'NEW' ? 'badge--neon' : ''}">${p.badge}</span>` : '';
        const stockClass = p.inStock ? '' : ' out-of-stock';
        const btnDisabled = p.inStock ? '' : ' disabled';
        const btnText = p.inStock ? '<i class="ph ph-plus"></i>' : 'AGOTADO';
        const sizesOptions = p.sizes.map((s, i) =>
            `<option value="${s}"${i === 1 || (i === 0 && p.sizes.length === 1) ? ' selected' : ''}>${s}</option>`
        ).join('');

        els.shopContainer.innerHTML += `
            <article class="product-card${stockClass}">
                <div class="product-image">
                    <a href="index.php?page=product&id=${p.id}">${badge}<img src="${p.image}" loading="lazy"></a>
                </div>
                <div class="product-info">
                    <h4>${p.name}</h4><p class="price">$${p.price.toFixed(2)}</p>
                    <div class="product-controls">
                        <select id="size-${p.id}" class="size-selector"${btnDisabled}>${sizesOptions}</select>
                        <button class="btn btn--primary btn--icon" onclick="addToCart('${p.id}')"${btnDisabled}>${btnText}</button>
                    </div>
                </div>
            </article>`;
    });
}

// --- BÚSQUEDA EN TIEMPO REAL ---
function searchProducts(query) {
    renderShop(currentCategory, query);
}

// --- RENDER PRODUCT DETAIL PAGE ---
function renderProductDetail() {
    const container = document.getElementById('product-detail-container');
    if (!container) return;
    const id = new URLSearchParams(window.location.search).get('id');
    const p = PRODUCTS_DB.find(prod => prod.id === id);
    if (!p) { container.innerHTML = '<p class="text-center">Producto no encontrado</p>'; return; }

    const sizesOptions = p.sizes.map((s, i) =>
        `<option value="${s}"${i === 1 || (i === 0 && p.sizes.length === 1) ? ' selected' : ''}>${s}</option>`
    ).join('');
    const btnDisabled = p.inStock ? '' : ' disabled';
    const btnText = p.inStock ? 'AGREGAR AL CARRITO' : 'PRODUCTO AGOTADO';

    container.innerHTML = `
        <a href="index.php?page=shop" class="back-link">← Volver</a>
        <div class="product-detail-grid">
            <div class="pd-image"><img src="${p.image}"></div>
            <div class="pd-info">
                <h1 class="pd-title">${p.name}</h1>
                <span class="pd-price">$${p.price.toFixed(2)}</span>
                <p class="pd-description">${p.description}</p>
                <div class="pd-actions">
                    <select id="size-detail-${p.id}" class="size-selector-lg"${btnDisabled}>${sizesOptions}</select>
                    <button class="btn btn--primary btn--full" onclick="addToCartDetail('${p.id}')"${btnDisabled}>${btnText}</button>
                </div>
            </div>
        </div>`;
}

window.addToCartDetail = function (id) {
    const p = PRODUCTS_DB.find(prod => prod.id === id);
    const size = document.getElementById(`size-detail-${id}`).value;
    const existing = cart.find(i => i.id === id && i.size === size);
    if (existing) existing.quantity++;
    else cart.push({ ...p, size, quantity: 1 });
    renderCart(); openCart();
}

// --- TOAST NOTIFICATIONS ---
function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;

    let icon = 'info';
    if (type === 'success') icon = 'check-circle';
    if (type === 'error') icon = 'warning';

    toast.innerHTML = `
        <i class="ph ph-${icon} toast__icon"></i>
        <div class="toast__content">${message}</div>
    `;

    container.appendChild(toast);

    // Auto remove
    setTimeout(() => {
        toast.classList.add('toast--hiding');
        toast.addEventListener('animationend', () => toast.remove());
    }, 4000);
}

// --- INIT SHOP RENDER ---
function initShopRender() {
    // Solo ejecutar si existe el contenedor de tienda
    if (!els.shopContainer) return;

    const urlParams = new URLSearchParams(window.location.search);
    const categoryParam = urlParams.get('category');

    if (categoryParam) {
        // Activar visualmente el botón correspondiente
        const btn = document.querySelector(`.filter-btn[data-category="${categoryParam}"]`);
        if (btn) {
            // Remover activo de todos
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            // Activar este
            btn.classList.add('active');
            // Renderizar sin click (evita loop)
            renderShop(categoryParam);
        } else {
            renderShop(categoryParam);
        }
    } else {
        renderShop('all');
    }
}

// --- INIT ---
document.addEventListener('DOMContentLoaded', () => {
    fetchConfig(); // Configuración (Tasa/Métodos)
    fetchProducts(); // Productos (Catálogo)

    renderCart();

    // Listeners generales
    if (els.cartToggle) els.cartToggle.addEventListener('click', openCart);
    if (els.closeCart) els.closeCart.addEventListener('click', closeCart);
    if (els.cartOverlay) els.cartOverlay.addEventListener('click', closeCart);

    // Checkout Modal Listeners
    if (els.checkoutBtn) els.checkoutBtn.addEventListener('click', openCheckoutModal);
    if (els.closeCheckout) els.closeCheckout.addEventListener('click', closeCheckoutModal);
    if (els.checkoutForm) els.checkoutForm.addEventListener('submit', submitOrder);

    // Filtros
    if (els.filterBtns) els.filterBtns.forEach(btn => btn.addEventListener('click', () => {
        els.filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderShop(btn.dataset.category, currentSearchQuery);
    }));

    // Buscador en tiempo real (con debounce)
    if (els.searchInput) {
        let searchTimeout;
        els.searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchProducts(e.target.value);
            }, 300); // Debounce de 300ms
        });
    }

    // Botones estáticos (Home) - YA NO SON NECESARIOS PORQUE USAMOS ONCLICK
    // document.querySelectorAll('.add-to-cart').forEach(btn => {
    //    btn.addEventListener('click', () => window.addToCart(btn.dataset.id));
    // });
});