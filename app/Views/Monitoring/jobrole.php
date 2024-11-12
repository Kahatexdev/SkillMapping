<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row my-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <h4 class="font-weight-bolder mb-0">
                        <a href="#" class="btn bg-gradient-info">
                            <i class="fas fa-user-tie text-lg opacity-10"></i>
                        </a>
                        Data Job Role
                    </h4>
                    <a href="<?= base_url('monitoring/jobroleCreate') ?>" class="btn bg-gradient-info btn-sm">
                        <i class="fas fa-plus text-lg opacity-10"></i>
                        Job Role
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Tabel Data Job Role</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="jobroleTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th colspan="2">Nama Bagian</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($jobrole)) : ?>
                                    <?php foreach ($jobrole as $role) : ?>
                                        <tr>
                                            <td><?= $role['id_jobrole'] ?></td>
                                            <td colspan="2"><?= $role['nama_bagian'] ?> - <?= $role['area'] ?></td>
                                            <td>
                                                <button class="btn bg-gradient-info btn-sm toggle-detail" data-id="<?= $role['id_jobrole'] ?>">Detail</button>
                                                <a href="<?= base_url('monitoring/jobroleEdit/' . $role['id_jobrole']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <button class="btn bg-gradient-danger btn-sm" onclick="confirmDelete('<?= $role['id_jobrole'] ?>')">Delete</button>
                                            </td>
                                        </tr>
                                        <tr id="detail-<?= $role['id_jobrole'] ?>" class="detail-row" style="display: none;">
                                            <td colspan="4">
                                                <div class="card bg-light p-3">
                                                    <h5 class="card-title">Detail Jobdesk</h5>
                                                    <div class="row">
                                                        <?php $categories = [
                                                            'KNITTER',
                                                            'C.O',
                                                            'Ringan',
                                                            'Standar',
                                                            'Sulit',
                                                            'ROSSO',
                                                            'SETTING',
                                                            'Potong Manual',
                                                            'Overdeck',
                                                            'Obras',
                                                            'Single Needle',
                                                            'Mc Lipat',
                                                            'Mc Kancing',
                                                            'Mc Press',
                                                            'JOB',
                                                            '6S'
                                                        ]; ?>
                                                        <?php foreach ($categories as $category): ?>
                                                            <?php $items = $groupedData[$role['id_jobrole']][$category] ?? null; ?>
                                                            <?php if ($items): ?>
                                                                <div class="col-6">
                                                                    <h6><?= $category ?></h6>
                                                                    <ol>
                                                                        <?php foreach ($items as $item): ?>
                                                                            <li><?= $item ?></li>
                                                                        <?php endforeach; ?>
                                                                    </ol>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No Job Role found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();

        $('.toggle-detail').on('click', function() {
            const id = $(this).data('id');
            $(`#detail-${id}`).toggle();
        });
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('monitoring/jobroleDelete/') ?>" + id;
            }
        });
    }
</script>

<script>
    $(document).ready(function() {
        <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: '<?= session()->getFlashdata('success') ?>',
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: '<?= session()->getFlashdata('error') ?>',
            });
        <?php endif; ?>
    });
</script>

<?php $this->endSection(); ?>