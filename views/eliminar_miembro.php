<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: miembros.php');
    exit;
}

$id = $conn->real_escape_string($_GET['id']);

// Verificar si el miembro existe
$sql = "SELECT * FROM miembros WHERE id = '$id'";
$resultado = $conn->query($sql);

if ($resultado->num_rows === 0) {
    $_SESSION['mensaje'] = "Miembro no encontrado";
    $_SESSION['tipo_mensaje'] = "danger";
    header('Location: miembros.php');
    exit;
}

// Eliminar pagos asociados a las membresías del miembro
$sql = "DELETE p FROM pagos p 
        INNER JOIN membresias m ON p.membresia_id = m.id 
        WHERE m.miembro_id = '$id'";
$conn->query($sql);

// Eliminar membresías del miembro
$sql = "DELETE FROM membresias WHERE miembro_id = '$id'";
$conn->query($sql);

// Eliminar asistencias del miembro
$sql = "DELETE FROM asistencias WHERE miembro_id = '$id'";
$conn->query($sql);

// Finalmente, eliminar el miembro
$sql = "DELETE FROM miembros WHERE id = '$id'";

if ($conn->query($sql)) {
    $_SESSION['mensaje'] = "Miembro eliminado exitosamente";
    $_SESSION['tipo_mensaje'] = "success";
} else {
    $_SESSION['mensaje'] = "Error al eliminar el miembro: " . $conn->error;
    $_SESSION['tipo_mensaje'] = "danger";
}

header('Location: miembros.php');
exit;
?>
