# IMPORTANTE: Configuración para Render + Railway
# 
# Este archivo explica cómo configurar el stack completo

## 🗄️ RAILWAY - Base de Datos PostgreSQL

1. Entra a railway.app
2. Crea un nuevo proyecto
3. Agrega servicio "PostgreSQL"
4. Copia la `DATABASE_URL` generada
5. Ejecuta el schema:
   ```
   psql $DATABASE_URL < database/database.sql
   ```

**Variables de Railway a usar en Render:**
- `DATABASE_URL`

---

## 🔧 RENDER - Backend PHP

1. Entra a render.com
2. Conecta tu repositorio Git
3. Crea nuevo "Web Service"
4. Selecciona "Docker" como entorno
5. Agrega variables de entorno:

```
APP_URL=https://tu-backend-render.onrender.com
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_app_password
WEBPAY_COMMERCE_CODE=597055555532
WEBPAY_API_KEY=tu_api_key
WEBPAY_ENVIRONMENT=production
DEBUG_MODE=false
USE_DATABASE=true
DB_HOST=tu-railway-db.railway.app
DB_USER=postgres
DB_PASSWORD=tu_password_railway
DB_NAME=railway
DB_PORT=5432
```

6. Deploy

---

## 🎨 VERCEL - Frontend

1. Entra a vercel.com
2. Conecta tu repositorio (carpeta `/frontend/`)
3. Configura la raíz del proyecto en `/frontend/`
4. Deploy automático

**Actualiza el archivo `frontend/script.js` para apuntar a Render:**

```javascript
const API_BASE_URL = 'https://tu-backend-render.onrender.com/backend';
```

---

## 📋 Configuración en Detalle

### Railway PostgreSQL
- URL: `postgresql://user:pass@host:port/dbname`
- Se obtiene automáticamente al crear la BD

### Render PHP
- Soporta PHP nativamente
- Lee las variables de entorno desde el dashboard
- El Procfile ejecuta el servidor PHP

### Vercel Frontend
- Sirve contenido estático
- Las llamadas a API van a Render

---

## ✅ Checklist Final

- [ ] Railway: PostgreSQL creada y schema ejecutado
- [ ] Railway: DATABASE_URL copiada
- [ ] Render: Repositorio conectado
- [ ] Render: Variables de entorno configuradas
- [ ] Render: Deploy exitoso
- [ ] Vercel: Repositorio conectado a rama `main`
- [ ] Vercel: API_BASE_URL actualizado en `script.js`
- [ ] Vercel: Deploy exitoso
- [ ] Test: Registrarse en https://tu-vercel.app
- [ ] Test: Login y compra

---

## 🚨 Importante: JSON vs PostgreSQL

**Para Render:**
- NO uses guardar en JSON (archivos se pierden)
- SIEMPRE usa PostgreSQL (DATABASE_URL)
- En `.env`, define: `USE_DATABASE=true`

**En el código:**
- `USE_DATABASE=true` → Usa PostgreSQL
- `USE_DATABASE=false` → Usa JSON (solo local)

---

## URLs Finales

- Frontend: `https://tu-vercel.app`
- Backend API: `https://tu-backend-render.onrender.com/backend`
- PostgreSQL: `postgresql://...@tu-railway-db.railway.app`

---

## Costos

- **Railway PostgreSQL:** $5/mes (o gratis si no usas mucho)
- **Render Backend:** Gratis (con limitaciones)
- **Vercel Frontend:** Gratis

**Total: ~$5/mes o GRATIS** ✅
