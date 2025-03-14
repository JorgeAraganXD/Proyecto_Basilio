<?php
session_start();
require_once '../config/database.php';

// Obtener membresías activas para el datalist
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
    <title>Gestión de Pagos - Supremo Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
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

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <i class="fas fa-dollar-sign"></i> Gestión de Pagos
                    </h2>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle"></i> Nuevo Pago
                </h5>
            </div>
            <div class="card-body">
                <form id="nuevoPago" method="POST" action="">
                    <input type="hidden" name="action" value="crear">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="searchMembresia" class="form-label">Buscar Membresía</label>
                            <input class="form-control" list="membresiaOptions" id="searchMembresia" 
                                   placeholder="Escribe para buscar una membresía..." required>
                            <datalist id="membresiaOptions">
                                <?php 
                                $membresias_list = array();
                                while ($row = $resultado_membresias->fetch_assoc()): 
                                    $tipo_texto = [
                                        'dia' => 'Por Día',
                                        'semanal' => 'Semanal',
                                        'mensual' => 'Mensual'
                                    ][$row['tipo']];
                                    $display_text = $row['nombre_completo'] . ' - ' . $tipo_texto;
                                    $membresias_list[] = array(
                                        'id' => $row['id'],
                                        'texto' => $display_text,
                                        'tipo' => $row['tipo']
                                    );
                                    echo "<option value='" . htmlspecialchars($display_text) . "'>";
                                endwhile; 
                                ?>
                            </datalist>
                            <input type="hidden" name="membresia_id" id="membresia_id" required>
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
                                <i class="fas fa-save"></i> Registrar Pago
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history"></i> Historial de Pagos
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
                                <th>Método de Pago</th>
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
        // Convertir el array PHP de membresías a JavaScript
        const membresiasList = <?php echo json_encode($membresias_list); ?>;
        
        // Función para actualizar el ID de membresía y monto cuando se selecciona una
        document.getElementById('searchMembresia').addEventListener('input', function(e) {
            const selectedMembresia = membresiasList.find(m => m.texto === this.value);
            if (selectedMembresia) {
                document.getElementById('membresia_id').value = selectedMembresia.id;
                
                const precios = {
                    'dia': 80,
                    'semanal': 120,
                    'mensual': 250
                };
                
                document.getElementById('monto').value = precios[selectedMembresia.tipo];
            } else {
                document.getElementById('membresia_id').value = '';
                document.getElementById('monto').value = '';
            }
        });

        // Procesar el formulario
        document.getElementById('nuevoPago').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!this.checkValidity()) {
                e.stopPropagation();
                return;
            }

            const formData = new FormData(this);
            
            fetch('procesar_pago.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {

                    window.location.reload();
                } else {
                    alert('Error al registrar el pago: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        });
    </script>
</body>
</html>
