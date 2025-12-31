/**
 * Verificar estado de autenticación del usuario
 * Actualizar la interfaz según el estado
 */

async function checkAuthStatus() {
    try {
        const response = await fetch('https://floreria-wildgarden.onrender.com/check-auth.php', {
            method: 'GET',
            credentials: 'include'
        });
        
        const data = await response.json();
        
        if (data.logged_in) {
            // Usuario logueado
            updateUserMenuLogged(data);
        } else {
            // Usuario no logueado
            updateUserMenuLoggedOut();
        }
    } catch (error) {
        console.error('Error checking auth:', error);
        updateUserMenuLoggedOut();
    }
}

function updateUserMenuLogged(userData) {
    const userDropdown = document.getElementById('user-dropdown');
    if (!userDropdown) return;
    
    const userName = userData.user_name || userData.user_email;
    
    userDropdown.innerHTML = `
        <div class="user-info">
            <p>👤 ${userName}</p>
            <small>${userData.user_email}</small>
        </div>
        <hr style="margin: 8px 0;">
        <a href="https://floreria-wildgarden.onrender.com/my-account.php">Mi Cuenta</a>
        <a href="https://floreria-wildgarden.onrender.com/logout.php">Cerrar Sesión</a>
    `;
}

function updateUserMenuLoggedOut() {
    const userDropdown = document.getElementById('user-dropdown');
    if (!userDropdown) return;
    
    userDropdown.innerHTML = `
        <a href="https://floreria-wildgarden.onrender.com/login.php">👤 Iniciar Sesión</a>
        <a href="https://floreria-wildgarden.onrender.com/register.php">✏️ Registrarse</a>
    `;
}

// Verificar autenticación cuando carga la página
document.addEventListener('DOMContentLoaded', () => {
    checkAuthStatus();
    
    // Re-verificar cada 30 segundos
    setInterval(checkAuthStatus, 30000);
});
