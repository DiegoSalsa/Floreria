# 🌹 Floreria Wildgarden - Tienda Online

Tienda online de flores con autenticación de usuarios, carrito de compras e integración con Webpay para pagos.

---

## 📋 Descripción General

Sistema completo de e-commerce para una florería que incluye:

- **Frontend:** Sitio responsive con carrito de compras
- **Backend:** API PHP con autenticación y gestión de usuarios
- **Base de Datos:** PostgreSQL/MySQL para usuarios, órdenes y transacciones
- **Pagos:** Integración con Webpay para procesar tarjetas de crédito
- **Emails:** Confirmaciones automáticas de compra y verificación de cuenta

---

## 🏗️ Estructura del Proyecto

```
floreria/
├── frontend/              # HTML, CSS, JavaScript del sitio
│   ├── index.html
│   ├── styles.css
│   ├── script.js
│   ├── cart.js
│   ├── robots.txt
│   └── sitemap.xml
│
├── backend/              # PHP - Lógica de servidor
│   ├── auth-config.php          # Configuración de autenticación y emails
│   ├── login.php                # Login de usuarios
│   ├── register.php             # Registro de nuevos usuarios
│   ├── logout.php               # Cerrar sesión
│   ├── verify-email.php         # Verificación de email
│   ├── my-account.php           # Perfil del usuario
│   ├── admin-dashboard.php      # Panel de administrador
│   ├── manage-users.php         # Gestión de usuarios (admin)
│   ├── payment-success.php      # Página de pago exitoso
│   ├── payment-failure.php      # Página de pago fallido
│   ├── payment-cancel.php       # Página de pago cancelado
│   ├── api_webpay_create_transaction.php  # Crear transacción Webpay
│   ├── webpay-config.php        # Configuración de Webpay
│   ├── webpay-response.php      # Respuesta de Webpay
│   ├── servidor_simple.php      # Servidor local (desarrollo)
│   ├── mailer.php               # Sistema de emails
│   └── .htaccess                # Configuración Apache
│
├── database/             # Base de datos
│   └── database.sql      # Schema y estructura
│
├── users/                # Almacenamiento de usuarios (JSON)
│   ├── admin_demo.json
│   └── customer_demo.json
│
├── sessions/             # Sesiones de usuarios
├── logs/                 # Logs de actividad
│
└── README.md             # Este archivo
```

---

## 🚀 Inicio Rápido

### Desarrollo Local (PHP)

```bash
# Requiere PHP 7.4+
cd backend/
php -S localhost:8000
# Accede a http://localhost:8000/
```

### Credenciales de Prueba

```
Email:      admin@wildgarden.com
Contraseña: password123
Rol:        Admin (acceso a panel administrativo)
```

```
Email:      cliente@ejemplo.com
Contraseña: password123
Rol:        Customer (cliente normal)
```

---

## 🔐 Autenticación

### Crear Usuario

```
POST /backend/register.php
- email: usuario@ejemplo.com
- name: Nombre Completo
- password: contraseña (mín 6 caracteres)
```

### Login

```
POST /backend/login.php
- email: usuario@ejemplo.com
- password: contraseña
```

---

## 📧 Emails Automáticos

El sistema envía 3 tipos de emails:

### 1. Verificación de Registro
- **Cuándo:** Nuevo usuario se registra
- **Contenido:** Link para verificar email

### 2. Bienvenida Admin
- **Cuándo:** Admin crea otro admin
- **Contenido:** Credenciales temporales

### 3. Confirmación de Compra
- **Cuándo:** Pago completado
- **Contenido:** Detalles de orden, productos, total

**Configuración SMTP** en `backend/auth-config.php`:
```php
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'tu_email@gmail.com');
define('MAIL_PASSWORD', 'tu_app_password');
```

---

## 💳 Webpay Integration

### Configuración

Editar `backend/webpay-config.php`:
```php
define('WEBPAY_COMMERCE_CODE', 'TU_CODIGO');
define('WEBPAY_API_KEY', 'TU_API_KEY');
define('WEBPAY_ENVIRONMENT', 'test'); // o 'production'
```

### Tarjetas de Prueba

```
Éxito:     4051885600446623
Rechazo:   4051885600446631
```

---

## 📊 Base de Datos

### Configuración

Editar `backend/auth-config.php`:
```php
define('USE_DATABASE', true); // false = JSON, true = MySQL/PostgreSQL
define('DB_HOST', 'localhost');
define('DB_USER', 'usuario');
define('DB_PASSWORD', 'password');
define('DB_NAME', 'floreria_db');
```

### Crear BD

```bash
mysql -u usuario -p floreria_db < database/database.sql
```

---

## 🔒 Seguridad

- ✅ Contraseñas hasheadas con bcrypt (cost 12)
- ✅ Sesiones con timeout (1 hora)
- ✅ Validación de input en todas partes
- ✅ Prevención de SQL injection
- ✅ Email verification obligatoria
- ✅ Logs de actividad completos
- ✅ HTTPS recomendado en producción

---

## 🌐 Despliegue en Producción (Railway + Render + Vercel)

### 1️⃣ Railway - Base de Datos PostgreSQL

```bash
# En railway.app:
1. Crea nuevo proyecto
2. Agrega "PostgreSQL"
3. Copia DATABASE_URL (la usarás en Render)
4. Ejecuta el schema:
   psql $DATABASE_URL < database/database.sql
```

### 2️⃣ Render - Backend PHP

```bash
# En render.com:
1. Conecta tu repositorio Git
2. Nuevo "Web Service"
3. Selecciona "Docker"
4. Agrega variables de entorno (ver .env.example):
   - APP_URL: https://floreria-backend.onrender.com
   - DB_HOST: tu-railway-host.railway.app
   - DB_USER: postgres
   - DB_PASSWORD: [de Railway]
   - DB_NAME: railway
   - MAIL_USERNAME, MAIL_PASSWORD, etc
   - WEBPAY_COMMERCE_CODE, WEBPAY_API_KEY
5. Deploy
```

### 3️⃣ Vercel - Frontend

```bash
# En vercel.com:
1. Conecta tu repositorio
2. Root Directory: ./frontend
3. Deploy
4. Tu sitio estará en: https://tu-proyecto.vercel.app
```

### 4️⃣ Conectar Frontend ↔ Backend

En `frontend/script.js`, actualiza la URL de la API:

```javascript
const API_BASE_URL = 'https://floreria-backend.onrender.com/backend';
```

---

### Variables de Entorno Completas

Copia estas variables en el dashboard de **Render**:

```
APP_URL=https://floreria-backend.onrender.com
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_app_password
MAIL_FROM=noreply@wildgardenflores.cl
MAIL_FROM_NAME=Floreria Wildgarden

USE_DATABASE=true
DB_HOST=tu-railway-host.railway.app
DB_PORT=5432
DB_USER=postgres
DB_PASSWORD=tu_railway_password
DB_NAME=railway

WEBPAY_COMMERCE_CODE=597055555532
WEBPAY_API_KEY=tu_api_key
WEBPAY_ENVIRONMENT=production
DEBUG_MODE=false
```

---

### URLs Finales

- **Frontend:** https://tu-proyecto.vercel.app
- **Backend API:** https://floreria-backend.onrender.com/backend
- **Base de Datos:** PostgreSQL en Railway

---

### Costos

- **Railway PostgreSQL:** ~$5/mes (gratis si < 100 horas/mes)
- **Render Backend:** Gratis (con limitaciones: inactividad, RAM limitada)
- **Vercel Frontend:** Gratis

**Total: Gratis o $5/mes** ✅

---

## 📁 Directorios Dinámicos

Crear en producción:
```bash
mkdir -p users sessions logs
chmod 755 users sessions logs
```

---

## 🛠️ Archivos Clave

| Archivo | Función |
|---------|---------|
| `frontend/index.html` | Página principal |
| `frontend/cart.js` | Lógica de carrito |
| `backend/auth-config.php` | Configuración central |
| `backend/admin-dashboard.php` | Panel de admin |
| `backend/api_webpay_create_transaction.php` | API Webpay |
| `database/database.sql` | Schema de BD |

---

## 🧪 Testing

### Flujo Completo

1. **Registro:** http://localhost:8000/register.php
2. **Verificar email:** Buscar en logs o email
3. **Login:** http://localhost:8000/login.php
4. **Comprar:** Agregar productos al carrito
5. **Checkout:** Completar formulario de compra
6. **Pago:** Seleccionar Webpay o WhatsApp
7. **Confirmación:** Recibir email de orden

---

## 📞 URLs Principales

```
Sitio principal:        /
Login:                  /backend/login.php
Registro:               /backend/register.php
Mi cuenta:              /backend/my-account.php
Admin panel:            /backend/admin-dashboard.php
Gestión usuarios:       /backend/manage-users.php
Pago exitoso:           /backend/payment-success.php
Pago fallido:           /backend/payment-failure.php
```

---

## 🔄 Flujos de Datos

### Registro
```
Usuario → /register.php → Hash contraseña → BD/JSON → Email verificación
```

### Login
```
Usuario → /login.php → Validar → Crear sesión → Redirect
```

### Compra
```
Carrito → Checkout → /api_webpay_create_transaction.php → Webpay → payment-success.php → Email
```

---

## 🎯 Características

### Frontend
- ✅ Diseño responsive mobile-first
- ✅ Carrito de compras con localStorage
- ✅ Menú de usuario dropdown
- ✅ SEO optimizado
- ✅ Performance optimizado

### Backend
- ✅ Autenticación segura (bcrypt)
- ✅ Gestión de sesiones
- ✅ Emails automáticos
- ✅ Logs de actividad
- ✅ Manejo de errores

### Base de Datos
- ✅ Soporte JSON y MySQL/PostgreSQL
- ✅ Backups automáticos (en cloud)
- ✅ Índices optimizados
- ✅ Queries preparadas

---

## 🚨 Troubleshooting

### Login no funciona
- **Causa:** No tienes PHP instalado
- **Solución:** Instalar XAMPP o usar Railway en producción

### Emails no se envían
- **Causa:** SMTP no configurado
- **Solución:** Editar `backend/auth-config.php` con credenciales Gmail

### 404 en backend
- **Causa:** Archivos en carpeta equivocada
- **Solución:** Asegurar estructura `backend/` y `frontend/`

---

## 📦 Dependencias

- **PHP:** 7.4 o superior
- **MySQL/PostgreSQL:** 5.7+
- **Python:** 3.7+ (solo para desarrollo local)
- **Navegadores:** Todos modernos (Chrome, Firefox, Safari, Edge)

---

## 📄 Licencia

Proyecto privado para Floreria Wildgarden.

---

## 👤 Autor

Desarrollado como sistema de e-commerce personalizado para Floreria Wildgarden.

---

## 🎉 Características Futuras

- [ ] Historial de compras del cliente
- [ ] Sistema de cupones/descuentos
- [ ] Notificaciones push
- [ ] Chat en vivo
- [ ] Aplicación móvil
- [ ] Integración con más formas de pago

---

**Última actualización:** Enero 2025
**Versión:** 1.0
**Status:** Listo para producción
