<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-scooter me-2"></i><?= esc($moto['placa']) ?></h2>
    <div class="d-flex gap-2">
        <?php if (puede('motos', 'editar')): ?>
            <a href="<?= base_url('motos/edit/' . $moto['id_moto']) ?>" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
        <?php endif; ?>
        <a href="<?= base_url('motos') ?>" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Línea</dt><dd class="col-sm-9"><?= esc($moto['linea']) ?></dd>
            <dt class="col-sm-3">Modelo</dt><dd class="col-sm-9"><?= esc($moto['modelo'] ?? '—') ?></dd>
            <dt class="col-sm-3">Color</dt><dd class="col-sm-9"><?= esc($moto['color'] ?? '—') ?></dd>
            <dt class="col-sm-3">Cilindraje</dt><dd class="col-sm-9"><?= esc($moto['cilindraje'] ?? '—') ?></dd>
            <dt class="col-sm-3">Kilometraje</dt><dd class="col-sm-9"><?= esc($moto['kilometraje'] ?? '—') ?></dd>
            <dt class="col-sm-3">Propietario</dt>
            <dd class="col-sm-9">
                <?php if ($esAdmin && puede('clientes', 'ver')): ?>
                    <a href="<?= base_url('clientes/show/' . $moto['id_cliente']) ?>"><?= esc($moto['cliente_nombre']) ?></a>
                <?php else: ?>
                    <?= esc($moto['cliente_nombre']) ?>
                <?php endif; ?>
            </dd>
        </dl>
    </div>
</div>

<h5 class="fw-bold">Servicios asignados (<?= count($servicios) ?>)</h5>
<div class="table-responsive bg-white rounded-3 shadow-sm border p-3 mb-4">
    <?php if (empty($servicios)): ?>
        <p class="text-muted mb-0">Sin servicios asignados.</p>
    <?php else: ?>
        <table class="table table-sm mb-0">
            <thead><tr><th>Servicio</th><th>Asignado</th></tr></thead>
            <tbody>
                <?php foreach ($servicios as $s): ?>
                    <tr><td><?= esc($s['nombre_servicio']) ?></td><td><?= esc($s['fecha_asignacion']) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<h5 class="fw-bold">Historial de servicios (<?= count($historial) ?>)</h5>
<div class="table-responsive bg-white rounded-3 shadow-sm border p-3">
    <?php if (empty($historial)): ?>
        <p class="text-muted mb-0">Sin historial registrado.</p>
    <?php else: ?>
        <table class="table table-sm mb-0">
            <thead><tr><th>Fecha</th><th>Servicio</th><th>Estado</th></tr></thead>
            <tbody>
                <?php foreach ($historial as $h): ?>
                    <tr><td><?= esc($h['fecha']) ?></td><td><?= esc($h['servicio']) ?></td><td><?= esc($h['estado']) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
