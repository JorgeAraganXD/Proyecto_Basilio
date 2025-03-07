<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    die('ID de pago no especificado');
}

$id = $conn->real_escape_string($_GET['id']);

// Obtener información del pago
$sql = "SELECT p.*, m.tipo as tipo_membresia, m.fecha_inicio, m.fecha_fin,
        CONCAT(mi.nombre, ' ', mi.apellidos) as nombre_completo, mi.email, mi.telefono
        FROM pagos p 
        JOIN membresias m ON p.membresia_id = m.id 
        JOIN miembros mi ON m.miembro_id = mi.id 
        WHERE p.id = '$id'";
$resultado = $conn->query($sql);

if ($resultado->num_rows === 0) {
    die('Pago no encontrado');
}

$pago = $resultado->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago - Supremo Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: 14px;
        }
        .receipt {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }
        .receipt-body {
            margin-bottom: 20px;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .receipt {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <h2>Supremo Gym</h2>
            <h4>Recibo de Pago</h4>
            <p>
                Fecha: <?php echo date('d/m/Y H:i', strtotime($pago['fecha_pago'])); ?><br>
                No. de Recibo: <?php echo str_pad($pago['id'], 6, '0', STR_PAD_LEFT); ?>
            </p>
        </div>

        <div class="receipt-body">
            <div class="row mb-4">
                <div class="col-6">
                    <strong>Cliente:</strong><br>
                    <?php echo $pago['nombre_completo']; ?><br>
                    <?php if ($pago['email']): ?>
                    Email: <?php echo $pago['email']; ?><br>
                    <?php endif; ?>
                    <?php if ($pago['telefono']): ?>
                    Tel: <?php echo $pago['telefono']; ?>
                    <?php endif; ?>
                </div>
                <div class="col-6 text-end">
                    <strong>Detalles de Membresía:</strong><br>
                    Tipo: <?php echo ucfirst($pago['tipo_membresia']); ?><br>
                    Vigencia: <?php echo date('d/m/Y', strtotime($pago['fecha_inicio'])); ?> al 
                             <?php echo date('d/m/Y', strtotime($pago['fecha_fin'])); ?>
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th class="text-end">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Pago de membresía <?php echo ucfirst($pago['tipo_membresia']); ?></td>
                        <td class="text-end">$<?php echo number_format($pago['monto'], 2); ?></td>
                    </tr>
                    <tr>
                        <td class="text-end"><strong>Total</strong></td>
                        <td class="text-end"><strong>$<?php echo number_format($pago['monto'], 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="mb-4">
                <strong>Método de Pago:</strong> <?php echo ucfirst($pago['metodo_pago']); ?>
            </div>
        </div>

        <div class="receipt-footer">
            <p>¡Gracias por su preferencia!</p>
            <small>Este documento es un comprobante de pago válido</small>
        </div>
    </div>

    <div class="text-center mb-4 no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimir Recibo
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Cerrar
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
