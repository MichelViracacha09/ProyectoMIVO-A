<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="card shadow-sm border-0 mx-auto" style="max-width: 600px;">
    <div class="card-body p-4">
        <h3 class="fw-bold text-primary mb-2"><i class="bi bi-person-plus me-2"></i>Nuevo Administrador</h3>
        <p class="text-muted small">Los clientes se gestionan en el módulo Clientes y los técnicos en el módulo técnico.</p>
        <form action="<?= base_url('usuarios/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Nombre completo</label>
                <input type="text" name="nombre_completo" class="form-control" maxlength="150" required value="<?= esc(old('nombre_completo')) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="usuario_login" class="form-control" maxlength="50" required value="<?= esc(old('usuario_login')) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Correo electrónico (se usa para iniciar sesión)</label>
                <input type="email" name="correo" class="form-control" maxlength="150" required value="<?= esc(old('correo')) ?>">
            </div>
            <div class="mb-4">
                <label class="form-label">Contraseña (mínimo 8 caracteres)</label>
                <input type="password" name="password" class="form-control" minlength="8" maxlength="72" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-check-circle me-1"></i> Crear usuario</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
