<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="bi bi-shield-lock me-2"></i>Roles</h2>
    <a href="<?= base_url('roles/new') ?>" class="btn btn-primary fw-bold"><i class="bi bi-plus-circle me-1"></i> Nuevo Rol</a>
</div>
<div class="table-responsive bg-white rounded-3 shadow-sm border p-3">
    <table class="table align-middle mb-0">
        <thead><tr><th>#</th><th>Rol</th><th>Descripción</th><th>Usuarios</th><th class="text-end">Acciones</th></tr></thead>
        <tbody>
            <?php foreach ($roles as $r): ?>
                <?php $esBase = in_array((int) $r['id_rol'], $base, true); ?>
                <tr>
                    <td><?= esc($r['id_rol']) ?></td>
                    <td class="fw-bold"><?= esc($r['nombre']) ?> <?= $esBase ? '<span class="badge bg-secondary">base</span>' : '' ?></td>
                    <td><?= esc($r['descripcion'] ?? '—') ?></td>
                    <td><?= (int) $r['total_usuarios'] ?></td>
                    <td class="text-end">
                        <a href="<?= base_url('roles/show/' . $r['id_rol']) ?>" class="btn btn-sm btn-outline-secondary" title="Usuarios"><i class="bi bi-eye"></i></a>
                        <a href="<?= base_url('permisos?id_rol=' . $r['id_rol']) ?>" class="btn btn-sm btn-outline-success" title="Permisos"><i class="bi bi-key"></i></a>
                        <a href="<?= base_url('roles/edit/' . $r['id_rol']) ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                        <?php if (!$esBase && (int) $r['total_usuarios'] === 0): ?>
                            <form action="<?= base_url('roles/delete/' . $r['id_rol']) ?>" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Eliminar este rol?')"><i class="bi bi-trash"></i></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
