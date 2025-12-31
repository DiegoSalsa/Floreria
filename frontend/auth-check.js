/**
 * Verificar autenticación llamando al backend
 */

document.addEventListener('DOMContentLoaded', function() {
    checkAuthFromBackend();
});

// Re-verificar cuando vuelva el focus (activa pestaña)
window.addEventListener('focus', checkAuthFromBackend);

// Re-verificar cada 30 segundos
setInterval(checkAuthFromBackend, 30000);

async function checkAuthFromBackend() {
    try {
        const response = await fetch('https://floreria-wildgarden.onrender.com/check-auth.php', {
            method: 'GET',
            credentials: 'include', // Enviar cookies
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        console.log('Backend auth response:', data);
        
        if (data.logged_in) {
            updateAuthUI({
                email: data.user_email,
                name: data.user_name,
                role: data.user_role
            });
        } else {
            clearAuthUI();
        }
    } catch (error) {
        console.error('Error checking auth:', error);
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
