// --- CONFIGURACIÓN ---
const CONFIG = {
    whatsappNumber: "584120936783", // Tu número de WhatsApp
    currency: "$",
    manualRate: 450.50 // TASA DE RESPALDO (Por si falla la API)
};

// --- ESTADO ---
let cart = JSON.parse(localStorage.getItem('hypeCart')) || [];
let currentBcvRate = CONFIG.manualRate; // Iniciamos con manual

// --- BASE DE DATOS DE PRODUCTOS (ÚNICA FUENTE DE VERDAD) ---
const PRODUCTS_DB = [
    {
        id: "1",
        name: "HYPE Oversize Tee",
        price: 35.00,
        category: "street",
        image: "https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?q=80&w=1470&auto=format&fit=crop",
        badge: "BEST SELLER",
        featured: true,
        inStock: true,
        sizes: ["S", "M", "L", "XL"],
        description: "Camiseta oversize de algodón premium con fit urbano. Perfecta para entrenar o para el día a día."
    },
    {
        id: "2",
        name: "Short HYPE Training",
        price: 30.00,
        category: "gym",
        image: "https://images.unsplash.com/photo-1552364142-f06dd635a796?q=80&w=1170&auto=format&fit=crop",
        badge: null,
        featured: true,
        inStock: true,
        sizes: ["M", "L", "XL"],
        description: "Short de alto rendimiento con tecnología anti-transpirante y bolsillos con cierre."
    },
    {
        id: "3",
        name: "HYPE Tech Hoodie",
        price: 65.00,
        category: "street",
        image: "https://images.unsplash.com/photo-1518310383802-640c2de311b2?q=80&w=1470&auto=format&fit=crop",
        badge: "NEW",
        featured: true,
        inStock: true,
        sizes: ["S", "M", "L", "XL"],
        description: "Hoodie técnico con capucha ajustable, bolsillos frontales tipo canguro y acabado premium."
    },
    {
        id: "4",
        name: "Pro Combat Leggings",
        price: 45.00,
        category: "gym",
        image: "img/4.jpg",
        badge: null,
        featured: false,
        inStock: true,
        sizes: ["S", "M", "L"],
        description: "Leggings de compresión para máximo rendimiento. Diseño ergonómico y tela de secado rápido."
    },
    {
        id: "5",
        name: "HYPE Cap Black",
        price: 20.00,
        category: "accessories",
        image: "img/5.jpg",
        badge: null,
        featured: false,
        inStock: true,
        sizes: ["Única"],
        description: "Gorra ajustable con logo HYPE bordado. Perfecta para completar tu outfit deportivo."
    },
    {
        id: "6",
        name: "Urban Joggers Grey",
        price: 55.00,
        category: "street",
        image: "img/7.jpg",
        badge: "SALE",
        featured: false,
        inStock: false,
        sizes: ["M", "L", "XL"],
        description: "Joggers de corte urban con cintura elástica y puños ajustables. Estilo y comodidad."
    }
];

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

// --- API BCV ---
async function fetchBCV() {
    try {
        // Usamos una API gratuita común. Si falla, usa manualRate.
        const response = await fetch('https://pydolarvenezuela-api-ryder.koyeb.app/api/v1/dollar?page=bcv');
        const data = await response.json();

        // Verifica la estructura de la respuesta
        if (data && data.monitors && data.monitors.usd) {
            currentBcvRate = parseFloat(data.monitors.usd.price);
            console.log("BCV Rate Updated:", currentBcvRate);
            // Notificar tasa actualizada (solo si hay contenedor)
            if (document.getElementById('toastContainer')) {
                showToast(`Tasa BCV actualizada: Bs. ${currentBcvRate.toFixed(2)}`, 'info');
            }
        }
    } catch (error) {
        console.warn("API Error, using manual rate:", error);
        currentBcvRate = CONFIG.manualRate;
    }

    // Si el modal está abierto, actualiza el texto
    if (els.displayRate) els.displayRate.innerText = `Bs. ${currentBcvRate.toFixed(2)}`;
}

// --- SISTEMA DE NOTIFICACIONES TOAST ---
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const icons = {
        success: 'ph-check-circle',
        error: 'ph-x-circle',
        info: 'ph-info'
    };

    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;
    toast.innerHTML = `
        <i class="ph ${icons[type]} toast__icon"></i>
        <div class="toast__content">${message}</div>
    `;

    container.appendChild(toast);

    // Auto-dismiss después de 3 segundos
    setTimeout(() => {
        toast.classList.add('toast--hiding');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}


// --- LOGICA DE CHECKOUT ---
function openCheckoutModal() {
    if (cart.length === 0) return;

    const totalUSD = cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
    const totalBS = totalUSD * currentBcvRate;

    // Actualiza los valores visibles
    els.displayRate.innerText = `Bs. ${currentBcvRate.toFixed(2)}`;
    els.displayTotalUSD.innerText = `$${totalUSD.toFixed(2)}`;
    els.displayTotalBS.innerText = `Bs. ${totalBS.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

    closeCart(); // Cierra el carrito
    els.checkoutOverlay.classList.add('open'); // Abre el modal
}

function closeCheckoutModal() {
    els.checkoutOverlay.classList.remove('open');
}

function submitOrder(e) {
    e.preventDefault();

    const name = document.getElementById('clientName').value;
    const phone = document.getElementById('clientPhone').value;
    const method = document.getElementById('paymentMethod').value;
    const address = document.getElementById('clientAddress').value;

    const totalUSD = cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
    const totalBS = totalUSD * currentBcvRate;

    // Mensaje para WhatsApp
    let msg = `*NUEVO PEDIDO HYPE* 🚀\n`;
    msg += `--------------------------------\n`;
    msg += `👤 *Cliente:* ${name}\n`;
    msg += `📱 *Teléfono:* ${phone}\n`;
    msg += `📍 *Dirección:* ${address}\n`;
    msg += `💳 *Método:* ${method}\n`;
    msg += `--------------------------------\n`;
    msg += `🛒 *PEDIDO:*\n`;

    cart.forEach(item => {
        msg += `▪️ ${item.name} (${item.size}) x${item.quantity}\n`;
    });

    msg += `--------------------------------\n`;
    msg += `💵 *TOTAL USD: $${totalUSD.toFixed(2)}*\n`;
    msg += `🇻🇪 *TOTAL BS: Bs. ${totalBS.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}*\n`;
    msg += `📉 *Tasa BCV:* ${currentBcvRate.toFixed(2)}\n`;
    msg += `--------------------------------\n`;
    msg += `Espero datos de pago.`;

    window.open(`https://wa.me/${CONFIG.whatsappNumber}?text=${encodeURIComponent(msg)}`, '_blank');

    // --- FLUJO POST-COMPRA ---
    // 1. Cerrar modal de checkout
    closeCheckoutModal();

    // 2. Limpiar carrito
    cart = [];
    renderCart();

    // 3. Resetear formulario
    els.checkoutForm.reset();

    // 4. Mostrar mensaje de éxito
    showToast('¡Pedido enviado! Te contactaremos por WhatsApp.', 'success');
}

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

    const featured = PRODUCTS_DB.filter(p => p.featured === true);
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
                    <a href="product.html?id=${p.id}">
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
                        <button class="btn btn--primary btn--icon add-to-cart" data-id="${p.id}"${btnDisabled}>
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
                    <a href="product.html?id=${p.id}">${badge}<img src="${p.image}" loading="lazy"></a>
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
        <a href="shop.html" class="back-link">← Volver</a>
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

// --- INIT ---
document.addEventListener('DOMContentLoaded', () => {
    fetchBCV();
    renderCart();
    renderFeaturedProducts();  // Renderiza productos destacados en Home
    renderShop('all');
    renderProductDetail();

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

    // Botones estáticos (Home)
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', () => window.addToCart(btn.dataset.id));
    });
});