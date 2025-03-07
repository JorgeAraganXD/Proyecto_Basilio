<?php
require_once '../../config/database.php';

// Obtener miembros activos
$sql = "SELECT m.*, 
        (SELECT COUNT(*) FROM membresias WHERE miembro_id = m.id AND estado = 'activa') as tiene_membresia
        FROM miembros m 
        ORDER BY m.nombre";
$resultado = $conn->query($sql);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Miembros Activos - Supremo Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Reporte de Miembros Activos</h1>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <a href="../reportes.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cerrar
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="text-end mb-3">
                    <strong>Fecha:</strong> <?php echo date('d/m/Y'); ?>
                </div>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['nombre'] . ' ' . $row['apellidos']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['telefono']; ?></td>
                            <td>
                                <?php if ($row['tiene_membresia'] > 0): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Sin Membresía</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
