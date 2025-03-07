<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'supremo_gym');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    $conn->select_db(DB_NAME);
    
    // Create tables
    $tables = [
        "CREATE TABLE IF NOT EXISTS miembros (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            apellidos VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE,
            telefono VARCHAR(15),
            fecha_nacimiento DATE,
            fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS membresias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            miembro_id INT,
            tipo ENUM('dia', 'semanal', 'mensual') NOT NULL,
            fecha_inicio DATE NOT NULL,
            fecha_fin DATE NOT NULL,
            precio DECIMAL(10,2) NOT NULL,
            estado ENUM('activa', 'vencida', 'cancelada') DEFAULT 'activa',
            FOREIGN KEY (miembro_id) REFERENCES miembros(id)
        )",
        
        "CREATE TABLE IF NOT EXISTS pagos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            membresia_id INT,
            monto DECIMAL(10,2) NOT NULL,
            fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
            metodo_pago VARCHAR(50),
            FOREIGN KEY (membresia_id) REFERENCES membresias(id)
        )",
        
        "CREATE TABLE IF NOT EXISTS asistencias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            miembro_id INT,
            fecha_entrada DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (miembro_id) REFERENCES miembros(id)
        )"
    ];
    
    foreach ($tables as $table) {
        if ($conn->query($table) !== TRUE) {
            echo "Error creating table: " . $conn->error;
        }
    }
} else {
    echo "Error creating database: " . $conn->error;
}
?>
