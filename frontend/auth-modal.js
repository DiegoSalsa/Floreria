// Auth Modal Functions

function openLoginModal(e) {
    e.preventDefault();
    document.getElementById('loginModal').classList.add('active');
}

function closeLoginModal() {
    document.getElementById('loginModal').classList.remove('active');
}

function openRegisterModal(e) {
    e.preventDefault();
    document.getElementById('registerModal').classList.add('active');
}

function closeRegisterModal() {
    document.getElementById('registerModal').classList.remove('active');
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
            method: 'GET'
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
