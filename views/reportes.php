<?php
session_start();
require_once '../config/database.php';

// Función para obtener estadísticas generales
function obtener_estadisticas($conn) {
    $stats = [];
    
    // Total de miembros
    $sql = "SELECT COUNT(*) as total FROM miembros";
    $result = $conn->query($sql);
    $stats['total_miembros'] = $result->fetch_assoc()['total'];
    
    // Membresías activas
    $sql = "SELECT COUNT(*) as total FROM membresias WHERE estado = 'activa' AND fecha_fin >= CURDATE()";
    $result = $conn->query($sql);
    $stats['membresias_activas'] = $result->fetch_assoc()['total'];
    
    // Total ingresos del mes
    $sql = "SELECT SUM(monto) as total FROM pagos WHERE MONTH(fecha_pago) = MONTH(CURRENT_DATE())";
    $result = $conn->query($sql);
    $stats['ingresos_mes'] = $result->fetch_assoc()['total'] ?: 0;
    
    // Membresías por vencer (próximos 7 días)
    $sql = "SELECT COUNT(*) as total FROM membresias 
            WHERE estado = 'activa' 
            AND fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
    $result = $conn->query($sql);
    $stats['membresias_por_vencer'] = $result->fetch_assoc()['total'];
    
    return $stats;
}

$estadisticas = obtener_estadisticas($conn);

// Obtener membresías por vencer
$sql_por_vencer = "SELECT m.*, CONCAT(mi.nombre, ' ', mi.apellidos) as nombre_completo 
                   FROM membresias m 
                   JOIN miembros mi ON m.miembro_id = mi.id 
                   WHERE m.estado = 'activa' 
                   AND m.fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                   ORDER BY m.fecha_fin ASC";
$resultado_por_vencer = $conn->query($sql_por_vencer);

// Obtener últimos pagos
$sql_pagos = "SELECT p.*, CONCAT(mi.nombre, ' ', mi.apellidos) as nombre_completo 
              FROM pagos p 
              JOIN membresias m ON p.membresia_id = m.id 
              JOIN miembros mi ON m.miembro_id = mi.id 
              ORDER BY p.fecha_pago DESC 
              LIMIT 10";
$resultado_pagos = $conn->query($sql_pagos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Supremo Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Supremo Gym</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="miembros.php">
                            <i class="fas fa-users"></i> Miembros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="membresias.php">
                            <i class="fas fa-id-card"></i> Membresías
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pagos.php">
                            <i class="fas fa-money-bill"></i> Pagos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reportes.php">
                            <i class="fas fa-chart-bar"></i> Reportes
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Miembros</h6>
                                <h2 class="mb-0"><?php echo $estadisticas['total_miembros']; ?></h2>
                            </div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Membresías Activas</h6>
                                <h2 class="mb-0"><?php echo $estadisticas['membresias_activas']; ?></h2>
                            </div>
                            <i class="fas fa-id-card fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Ingresos del Mes</h6>
                                <h2 class="mb-0">$<?php echo number_format($estadisticas['ingresos_mes'], 2); ?></h2>
                            </div>
                            <i class="fas fa-money-bill fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Por Vencer (7 días)</h6>
                                <h2 class="mb-0"><?php echo $estadisticas['membresias_por_vencer']; ?></h2>
                            </div>
                            <i class="fas fa-clock fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Membresías por Vencer
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Miembro</th>
                                        <th>Tipo</th>
                                        <th>Vence</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $resultado_por_vencer->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['nombre_completo']; ?></td>
                                        <td><?php echo ucfirst($row['tipo']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['fecha_fin'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-money-bill"></i> Últimos Pagos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Miembro</th>
                                        <th>Monto</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $resultado_pagos->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['nombre_completo']; ?></td>
                                        <td>$<?php echo number_format($row['monto'], 2); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['fecha_pago'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-pdf"></i> Generar Reportes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="reportes/miembros_activos.php" class="btn btn-outline-primary w-100 mb-3">
                                    <i class="fas fa-users"></i> Reporte de Miembros Activos
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="reportes/ingresos_mensuales.php" class="btn btn-outline-success w-100 mb-3">
                                    <i class="fas fa-chart-line"></i> Reporte de Ingresos Mensuales
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="reportes/membresias_vencidas.php" class="btn btn-outline-warning w-100 mb-3">
                                    <i class="fas fa-clock"></i> Reporte de Membresías Vencidas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
