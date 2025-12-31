/**
 * Verificar estado de autenticación del usuario
 * Actualizar la interfaz según el estado
 */

async function checkAuthStatus() {
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
        }
    }
    
    // Si no hay en localStorage, verificar con el backend
    try {
        const response = await fetch('https://floreria-wildgarden.onrender.com/check-auth.php', {
            method: 'GET',
            credentials: 'include'
        });
        
        const data = await response.json();
        
        if (data.logged_in) {
            // Usuario logueado - guardar en localStorage
            localStorage.setItem('user_logged_in', 'true');
            localStorage.setItem('user_data', JSON.stringify({
                email: data.user_email,
                name: data.user_name,
                role: data.user_role
            }));
            updateUserMenuLogged(data);
        } else {
            // Usuario no logueado - limpiar localStorage
            localStorage.removeItem('user_logged_in');
            localStorage.removeItem('user_data');
            updateUserMenuLoggedOut();
        }
    } catch (error) {
        console.error('Error checking auth:', error);
        // En caso de error, revisar localStorage
        if (loggedIn && userData) {
            updateUserMenuLogged(JSON.parse(userData));
        } else {
            updateUserMenuLoggedOut();
        }
    }
}

function updateUserMenuLogged(userData) {
    const userDropdown = document.getElementById('user-dropdown');
    if (!userDropdown) return;
    
    const userName = userData.name || userData.user_name || userData.email;
    const userEmail = userData.email || userData.user_email;
    
    userDropdown.innerHTML = `
        <div class="user-info" style="padding: 10px; border-bottom: 1px solid #eee;">
            <p style="margin: 0; font-weight: 600;">👤 ${userName}</p>
            <small style="color: #666;">${userEmail}</small>
        </div>
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
