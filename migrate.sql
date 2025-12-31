-- ============================================
-- MIGRACIONES PARA FLORERIA WILDGARDEN
-- Ejecutar manualmente en Railway si es necesario
-- ============================================

-- Actualizar admin password a admin123
UPDATE admin_users 
SET password_hash = '$2y$12$xK6C2Gms4xWPxGr.1L4Feu0EQLrHgPLKpqLLzKQDiJFGVGDlKB3x2' 
WHERE username = 'admin';

-- Verificar que se actualizó
SELECT id, username, email FROM admin_users WHERE username = 'admin';
