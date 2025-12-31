/**
 * Verificar estado de autenticación del usuario
 * Lee datos de localStorage y actualiza UI
 */

function checkAuthStatus() {
    const loggedIn = localStorage.getItem('user_logged_in') === 'true';
    
    if (!loggedIn) {
        updateUserMenuLoggedOut();
        return;
    }
    
    try {
        const userDataStr = localStorage.getItem('user_data');
        if (!userDataStr) {
            updateUserMenuLoggedOut();
            return;
        }
        
        const userData = JSON.parse(userDataStr);
        updateUserMenuLogged(userData);
    } catch (e) {
        console.error('Error parsing user data:', e);
        updateUserMenuLoggedOut();
    }
}

function updateUserMenuLogged(userData) {
    // Ocultar botones de login/registro
    document.querySelectorAll('.user-dropdown a[href*="login.php"], .user-dropdown a[href*="register.php"]').forEach(link => {
        link.style.display = 'none';
    });
    
    // Mostrar botones de admin (si es admin) y logout
    const adminLink = document.getElementById('admin-link');
    const logoutBtn = document.getElementById('logout-btn');
    
    if (adminLink) {
        adminLink.style.display = (userData.role === 'admin') ? 'block' : 'none';
    }
    if (logoutBtn) {
        logoutBtn.style.display = 'block';
    }
    
    // Actualizar nombre de usuario en el icono
    const userToggle = document.getElementById('user-toggle');
    if (userToggle) {
        const firstName = userData.name ? userData.name.split(' ')[0] : userData.email;
        userToggle.innerHTML = firstName;
        userToggle.style.fontSize = '12px';
    }
    
    console.log('Usuario logueado:', userData.email);
}

function updateUserMenuLoggedOut() {
    // Mostrar botones de login/registro
    document.querySelectorAll('.user-dropdown a[href*="login.php"], .user-dropdown a[href*="register.php"]').forEach(link => {
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
    }
    
    console.log('Usuario no logueado');
}

// Ejecutar al cargar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', checkAuthStatus);
} else {
    checkAuthStatus();
}

// Re-verificar cada 5 segundos
setInterval(checkAuthStatus, 5000);
