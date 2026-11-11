<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Moto - MIVO</title>
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
            <h3 class="fw-bold text-primary mb-4"><i class="bi bi-pencil me-2"></i>Editar Moto: <?= esc($moto['placa']) ?></h3>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('motos/update/' . $moto['id_moto']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Línea</label>
                    <input type="text" name="linea" class="form-control" value="<?= old('linea', $moto['linea']) ?>" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Modelo (año)</label>
                        <input type="text" name="modelo" class="form-control" value="<?= old('modelo', $moto['modelo']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-control" value="<?= old('color', $moto['color']) ?>">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Cilindraje</label>
                        <input type="text" name="cilindraje" class="form-control" value="<?= old('cilindraje', $moto['cilindraje']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kilometraje</label>
                        <input type="number" name="kilometraje" class="form-control" value="<?= old('kilometraje', $moto['kilometraje']) ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-check-circle me-1"></i> Actualizar Moto
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>