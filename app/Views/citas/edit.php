<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cita - MIVO</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="<?= base_url('citas') ?>">
            <i class="bi bi-arrow-left me-2"></i>Volver a Citas
        </a>
    </div>
</nav>

<div class="container mt-4" style="max-width: 600px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h3 class="fw-bold text-primary mb-4"><i class="bi bi-pencil me-2"></i>Editar Cita</h3>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('citas/update/' . $cita['id_citas']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha_cita" class="form-control" value="<?= old('fecha_cita', $cita['fecha_cita']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hora</label>
                        <input type="time" name="hora_cita" class="form-control" value="<?= old('hora_cita', $cita['hora_cita']) ?>" required>
                    </div>
                </div>

                <?php if ($esAdmin): ?>
                    <div class="mb-4">
                        <label class="form-label">Estado de la cita</label>
                        <select name="estado_cita" class="form-select">
                            <option value="PENDIENTE" <?= $cita['estado_cita'] == 'PENDIENTE' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="CONFIRMADA" <?= $cita['estado_cita'] == 'CONFIRMADA' ? 'selected' : '' ?>>Confirmada</option>
                            <option value="COMPLETADA" <?= $cita['estado_cita'] == 'COMPLETADA' ? 'selected' : '' ?>>Completada</option>
                            <option value="CANCELADA" <?= $cita['estado_cita'] == 'CANCELADA' ? 'selected' : '' ?>>Cancelada</option>
                        </select>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-check-circle me-1"></i> Actualizar Cita
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>