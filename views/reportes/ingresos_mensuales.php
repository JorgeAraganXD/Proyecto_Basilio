<?php
require_once '../../config/database.php';

// Obtener ingresos mensuales del último año
$sql = "SELECT DATE_FORMAT(fecha_pago, '%Y-%m') as mes, 
        SUM(monto) as total,
        COUNT(*) as num_pagos
        FROM pagos 
        WHERE fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(fecha_pago, '%Y-%m')
        ORDER BY mes DESC";
$resultado = $conn->query($sql);

// Calcular totales
$total_ingresos = 0;
$total_pagos = 0;
$datos_meses = [];

while ($row = $resultado->fetch_assoc()) {
    $total_ingresos += $row['total'];
    $total_pagos += $row['num_pagos'];
    $datos_meses[] = $row;
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ingresos Mensuales - Supremo Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
        }
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Reporte de Ingresos Mensuales</h1>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <a href="../reportes.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cerrar
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="text-end mb-3">
                    <strong>Fecha:</strong> <?php echo date('d/m/Y'); ?>
                </div>

                <div class="alert alert-info">
                    <h5>Resumen del Último Año:</h5>
                    <p class="mb-1">Total de Ingresos: $<?php echo number_format($total_ingresos, 2); ?></p>
                    <p class="mb-0">Total de Pagos Registrados: <?php echo $total_pagos; ?></p>
                </div>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Mes</th>
                            <th>Número de Pagos</th>
                            <th>Total Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datos_meses as $row): ?>
                        <tr>
                            <td><?php echo date('F Y', strtotime($row['mes'] . '-01')); ?></td>
                            <td><?php echo $row['num_pagos']; ?></td>
                            <td>$<?php echo number_format($row['total'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td>Total</td>
                            <td><?php echo $total_pagos; ?></td>
                            <td>$<?php echo number_format($total_ingresos, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
