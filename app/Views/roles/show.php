<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-shield-lock me-2"></i><?= esc($rol['nombre']) ?></h2>
    <a href="<?= base_url('roles') ?>" class="btn btn-outline-secondary">Volver</a>
</div>
<p class="text-muted"><?= esc($rol['descripcion'] ?? '') ?></p>
<h5 class="fw-bold">Usuarios con este rol (<?= count($usuarios) ?>)</h5>
<div class="table-responsive bg-white rounded-3 shadow-sm border p-3">
    <?php if (empty($usuarios)): ?>
        <p class="text-muted mb-0">Ningún usuario tiene este rol.</p>
    <?php else: ?>
        <table class="table table-sm mb-0">
            <thead><tr><th>#</th><th>Nombre</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= esc($u['id_usuario']) ?></td>
                        <td><?= esc($u['admin_nombre'] ?? $u['cliente_nombre'] ?? 'Sin perfil en esta aplicación') ?></td>
                        <td><?= esc($u['estado']) ?></td>
                        <td class="text-end"><a href="<?= base_url('usuarios/show/' . $u['id_usuario']) ?>">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
