/**
 * Verificar estado de autenticación del usuario
 * Actualizar la interfaz según el estado
 */

function checkAuthStatus() {
    // Primero verificar localStorage (más rápido)
    const loggedIn = localStorage.getItem('user_logged_in') === 'true';
    const userData = localStorage.getItem('user_data');
    
    if (loggedIn && userData) {
        try {
            const user = JSON.parse(userData);
            updateUserMenuLogged(user);
            return;
        } catch (e) {
            console.error('Error parsing user data:', e);
            localStorage.removeItem('user_logged_in');
            localStorage.removeItem('user_data');
        }
    }
    
    updateUserMenuLoggedOut();
}

function updateUserMenuLogged(userData) {
    // Ocultar botones de login/registro
    const loginLinks = document.querySelectorAll('.user-dropdown a[href*="login.php"], .user-dropdown a[href*="register.php"]');
    loginLinks.forEach(link => {
        link.style.display = 'none';
    });
    
    // Mostrar botones de admin y logout
    const adminLink = document.getElementById('admin-link');
    const logoutBtn = document.getElementById('logout-btn');
    
    if (adminLink) {
        if (userData.role === 'admin') {
            adminLink.style.display = 'block';
        } else {
            adminLink.style.display = 'none';
        }
    }
    
    if (logoutBtn) {
        logoutBtn.style.display = 'block';
    }
    
    // Cambiar el icono de usuario para mostrar nombre
    const userToggle = document.getElementById('user-toggle');
    if (userToggle) {
        const userName = userData.name || userData.email || 'Usuario';
        userToggle.textContent = `${userName.split(' ')[0]}`;
        userToggle.title = userName;
    }
}

function updateUserMenuLoggedOut() {
    // Mostrar botones de login/registro
    const loginLinks = document.querySelectorAll('.user-dropdown a[href*="login.php"], .user-dropdown a[href*="register.php"]');
    loginLinks.forEach(link => {
        link.style.display = 'block';
    });
    
    // Ocultar botones de admin y logout
    const adminLink = document.getElementById('admin-link');
    const logoutBtn = document.getElementById('logout-btn');
    
    if (adminLink) adminLink.style.display = 'none';
    if (logoutBtn) logoutBtn.style.display = 'none';
    
    // Restaurar icono de usuario
    const userToggle = document.getElementById('user-toggle');
    if (userToggle) {
        userToggle.innerHTML = '<i class="fas fa-user-circle"></i>';
        userToggle.title = 'Menú de usuario';
    }
}

// Verificar autenticación cuando carga la página
document.addEventListener('DOMContentLoaded', () => {
    checkAuthStatus();
    
    // Re-verificar cada 10 segundos
    setInterval(checkAuthStatus, 10000);
});
