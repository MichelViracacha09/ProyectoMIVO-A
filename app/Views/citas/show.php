<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-calendar-check me-2"></i>Cita #<?= esc($cita['id_citas']) ?></h2>
    <div class="d-flex gap-2">
        <?php if (puede('citas', 'editar')): ?>
            <a href="<?= base_url('citas/edit/' . $cita['id_citas']) ?>" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
        <?php endif; ?>
        <?php if ($cancelable): ?>
            <form action="<?= base_url('citas/cancel/' . $cita['id_citas']) ?>" method="POST">
                <?= csrf_field() ?>
                <button class="btn btn-outline-warning" onclick="return confirm('¿Cancelar esta cita?')"><i class="bi bi-x-circle me-1"></i>Cancelar cita</button>
            </form>
        <?php endif; ?>
        <a href="<?= base_url('citas') ?>" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Fecha</dt><dd class="col-sm-9"><?= esc($cita['fecha_cita']) ?></dd>
            <dt class="col-sm-3">Hora</dt><dd class="col-sm-9"><?= esc(substr($cita['hora_cita'], 0, 5)) ?></dd>
            <dt class="col-sm-3">Estado</dt><dd class="col-sm-9"><?= esc($cita['estado_cita']) ?></dd>
            <dt class="col-sm-3">Servicio</dt><dd class="col-sm-9"><?= esc($cita['nombre_servicio']) ?> (<?= number_format((float) $cita['precio_servicio'], 0, ',', '.') ?>)</dd>
            <dt class="col-sm-3">Cliente</dt><dd class="col-sm-9"><?= esc($cita['cliente_nombre']) ?></dd>
            <?php if ($esAdmin): ?>
                <dt class="col-sm-3">Contacto</dt><dd class="col-sm-9"><?= esc($cita['cliente_celular']) ?> · <?= esc($cita['cliente_correo']) ?></dd>
            <?php endif; ?>
            <dt class="col-sm-3">Observaciones</dt><dd class="col-sm-9"><?= esc($cita['servicio_a_realizar'] ?? '—') ?></dd>
            <dt class="col-sm-3">Registrada</dt><dd class="col-sm-9"><?= esc($cita['fecha_creacion_cita']) ?></dd>
        </dl>
    </div>
</div>
<?= $this->endSection() ?>
