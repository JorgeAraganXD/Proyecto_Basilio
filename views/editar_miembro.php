<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: miembros.php');
    exit;
}

$id = $conn->real_escape_string($_GET['id']);

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellidos = $conn->real_escape_string($_POST['apellidos']);
    $email = $conn->real_escape_string($_POST['email']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $fecha_nacimiento = $conn->real_escape_string($_POST['fecha_nacimiento']);

    $sql = "UPDATE miembros SET 
            nombre = '$nombre',
            apellidos = '$apellidos',
            email = '$email',
            telefono = '$telefono',
            fecha_nacimiento = " . ($fecha_nacimiento ? "'$fecha_nacimiento'" : "NULL") . "
            WHERE id = '$id'";

    if ($conn->query($sql)) {
        $_SESSION['mensaje'] = "Información del miembro actualizada exitosamente";
        $_SESSION['tipo_mensaje'] = "success";
        header("Location: ver_miembro.php?id=$id");
        exit;
    } else {
        $_SESSION['mensaje'] = "Error al actualizar la información: " . $conn->error;
        $_SESSION['tipo_mensaje'] = "danger";
    }
}

// Obtener datos actuales del miembro
$sql = "SELECT * FROM miembros WHERE id = '$id'";
$resultado = $conn->query($sql);

if ($resultado->num_rows === 0) {
    header('Location: miembros.php');
    exit;
}

$miembro = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Miembro - Supremo Gym</title>
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
                        <li class="breadcrumb-item"><a href="ver_miembro.php?id=<?php echo $id; ?>">Detalles del Miembro</a></li>
                        <li class="breadcrumb-item active">Editar Miembro</li>
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
                            <i class="fas fa-edit"></i> Editar Información del Miembro
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" onsubmit="return validarFormulario(this)">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?php echo htmlspecialchars($miembro['nombre']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="apellidos" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" id="apellidos" name="apellidos" 
                                           value="<?php echo htmlspecialchars($miembro['apellidos']); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($miembro['email']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?php echo htmlspecialchars($miembro['telefono']); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" 
                                           value="<?php echo $miembro['fecha_nacimiento']; ?>">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Guardar Cambios
                                    </button>
                                    <a href="ver_miembro.php?id=<?php echo $id; ?>" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <a href="#" class="btn btn-danger" onclick="confirmarEliminacion(<?php echo $id; ?>)">
                                        <i class="fas fa-trash"></i> Eliminar Miembro
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
        function confirmarEliminacion(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este miembro? Esta acción no se puede deshacer y eliminará todas sus membresías y pagos asociados.')) {
                window.location.href = 'eliminar_miembro.php?id=' + id;
            }
        }
    </script>
</body>
</html>
