-- Script de Creación de Base de Datos - Atlantic City (Refinado)
-- Autor: Antigravity (Google Deepmind)
-- Fecha: 2025-11-27

-- 1. Limpieza y Creación de Base de Datos
DROP DATABASE IF EXISTS atlantic_city_db;
CREATE DATABASE atlantic_city_db;
USE atlantic_city_db;

-- 2. Crear Tablas

-- Tabla: Clientes
-- Incluye campo password para login
CREATE TABLE Clientes (
    cliente_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    correo VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    preferencias TEXT,
    segmento VARCHAR(50),
    password VARCHAR(255) NOT NULL, -- Seguridad
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: Areas (Organización)
-- Catálogo de gerencias/áreas
CREATE TABLE Areas (
    area_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

-- Tabla: Empleados
-- Incluye campo password y relación con Areas
CREATE TABLE Empleados (
    empleado_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cargo VARCHAR(100),
    area_id INT, -- Relación con Areas
    correo VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Seguridad
    FOREIGN KEY (area_id) REFERENCES Areas(area_id) ON DELETE SET NULL
);

-- Tabla: Visitas
CREATE TABLE Visitas (
    visita_id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    detalles TEXT,
    preferencias_visita TEXT,
    FOREIGN KEY (cliente_id) REFERENCES Clientes(cliente_id) ON DELETE CASCADE
);

-- Tabla: Promociones
CREATE TABLE Promociones (
    promocion_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE
);

-- Tabla: Cliente_Promociones
CREATE TABLE Cliente_Promociones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    promocion_id INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(50) DEFAULT 'Enviada',
    FOREIGN KEY (cliente_id) REFERENCES Clientes(cliente_id) ON DELETE CASCADE,
    FOREIGN KEY (promocion_id) REFERENCES Promociones(promocion_id) ON DELETE CASCADE
);

-- Módulo de Incidencias (Refactorizado)

-- Tabla: Tipo_Incidencia (Catálogo)
CREATE TABLE Tipo_Incidencia (
    tipo_incidencia_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL -- Queja, Sugerencia, Consulta, Reclamo
);

-- Tabla: Incidencias
CREATE TABLE Incidencias (
    incidencia_id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    empleado_id INT,
    tipo_incidencia_id INT NOT NULL,
    descripcion TEXT NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(50) DEFAULT 'Pendiente',
    solucion TEXT,
    FOREIGN KEY (cliente_id) REFERENCES Clientes(cliente_id) ON DELETE CASCADE,
    FOREIGN KEY (empleado_id) REFERENCES Empleados(empleado_id) ON DELETE SET NULL,
    FOREIGN KEY (tipo_incidencia_id) REFERENCES Tipo_Incidencia(tipo_incidencia_id)
);

-- Módulo de Juegos (Data-Driven)

-- Tabla: Juegos (Catálogo)
CREATE TABLE Juegos (
    juego_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) -- Slot, Mesa, Ruleta, etc.
);

-- Tabla: Sesiones_Juego (Transaccional)
CREATE TABLE Sesiones_Juego (
    sesion_id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    juego_id INT NOT NULL,
    puntaje_obtenido INT DEFAULT 0,
    resultado VARCHAR(20), -- Ganó, Perdió
    duracion_segundos INT,
    fecha_jugada DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES Clientes(cliente_id) ON DELETE CASCADE,
    FOREIGN KEY (juego_id) REFERENCES Juegos(juego_id) ON DELETE CASCADE
);

-- 3. Insertar Datos de Prueba (Seed)

-- Insertar Areas
INSERT INTO Areas (nombre, descripcion) VALUES
('Operaciones', 'Gestión del piso y juegos'),
('Marketing', 'Promociones y publicidad'),
('Recursos Humanos', 'Gestión de personal'),
('Desarrollo de Negocios', 'Estrategia y crecimiento'),
('Servicio al Cliente', 'Atención y fidelización'),
('TI', 'Sistemas y tecnología');

-- Insertar Tipos de Incidencia
INSERT INTO Tipo_Incidencia (nombre) VALUES
('Queja'), ('Sugerencia'), ('Consulta'), ('Reclamo');

-- Insertar Juegos
INSERT INTO Juegos (nombre, tipo) VALUES
('Tragamonedas Virtual', 'Slot'),
('Blackjack Demo', 'Cartas'),
('Ruleta Europea', 'Mesa'),
('Poker Texas Holdem', 'Cartas');

-- Insertar Clientes (con password)
INSERT INTO Clientes (nombre, direccion, correo, telefono, preferencias, segmento, password) VALUES
('Juan Pérez', 'Av. Larco 123', 'juan.perez@email.com', '999111222', 'Slots clásicos', 'Regular', '123456'),
('Maria Rodriguez', 'Calle Los Pinos 456', 'maria.rodriguez@email.com', '999333444', 'Blackjack', 'VIP', '123456'),
('Carlos Gomez', 'Jr. Unión 789', 'carlos.gomez@email.com', '999555666', 'Poker', 'Regular', '123456'),
('Ana Lopez', 'Av. Javier Prado 101', 'ana.lopez@email.com', '999777888', 'Cena y juego', 'Nuevo', '123456');

-- Insertar Empleados (con password y area_id)
INSERT INTO Empleados (nombre, cargo, area_id, correo, password) VALUES
('Luis Ramirez', 'Gerente de Turno', 1, 'lramirez@atlanticcity.com', '123456'),
('Sofia Torres', 'Analista', 2, 'storres@atlanticcity.com', '123456'),
('Jorge Diaz', 'Soporte', 6, 'jdiaz@atlanticcity.com', '123456'),
('Elena Castillo', 'Atención', 5, 'ecastillo@atlanticcity.com', '123456');

-- Insertar Visitas
INSERT INTO Visitas (cliente_id, fecha_hora, detalles, preferencias_visita) VALUES
(1, '2023-10-01 18:30:00', 'Jugó 2 horas', 'Café'),
(2, '2023-10-02 20:00:00', 'Mesa VIP', 'Dealer específico');

-- Insertar Promociones
INSERT INTO Promociones (nombre, descripcion, fecha_inicio, fecha_fin) VALUES
('Bono Bienvenida', '50 soles gratis', '2023-01-01', '2023-12-31'),
('Noche de Blackjack', 'Doble puntaje', '2023-10-01', '2023-10-31');

-- Insertar Cliente_Promociones
INSERT INTO Cliente_Promociones (cliente_id, promocion_id, fecha_asignacion, estado) VALUES
(4, 1, '2023-10-01 10:00:00', 'Canjeada'),
(2, 2, '2023-10-02 09:00:00', 'Enviada');

-- Insertar Incidencias (Nueva estructura)
INSERT INTO Incidencias (cliente_id, empleado_id, tipo_incidencia_id, descripcion, estado, solucion) VALUES
(1, 4, 1, 'Máquina trabada', 'Resuelto', 'Reinicio remoto'), -- Queja
(2, 1, 4, 'Demora en bebidas', 'Pendiente', NULL), -- Reclamo
(3, NULL, 3, 'Horario de torneo', 'Resuelto', 'Se envió info por correo'); -- Consulta

-- Insertar Sesiones de Juego (Data-Driven)
INSERT INTO Sesiones_Juego (cliente_id, juego_id, puntaje_obtenido, resultado, duracion_segundos, fecha_jugada) VALUES
(1, 1, 500, 'Ganó', 1200, '2023-10-01 19:00:00'),
(1, 1, 0, 'Perdió', 600, '2023-10-01 19:30:00'),
(2, 2, 1500, 'Ganó', 3600, '2023-10-02 21:00:00'),
(3, 4, 200, 'Perdió', 1800, '2023-10-03 15:00:00'),
(4, 3, 800, 'Ganó', 900, '2023-10-04 20:00:00'),
(2, 2, 0, 'Perdió', 2400, '2023-10-05 22:00:00');

-- Fin del Script
