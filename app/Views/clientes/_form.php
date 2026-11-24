<?php $esEdicion = isset($cliente); ?>
<?= csrf_field() ?>
<div class="mb-3">
    <label class="form-label">Nombre completo</label>
    <input type="text" name="nombre_completo" class="form-control" maxlength="150" required value="<?= esc(old('nombre_completo', $cliente['nombre_completo'] ?? '')) ?>">
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Tipo de documento</label>
        <select name="id_identificacion" class="form-select" required>
            <option value="">Seleccione...</option>
            <?php foreach ($tipos_doc as $t): ?>
                <option value="<?= $t['id_identificacion'] ?>" <?= (string) old('id_identificacion', $cliente['id_identificacion'] ?? '') === (string) $t['id_identificacion'] ? 'selected' : '' ?>><?= esc($t['abreviatura']) ?> - <?= esc($t['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label">Número de identificación</label>
        <input type="text" name="numero_identificacion" class="form-control" maxlength="30" required value="<?= esc(old('numero_identificacion', $cliente['numero_identificacion'] ?? '')) ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Celular</label>
        <input type="text" name="numero_celular" class="form-control" maxlength="20" required value="<?= esc(old('numero_celular', $cliente['numero_celular'] ?? '')) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Correo electrónico</label>
        <input type="email" name="correo_electronico" class="form-control" maxlength="150" required value="<?= esc(old('correo_electronico', $cliente['correo_electronico'] ?? '')) ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-8">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" class="form-control" maxlength="255" value="<?= esc(old('direccion', $cliente['direccion'] ?? '')) ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" class="form-control" value="<?= esc(old('fecha_nacimiento', $cliente['fecha_nacimiento'] ?? '')) ?>">
    </div>
</div>
<?php if ($esEdicion): ?>
    <div class="mb-4">
        <label class="form-label">Estado</label>
        <select name="id_estado_cliente" class="form-select" required>
            <?php foreach ($estados as $e): ?>
                <option value="<?= $e['id_estado_cliente'] ?>" <?= (string) old('id_estado_cliente', $cliente['id_estado_cliente']) === (string) $e['id_estado_cliente'] ? 'selected' : '' ?>><?= esc($e['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
<?php else: ?>
    <div class="mb-4">
        <label class="form-label">Contraseña de acceso (opcional)</label>
        <input type="password" name="password" class="form-control" minlength="8" maxlength="72" autocomplete="new-password">
        <div class="form-text">Si la deja vacía, el cliente se registra sin cuenta para iniciar sesión.</div>
    </div>
<?php endif; ?>
<button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-check-circle me-1"></i> Guardar</button>
