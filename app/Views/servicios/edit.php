<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Servicio - MIVO</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="<?= base_url('servicios') ?>">
            <i class="bi bi-arrow-left me-2"></i>Volver a Servicios
        </a>
    </div>
</nav>

<div class="container mt-4" style="max-width: 600px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h3 class="fw-bold text-primary mb-4"><i class="bi bi-pencil me-2"></i>Editar Servicio</h3>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('servicios/update/' . $servicio['id_servicio']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Nombre del servicio</label>
                    <input type="text" name="nombre_servicio" class="form-control" value="<?= old('nombre_servicio', $servicio['nombre_servicio']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion_servicio" class="form-control" rows="3"><?= old('descripcion_servicio', $servicio['descripcion_servicio']) ?></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Precio</label>
                        <input type="number" name="precio_servicio" class="form-control" value="<?= old('precio_servicio', $servicio['precio_servicio']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Duración estimada</label>
                        <input type="text" name="duracion_estimada" class="form-control" value="<?= old('duracion_estimada', $servicio['duracion_estimada']) ?>">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Garantía</label>
                        <input type="text" name="garantia_servicio" class="form-control" value="<?= old('garantia_servicio', $servicio['garantia_servicio']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nivel de complejidad</label>
                        <select name="nivel_complejidad" class="form-select">
                            <option value="Bajo" <?= $servicio['nivel_complejidad'] == 'Bajo' ? 'selected' : '' ?>>Bajo</option>
                            <option value="Medio" <?= $servicio['nivel_complejidad'] == 'Medio' ? 'selected' : '' ?>>Medio</option>
                            <option value="Alto" <?= $servicio['nivel_complejidad'] == 'Alto' ? 'selected' : '' ?>>Alto</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-check-circle me-1"></i> Actualizar Servicio
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>