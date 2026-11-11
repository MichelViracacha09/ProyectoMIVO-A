<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tareas - Sistema CI4</title>
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
        <h2 class="fw-bold text-primary"><i class="bi bi-list-check me-2"></i>Mis Tareas</h2>
        <a href="<?= base_url('tasks/new') ?>" class="btn btn-primary fw-bold">
            <i class="bi bi-plus-circle me-1"></i> Nueva Tarea
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (empty($tasks)): ?>
        <div class="text-center text-muted p-5 bg-white rounded-3 shadow-sm border">
            <i class="bi bi-inbox display-4"></i>
            <p class="mt-3">Todavía no tienes tareas registradas.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive bg-white rounded-3 shadow-sm border p-3">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td class="fw-bold"><?= esc($task['title']) ?></td>
                            <td class="text-muted small"><?= esc($task['description']) ?></td>
                            <td>
                                <?php
                                    $badgeClass = match($task['status']) {
                                        'pendiente' => 'secondary',
                                        'en_progreso' => 'warning text-dark',
                                        'completada' => 'success',
                                        default => 'secondary'
                                    };
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>"><?= esc(str_replace('_', ' ', $task['status'])) ?></span>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('tasks/edit/' . $task['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= base_url('tasks/delete/' . $task['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta tarea?')">
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