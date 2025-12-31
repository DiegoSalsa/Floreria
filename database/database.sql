-- ============================================
-- BASE DE DATOS PARA FLORERIA WILDGARDEN
-- PostgreSQL Schema
-- ============================================

-- Crear extensiones
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ============================================
-- TABLA DE TRANSACCIONES
-- ============================================
CREATE TABLE IF NOT EXISTS transactions (
    id SERIAL PRIMARY KEY,
    order_id VARCHAR(255) UNIQUE NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'CLP',
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20),
    customer_address VARCHAR(500),
    customer_city VARCHAR(100),
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'completed', 'failed', 'cancelled')),
    cart_items JSONB,
    webpay_token VARCHAR(255),
    webpay_url VARCHAR(512),
    payment_method VARCHAR(20) DEFAULT 'webpay' CHECK (payment_method IN ('webpay', 'whatsapp', 'transfer')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_transactions_order_id ON transactions(order_id);
CREATE INDEX idx_transactions_email ON transactions(customer_email);
CREATE INDEX idx_transactions_status ON transactions(status);
CREATE INDEX idx_transactions_created ON transactions(created_at);

-- ============================================
-- TABLA DE PRODUCTOS
-- ============================================
CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    product_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(500),
    category VARCHAR(100),
    stock INT DEFAULT -1,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_products_product_id ON products(product_id);
CREATE INDEX idx_products_active ON products(is_active);

-- ============================================
-- TABLA DE ITEMS DE ÓRDENES
-- ============================================
CREATE TABLE IF NOT EXISTS order_items (
    id SERIAL PRIMARY KEY,
    order_id VARCHAR(255) NOT NULL,
    product_id VARCHAR(50) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    
    CONSTRAINT fk_order_id FOREIGN KEY (order_id) REFERENCES transactions(order_id) ON DELETE CASCADE
);

CREATE INDEX idx_order_items_order_id ON order_items(order_id);

-- ============================================
-- TABLA DE LOGS
-- ============================================
CREATE TABLE IF NOT EXISTS logs (
    id SERIAL PRIMARY KEY,
    type VARCHAR(50),
    message TEXT,
    data JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_logs_type ON logs(type);
CREATE INDEX idx_logs_created ON logs(created_at);

-- ============================================
-- TABLA DE USUARIOS CLIENTES
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100),
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address VARCHAR(500),
    city VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT TRUE,
    verification_token VARCHAR(255),
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_active ON users(is_active);

-- ============================================
-- TABLA DE USUARIOS ADMIN
-- ============================================
CREATE TABLE IF NOT EXISTS admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'moderator' CHECK (role IN ('admin', 'moderator', 'viewer')),
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_admin_username ON admin_users(username);
CREATE INDEX idx_admin_email ON admin_users(email);
CREATE INDEX idx_admin_active ON admin_users(is_active);

-- ============================================
-- TABLA DE CONFIGURACIÓN
-- ============================================
CREATE TABLE IF NOT EXISTS settings (
    id SERIAL PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_settings_key ON settings(setting_key);

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
GROUP BY customer_email, customer_name
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
-- PROCEDIMIENTOS ALMACENADOS (PostgreSQL Functions)
-- ============================================

-- Función: Obtener transacciones del mes
CREATE OR REPLACE FUNCTION get_monthly_transactions(p_year INT, p_month INT)
RETURNS TABLE(id INT, order_id VARCHAR, amount DECIMAL, currency VARCHAR, customer_name VARCHAR, 
              customer_email VARCHAR, customer_phone VARCHAR, customer_address VARCHAR, customer_city VARCHAR,
              status VARCHAR, cart_items JSONB, webpay_token VARCHAR, webpay_url VARCHAR, 
              payment_method VARCHAR, created_at TIMESTAMP, updated_at TIMESTAMP) AS $$
SELECT * FROM transactions
WHERE EXTRACT(YEAR FROM created_at) = p_year 
AND EXTRACT(MONTH FROM created_at) = p_month
ORDER BY created_at DESC;
$$ LANGUAGE SQL;

-- Función: Obtener estadísticas rápidas
CREATE OR REPLACE FUNCTION get_dashboard_stats()
RETURNS TABLE(total_orders BIGINT, total_sales DECIMAL, avg_order DECIMAL, today_orders BIGINT, today_sales DECIMAL) AS $$
SELECT 
    (SELECT COUNT(*) FROM transactions WHERE status = 'completed')::BIGINT as total_orders,
    (SELECT SUM(amount) FROM transactions WHERE status = 'completed') as total_sales,
    (SELECT AVG(amount) FROM transactions WHERE status = 'completed') as avg_order,
    (SELECT COUNT(*) FROM transactions WHERE DATE(created_at) = CURRENT_DATE AND status = 'completed')::BIGINT as today_orders,
    (SELECT SUM(amount) FROM transactions WHERE DATE(created_at) = CURRENT_DATE AND status = 'completed') as today_sales;
$$ LANGUAGE SQL;

-- ============================================
-- INSERTAR DATOS INICIALES
-- ============================================

-- Insertar productos de ejemplo (ignorar si ya existen)
INSERT INTO products (product_id, name, description, price, category) VALUES
('prod-001', 'Mix Floral Pequeño', 'Arreglo compacto y fresco con flores variadas', 23900, 'Arreglos'),
('prod-002', 'Mix Floral del día (S)', 'Composición pequeña con flores frescas del día', 28900, 'Arreglos'),
('prod-003', 'Mix Floral del día (M)', 'Arreglo mediano con variedad de flores frescas', 38900, 'Arreglos'),
('prod-004', 'Mix Floral del Día (L)', 'Arreglo grande con flores frescas seleccionadas', 48900, 'Arreglos'),
('prod-005', 'Mix Floral del Día (XL)', 'Arreglo extra grande con flores premium', 58900, 'Arreglos'),
('prod-006', 'Ramo 8 Girasoles', 'Radiante ramo de 8 girasoles frescos', 32000, 'Ramos')
ON CONFLICT DO NOTHING;

-- Insertar configuración inicial (ignorar si ya existen)
INSERT INTO settings (setting_key, setting_value, description) VALUES
('shop_name', 'Floreria Wildgarden', 'Nombre de la tienda'),
('shop_email', 'ventas@wildgardenflores.cl', 'Email de contacto'),
('shop_phone', '+56996744579', 'Teléfono'),
('shipping_cost', '5000', 'Costo de envío en pesos'),
('currency', 'CLP', 'Moneda'),
('webpay_environment', 'test', 'Ambiente: test o production'),
('timezone', 'America/Santiago', 'Zona horaria')
ON CONFLICT DO NOTHING;

-- Insertar usuario admin (ignorar si ya existe)
INSERT INTO admin_users (username, email, password_hash, role) VALUES
('admin', 'admin@wildgardenflores.cl', '$2y$12$xK6C2Gms4xWPxGr.1L4Feu0EQLrHgPLKpqLLzKQDiJFGVGDlKB3x2', 'admin')
ON CONFLICT DO NOTHING;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
