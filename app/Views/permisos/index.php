<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-key me-2"></i>Permisos por rol</h2>
</div>

<form method="GET" action="<?= base_url('permisos') ?>" class="row g-2 mb-3">
    <div class="col-md-4">
        <select name="id_rol" class="form-select" onchange="this.form.submit()">
            <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id_rol'] ?>" <?= (int) $r['id_rol'] === (int) $rol['id_rol'] ? 'selected' : '' ?>><?= esc($r['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</form>

<?php if ($bloqueado): ?>
    <div class="alert alert-info">Los permisos del rol Administrador son de solo lectura para evitar perder el acceso al sistema.</div>
<?php endif; ?>

<form action="<?= base_url('permisos/save/' . $rol['id_rol']) ?>" method="POST">
    <?= csrf_field() ?>
    <div class="table-responsive bg-white rounded-3 shadow-sm border p-3">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Módulo</th>
                    <th>Ruta</th>
                    <?php foreach ($acciones as $a): ?><th class="text-center"><?= ucfirst($a) ?></th><?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matriz as $m): ?>
                    <tr>
                        <td class="fw-bold">
                            <i class="bi <?= esc($m['icono']) ?> me-1"></i><?= esc($m['nombre']) ?>
                            <?= $m['activo'] ? '' : '<span class="badge bg-secondary">inactivo</span>' ?>
                        </td>
                        <td><code>/<?= esc($m['ruta']) ?></code></td>
                        <?php foreach ($acciones as $a): ?>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input"
                                    name="permiso[<?= $m['id_modulo'] ?>][<?= $a ?>]" value="1"
                                    <?= $m['puede_' . $a] ? 'checked' : '' ?> <?= $bloqueado ? 'disabled' : '' ?>>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (!$bloqueado): ?>
        <button type="submit" class="btn btn-primary fw-bold mt-3"><i class="bi bi-check-circle me-1"></i> Guardar permisos</button>
    <?php endif; ?>
</form>
<p class="text-muted small mt-3">Los módulos Usuarios, Roles y Permisos son exclusivos del administrador y no se asignan por rol.</p>
<?= $this->endSection() ?>
