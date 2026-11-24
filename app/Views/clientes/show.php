<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-person me-2"></i><?= esc($cliente['nombre_completo']) ?></h2>
    <div class="d-flex gap-2">
        <?php if (puede('clientes', 'editar')): ?>
            <a href="<?= base_url('clientes/edit/' . $cliente['id_cliente']) ?>" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
        <?php endif; ?>
        <a href="<?= base_url('clientes') ?>" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Documento</dt><dd class="col-sm-9"><?= esc($cliente['tipo_doc']) ?> <?= esc($cliente['numero_identificacion']) ?></dd>
            <dt class="col-sm-3">Celular</dt><dd class="col-sm-9"><?= esc($cliente['numero_celular']) ?></dd>
            <dt class="col-sm-3">Correo</dt><dd class="col-sm-9"><?= esc($cliente['correo_electronico']) ?></dd>
            <dt class="col-sm-3">Dirección</dt><dd class="col-sm-9"><?= esc($cliente['direccion'] ?? '—') ?></dd>
            <dt class="col-sm-3">Nacimiento</dt><dd class="col-sm-9"><?= esc($cliente['fecha_nacimiento'] ?? '—') ?></dd>
            <dt class="col-sm-3">Estado</dt><dd class="col-sm-9"><?= esc($cliente['estado_nombre']) ?></dd>
            <dt class="col-sm-3">Cuenta de acceso</dt><dd class="col-sm-9"><?= $cliente['id_usuario'] ? 'Sí' : 'No' ?></dd>
        </dl>
    </div>
</div>

<h5 class="fw-bold">Motocicletas (<?= count($motos) ?>)</h5>
<div class="table-responsive bg-white rounded-3 shadow-sm border p-3 mb-4">
    <?php if (empty($motos)): ?>
        <p class="text-muted mb-0">Sin motocicletas registradas.</p>
    <?php else: ?>
        <table class="table table-sm mb-0">
            <thead><tr><th>Placa</th><th>Línea</th><th>Modelo</th><th>Color</th></tr></thead>
            <tbody>
                <?php foreach ($motos as $m): ?>
                    <tr>
                        <td><a href="<?= base_url('motos/show/' . $m['id_moto']) ?>"><?= esc($m['placa']) ?></a></td>
                        <td><?= esc($m['linea']) ?></td><td><?= esc($m['modelo']) ?></td><td><?= esc($m['color']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<h5 class="fw-bold">Citas (<?= count($citas) ?>)</h5>
<div class="table-responsive bg-white rounded-3 shadow-sm border p-3">
    <?php if (empty($citas)): ?>
        <p class="text-muted mb-0">Sin citas registradas.</p>
    <?php else: ?>
        <table class="table table-sm mb-0">
            <thead><tr><th>Fecha</th><th>Hora</th><th>Servicio</th><th>Estado</th></tr></thead>
            <tbody>
                <?php foreach ($citas as $ci): ?>
                    <tr>
                        <td><a href="<?= base_url('citas/show/' . $ci['id_citas']) ?>"><?= esc($ci['fecha_cita']) ?></a></td>
                        <td><?= esc($ci['hora_cita']) ?></td><td><?= esc($ci['nombre_servicio']) ?></td><td><?= esc($ci['estado_cita']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
