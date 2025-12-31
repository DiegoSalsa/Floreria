# FLORERIA WILDGARDEN - GUÍA DE FUNCIONAMIENTO

## ✅ SISTEMA FUNCIONANDO

### 1. REGISTRO Y LOGIN
- **Registro**: Usuario se crea automáticamente activo (sin verificación de email)
- **Login**: Funciona correctamente sin pedir verificación
- **Rutas**: `/register.php` y `/login.php`

### 2. BASE DE DATOS
- **PostgreSQL en Railway**: Conectado y funcionando
- **Tablas**: transactions, products, admin_users, etc.
- **Variables de entorno en Render**: DB_HOST, DB_USER, DB_PASSWORD, DB_NAME

### 3. WEBPAY
- **Flujo simple**: El usuario completa su carrito
- **Pago**: Click en "Comprar" → Redirige a https://www.webpay.cl/form-pay/197981
- **Confirmación**: El usuario complete el pago manualmente y luego contacta para confirmación
- **Credenciales**: Cargadas desde variables de entorno en Render

### 4. FRONTEND
- **URL**: Será servido desde Vercel (cuando hagas deploy)
- **Sin Render URLs**: El frontend usa rutas locales `/login.php`, `/register.php`
- **Carrito**: Guarda órdenes en localStorage

## 🧪 TESTING

### Verificar que todo está listo:
```bash
# Acceder a:
https://floreria-wildgarden.onrender.com/test-config.php

# Debe mostrar:
- database_config: OK
- webpay_config: OK
- email_config: OK
- database_connection: OK
```

## 🔧 CAMBIOS RECIENTES

### Commit: 00809d0
- WebPay ahora redirige directamente al form funcional
- Agregado endpoint de test para verificar configuración

### Commit: a147f1d
- Removido requisito de verificación de email
- Frontend usa rutas locales, no Render URLs
- is_active = true al registrarse

### Commit: 0e06849
- Directorio de logs se crea automáticamente
- Mejor manejo de errores en Webpay

### Commit: b5c39c9
- load-env.php incluido en webpay-config.php
- Auto-redirección después de registro

## 📱 FLUJO DE USUARIO

1. **Registro**
   - Usuario va a `/register.php`
   - Completa formulario
   - Se registra automáticamente (sin verificación)
   - Redirecciona a login después de 3 segundos

2. **Login**
   - Usuario va a `/login.php`
   - Entra con sus credenciales
   - Accede al sitio

3. **Compra**
   - Usuario agrega productos al carrito
   - Hace click en "Comprar Ahora"
   - Se llena formulario de envío
   - Se redirige a https://www.webpay.cl/form-pay/197981
   - Usuario completa pago
   - Contacta por WhatsApp para confirmación

## 🚀 PRÓXIMOS PASOS

1. **Deployar a Vercel** (frontend)
   - Las rutas locales funcionar

2. **Rediseño visual** (después que funcione todo)
   - Similar a acaciaflores.cl
   - Enfoque en arreglos de iglesias

3. **Email de confirmación** (opcional)
   - Cuando usuario complete pago
   - Notificación al admin

## 🔒 SEGURIDAD

- ✅ Variables de entorno en Render (no en git)
- ✅ .gitignore protege .env
- ✅ Credenciales de Webpay seguras
- ✅ Brevo API para emails

## 📞 SOPORTE

**Problemas a verificar:**
- Si login pide email: Busca `is_active` en auth-config.php
- Si Webpay sin credenciales: Verifica load-env.php está incluido
- Si BD no conecta: Verifica variables de entorno en Render

**Endpoint de debug:**
```
https://floreria-wildgarden.onrender.com/debug-webpay.php
```

---

**Estado**: ✅ FUNCIONANDO - Listo para design
**Última actualización**: 2025-12-30
