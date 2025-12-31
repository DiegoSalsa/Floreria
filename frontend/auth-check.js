/**
 * Verificar autenticación desde localStorage
 */

document.addEventListener('DOMContentLoaded', function() {
    checkAuthFromLocalStorage();
});

// Re-verificar cuando vuelva el focus
window.addEventListener('focus', checkAuthFromLocalStorage);

function checkAuthFromLocalStorage() {
    const token = localStorage.getItem('auth_token');
    const email = localStorage.getItem('user_email');
    const name = localStorage.getItem('user_name');
    const role = localStorage.getItem('user_role');
    
    console.log('Auth check - Token:', token ? 'exists' : 'none', 'Email:', email);
    
    if (token && email) {
        updateAuthUI({
            email: email,
            name: name || '',
            role: role || 'customer'
        });
    } else {
        clearAuthUI();
    }
}

function updateAuthUI(userData) {
    console.log('Actualizando UI con usuario:', userData);
    
    const userToggle = document.getElementById('user-toggle');
    const adminLink = document.getElementById('admin-link');
    const logoutBtn = document.getElementById('logout-btn');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (!userToggle || !adminLink || !logoutBtn || !userDropdown) {
        console.error('Elementos del menú no encontrados');
        return;
    }
    
    // Cambiar nombre en icono
    const firstName = userData.name ? userData.name.split(' ')[0] : userData.email;
    userToggle.innerHTML = firstName;
    userToggle.style.fontSize = '14px';
    userToggle.style.fontWeight = '600';
    userToggle.style.color = '#1B4332';
    
    // Ocultar login/register
    const loginLink = userDropdown.querySelector('a[href*="login.php"]');
    const registerLink = userDropdown.querySelector('a[href*="register.php"]');
    if (loginLink) loginLink.style.display = 'none';
    if (registerLink) registerLink.style.display = 'none';
    
    // Mostrar logout y admin si corresponde
    logoutBtn.style.display = 'block';
    adminLink.style.display = (userData.role === 'admin') ? 'block' : 'none';
    
    console.log('UI actualizada - Usuario:', userData.email);
}

function clearAuthUI() {
    console.log('Limpiando UI - No hay usuario');
    
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
