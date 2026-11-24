<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<?php
    $nombre = $usuario['admin_nombre'] ?? $usuario['cliente_nombre'];
    $correo = $usuario['admin_correo'] ?? $usuario['cliente_correo'];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-person me-2"></i><?= esc($nombre ?? 'Usuario #' . $usuario['id_usuario']) ?></h2>
    <div class="d-flex gap-2">
        <a href="<?= base_url('usuarios/edit/' . $usuario['id_usuario']) ?>" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
        <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>
<div class="card shadow-sm border-0">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Rol</dt><dd class="col-sm-9"><?= esc($usuario['rol_nombre']) ?></dd>
            <dt class="col-sm-3">Estado</dt><dd class="col-sm-9"><?= esc($usuario['estado']) ?></dd>
            <dt class="col-sm-3">Correo</dt><dd class="col-sm-9"><?= esc($correo ?? '—') ?></dd>
            <?php if ($usuario['id_administrador']): ?>
                <dt class="col-sm-3">Usuario de acceso</dt><dd class="col-sm-9"><?= esc($usuario['usuario_login']) ?></dd>
            <?php endif; ?>
            <?php if ($usuario['id_cliente']): ?>
                <dt class="col-sm-3">Perfil de cliente</dt><dd class="col-sm-9"><a href="<?= base_url('clientes/show/' . $usuario['id_cliente']) ?>">Ver cliente</a></dd>
            <?php endif; ?>
            <dt class="col-sm-3">Creado</dt><dd class="col-sm-9"><?= esc($usuario['fecha_creacion']) ?></dd>
            <dt class="col-sm-3">Actualizado</dt><dd class="col-sm-9"><?= esc($usuario['fecha_actualizacion'] ?? '—') ?></dd>
        </dl>
    </div>
</div>
<?= $this->endSection() ?>
