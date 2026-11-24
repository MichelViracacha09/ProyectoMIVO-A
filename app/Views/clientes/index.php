<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-people me-2"></i>Clientes</h2>
    <?php if (puede('clientes', 'crear')): ?>
        <a href="<?= base_url('clientes/new') ?>" class="btn btn-primary fw-bold">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Cliente
        </a>
    <?php endif; ?>
</div>

<form method="GET" action="<?= base_url('clientes') ?>" class="row g-2 mb-3">
    <div class="col-md-6">
        <input type="text" name="q" class="form-control" placeholder="Buscar por nombre, documento, correo o celular" value="<?= esc($filtros['q']) ?>">
    </div>
    <div class="col-md-3">
        <select name="id_estado_cliente" class="form-select">
            <option value="">Todos los estados</option>
            <?php foreach ($estados as $e): ?>
                <option value="<?= $e['id_estado_cliente'] ?>" <?= (string) $filtros['id_estado_cliente'] === (string) $e['id_estado_cliente'] ? 'selected' : '' ?>><?= esc($e['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-outline-primary flex-fill"><i class="bi bi-search"></i> Buscar</button>
        <a href="<?= base_url('clientes') ?>" class="btn btn-outline-secondary">Limpiar</a>
    </div>
</form>

<?php if (empty($clientes)): ?>
    <div class="text-center text-muted p-5 bg-white rounded-3 shadow-sm border">
        <i class="bi bi-people display-4"></i>
        <p class="mt-3">No se encontraron clientes.</p>
    </div>
<?php else: ?>
    <div class="table-responsive bg-white rounded-3 shadow-sm border p-3">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Documento</th>
                    <th>Celular</th>
                    <th>Correo</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $c): ?>
                    <?php
                        $badge = match ((int) $c['id_estado_cliente']) {
                            1 => 'success',
                            2 => 'secondary',
                            default => 'danger',
                        };
                    ?>
                    <tr>
                        <td class="fw-bold"><?= esc($c['nombre_completo']) ?></td>
                        <td><?= esc($c['tipo_doc']) ?> <?= esc($c['numero_identificacion']) ?></td>
                        <td><?= esc($c['numero_celular']) ?></td>
                        <td><?= esc($c['correo_electronico']) ?></td>
                        <td><span class="badge bg-<?= $badge ?>"><?= esc($c['estado_nombre']) ?></span></td>
                        <td class="text-end">
                            <a href="<?= base_url('clientes/show/' . $c['id_cliente']) ?>" class="btn btn-sm btn-outline-secondary" title="Detalle"><i class="bi bi-eye"></i></a>
                            <?php if (puede('clientes', 'editar')): ?>
                                <a href="<?= base_url('clientes/edit/' . $c['id_cliente']) ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form action="<?= base_url('clientes/estado/' . $c['id_cliente']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-warning" title="<?= (int) $c['id_estado_cliente'] === 1 ? 'Inactivar' : 'Activar' ?>"
                                        onclick="return confirm('¿Cambiar el estado de este cliente?')">
                                        <i class="bi bi-<?= (int) $c['id_estado_cliente'] === 1 ? 'toggle-on' : 'toggle-off' ?>"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            <?php if (puede('clientes', 'eliminar')): ?>
                                <form action="<?= base_url('clientes/delete/' . $c['id_cliente']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Eliminar este cliente?')"><i class="bi bi-trash"></i></button>
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
