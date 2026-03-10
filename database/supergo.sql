DROP DATABASE IF EXISTS supergo;
CREATE DATABASE supergo;
USE supergo;

-- tabla usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    email VARCHAR(100),
    password VARCHAR(100)
);

-- tabla categorias
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50)
);

-- tabla productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    precio DECIMAL(10,2),
    stock INT,
    id_categoria INT,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id)
);

-- tabla carrito
CREATE TABLE carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_producto INT,
    cantidad INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_producto) REFERENCES productos(id)
);

-- datos de prueba
INSERT INTO categorias(nombre) VALUES
('Frutas'),
('Verduras'),
('Bebidas');

INSERT INTO productos(nombre,precio,stock,id_categoria) VALUES
('Manzana',500,50,1),
('Pera',450,40,1),
('Lechuga',300,30,2),
('Coca Cola',900,20,3);