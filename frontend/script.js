// Configuración
const PHONE_NUMBER = '56996744579'; // Reemplaza con tu número de WhatsApp (sin símbolos ni espacios)
const BUSINESS_NAME = 'Floreria Wildgarden';

// Mobile Menu Toggle
const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('nav-menu');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('active');
});

// Close menu when clicking on a link
const navLinks = navMenu.querySelectorAll('a');
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
    });
});

// USER MENU TOGGLE
const userMenuToggle = document.querySelector('.user-menu-toggle');
const userDropdown = document.getElementById('user-dropdown');

if (userMenuToggle) {
    userMenuToggle.addEventListener('click', (e) => {
        e.preventDefault();
        userDropdown.style.display = userDropdown.style.display === 'none' ? 'block' : 'none';
    });
}

// Close user menu when clicking outside
document.addEventListener('click', (e) => {
    if (userDropdown && !e.target.closest('.header-actions')) {
        userDropdown.style.display = 'none';
    }
});

// Dynamic Year
document.getElementById('year').textContent = new Date().getFullYear();

// Floating WhatsApp Button - Show after scroll
const whatsappButton = document.getElementById('whatsapp-float');
const hero = document.querySelector('.hero');

window.addEventListener('scroll', () => {
    const heroBottom = hero.offsetHeight;
    if (window.scrollY > heroBottom * 0.5) {
        whatsappButton.classList.add('show');
    } else {
        whatsappButton.classList.remove('show');
    }
});

// WhatsApp Integration
const whatsappButtons = document.querySelectorAll('.whatsapp-btn');

whatsappButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const productName = button.getAttribute('data-product');
        const productPrice = button.getAttribute('data-price');
        
        // Crear mensaje personalizado
        const message = `Hola ${BUSINESS_NAME}, me interesa el ${productName} (${productPrice}) que vi en su web. ¿Está disponible?`;
        
        // Codificar mensaje para URL
        const encodedMessage = encodeURIComponent(message);
        
        // Abrir WhatsApp
        const whatsappLink = `https://wa.me/${PHONE_NUMBER}?text=${encodedMessage}`;
        window.open(whatsappLink, '_blank');
    });
});

// Floating WhatsApp Button - General contact
whatsappButton.addEventListener('click', (e) => {
    e.preventDefault();
    const message = `Hola ${BUSINESS_NAME}, me gustaría conocer más sobre sus productos y servicios. ¿Pueden ayudarme?`;
    const encodedMessage = encodeURIComponent(message);
    const whatsappLink = `https://wa.me/${PHONE_NUMBER}?text=${encodedMessage}`;
    window.open(whatsappLink, '_blank');
});

// Smooth scroll for buttons
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href !== '#inicio' && href !== '#catalogo' && href !== '#contacto') {
            return;
        }
        const target = document.querySelector(href);
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// ============================================
// CHECKOUT FORM HANDLING
// ============================================

const checkoutForm = document.getElementById('checkout-form');
if (checkoutForm) {
    checkoutForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Validar que haya items en el carrito
        if (cart.items.length === 0) {
            cart.showNotification('Tu carrito está vacío');
            return;
        }

        // Obtener datos del formulario
        const formData = new FormData(checkoutForm);
        const customerData = {
            name: formData.get('name'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            address: formData.get('address'),
            city: formData.get('city'),
            paymentMethod: formData.get('payment-method')
        };

        // Validar datos
        if (!customerData.name || !customerData.email || !customerData.phone) {
            cart.showNotification('Por favor completa todos los campos requeridos');
            return;
        }

        // Si el método es Webpay, procesar pago
        if (customerData.paymentMethod === 'webpay') {
            // Desactivar botón durante el procesamiento
            const submitBtn = checkoutForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Procesando...';

            try {
                // Procesar pago con Webpay
                cart.processWebpayPayment(customerData);
                
                // Aquí el usuario será redirigido a Webpay
                // Esperar a que se complete la redirección
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }, 1000);
            } catch (error) {
                console.error('Error:', error);
                cart.showNotification('Hubo un error al procesar el pago');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        } else if (customerData.paymentMethod === 'whatsapp') {
            // Preparar resumen de compra para enviar por WhatsApp
            const totals = cart.getCartTotals();
            const itemsList = cart.items
                .map(item => `${item.quantity}x ${item.name} - $${cart.formatPrice(item.price * item.quantity)}`)
                .join('\n');

            const message = `Hola, me gustaría confirmar mi pedido:\n\n${itemsList}\n\nSubtotal: $${cart.formatPrice(totals.subtotal)}\nEnvío: $${cart.formatPrice(totals.shipping)}\nTotal: $${cart.formatPrice(totals.total)}\n\nDatos de entrega:\nNombre: ${customerData.name}\nEmail: ${customerData.email}\nTeléfono: ${customerData.phone}\nDirección: ${customerData.address}\nCiudad: ${customerData.city}`;

            const encodedMessage = encodeURIComponent(message);
            const whatsappLink = `https://wa.me/${PHONE_NUMBER}?text=${encodedMessage}`;
            
            // Cerrar modales y limpiar carrito
            document.getElementById('checkout-modal').classList.remove('active');
            document.getElementById('cart-modal').classList.remove('active');
            cart.clearCart();
            
            cart.showNotification('Abriendo WhatsApp...');
            
            // Abrir WhatsApp
            window.open(whatsappLink, '_blank');
        }
    });

    // Actualizar total en el formulario cuando cambia el carrito
    const observer = new MutationObserver(() => {
        const totals = cart.getCartTotals();
        const formTotal = document.getElementById('form-total');
        if (formTotal) {
            formTotal.textContent = `$${cart.formatPrice(totals.total)}`;
        }
    });

    const cartSummary = document.getElementById('cart-summary');
    if (cartSummary) {
        observer.observe(cartSummary, { childList: true, subtree: true });
    }
}

