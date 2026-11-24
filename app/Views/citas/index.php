<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-calendar-check me-2"></i>Citas</h2>
    <?php if (puede('citas', 'crear')): ?>
        <a href="<?= base_url('citas/new') ?>" class="btn btn-primary fw-bold">
            <i class="bi bi-plus-circle me-1"></i> Agendar Cita
        </a>
    <?php endif; ?>
</div>

<form method="GET" action="<?= base_url('citas') ?>" class="row g-2 mb-3">
    <div class="col-md-3">
        <input type="text" name="q" class="form-control" placeholder="Buscar cliente, documento o servicio" value="<?= esc($filtros['q']) ?>">
    </div>
    <div class="col-md-2">
        <select name="estado" class="form-select">
            <option value="">Todos los estados</option>
            <?php foreach ($estados as $e): ?>
                <option value="<?= $e ?>" <?= $filtros['estado'] === $e ? 'selected' : '' ?>><?= ucfirst(strtolower($e)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2"><input type="date" name="fecha_desde" class="form-control" title="Desde" value="<?= esc($filtros['fecha_desde']) ?>"></div>
    <div class="col-md-2"><input type="date" name="fecha_hasta" class="form-control" title="Hasta" value="<?= esc($filtros['fecha_hasta']) ?>"></div>
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
    <div class="col-12 d-flex gap-2">
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Filtrar</button>
        <a href="<?= base_url('citas') ?>" class="btn btn-outline-secondary">Limpiar</a>
    </div>
</form>

<?php if (empty($citas)): ?>
    <div class="text-center text-muted p-5 bg-white rounded-3 shadow-sm border">
        <i class="bi bi-calendar-x display-4"></i>
        <p class="mt-3">No se encontraron citas.</p>
    </div>
<?php else: ?>
    <div class="table-responsive bg-white rounded-3 shadow-sm border p-3">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Servicio</th>
                    <?php if ($esAdmin): ?><th>Cliente</th><?php endif; ?>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($citas as $cita): ?>
                    <?php
                        $estado = strtoupper($cita['estado_cita']);
                        $badgeClass = match ($estado) {
                            'PENDIENTE'  => 'secondary',
                            'CONFIRMADA' => 'warning text-dark',
                            'COMPLETADA' => 'success',
                            'CANCELADA'  => 'danger',
                            default      => 'secondary',
                        };
                        $cancelable = !in_array($estado, ['COMPLETADA', 'CANCELADA'], true);
                    ?>
                    <tr>
                        <td><?= esc($cita['fecha_cita']) ?></td>
                        <td><?= esc(substr($cita['hora_cita'], 0, 5)) ?></td>
                        <td><?= esc($cita['nombre_servicio']) ?></td>
                        <?php if ($esAdmin): ?><td><?= esc($cita['cliente_nombre']) ?></td><?php endif; ?>
                        <td><span class="badge bg-<?= $badgeClass ?>"><?= esc($cita['estado_cita']) ?></span></td>
                        <td class="text-end">
                            <a href="<?= base_url('citas/show/' . $cita['id_citas']) ?>" class="btn btn-sm btn-outline-secondary" title="Detalle"><i class="bi bi-eye"></i></a>
                            <?php if (puede('citas', 'editar')): ?>
                                <a href="<?= base_url('citas/edit/' . $cita['id_citas']) ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                            <?php endif; ?>
                            <?php if ($cancelable): ?>
                                <form action="<?= base_url('citas/cancel/' . $cita['id_citas']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-warning" title="Cancelar" onclick="return confirm('¿Cancelar esta cita?')"><i class="bi bi-x-circle"></i></button>
                                </form>
                            <?php endif; ?>
                            <?php if (puede('citas', 'eliminar')): ?>
                                <form action="<?= base_url('citas/delete/' . $cita['id_citas']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Eliminar esta cita?')"><i class="bi bi-trash"></i></button>
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
