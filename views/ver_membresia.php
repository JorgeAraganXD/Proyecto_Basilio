<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: membresias.php');
    exit;
}

$id = $conn->real_escape_string($_GET['id']);

// Obtener datos de la membresía
$sql = "SELECT m.*, CONCAT(mi.nombre, ' ', mi.apellidos) as nombre_completo, mi.email, mi.telefono 
        FROM membresias m 
        JOIN miembros mi ON m.miembro_id = mi.id 
        WHERE m.id = '$id'";
$resultado = $conn->query($sql);

if ($resultado->num_rows === 0) {
    header('Location: membresias.php');
    exit;
}

$membresia = $resultado->fetch_assoc();

// Obtener pagos de la membresía
$sql_pagos = "SELECT * FROM pagos WHERE membresia_id = '$id' ORDER BY fecha_pago DESC";
$resultado_pagos = $conn->query($sql_pagos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Membresía - Supremo Gym</title>
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
                        <a class="nav-link active" href="membresias.php">
                            <i class="fas fa-id-card"></i> Membresías
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pagos.php">
                            <i class="fas fa-money-bill"></i> Pagos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reportes.php">
                            <i class="fas fa-chart-bar"></i> Reportes
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="membresias.php">Membresías</a></li>
                        <li class="breadcrumb-item active">Detalles de Membresía</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-id-card"></i> Información de la Membresía
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Miembro:</strong><br>
                                <?php echo $membresia['nombre_completo']; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Tipo:</strong><br>
                                <?php echo ucfirst($membresia['tipo']); ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Fecha de Inicio:</strong><br>
                                <?php echo date('d/m/Y', strtotime($membresia['fecha_inicio'])); ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Fecha de Fin:</strong><br>
                                <?php echo date('d/m/Y', strtotime($membresia['fecha_fin'])); ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Precio:</strong><br>
                                $<?php echo number_format($membresia['precio'], 2); ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Estado:</strong><br>
                                <?php
                                $estado_class = '';
                                if ($membresia['estado'] == 'activa') {
                                    $estado_class = 'success';
                                } elseif ($membresia['estado'] == 'vencida') {
                                    $estado_class = 'warning';
                                } else {
                                    $estado_class = 'danger';
                                }
                                ?>
                                <span class="badge bg-<?php echo $estado_class; ?>">
                                    <?php echo ucfirst($membresia['estado']); ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user"></i> Información de Contacto
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Email:</strong><br>
                                <?php echo $membresia['email'] ?: 'No registrado'; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Teléfono:</strong><br>
                                <?php echo $membresia['telefono'] ?: 'No registrado'; ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-money-bill"></i> Historial de Pagos
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($membresia['estado'] == 'activa'): ?>
                        <div class="mb-3">
                            
                        </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Método de Pago</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($pago = $resultado_pagos->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($pago['fecha_pago'])); ?></td>
                                        <td>$<?php echo number_format($pago['monto'], 2); ?></td>
                                        <td><?php echo ucfirst($pago['metodo_pago']); ?></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info" onclick="imprimirRecibo(<?php echo $pago['id']; ?>)">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
        function imprimirRecibo(pagoId) {
            window.open('imprimir_recibo.php?id=' + pagoId, 'Recibo', 'width=800,height=600');
        }
    </script>
</body>
</html>
