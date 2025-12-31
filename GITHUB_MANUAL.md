# 📤 Instrucciones Manuales para Subir a GitHub

Si el push automático no funcionó, sigue estos pasos manualmente:

## 1️⃣ Abre PowerShell o CMD en la carpeta del proyecto

```powershell
cd c:\Users\diego\Desktop\Floreria
```

## 2️⃣ Configura git (si no lo hiciste)

```powershell
git config --global user.email "tu_email@gmail.com"
git config --global user.name "Diego Salsa"
```

## 3️⃣ Inicializa el repositorio

```powershell
git init
```

## 4️⃣ Agrega todos los archivos

```powershell
git add .
```

## 5️⃣ Haz el commit inicial

```powershell
git commit -m "Floreria Wildgarden - E-commerce completo con autenticación, carrito y Webpay"
```

## 6️⃣ Renombra la rama a main

```powershell
git branch -M main
```

## 7️⃣ Agrega el repositorio remoto

```powershell
git remote add origin https://github.com/DiegoSalsa/WildGarden.git
```

## 8️⃣ Haz push a GitHub

```powershell
git push -u origin main
```

**Si pide contraseña:**
- GitHub ya no acepta contraseña normal
- Necesitas un **Personal Access Token**
- Ve a: https://github.com/settings/tokens
- Crea uno nuevo con permisos `repo`
- Usa el token como contraseña

## 9️⃣ Verifica que se subió

```powershell
git remote -v
```

Deberías ver:
```
origin  https://github.com/DiegoSalsa/WildGarden.git (fetch)
origin  https://github.com/DiegoSalsa/WildGarden.git (push)
```

---

## ✅ Listo

Una vez que veas los archivos en GitHub, estás listo para:
1. Railway - PostgreSQL
2. Render - Backend
3. Vercel - Frontend

¿Necesitas ayuda con algún paso?
