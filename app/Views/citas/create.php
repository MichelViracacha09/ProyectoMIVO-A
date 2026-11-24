<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="card shadow-sm border-0 mx-auto" style="max-width: 600px;">
    <div class="card-body p-4">
        <h3 class="fw-bold text-primary mb-4"><i class="bi bi-calendar-plus me-2"></i>Agendar Cita</h3>
        <?php if (empty($servicios)): ?>
            <div class="alert alert-warning">
                Aún no hay servicios disponibles para agendar. Contacta al administrador.
            </div>
        <?php else: ?>
            <form action="<?= base_url('citas/store') ?>" method="POST">
                <?= csrf_field() ?>
                <?php if ($esAdmin): ?>
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <select name="id_cliente" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($clientes as $cli): ?>
                                <option value="<?= $cli['id_cliente'] ?>" <?= (string) old('id_cliente') === (string) $cli['id_cliente'] ? 'selected' : '' ?>><?= esc($cli['nombre_completo']) ?> (<?= esc($cli['numero_identificacion']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <label class="form-label">Servicio</label>
                    <select name="id_servicio" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($servicios as $serv): ?>
                            <option value="<?= $serv['id_servicio'] ?>" <?= (string) old('id_servicio') === (string) $serv['id_servicio'] ? 'selected' : '' ?>><?= esc($serv['nombre_servicio']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha_cita" class="form-control" min="<?= date('Y-m-d') ?>" value="<?= esc(old('fecha_cita')) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hora</label>
                        <input type="time" name="hora_cita" class="form-control" value="<?= esc(old('hora_cita')) ?>" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Observaciones (opcional)</label>
                    <textarea name="observaciones" class="form-control" rows="3" maxlength="255"><?= esc(old('observaciones')) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-check-circle me-1"></i> Agendar Cita
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
