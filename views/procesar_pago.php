<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$membresia_id = $conn->real_escape_string($_POST['membresia_id']);
$monto = $conn->real_escape_string($_POST['monto']);
$metodo_pago = $conn->real_escape_string($_POST['metodo_pago']);

// Verificar que la membresía existe y está activa
$sql = "SELECT tipo FROM membresias WHERE id = '$membresia_id' AND estado = 'activa'";
$resultado = $conn->query($sql);

if ($resultado->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Membresía no encontrada o inactiva']);
    exit;
}

$membresia = $resultado->fetch_assoc();

// Verificar que el monto corresponde al tipo de membresía
$precios = [
    'dia' => 80,
    'semanal' => 120,
    'mensual' => 250
];

if ($monto != $precios[$membresia['tipo']]) {
    echo json_encode(['success' => false, 'message' => 'El monto no corresponde al tipo de membresía']);
    exit;
}

// Registrar el pago
$sql = "INSERT INTO pagos (membresia_id, monto, metodo_pago) VALUES ('$membresia_id', '$monto', '$metodo_pago')";

if ($conn->query($sql)) {
    $_SESSION['mensaje'] = "Pago registrado exitosamente";
    $_SESSION['tipo_mensaje'] = "success";
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar el pago: ' . $conn->error]);
}
?>
