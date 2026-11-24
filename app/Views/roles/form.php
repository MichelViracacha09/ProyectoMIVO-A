<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<?php
    $editando = isset($rol);
    $esBase   = $editando && in_array((int) $rol['id_rol'], \App\Filters\RoleFilter::ROLES, true);
?>
<div class="card shadow-sm border-0 mx-auto" style="max-width: 600px;">
    <div class="card-body p-4">
        <h3 class="fw-bold text-primary mb-4"><i class="bi bi-shield-lock me-2"></i><?= $editando ? 'Editar Rol' : 'Nuevo Rol' ?></h3>
        <form action="<?= base_url($editando ? 'roles/update/' . $rol['id_rol'] : 'roles/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" maxlength="50" <?= $esBase ? 'readonly' : 'required' ?> value="<?= esc(old('nombre', $rol['nombre'] ?? '')) ?>">
                <?php if ($esBase): ?><div class="form-text">Rol base del sistema: el nombre no se puede cambiar.</div><?php endif; ?>
            </div>
            <div class="mb-4">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" maxlength="255"><?= esc(old('descripcion', $rol['descripcion'] ?? '')) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-check-circle me-1"></i> Guardar</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
