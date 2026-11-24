<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-person-gear me-2"></i>Usuarios</h2>
    <a href="<?= base_url('usuarios/new') ?>" class="btn btn-primary fw-bold">
        <i class="bi bi-plus-circle me-1"></i> Nuevo Administrador
    </a>
</div>

<form method="GET" action="<?= base_url('usuarios') ?>" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="text" name="q" class="form-control" placeholder="Buscar por nombre, correo o usuario" value="<?= esc($filtros['q']) ?>">
    </div>
    <div class="col-md-2">
        <select name="id_rol" class="form-select">
            <option value="">Todos los roles</option>
            <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id_rol'] ?>" <?= (string) $filtros['id_rol'] === (string) $r['id_rol'] ? 'selected' : '' ?>><?= esc($r['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="estado" class="form-select">
            <option value="">Todos los estados</option>
            <?php foreach ($estados as $e): ?>
                <option value="<?= $e ?>" <?= $filtros['estado'] === $e ? 'selected' : '' ?>><?= ucfirst(strtolower($e)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-outline-primary flex-fill"><i class="bi bi-search"></i> Buscar</button>
        <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary">Limpiar</a>
    </div>
</form>

<?php if (empty($usuarios)): ?>
    <div class="text-center text-muted p-5 bg-white rounded-3 shadow-sm border">
        <p class="mb-0">No se encontraron usuarios.</p>
    </div>
<?php else: ?>
    <div class="table-responsive bg-white rounded-3 shadow-sm border p-3">
        <table class="table align-middle mb-0">
            <thead>
                <tr><th>#</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <?php
                        $nombre = $u['admin_nombre'] ?? $u['cliente_nombre'];
                        $correo = $u['admin_correo'] ?? $u['cliente_correo'];
                        $badge  = match (strtoupper($u['estado'])) {
                            'ACTIVO'   => 'success',
                            'INACTIVO' => 'secondary',
                            default    => 'danger',
                        };
                    ?>
                    <tr>
                        <td><?= esc($u['id_usuario']) ?></td>
                        <td class="fw-bold"><?= $nombre ? esc($nombre) : '<span class="text-muted">Sin perfil en esta aplicación</span>' ?></td>
                        <td><?= esc($correo ?? '—') ?></td>
                        <td><?= esc($u['rol_nombre']) ?></td>
                        <td><span class="badge bg-<?= $badge ?>"><?= esc($u['estado']) ?></span></td>
                        <td class="text-end">
                            <a href="<?= base_url('usuarios/show/' . $u['id_usuario']) ?>" class="btn btn-sm btn-outline-secondary" title="Detalle"><i class="bi bi-eye"></i></a>
                            <a href="<?= base_url('usuarios/edit/' . $u['id_usuario']) ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-3"><?= $pager->links() ?></div>
<?php endif; ?>
<?= $this->endSection() ?>
