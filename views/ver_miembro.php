<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: miembros.php');
    exit;
}

$id = $conn->real_escape_string($_GET['id']);

// Obtener información del miembro
$sql = "SELECT * FROM miembros WHERE id = '$id'";
$resultado = $conn->query($sql);

if ($resultado->num_rows === 0) {
    header('Location: miembros.php');
    exit;
}

$miembro = $resultado->fetch_assoc();

// Obtener membresía activa si existe
$sql = "SELECT * FROM membresias WHERE miembro_id = '$id' AND estado = 'activa'";
$resultado_membresia = $conn->query($sql);
$tiene_membresia_activa = $resultado_membresia->num_rows > 0;
$membresia = $tiene_membresia_activa ? $resultado_membresia->fetch_assoc() : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Miembro - Supremo Gym</title>
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
                        <a class="nav-link active" href="miembros.php">
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
                        <li class="breadcrumb-item"><a href="miembros.php">Miembros</a></li>
                        <li class="breadcrumb-item active">Detalles del Miembro</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <img src="../assets/img/Perfil.jpg" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px;">
                        </div>
                        <h4 class="card-title"><?php echo $miembro['nombre'] . ' ' . $miembro['apellidos']; ?></h4>
                        
                        <ul class="list-group list-group-flush text-start">
                            <li class="list-group-item">
                                <i class="fas fa-envelope"></i> Email:
                                <span class="float-end"><?php echo $miembro['email']; ?></span>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-phone"></i> Teléfono:
                                <span class="float-end"><?php echo $miembro['telefono']; ?></span>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-calendar"></i> Fecha de Nacimiento:
                                <span class="float-end"><?php echo date('d/m/Y', strtotime($miembro['fecha_nacimiento'])); ?></span>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-clock"></i> Fecha de Registro:
                                <span class="float-end"><?php echo date('d/m/Y', strtotime($miembro['fecha_registro'])); ?></span>
                            </li>
                        </ul>

                        <div class="mt-3">
                            <a href="editar_miembro.php?id=<?php echo $id; ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar Información
                            </a>
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-id-card"></i> Estado de Membresía
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($tiene_membresia_activa): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle"></i> Membresía Activa</h5>
                                <p>
                                    <strong>Tipo:</strong> <?php echo ucfirst($membresia['tipo']); ?><br>
                                    <strong>Fecha de Inicio:</strong> <?php echo date('d/m/Y', strtotime($membresia['fecha_inicio'])); ?><br>
                                    <strong>Fecha de Fin:</strong> <?php echo date('d/m/Y', strtotime($membresia['fecha_fin'])); ?><br>
                                    <strong>Precio:</strong> $<?php echo number_format($membresia['precio'], 2); ?>
                                </p>
                                <a href="ver_membresia.php?id=<?php echo $membresia['id']; ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <h5><i class="fas fa-exclamation-triangle"></i> Sin Membresía Activa</h5>
                                <p>Este miembro no tiene una membresía activa actualmente.</p>
                                <a href="membresias.php?miembro_id=<?php echo $id; ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus-circle"></i> Crear Nueva Membresía
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
