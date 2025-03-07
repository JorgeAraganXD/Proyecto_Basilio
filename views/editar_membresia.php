<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: membresias.php');
    exit;
}

$id = $conn->real_escape_string($_GET['id']);

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $fecha_inicio = $conn->real_escape_string($_POST['fecha_inicio']);
    $fecha_fin = $conn->real_escape_string($_POST['fecha_fin']);
    $precio = $conn->real_escape_string($_POST['precio']);
    $estado = $conn->real_escape_string($_POST['estado']);

    $sql = "UPDATE membresias SET 
            tipo = '$tipo',
            fecha_inicio = '$fecha_inicio',
            fecha_fin = '$fecha_fin',
            precio = '$precio',
            estado = '$estado'
            WHERE id = '$id'";

    if ($conn->query($sql)) {
        $_SESSION['mensaje'] = "Membresía actualizada exitosamente";
        $_SESSION['tipo_mensaje'] = "success";
        header("Location: ver_membresia.php?id=$id");
        exit;
    } else {
        $_SESSION['mensaje'] = "Error al actualizar la membresía: " . $conn->error;
        $_SESSION['tipo_mensaje'] = "danger";
    }
}

// Obtener datos actuales de la membresía
$sql = "SELECT m.*, CONCAT(mi.nombre, ' ', mi.apellidos) as nombre_completo 
        FROM membresias m 
        JOIN miembros mi ON m.miembro_id = mi.id 
        WHERE m.id = '$id'";
$resultado = $conn->query($sql);

if ($resultado->num_rows === 0) {
    header('Location: membresias.php');
    exit;
}

$membresia = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Membresía - Supremo Gym</title>
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
                        <li class="breadcrumb-item"><a href="ver_membresia.php?id=<?php echo $id; ?>">Detalles de Membresía</a></li>
                        <li class="breadcrumb-item active">Editar Membresía</li>
                    </ol>
                </nav>
            </div>
        </div>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> alert-dismissible fade show">
                <?php 
                echo $_SESSION['mensaje'];
                unset($_SESSION['mensaje']);
                unset($_SESSION['tipo_mensaje']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit"></i> Editar Membresía
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" onsubmit="return validarFormulario(this)">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Miembro</label>
                                    <input type="text" class="form-control" value="<?php echo $membresia['nombre_completo']; ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tipo" class="form-label">Tipo de Membresía</label>
                                    <select class="form-select" id="tipo" name="tipo" onchange="actualizarPrecio()" required>
                                        <option value="dia" <?php echo $membresia['tipo'] == 'dia' ? 'selected' : ''; ?>>Por Día</option>
                                        <option value="semanal" <?php echo $membresia['tipo'] == 'semanal' ? 'selected' : ''; ?>>Semanal</option>
                                        <option value="mensual" <?php echo $membresia['tipo'] == 'mensual' ? 'selected' : ''; ?>>Mensual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                           value="<?php echo $membresia['fecha_inicio']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                           value="<?php echo $membresia['fecha_fin']; ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="precio" class="form-label">Precio</label>
                                    <input type="number" step="0.01" class="form-control" id="precio" name="precio" 
                                           value="<?php echo $membresia['precio']; ?>" readonly required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="activa" <?php echo $membresia['estado'] == 'activa' ? 'selected' : ''; ?>>Activa</option>
                                        <option value="vencida" <?php echo $membresia['estado'] == 'vencida' ? 'selected' : ''; ?>>Vencida</option>
                                        <option value="cancelada" <?php echo $membresia['estado'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Guardar Cambios
                                    </button>
                                    <a href="ver_membresia.php?id=<?php echo $id; ?>" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <a href="#" class="btn btn-danger" onclick="confirmarEliminacion(<?php echo $id; ?>)">
                                        <i class="fas fa-trash"></i> Eliminar Membresía
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
        function actualizarPrecio() {
            const tipo = document.getElementById('tipo').value;
            const precios = {
                'dia': 80,
                'semanal': 120,
                'mensual': 250
            };
            document.getElementById('precio').value = precios[tipo];
        }

        function confirmarEliminacion(id) {
            if (confirm('¿Estás seguro de que deseas eliminar esta membresía? Esta acción no se puede deshacer.')) {
                window.location.href = 'eliminar_membresia.php?id=' + id;
            }
        }
    </script>
</body>
</html>
