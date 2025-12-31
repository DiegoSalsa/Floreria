@echo off
cd /d "c:\Users\diego\Desktop\Floreria"

echo === Verificando git ===
git --version

echo.
echo === Configurando usuario ===
git config --global user.email "diego@ejemplo.com"
git config --global user.name "Diego Salsa"

echo.
echo === Estado del repositorio ===
git status

echo.
echo === Verificando remoto ===
git remote -v

echo.
echo === Haciendo push ===
git push -u origin main

echo.
echo === Finalizado ===
pause
