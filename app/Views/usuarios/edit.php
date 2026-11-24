<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="card shadow-sm border-0 mx-auto" style="max-width: 600px;">
    <div class="card-body p-4">
        <h3 class="fw-bold text-primary mb-2"><i class="bi bi-pencil-square me-2"></i>Editar Usuario</h3>
        <p class="text-muted small">Rol: <strong><?= esc($usuario['rol_nombre']) ?></strong> (lo define el perfil del usuario).</p>
        <form action="<?= base_url('usuarios/update/' . $usuario['id_usuario']) ?>" method="POST">
            <?= csrf_field() ?>
            <?php if ($usuario['id_administrador']): ?>
                <div class="mb-3">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="nombre_completo" class="form-control" maxlength="150" required value="<?= esc(old('nombre_completo', $usuario['admin_nombre'])) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="usuario_login" class="form-control" maxlength="50" required value="<?= esc(old('usuario_login', $usuario['usuario_login'])) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="correo" class="form-control" maxlength="150" required value="<?= esc(old('correo', $usuario['admin_correo'])) ?>">
                </div>
            <?php elseif ($usuario['id_cliente']): ?>
                <p class="mb-3"><strong><?= esc($usuario['cliente_nombre']) ?></strong>. Los datos personales se editan en
                    <a href="<?= base_url('clientes/edit/' . $usuario['id_cliente']) ?>">Clientes</a>.</p>
            <?php else: ?>
                <p class="mb-3 text-muted">Usuario sin perfil en esta aplicación (por ejemplo, técnico). Solo se puede cambiar su estado y contraseña.</p>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select" <?= $esMismo ? 'title="No puede desactivar su propia cuenta"' : '' ?>>
                    <?php foreach ($estados as $e): ?>
                        <option value="<?= $e ?>" <?= old('estado', strtoupper($usuario['estado'])) === $e ? 'selected' : '' ?>><?= ucfirst(strtolower($e)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">Nueva contraseña (dejar vacío para no cambiarla)</label>
                <input type="password" name="password" class="form-control" minlength="8" maxlength="72" autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-check-circle me-1"></i> Guardar</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
