-- Crear una nueva base de datos
CREATE DATABASE tp_la_comanda;

-- Usar la base de datos
USE tp_la_comanda;

-- Crear una tabla

CREATE TABLE productos(
	id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(50) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    tipo ENUM('impresora', 'cartucho') NOT NULL,
    modelo VARCHAR(50),
    color VARCHAR(50) NOT NULL,
    stock INT
);

CREATE TABLE ventas(
	id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    marca VARCHAR(50),
    tipo ENUM('impresora', 'cartucho') NOT NULL,
    cantidad INT,
    fecha DATE,
    modelo VARCHAR(50) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL
);