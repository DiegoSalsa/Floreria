// Configuración
const PHONE_NUMBER = '56968465270'; // Reemplaza con tu número de WhatsApp (sin símbolos ni espacios)
const BUSINESS_NAME = 'El Jardín De Alejandrino';

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
const buyButtons = document.querySelectorAll('.buy-button');

buyButtons.forEach(button => {
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

// Initial animations
window.addEventListener('load', () => {
    document.body.style.opacity = '1';
});
