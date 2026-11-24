<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>
<div class="card shadow-sm border-0 mx-auto" style="max-width: 700px;">
    <div class="card-body p-4">
        <h3 class="fw-bold text-primary mb-4"><i class="bi bi-person-plus me-2"></i>Nuevo Cliente</h3>
        <form action="<?= base_url('clientes/store') ?>" method="POST">
            <?= $this->include('clientes/_form') ?>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
