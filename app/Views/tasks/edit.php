<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea - Sistema CI4</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="<?= base_url('tasks') ?>">
            <i class="bi bi-arrow-left me-2"></i>Volver a Mis Tareas
        </a>
    </div>
</nav>

<div class="container mt-4" style="max-width: 600px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h3 class="fw-bold text-primary mb-4"><i class="bi bi-pencil me-2"></i>Editar Tarea</h3>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('tasks/update/' . $task['id']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" name="title" class="form-control" value="<?= old('title', $task['title']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="3"><?= old('description', $task['description']) ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select" required>
                        <option value="pendiente" <?= $task['status'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="en_progreso" <?= $task['status'] == 'en_progreso' ? 'selected' : '' ?>>En progreso</option>
                        <option value="completada" <?= $task['status'] == 'completada' ? 'selected' : '' ?>>Completada</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-check-circle me-1"></i> Actualizar Tarea
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>