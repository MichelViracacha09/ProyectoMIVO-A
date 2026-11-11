<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MIVO</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="<?= base_url('home') ?>">
            <i class="bi bi-scooter text-warning me-2"></i>MIVO
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php foreach ($modulos as $mod): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url($mod['ruta']) ?>">
                            <i class="bi <?= $mod['icono'] ?> me-1"></i> <?= esc($mod['nombre']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="d-flex align-items-center text-white">
                <div class="me-3 text-end">
                    <div class="fw-bold leading-none"><?= esc($nombre) ?></div>
                    <small class="text-muted" style="font-size: 0.75rem;"><?= esc($rol) ?></small>
                </div>
                <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Salir
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="p-4 bg-white rounded-3 shadow-sm border mb-4">
        <h2 class="fw-bold text-primary">¡Bienvenido, <?= esc($nombre) ?>!</h2>
        <p class="text-muted">Rol actual: <strong><?= esc($rol) ?></strong>. A continuación se muestran los módulos a los que tienes acceso.</p>
    </div>

    <div class="row g-3">
        <?php foreach ($modulos as $mod): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-dark">
                            <i class="bi <?= $mod['icono'] ?> text-primary me-2"></i> <?= esc($mod['nombre']) ?>
                        </h5>
                        <p class="card-text text-muted small">Ruta: <code>/<?= esc($mod['ruta']) ?></code></p>
                        <div class="d-flex gap-1 mt-3">
                            <span class="badge bg-<?= $mod['puede_crear'] ? 'success' : 'secondary' ?>">Crear</span>
                            <span class="badge bg-<?= $mod['puede_editar'] ? 'warning text-dark' : 'secondary' ?>">Editar</span>
                            <span class="badge bg-<?= $mod['puede_eliminar'] ? 'danger' : 'secondary' ?>">Eliminar</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 text-end pb-3">
                        <a href="<?= base_url($mod['ruta']) ?>" class="btn btn-sm btn-outline-primary fw-bold">Ingresar »</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>