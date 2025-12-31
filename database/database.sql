-- ============================================
-- BASE DE DATOS PARA FLORERIA WILDGARDEN
-- Sistema de Transacciones y Órdenes
-- ============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS wildgarden_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wildgarden_db;

-- ============================================
-- TABLA DE TRANSACCIONES
-- ============================================
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(255) UNIQUE NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'CLP',
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20),
    customer_address VARCHAR(500),
    customer_city VARCHAR(100),
    status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    cart_items JSON,
    webpay_token VARCHAR(255),
    webpay_url VARCHAR(512),
    payment_method ENUM('webpay', 'whatsapp', 'transfer') DEFAULT 'webpay',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_order_id (order_id),
    INDEX idx_email (customer_email),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA DE PRODUCTOS
-- ============================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(500),
    category VARCHAR(100),
    stock INT DEFAULT -1, -- -1 = sin control de stock
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_product_id (product_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA DE ITEMS DE ÓRDENES
-- ============================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(255) NOT NULL,
    product_id VARCHAR(50) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    
    FOREIGN KEY (order_id) REFERENCES transactions(order_id),
    INDEX idx_order_id (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA DE LOGS
-- ============================================
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50), -- webpay, error, info, warning
    message TEXT,
    data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_type (type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA DE USUARIOS ADMIN
-- ============================================
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'moderator', 'viewer') DEFAULT 'moderator',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA DE CONFIGURACIÓN
-- ============================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERTAR DATOS INICIALES
-- ============================================

-- Insertar productos de ejemplo
INSERT INTO products (product_id, name, description, price, category) VALUES
('prod-001', 'Mix Floral Pequeño', 'Arreglo compacto y fresco con flores variadas', 23900, 'Arreglos'),
('prod-002', 'Mix Floral del día (S)', 'Composición pequeña con flores frescas del día', 28900, 'Arreglos'),
('prod-003', 'Mix Floral del día (M)', 'Arreglo mediano con variedad de flores frescas', 38900, 'Arreglos'),
('prod-004', 'Mix Floral del Día (L)', 'Arreglo grande con flores frescas seleccionadas', 48900, 'Arreglos'),
('prod-005', 'Mix Floral del Día (XL)', 'Arreglo extra grande con flores premium', 58900, 'Arreglos'),
('prod-006', 'Ramo 8 Girasoles', 'Radiante ramo de 8 girasoles frescos', 32000, 'Ramos');

-- Insertar configuración inicial
INSERT INTO settings (setting_key, setting_value, description) VALUES
('shop_name', 'Floreria Wildgarden', 'Nombre de la tienda'),
('shop_email', 'ventas@wildgardenflores.cl', 'Email de contacto'),
('shop_phone', '+56996744579', 'Teléfono'),
('shipping_cost', '5000', 'Costo de envío en pesos'),
('currency', 'CLP', 'Moneda'),
('webpay_environment', 'test', 'Ambiente: test o production'),
('timezone', 'America/Santiago', 'Zona horaria');

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista: Resumen de ventas diarias
CREATE OR REPLACE VIEW sales_daily AS
SELECT 
    DATE(created_at) as date,
    COUNT(*) as order_count,
    SUM(amount) as total_sales,
    AVG(amount) as avg_order
FROM transactions
WHERE status = 'completed'
GROUP BY DATE(created_at)
ORDER BY date DESC;

-- Vista: Top clientes
CREATE OR REPLACE VIEW top_customers AS
SELECT 
    customer_email,
    customer_name,
    COUNT(*) as order_count,
    SUM(amount) as total_spent
FROM transactions
WHERE status = 'completed'
GROUP BY customer_email
ORDER BY total_spent DESC
LIMIT 10;

-- Vista: Productos más vendidos
CREATE OR REPLACE VIEW top_products AS
SELECT 
    oi.product_name,
    SUM(oi.quantity) as total_sold,
    SUM(oi.subtotal) as total_revenue
FROM order_items oi
JOIN transactions t ON oi.order_id = t.order_id
WHERE t.status = 'completed'
GROUP BY oi.product_name
ORDER BY total_sold DESC;

-- ============================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================

-- Procedimiento: Obtener transacciones del mes
DELIMITER //
CREATE PROCEDURE get_monthly_transactions(IN p_year INT, IN p_month INT)
BEGIN
    SELECT * FROM transactions
    WHERE YEAR(created_at) = p_year 
    AND MONTH(created_at) = p_month
    ORDER BY created_at DESC;
END //
DELIMITER ;

-- Procedimiento: Obtener estadísticas rápidas
DELIMITER //
CREATE PROCEDURE get_dashboard_stats()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM transactions WHERE status = 'completed') as total_orders,
        (SELECT SUM(amount) FROM transactions WHERE status = 'completed') as total_sales,
        (SELECT AVG(amount) FROM transactions WHERE status = 'completed') as avg_order,
        (SELECT COUNT(*) FROM transactions WHERE DATE(created_at) = CURDATE() AND status = 'completed') as today_orders,
        (SELECT SUM(amount) FROM transactions WHERE DATE(created_at) = CURDATE() AND status = 'completed') as today_sales;
END //
DELIMITER ;

-- ============================================
-- CREAR ÍNDICES PARA MEJOR RENDIMIENTO
-- ============================================

-- Si la tabla crece mucho, estos índices ayudan
ALTER TABLE transactions ADD FULLTEXT INDEX idx_fulltext_customer (customer_name, customer_email);

-- ============================================
-- INSERTAR USUARIO ADMIN (CAMBIAR CONTRASEÑA)
-- ============================================

-- Hash SHA256 de "admin123" - CAMBIAR EN PRODUCCIÓN
INSERT INTO admin_users (username, email, password_hash, role) VALUES
('admin', 'admin@wildgardenflores.cl', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'admin');

-- ============================================
-- QUERIES ÚTILES
-- ============================================

-- Ver todas las transacciones
-- SELECT * FROM transactions ORDER BY created_at DESC;

-- Ver transacciones pendientes
-- SELECT * FROM transactions WHERE status = 'pending' ORDER BY created_at DESC;

-- Ver transacciones de hoy
-- SELECT * FROM transactions WHERE DATE(created_at) = CURDATE();

-- Llamar estadísticas
-- CALL get_dashboard_stats();

-- Ver productos más vendidos
-- SELECT * FROM top_products;

-- Ver mejores clientes
-- SELECT * FROM top_customers;

-- ============================================
-- LIMPIEZA Y MANTENIMIENTO
-- ============================================

-- Eliminar transacciones fallidas de más de 30 días
-- DELETE FROM transactions WHERE status = 'failed' AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Optimizar tablas (ejecutar mensualmente)
-- OPTIMIZE TABLE transactions;
-- OPTIMIZE TABLE order_items;
-- OPTIMIZE TABLE logs;

-- ============================================
-- SEGURIDAD
-- ============================================

-- Crear usuario de solo lectura para reportes
-- CREATE USER 'reports_user'@'localhost' IDENTIFIED BY 'strong_password';
-- GRANT SELECT ON wildgarden_db.* TO 'reports_user'@'localhost';

-- Crear usuario para aplicación (lectura/escritura)
-- CREATE USER 'app_user'@'localhost' IDENTIFIED BY 'app_password';
-- GRANT SELECT, INSERT, UPDATE ON wildgarden_db.* TO 'app_user'@'localhost';

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
