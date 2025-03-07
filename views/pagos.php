<?php
session_start();
require_once '../config/database.php';

// Obtener membresías activas para el select
$sql_membresias = "SELECT m.id, m.tipo, CONCAT(mi.nombre, ' ', mi.apellidos) as nombre_completo 
                   FROM membresias m 
                   JOIN miembros mi ON m.miembro_id = mi.id 
                   WHERE m.estado = 'activa'
                   ORDER BY mi.nombre";
$resultado_membresias = $conn->query($sql_membresias);

// Obtener historial de pagos
$sql = "SELECT p.*, m.tipo as tipo_membresia, CONCAT(mi.nombre, ' ', mi.apellidos) as nombre_completo 
        FROM pagos p 
        JOIN membresias m ON p.membresia_id = m.id 
        JOIN miembros mi ON m.miembro_id = mi.id 
        ORDER BY p.fecha_pago DESC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos - Supremo Gym</title>
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
                        <a class="nav-link active" href="pagos.php">
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

        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-money-bill"></i> Nuevo Pago
                </h5>
            </div>
            <div class="card-body">
                <form id="nuevoPago" method="POST" action="">
                    <input type="hidden" name="action" value="crear">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="membresia_id" class="form-label">Membresía</label>
                            <select class="form-select" id="membresia_id" name="membresia_id" required onchange="actualizarMonto()">
                                <option value="">Seleccione una membresía</option>
                                <?php while ($row = $resultado_membresias->fetch_assoc()): 
                                    $tipo_texto = [
                                        'dia' => 'Por Día',
                                        'semanal' => 'Semanal',
                                        'mensual' => 'Mensual'
                                    ][$row['tipo']];
                                ?>
                                <option value="<?php echo $row['id']; ?>" data-tipo="<?php echo $row['tipo']; ?>">
                                    <?php echo $row['nombre_completo'] . ' - ' . $tipo_texto; ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="monto" class="form-label">Monto</label>
                            <input type="number" step="0.01" class="form-control" id="monto" name="monto" readonly required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Método de Pago</label>
                            <input type="text" class="form-control" value="Efectivo" readonly>
                            <input type="hidden" name="metodo_pago" value="efectivo">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-money-bill"></i> Registrar Pago
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> Historial de Pagos
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Miembro</th>
                                <th>Tipo Membresía</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_pago'])); ?></td>
                                <td><?php echo $row['nombre_completo']; ?></td>
                                <td><?php echo ucfirst($row['tipo_membresia']); ?></td>
                                <td>$<?php echo number_format($row['monto'], 2); ?></td>
                                <td><?php echo ucfirst($row['metodo_pago']); ?></td>
                                <td>
                                    <a href="imprimir_recibo.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn btn-sm btn-info">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function actualizarMonto() {
            const select = document.getElementById('membresia_id');
            const option = select.options[select.selectedIndex];
            const tipo = option ? option.getAttribute('data-tipo') : '';
            
            const precios = {
                'dia': 80,
                'semanal': 120,
                'mensual': 250
            };

            document.getElementById('monto').value = tipo ? precios[tipo] : '';
        }

        // Procesar el formulario
        document.getElementById('nuevoPago').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('procesar_pago.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error al procesar el pago: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar el pago');
            });
        });
    </script>
</body>
</html>
