<?php
session_start();
require_once '../config/database.php';

// Procesar formulario de nueva membresía
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'crear') {
        $miembro_id = $conn->real_escape_string($_POST['miembro_id']);
        $tipo = $conn->real_escape_string($_POST['tipo']);
        $fecha_inicio = $conn->real_escape_string($_POST['fecha_inicio']);
        $fecha_fin = $conn->real_escape_string($_POST['fecha_fin']);
        $precio = $conn->real_escape_string($_POST['precio']);

        $sql = "INSERT INTO membresias (miembro_id, tipo, fecha_inicio, fecha_fin, precio) 
                VALUES ('$miembro_id', '$tipo', '$fecha_inicio', '$fecha_fin', '$precio')";
        
        if ($conn->query($sql)) {
            $_SESSION['mensaje'] = "Membresía creada exitosamente";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "Error al crear membresía: " . $conn->error;
            $_SESSION['tipo_mensaje'] = "danger";
        }
    }
}

// Obtener lista de miembros para el select
$sql_miembros = "SELECT id, nombre, apellidos FROM miembros ORDER BY nombre";
$resultado_miembros = $conn->query($sql_miembros);

// Obtener lista de membresías
$sql = "SELECT m.*, CONCAT(mi.nombre, ' ', mi.apellidos) as nombre_completo 
        FROM membresias m 
        JOIN miembros mi ON m.miembro_id = mi.id 
        ORDER BY m.fecha_inicio DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Membresías - Supremo Gym</title>
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
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus-circle"></i> Nueva Membresía
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="crear">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="miembro_id" class="form-label">Miembro</label>
                                    <select class="form-select" id="miembro_id" name="miembro_id" required>
                                        <option value="">Seleccione un miembro</option>
                                        <?php while($miembro = $resultado_miembros->fetch_assoc()): ?>
                                            <option value="<?php echo $miembro['id']; ?>">
                                                <?php echo $miembro['nombre'] . ' ' . $miembro['apellidos']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="tipo" class="form-label">Tipo de Membresía</label>
                                    <select class="form-select" id="tipo" name="tipo" onchange="actualizarPrecio()" required>
                                        <option value="dia">Por Día ($80)</option>
                                        <option value="semanal">Semanal ($120)</option>
                                        <option value="mensual">Mensual ($250)</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="precio" class="form-label">Precio</label>
                                    <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="80" readonly required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="metodo_pago" class="form-label">Método de Pago</label>
                                    <input type="text" class="form-control" value="Efectivo" readonly>
                                    <input type="hidden" name="metodo_pago" value="efectivo">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-save"></i> Guardar Membresía
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
                            <i class="fas fa-list"></i> Lista de Membresías
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Miembro</th>
                                        <th>Tipo</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Precio</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $resultado->fetch_assoc()): 
                                        $estado_class = '';
                                        $hoy = new DateTime();
                                        $fecha_fin = new DateTime($row['fecha_fin']);
                                        
                                        if ($row['estado'] == 'activa' && $fecha_fin >= $hoy) {
                                            $estado_class = 'status-active';
                                        } elseif ($row['estado'] == 'cancelada') {
                                            $estado_class = 'status-expired';
                                        } else {
                                            $estado_class = 'status-expired';
                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['nombre_completo']; ?></td>
                                        <td><?php echo ucfirst($row['tipo']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['fecha_inicio'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['fecha_fin'])); ?></td>
                                        <td>$<?php echo number_format($row['precio'], 2); ?></td>
                                        <td class="<?php echo $estado_class; ?>">
                                            <?php echo ucfirst($row['estado']); ?>
                                        </td>
                                        <td>
                                            <a href="ver_membresia.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editar_membresia.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($row['estado'] == 'activa'): ?>
                                            
                                            <?php endif; ?>
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
        function actualizarPrecio() {
            const tipo = document.getElementById('tipo').value;
            const precios = {
                'dia': 80,
                'semanal': 120,
                'mensual': 250
            };
            document.getElementById('precio').value = precios[tipo];
        }

        // Inicializar datepicker y manejar cambios de fecha
        document.addEventListener('DOMContentLoaded', function() {
            const fechaInicio = document.getElementById('fecha_inicio');
            const fechaFin = document.getElementById('fecha_fin');
            const tipoSelect = document.getElementById('tipo');

            // Establecer fecha de inicio por defecto
            const hoy = new Date();
            fechaInicio.valueAsDate = hoy;

            function calcularFechaFin() {
                const inicio = new Date(fechaInicio.value);
                const tipo = tipoSelect.value;
                let fin = new Date(inicio);

                switch(tipo) {
                    case 'dia':
                        fin.setDate(inicio.getDate() + 1);
                        break;
                    case 'semanal':
                        fin.setDate(inicio.getDate() + 7);
                        break;
                    case 'mensual':
                        fin.setMonth(inicio.getMonth() + 1);
                        break;
                }

                // Formatear la fecha para el input
                fin.setDate(fin.getDate() - 1); // Restar un día para que sea hasta el final del período
                const year = fin.getFullYear();
                const month = String(fin.getMonth() + 1).padStart(2, '0');
                const day = String(fin.getDate()).padStart(2, '0');
                fechaFin.value = `${year}-${month}-${day}`;
            }

            // Calcular fecha fin inicial
            calcularFechaFin();

            // Recalcular cuando cambie la fecha de inicio o el tipo
            fechaInicio.addEventListener('change', calcularFechaFin);
            tipoSelect.addEventListener('change', calcularFechaFin);
        });
    </script>
</body>
</html>
