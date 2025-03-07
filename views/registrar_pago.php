<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['membresia_id'])) {
    header('Location: membresias.php');
    exit;
}

$membresia_id = $conn->real_escape_string($_GET['membresia_id']);

// Procesar formulario de pago
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $monto = $conn->real_escape_string($_POST['monto']);
    $metodo_pago = $conn->real_escape_string($_POST['metodo_pago']);

    $sql = "INSERT INTO pagos (membresia_id, monto, metodo_pago) 
            VALUES ('$membresia_id', '$monto', '$metodo_pago')";

    if ($conn->query($sql)) {
        $_SESSION['mensaje'] = "Pago registrado exitosamente";
        $_SESSION['tipo_mensaje'] = "success";
        header("Location: ver_membresia.php?id=$membresia_id");
        exit;
    } else {
        $_SESSION['mensaje'] = "Error al registrar el pago: " . $conn->error;
        $_SESSION['tipo_mensaje'] = "danger";
    }
}

// Obtener datos de la membresía
$sql = "SELECT m.*, CONCAT(mi.nombre, ' ', mi.apellidos) as nombre_completo 
        FROM membresias m 
        JOIN miembros mi ON m.miembro_id = mi.id 
        WHERE m.id = '$membresia_id'";
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
    <title>Registrar Pago - Supremo Gym</title>
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
                        <li class="breadcrumb-item"><a href="ver_membresia.php?id=<?php echo $membresia_id; ?>">Detalles de Membresía</a></li>
                        <li class="breadcrumb-item active">Registrar Pago</li>
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
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-money-bill"></i> Registrar Nuevo Pago
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Membresía de:</strong> <?php echo $membresia['nombre_completo']; ?><br>
                            <strong>Tipo:</strong> <?php echo ucfirst($membresia['tipo']); ?><br>
                            <strong>Precio:</strong> $<?php 
                                $precios = [
                                    'dia' => 80,
                                    'semanal' => 120,
                                    'mensual' => 250
                                ];
                                $precio = $precios[$membresia['tipo']];
                                echo number_format($precio, 2); 
                            ?>
                        </div>

                        <form method="POST" action="" onsubmit="return validarFormulario(this)">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="monto" class="form-label">Monto a Pagar</label>
                                    <input type="number" step="0.01" class="form-control" id="monto" name="monto" 
                                           value="<?php echo $precio; ?>" readonly required>
                                </div>
                            </div>
                            <input type="hidden" name="metodo_pago" value="efectivo">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-money-bill"></i> Registrar Pago en Efectivo
                                    </button>
                                    <a href="ver_membresia.php?id=<?php echo $membresia_id; ?>" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
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
</body>
</html>
