<?php $this->extend('Monitoring/layout'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
    <script>
    $(document).ready(function() {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= session()->getFlashdata('success') ?>',
        });
    });
    </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
    <script>
    $(document).ready(function() {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?= session()->getFlashdata('error') ?>',
        });
    });
    </script>
    <?php endif; ?>

    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5>Data Bagian</h5>
                </div>
                <div class="card-body">
                    <a href="<?= base_url('monitoring/bagianCreate') ?>" class="btn btn-primary btn-sm">Tambah Data
                        Bagian</a>
                    <div class="table-responsive">
                        <table class="table align-items-center justify-content-center">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Bagian</th>
                                    <th>Area Utama</th>
                                    <th>Area</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bagian)) : ?>
                                <?php foreach ($bagian as $bagian) : ?>
                                <tr>
                                    <td><?= $bagian['id_bagian'] ?></td>
                                    <td><?= $bagian['nama_bagian'] ?></td>
                                    <td><?= $bagian['area_utama'] ?></td>
                                    <td><?= $bagian['area'] ?></td>
                                    <td><?= $bagian['keterangan'] ?></td>

                                    <td>
                                        <a href="<?= base_url('monitoring/bagianEdit/' . $bagian['id_bagian']) ?>"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="confirmDelete('<?= $bagian['id_bagian'] ?>')">Delete</button>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                                <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">No users found</td>
                                </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
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
            window.location.href = "<?= base_url('monitoring/userDelete/') ?>" + id;
        }
    })
}
</script>

<?php $this->endSection(); ?>