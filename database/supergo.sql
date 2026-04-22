DROP DATABASE IF EXISTS supergo;
CREATE DATABASE supergo;
USE supergo;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(120) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('ADMIN', 'CLIENTE') NOT NULL DEFAULT 'CLIENTE',
    foto_perfil VARCHAR(255) DEFAULT NULL,
    telefono VARCHAR(30) DEFAULT NULL,
    direccion VARCHAR(255) DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    ruta_imagen VARCHAR(255) DEFAULT NULL,
    id_categoria INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_productos_categoria
        FOREIGN KEY (id_categoria) REFERENCES categorias(id)
);

CREATE TABLE carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_carrito_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_carrito_producto
        FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE CASCADE,
    CONSTRAINT uq_carrito UNIQUE (id_usuario, id_producto)
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_pedido VARCHAR(30) NOT NULL UNIQUE,
    id_usuario INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) NOT NULL,
    impuestos DECIMAL(10,2) NOT NULL,
    envio DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente','pagado','enviado','entregado','cancelado') DEFAULT 'pagado',
    direccion_envio VARCHAR(255) DEFAULT NULL,
    telefono_envio VARCHAR(30) DEFAULT NULL,
    metodo_pago VARCHAR(50) DEFAULT 'simulado',
    CONSTRAINT fk_pedido_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE RESTRICT
);

CREATE TABLE detalle_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_detalle_pedido
        FOREIGN KEY (id_pedido) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_detalle_producto
        FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE RESTRICT
);

CREATE TABLE valoraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    id_pedido INT DEFAULT NULL,
    puntuacion TINYINT NOT NULL,
    comentario VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_val_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_val_producto FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE CASCADE,
    CONSTRAINT fk_val_pedido FOREIGN KEY (id_pedido) REFERENCES pedidos(id) ON DELETE SET NULL,
    CONSTRAINT uq_valoracion UNIQUE (id_usuario, id_producto, id_pedido)
);

INSERT INTO categorias (nombre) VALUES
('Abarrotes'),
('Jugos y Bebidas'),
('Mascotas'),
('Bebidas Alcohólicas'),
('Limpieza'),
('Higiene y belleza'),
('Lácteos y Huevos'),
('Electrónicos y Herramientas'),
('Rebajas');

INSERT INTO productos (nombre, descripcion, precio, stock, ruta_imagen, id_categoria) VALUES
('Arroz 1kg', 'Arroz blanco de excelente calidad', 1250, 30, 'img/abarrotes.jpg', 1),
('Frijoles negros 800g', 'Frijoles ideales para tu despensa', 980, 25, 'img/abarrotes.jpg', 1),
('Jugo de naranja 1L', 'Jugo refrescante natural', 1350, 20, 'img/bebidas.png', 2),
('Gaseosa cola 2L', 'Bebida gaseosa clásica', 1650, 18, 'img/bebidas.png', 2),
('Alimento para perro 2kg', 'Nutrición balanceada para tu mascota', 4200, 12, 'img/mascotas.png', 3),
('Detergente líquido', 'Limpieza profunda para tu ropa', 2850, 16, 'img/limpieza.webp', 5);

-- contraseña: admin123
INSERT INTO usuarios (nombre, correo, contrasena, rol, telefono, direccion) VALUES
('Administrador', 'admin@supergo.com', '$2y$10$2G6oKzw5f/9kr6w7tXifHOVMl6UpM4yxSaVv/jW/23LJ4mZzTH4U.', 'ADMIN', '2222-2222', 'Sucursal central'),
('Cliente Demo', 'cliente@supergo.com', '$2y$10$2G6oKzw5f/9kr6w7tXifHOVMl6UpM4yxSaVv/jW/23LJ4mZzTH4U.', 'CLIENTE', '8888-8888', 'San José, Costa Rica');