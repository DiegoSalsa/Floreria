/**
 * Sistema simple de autenticación con localStorage
 */

// Ejecutar apenas se cargue el DOM
document.addEventListener('DOMContentLoaded', function() {
    updateAuthUI();
});

// También ejecutar cada vez que cambia el focus (vuelve a la pestaña)
window.addEventListener('focus', updateAuthUI);

function updateAuthUI() {
    const loggedIn = localStorage.getItem('user_logged_in') === 'true';
    const userDataStr = localStorage.getItem('user_data');
    
    console.log('Auth Check - Logged In:', loggedIn, 'Data:', userDataStr);
    
    // Obtener elementos
    const userToggle = document.getElementById('user-toggle');
    const adminLink = document.getElementById('admin-link');
    const logoutBtn = document.getElementById('logout-btn');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (!userToggle || !adminLink || !logoutBtn || !userDropdown) {
        console.error('Elementos del menú no encontrados');
        return;
    }
    
    if (loggedIn && userDataStr) {
        try {
            const userData = JSON.parse(userDataStr);
            console.log('Usuario logueado:', userData);
            
            // Cambiar nombre en icono
            const firstName = userData.name ? userData.name.split(' ')[0] : userData.email;
            userToggle.innerHTML = firstName;
            userToggle.style.fontSize = '14px';
            userToggle.style.fontWeight = '600';
            
            // Ocultar login/register
            const loginLink = userDropdown.querySelector('a[href*="login.php"]');
            const registerLink = userDropdown.querySelector('a[href*="register.php"]');
            if (loginLink) loginLink.style.display = 'none';
            if (registerLink) registerLink.style.display = 'none';
            
            // Mostrar logout y admin si corresponde
            logoutBtn.style.display = 'block';
            adminLink.style.display = (userData.role === 'admin') ? 'block' : 'none';
            
        } catch (e) {
            console.error('Error parsing userData:', e);
            clearAuth();
        }
    } else {
        clearAuth();
    }
}

function clearAuth() {
    console.log('No hay usuario logueado');
    
    const userToggle = document.getElementById('user-toggle');
    const adminLink = document.getElementById('admin-link');
    const logoutBtn = document.getElementById('logout-btn');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (userToggle) {
        userToggle.innerHTML = '<i class="fas fa-user-circle"></i>';
        userToggle.style.fontSize = 'inherit';
    }
    
    if (adminLink) adminLink.style.display = 'none';
    if (logoutBtn) logoutBtn.style.display = 'none';
    
    if (userDropdown) {
        const loginLink = userDropdown.querySelector('a[href*="login.php"]');
        const registerLink = userDropdown.querySelector('a[href*="register.php"]');
        if (loginLink) loginLink.style.display = 'block';
        if (registerLink) registerLink.style.display = 'block';
    }
}
