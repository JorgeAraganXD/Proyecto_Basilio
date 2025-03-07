<?php
require_once '../../config/database.php';

// Obtener membresías por vencer en los próximos 7 días
$sql = "SELECT m.*, 
        CONCAT(mi.nombre, ' ', mi.apellidos) as nombre_completo,
        DATEDIFF(m.fecha_fin, CURDATE()) as dias_restantes
        FROM membresias m 
        JOIN miembros mi ON m.miembro_id = mi.id
        WHERE m.estado = 'activa' 
        AND m.fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ORDER BY m.fecha_fin ASC";
$resultado = $conn->query($sql);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Membresías por Vencer - Supremo Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
        }
        .dias-restantes {
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Reporte de Membresías por Vencer</h1>
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

                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Atención</h5>
                    <p class="mb-0">Este reporte muestra las membresías que vencerán en los próximos 7 días.</p>
                </div>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Miembro</th>
                            <th>Tipo de Membresía</th>
                            <th>Fecha de Inicio</th>
                            <th>Fecha de Vencimiento</th>
                            <th>Días Restantes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['nombre_completo']; ?></td>
                            <td><?php echo ucfirst($row['tipo']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['fecha_inicio'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['fecha_fin'])); ?></td>
                            <td class="dias-restantes">
                                <?php echo $row['dias_restantes']; ?> días
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <?php if ($resultado->num_rows === 0): ?>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> No hay membresías por vencer en los próximos 7 días.
                </div>
                <?php endif; ?>

                <div class="alert alert-secondary mt-4">
                    <strong>Nota:</strong> Se recomienda contactar a los miembros para renovar sus membresías antes de la fecha de vencimiento.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
