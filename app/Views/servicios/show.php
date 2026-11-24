<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-tools me-2"></i><?= esc($servicio['nombre_servicio']) ?></h2>
    <div class="d-flex gap-2">
        <?php if (puede('servicios', 'editar')): ?>
            <a href="<?= base_url('servicios/edit/' . $servicio['id_servicio']) ?>" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
        <?php endif; ?>
        <a href="<?= base_url('servicios') ?>" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>
<div class="card shadow-sm border-0">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Tipo</dt><dd class="col-sm-9"><?= esc($servicio['tipo_nombre']) ?></dd>
            <dt class="col-sm-3">Descripción</dt><dd class="col-sm-9"><?= esc($servicio['descripcion_servicio'] ?? '—') ?></dd>
            <dt class="col-sm-3">Precio</dt><dd class="col-sm-9">$<?= number_format((float) $servicio['precio_servicio'], 0, ',', '.') ?></dd>
            <dt class="col-sm-3">Duración estimada</dt><dd class="col-sm-9"><?= esc($servicio['duracion_estimada'] ?? '—') ?></dd>
            <dt class="col-sm-3">Garantía</dt><dd class="col-sm-9"><?= esc($servicio['garantia_servicio'] ?? '—') ?></dd>
            <dt class="col-sm-3">Complejidad</dt><dd class="col-sm-9"><?= esc($servicio['nivel_complejidad'] ?? '—') ?></dd>
            <dt class="col-sm-3">Usos (citas y motos)</dt><dd class="col-sm-9"><?= (int) $usos ?></dd>
        </dl>
    </div>
</div>
<?= $this->endSection() ?>
