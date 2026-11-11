<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas - MIVO</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="<?= base_url('home') ?>">
            <i class="bi bi-arrow-left me-2"></i>Volver al Dashboard
        </a>
        <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3">
            <i class="bi bi-box-arrow-right me-1"></i> Salir
        </a>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary"><i class="bi bi-calendar-check me-2"></i>Citas</h2>
        <?php if (!$esAdmin): ?>
            <a href="<?= base_url('citas/new') ?>" class="btn btn-primary fw-bold">
                <i class="bi bi-plus-circle me-1"></i> Agendar Cita
            </a>
        <?php endif; ?>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (empty($citas)): ?>
        <div class="text-center text-muted p-5 bg-white rounded-3 shadow-sm border">
            <i class="bi bi-calendar-x display-4"></i>
            <p class="mt-3">No hay citas registradas todavía.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive bg-white rounded-3 shadow-sm border p-3">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Servicio</th>
                        <?php if ($esAdmin): ?><th>Cliente</th><?php endif; ?>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citas as $cita): ?>
                        <tr>
                            <td><?= esc($cita['fecha_cita']) ?></td>
                            <td><?= esc($cita['hora_cita']) ?></td>
                            <td><?= esc($cita['nombre_servicio']) ?></td>
                            <?php if ($esAdmin): ?><td><?= esc($cita['cliente_nombre']) ?></td><?php endif; ?>
                            <td>
                                <?php
                                    $badgeClass = match(strtoupper($cita['estado_cita'])) {
                                        'PENDIENTE' => 'secondary',
                                        'CONFIRMADA' => 'warning text-dark',
                                        'COMPLETADA' => 'success',
                                        'CANCELADA' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>"><?= esc($cita['estado_cita']) ?></span>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('citas/edit/' . $cita['id_citas']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= base_url('citas/delete/' . $cita['id_citas']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta cita?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>