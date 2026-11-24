<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-tools me-2"></i>Servicios</h2>
    <?php if (puede('servicios', 'crear')): ?>
        <a href="<?= base_url('servicios/new') ?>" class="btn btn-primary fw-bold">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Servicio
        </a>
    <?php endif; ?>
</div>

<form method="GET" action="<?= base_url('servicios') ?>" class="row g-2 mb-3">
    <div class="col-md-6">
        <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o descripción" value="<?= esc($filtros['q']) ?>">
    </div>
    <div class="col-md-3">
        <select name="id_tipo_servicio" class="form-select">
            <option value="">Todos los tipos</option>
            <?php foreach ($tipos as $t): ?>
                <option value="<?= $t['id_tipo_servicio'] ?>" <?= (string) $filtros['id_tipo_servicio'] === (string) $t['id_tipo_servicio'] ? 'selected' : '' ?>><?= esc($t['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-outline-primary flex-fill"><i class="bi bi-search"></i> Buscar</button>
        <a href="<?= base_url('servicios') ?>" class="btn btn-outline-secondary">Limpiar</a>
    </div>
</form>

<?php if (empty($servicios)): ?>
    <div class="text-center text-muted p-5 bg-white rounded-3 shadow-sm border">
        <i class="bi bi-tools display-4"></i>
        <p class="mt-3">No se encontraron servicios.</p>
    </div>
<?php else: ?>
    <div class="table-responsive bg-white rounded-3 shadow-sm border p-3">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Tipo</th>
                    <th>Precio</th>
                    <th>Duración</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicios as $serv): ?>
                    <tr>
                        <td class="fw-bold"><?= esc($serv['nombre_servicio']) ?></td>
                        <td><?= esc($serv['tipo_nombre']) ?></td>
                        <td>$<?= number_format((float) $serv['precio_servicio'], 0, ',', '.') ?></td>
                        <td><?= esc($serv['duracion_estimada'] ?? '—') ?></td>
                        <td class="text-end">
                            <a href="<?= base_url('servicios/show/' . $serv['id_servicio']) ?>" class="btn btn-sm btn-outline-secondary" title="Detalle"><i class="bi bi-eye"></i></a>
                            <?php if (puede('servicios', 'editar')): ?>
                                <a href="<?= base_url('servicios/edit/' . $serv['id_servicio']) ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                            <?php endif; ?>
                            <?php if (puede('servicios', 'eliminar')): ?>
                                <form action="<?= base_url('servicios/delete/' . $serv['id_servicio']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Eliminar este servicio?')"><i class="bi bi-trash"></i></button>
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
