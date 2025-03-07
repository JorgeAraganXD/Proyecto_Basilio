<?php
session_start();
require_once '../config/database.php';

// Procesar formulario de nuevo miembro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'crear') {
        $nombre = $conn->real_escape_string($_POST['nombre']);
        $apellidos = $conn->real_escape_string($_POST['apellidos']);
        $email = $conn->real_escape_string($_POST['email']);
        $telefono = $conn->real_escape_string($_POST['telefono']);
        $fecha_nacimiento = $conn->real_escape_string($_POST['fecha_nacimiento']);

        // Verificar si el correo ya existe
        $checkEmailQuery = "SELECT id FROM miembros WHERE email = '$email'";
        $result = $conn->query($checkEmailQuery);

        if ($result->num_rows > 0) {
            $_SESSION['mensaje'] = "Error: El correo ya está registrado.";
            $_SESSION['tipo_mensaje'] = "danger";
        } else {
            // Insertar nuevo miembro si el correo es único
            $sql = "INSERT INTO miembros (nombre, apellidos, email, telefono, fecha_nacimiento) 
                    VALUES ('$nombre', '$apellidos', '$email', '$telefono', '$fecha_nacimiento')";

            if ($conn->query($sql)) {
                $_SESSION['mensaje'] = "Miembro agregado exitosamente";
                $_SESSION['tipo_mensaje'] = "success";
            } else {
                $_SESSION['mensaje'] = "Error al agregar miembro: " . $conn->error;
                $_SESSION['tipo_mensaje'] = "danger";
            }
        }
    }
}


// Obtener lista de miembros
$sql = "SELECT * FROM miembros ORDER BY fecha_registro DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Miembros - Supremo Gym</title>
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
                            <i class="fas fa-users"></i> MiembrosA
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

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-plus"></i> Nuevo Miembro
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="crear">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="apellidos" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-4 mb-3">
    <label for="telefono" class="form-label">Teléfono</label>
    <input type="tel" class="form-control" id="telefono" name="telefono" 
        pattern="[0-9]{10}" maxlength="10" minlength="10" required
        title="El teléfono debe contener exactamente 10 dígitos">
</div>

                                <div class="col-md-4 mb-3">
                                    <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
                                </div>
                                <div class="col-md-4 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save"></i> Guardar Miembro
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users"></i> Lista de Miembros
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Completo</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Fecha de Registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $resultado->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['nombre'] . ' ' . $row['apellidos']; ?></td>
                                        <td><?php echo $row['email']; ?></td>
                                        <td><?php echo $row['telefono']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['fecha_registro'])); ?></td>
                                        <td>
                                            <a href="ver_miembro.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editar_miembro.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="ver_membresia.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-id-card"></i>
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
</body>
</html>
