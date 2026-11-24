<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-scooter me-2"></i>Motos</h2>
    <?php if (puede('motos', 'crear')): ?>
        <a href="<?= base_url('motos/new') ?>" class="btn btn-primary fw-bold">
            <i class="bi bi-plus-circle me-1"></i> Nueva Moto
        </a>
    <?php endif; ?>
</div>

<form method="GET" action="<?= base_url('motos') ?>" class="row g-2 mb-3">
    <div class="col-md-3">
        <input type="text" name="placa" class="form-control" placeholder="Buscar por placa" value="<?= esc($filtros['placa']) ?>">
    </div>
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Buscar por línea<?= $esAdmin ? ' o dueño' : '' ?>" value="<?= esc($filtros['q']) ?>">
    </div>
    <?php if ($esAdmin): ?>
        <div class="col-md-3">
            <select name="id_cliente" class="form-select">
                <option value="">Todos los clientes</option>
                <?php foreach ($clientes as $cli): ?>
                    <option value="<?= $cli['id_cliente'] ?>" <?= (string) $filtros['id_cliente'] === (string) $cli['id_cliente'] ? 'selected' : '' ?>><?= esc($cli['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>
    <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-outline-primary flex-fill"><i class="bi bi-search"></i></button>
        <a href="<?= base_url('motos') ?>" class="btn btn-outline-secondary">Limpiar</a>
    </div>
</form>

<?php if (empty($motos)): ?>
    <div class="text-center text-muted p-5 bg-white rounded-3 shadow-sm border">
        <i class="bi bi-scooter display-4"></i>
        <p class="mt-3">No se encontraron motos.</p>
    </div>
<?php else: ?>
    <div class="table-responsive bg-white rounded-3 shadow-sm border p-3">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Línea</th>
                    <th>Modelo</th>
                    <th>Color</th>
                    <?php if ($esAdmin): ?><th>Dueño</th><?php endif; ?>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($motos as $moto): ?>
                    <tr>
                        <td class="fw-bold"><?= esc($moto['placa']) ?></td>
                        <td><?= esc($moto['linea']) ?></td>
                        <td><?= esc($moto['modelo']) ?></td>
                        <td><?= esc($moto['color']) ?></td>
                        <?php if ($esAdmin): ?><td><?= esc($moto['cliente_nombre']) ?></td><?php endif; ?>
                        <td class="text-end">
                            <a href="<?= base_url('motos/show/' . $moto['id_moto']) ?>" class="btn btn-sm btn-outline-secondary" title="Detalle"><i class="bi bi-eye"></i></a>
                            <?php if (puede('motos', 'editar')): ?>
                                <a href="<?= base_url('motos/edit/' . $moto['id_moto']) ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                            <?php endif; ?>
                            <?php if (puede('motos', 'eliminar')): ?>
                                <form action="<?= base_url('motos/delete/' . $moto['id_moto']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Eliminar esta moto?')"><i class="bi bi-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-3"><?= $pager->links() ?></div>
<?php endif; ?>
<?= $this->endSection() ?>
