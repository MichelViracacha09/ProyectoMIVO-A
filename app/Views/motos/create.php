<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Moto - MIVO</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="<?= base_url('motos') ?>">
            <i class="bi bi-arrow-left me-2"></i>Volver a Motos
        </a>
    </div>
</nav>

<div class="container mt-4" style="max-width: 600px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h3 class="fw-bold text-primary mb-4"><i class="bi bi-plus-circle me-2"></i>Nueva Moto</h3>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('motos/store') ?>" method="POST">
                <?= csrf_field() ?>

                <?php if ($esAdmin): ?>
                    <div class="mb-3">
                        <label class="form-label">Cliente dueño</label>
                        <select name="id_cliente" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($clientes as $cli): ?>
                                <option value="<?= $cli['id_cliente'] ?>"><?= esc($cli['nombre_completo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Placa</label>
                        <input type="text" name="placa" class="form-control" value="<?= old('placa') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Línea</label>
                        <input type="text" name="linea" class="form-control" value="<?= old('linea') ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Modelo (año)</label>
                        <input type="text" name="modelo" class="form-control" value="<?= old('modelo') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-control" value="<?= old('color') ?>">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Cilindraje</label>
                        <input type="text" name="cilindraje" class="form-control" value="<?= old('cilindraje') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kilometraje</label>
                        <input type="number" name="kilometraje" class="form-control" value="<?= old('kilometraje') ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-check-circle me-1"></i> Registrar Moto
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>