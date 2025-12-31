// ============================================
// SISTEMA DE CARRITO DE COMPRAS
// ============================================

class ShoppingCart {
    constructor() {
        this.items = this.loadFromStorage();
        this.initializeCart();
    }

    // Cargar carrito del localStorage
    loadFromStorage() {
        const stored = localStorage.getItem('wildgarden_cart');
        return stored ? JSON.parse(stored) : [];
    }

    // Guardar carrito en localStorage
    saveToStorage() {
        localStorage.setItem('wildgarden_cart', JSON.stringify(this.items));
    }

    // Inicializar eventos del carrito
    initializeCart() {
        this.setupAddToCartButtons();
        this.setupCartButtons();
        this.updateCartUI();
    }

    // Configurar botones de "Agregar al carrito"
    setupAddToCartButtons() {
        const addButtons = document.querySelectorAll('.add-to-cart-btn');
        addButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const productId = button.getAttribute('data-product-id');
                const productName = button.getAttribute('data-product-name');
                const productPrice = parseFloat(button.getAttribute('data-product-price'));
                const productImage = button.getAttribute('data-product-image');

                this.addItem(productId, productName, productPrice, productImage);
                this.showNotification(`${productName} agregado al carrito`);
                button.textContent = 'Agregado ✓';
                setTimeout(() => {
                    button.textContent = 'Agregar al Carrito';
                }, 2000);
            });
        });
    }

    // Configurar botones del carrito
    setupCartButtons() {
        const cartIcon = document.querySelector('.cart-icon');
        if (cartIcon) {
            cartIcon.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleCartModal();
            });
        }

        const closeButton = document.getElementById('close-cart');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                this.toggleCartModal();
            });
        }

        const checkoutButton = document.getElementById('checkout-btn');
        if (checkoutButton) {
            checkoutButton.addEventListener('click', () => {
                this.proceedToCheckout();
            });
        }

        // Cerrar modal al hacer click fuera
        document.addEventListener('click', (e) => {
            const cartModal = document.getElementById('cart-modal');
            if (cartModal && e.target === cartModal) {
                this.toggleCartModal();
            }
        });
    }

    // Agregar item al carrito
    addItem(productId, productName, productPrice, productImage) {
        const existingItem = this.items.find(item => item.id === productId);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.items.push({
                id: productId,
                name: productName,
                price: productPrice,
                image: productImage,
                quantity: 1
            });
        }

        this.saveToStorage();
        this.updateCartUI();
    }

    // Eliminar item del carrito
    removeItem(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.saveToStorage();
        this.updateCartUI();
    }

    // Actualizar cantidad
    updateQuantity(productId, quantity) {
        if (quantity <= 0) {
            this.removeItem(productId);
            return;
        }
        const item = this.items.find(item => item.id === productId);
        if (item) {
            item.quantity = quantity;
            this.saveToStorage();
            this.updateCartUI();
        }
    }

    // Calcular totales
    getCartTotals() {
        const subtotal = this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const shipping = this.items.length > 0 ? 5000 : 0; // $5000 por envío
        const total = subtotal + shipping;

        return {
            subtotal: Math.round(subtotal),
            shipping: shipping,
            total: Math.round(total),
            itemCount: this.items.reduce((sum, item) => sum + item.quantity, 0)
        };
    }

    // Actualizar UI del carrito
    updateCartUI() {
        const totals = this.getCartTotals();
        const cartCount = document.querySelector('.cart-count');
        const cartItems = document.getElementById('cart-items');
        const cartSummary = document.getElementById('cart-summary');

        // Actualizar contador
        if (cartCount) {
            cartCount.textContent = totals.itemCount;
            cartCount.style.display = totals.itemCount > 0 ? 'flex' : 'none';
        }

        // Actualizar items
        if (cartItems) {
            if (this.items.length === 0) {
                cartItems.innerHTML = '<p class="empty-cart">Tu carrito está vacío</p>';
            } else {
                cartItems.innerHTML = this.items.map(item => `
                    <div class="cart-item">
                        <img src="${item.image}" alt="${item.name}">
                        <div class="item-details">
                            <h4>${item.name}</h4>
                            <p>$${this.formatPrice(item.price)}</p>
                        </div>
                        <div class="item-quantity">
                            <button class="qty-btn" onclick="cart.updateQuantity('${item.id}', ${item.quantity - 1})">−</button>
                            <input type="number" value="${item.quantity}" readonly>
                            <button class="qty-btn" onclick="cart.updateQuantity('${item.id}', ${item.quantity + 1})">+</button>
                        </div>
                        <div class="item-total">
                            $${this.formatPrice(item.price * item.quantity)}
                        </div>
                        <button class="remove-btn" onclick="cart.removeItem('${item.id}')">✕</button>
                    </div>
                `).join('');
            }
        }

        // Actualizar resumen
        if (cartSummary) {
            cartSummary.innerHTML = `
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$${this.formatPrice(totals.subtotal)}</span>
                </div>
                <div class="summary-row">
                    <span>Envío:</span>
                    <span>$${this.formatPrice(totals.shipping)}</span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>$${this.formatPrice(totals.total)}</span>
                </div>
            `;
        }
    }

    // Toggle carrito modal
    toggleCartModal() {
        const cartModal = document.getElementById('cart-modal');
        if (cartModal) {
            cartModal.classList.toggle('active');
        }
    }

    // Proceder al checkout
    proceedToCheckout() {
        const totals = this.getCartTotals();
        
        if (this.items.length === 0) {
            this.showNotification('Tu carrito está vacío');
            return;
        }

        // Abrir formulario de checkout
        this.showCheckoutForm();
    }

    // Mostrar formulario de checkout
    showCheckoutForm() {
        const checkoutModal = document.getElementById('checkout-modal');
        if (checkoutModal) {
            checkoutModal.classList.add('active');
        }
    }

    // Procesar pago con Webpay
    processWebpayPayment(customerData) {
        const totals = this.getCartTotals();
        
        // Guardar info de la orden en localStorage para referencia
        const orderInfo = {
            orderId: this.generateOrderId(),
            amount: totals.total,
            customer: customerData,
            items: this.items,
            timestamp: new Date().toISOString()
        };
        localStorage.setItem('pendingOrder', JSON.stringify(orderInfo));
        
        // Redirigir directamente al link de WebPay funcional
        // El usuario completará el pago ahí y luego puede contactar para confirmar
        window.location.href = 'https://www.webpay.cl/form-pay/197981';
    }

    // Generar ID de orden único
    generateOrderId() {
        return 'ORD-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    }

    // Formatear precio
    formatPrice(price) {
        return price.toLocaleString('es-CL');
    }

    // Mostrar notificación
    showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Vaciar carrito
    clearCart() {
        this.items = [];
        this.saveToStorage();
        this.updateCartUI();
    }
}

// Inicializar carrito global
const cart = new ShoppingCart();
