<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: membresias.php');
    exit;
}

$id = $conn->real_escape_string($_GET['id']);

// Verificar si la membresía existe
$sql = "SELECT * FROM membresias WHERE id = '$id'";
$resultado = $conn->query($sql);

if ($resultado->num_rows === 0) {
    $_SESSION['mensaje'] = "Membresía no encontrada";
    $_SESSION['tipo_mensaje'] = "danger";
    header('Location: membresias.php');
    exit;
}

// Eliminar pagos asociados a la membresía
$sql = "DELETE FROM pagos WHERE membresia_id = '$id'";
$conn->query($sql);

// Eliminar la membresía
$sql = "DELETE FROM membresias WHERE id = '$id'";

if ($conn->query($sql)) {
    $_SESSION['mensaje'] = "Membresía eliminada exitosamente";
    $_SESSION['tipo_mensaje'] = "success";
} else {
    $_SESSION['mensaje'] = "Error al eliminar la membresía: " . $conn->error;
    $_SESSION['tipo_mensaje'] = "danger";
}

header('Location: membresias.php');
exit;
?>
