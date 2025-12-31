// Auth Modal Functions

function openLoginModal(e) {
    e.preventDefault();
    document.getElementById('loginModal').style.display = 'flex';
    document.getElementById('loginModal').style.alignItems = 'center';
    document.getElementById('loginModal').style.justifyContent = 'center';
}

function closeLoginModal() {
    document.getElementById('loginModal').style.display = 'none';
}

function openRegisterModal(e) {
    e.preventDefault();
    document.getElementById('registerModal').style.display = 'flex';
    document.getElementById('registerModal').style.alignItems = 'center';
    document.getElementById('registerModal').style.justifyContent = 'center';
}

function closeRegisterModal() {
    document.getElementById('registerModal').style.display = 'none';
}

function switchToLogin(e) {
    e.preventDefault();
    closeRegisterModal();
    openLoginModal({preventDefault: () => {}});
}

function switchToRegister(e) {
    e.preventDefault();
    closeLoginModal();
    openRegisterModal({preventDefault: () => {}});
}

async function handleLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    
    try {
        const response = await fetch('https://floreria-wildgarden.onrender.com/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                email: email,
                password: password
            })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            closeLoginModal();
            // Mostrar notificación
            showNotification('¡Sesión iniciada correctamente!', 'success');
            // Actualizar UI después de un pequeño delay
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            showNotification(data.message || 'Error al iniciar sesión', 'error');
        }
    } catch (error) {
        console.error('Login error:', error);
        showNotification('Error al conectar con el servidor', 'error');
    }
}

async function handleRegister(e) {
    e.preventDefault();
    
    const name = document.getElementById('registerName').value;
    const email = document.getElementById('registerEmail').value;
    const password = document.getElementById('registerPassword').value;
    const phone = document.getElementById('registerPhone').value;
    
    try {
        const response = await fetch('https://floreria-wildgarden.onrender.com/register.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                name: name,
                email: email,
                password: password,
                phone: phone
            })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            closeRegisterModal();
            showNotification('¡Cuenta creada correctamente! Iniciando sesión...', 'success');
            // Actualizar UI después de un pequeño delay
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            showNotification(data.message || 'Error al crear cuenta', 'error');
        }
    } catch (error) {
        console.error('Register error:', error);
        showNotification('Error al conectar con el servidor', 'error');
    }
}

async function logout(e) {
    e.preventDefault();
    
    try {
        const response = await fetch('https://floreria-wildgarden.onrender.com/logout.php', {
            method: 'GET',
            credentials: 'include'
        });
        
        if (response.ok) {
            showNotification('Sesión cerrada', 'success');
            setTimeout(() => {
                location.reload();
            }, 500);
        }
    } catch (error) {
        console.error('Logout error:', error);
        showNotification('Error al cerrar sesión', 'error');
    }
}

function showNotification(message, type = 'info') {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 10000;
        animation: slideIn 0.3s ease-in-out;
    `;
    
    container.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Cerrar modales al clickear fuera
window.onclick = function(e) {
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    
    if (e.target === loginModal) {
        closeLoginModal();
    }
    if (e.target === registerModal) {
        closeRegisterModal();
    }
}

// Estilos para los modales
const style = document.createElement('style');
style.textContent = `
.modal {
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex !important;
}

.modal-content {
    background-color: #fefefe;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    animation: slideDown 0.3s ease-in-out;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: #4B6145;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-group input:focus {
    outline: none;
    border-color: #4B6145;
    box-shadow: 0 0 5px rgba(75, 107, 69, 0.3);
}

.full-width {
    width: 100%;
}

@keyframes slideDown {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(400px);
        opacity: 0;
    }
}
`;
document.head.appendChild(style);
