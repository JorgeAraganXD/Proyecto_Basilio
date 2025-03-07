<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supremo Gym - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Supremo Gym</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="views/miembros.php">
                            <i class="fas fa-users"></i> Miembros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="views/membresias.php">
                            <i class="fas fa-id-card"></i> Membresías
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="views/pagos.php">
                            <i class="fas fa-money-bill"></i> Pagos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="views/reportes.php">
                            <i class="fas fa-chart-bar"></i> Reportes
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center mb-4">Bienvenido a Supremo Gym</h1>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="card text-center mb-4">
                    <div class="card-body">
                        <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Gestión de Miembros</h5>
                        <p class="card-text">Administra los miembros del gimnasio</p>
                        <a href="views/miembros.php" class="btn btn-primary">Acceder</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-center mb-4">
                    <div class="card-body">
                        <i class="fas fa-id-card fa-3x mb-3 text-success"></i>
                        <h5 class="card-title">Membresías</h5>
                        <p class="card-text">Control de membresías activas</p>
                        <a href="views/membresias.php" class="btn btn-success">Acceder</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-center mb-4">
                    <div class="card-body">
                        <i class="fas fa-money-bill fa-3x mb-3 text-warning"></i>
                        <h5 class="card-title">Pagos</h5>
                        <p class="card-text">Registro y control de pagos</p>
                        <a href="views/pagos.php" class="btn btn-warning">Acceder</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-center mb-4">
                    <div class="card-body">
                        <i class="fas fa-chart-bar fa-3x mb-3 text-info"></i>
                        <h5 class="card-title">Reportes</h5>
                        <p class="card-text">Genera reportes y estadísticas</p>
                        <a href="views/reportes.php" class="btn btn-info">Acceder</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
