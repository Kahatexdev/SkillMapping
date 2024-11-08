<?= $this->extend('Layout/index'); ?>
<?= $this->section('content'); ?>
<style>
    /* Style untuk container dinamis */
    #jobdesc-container {
        border: 1px solid #dcdcdc;
        padding: 15px;
        border-radius: 8px;
        background-color: #f4f6f9;
        /* Latar belakang lembut */
        margin-top: 15px;
    }

    /* Style untuk setiap item jobdesc dan keterangan */
    .jobdesc-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    /* Style untuk input dan select */
    .jobdesc-item select,
    .jobdesc-item input {
        margin-right: 10px;
        flex: 1;
        border-radius: 4px;
        border: 1px solid #ced4da;
        padding: 8px;
    }

    /* Tombol Add More dengan ikon + */
    .add-more {
        background-color: #5cb85c;
        /* Warna hijau lembut */
        color: #fff;
        border: none;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        font-size: 1.2em;
    }

    /* Tombol Remove dengan warna netral */
    .remove {
        background-color: #dc3545;
        /* Warna merah lembut */
        color: #fff;
        border: none;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        font-size: 1.2em;
    }

    /* Hover effects for buttons */
    .add-more:hover {
        background-color: #4cae4c;
    }

    .remove:hover {
        background-color: #c82333;
    }
</style>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0">
                                <a href="" # class="btn bg-gradient-info">
                                    <!-- icon data karyawan -->
                                    <i class="fas fa-user-tie text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                Form Edit Data Job Role
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('monitoring/dataJob') ?>"
                                    class="btn bg-gradient-secondary btn-sm">
                                    <!-- icon-->
                                    <i class="fas fa-solid fa-arrow-left text-sm opacity-10"></i>
                                    Kembali
                                </a>
                                <div> &nbsp;</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('monitoring/jobroleUpdate/' . $jobrole['id_jobrole']) ?>" method="post">
                        <!-- Pilihan Nama Bagian -->
                        <div class="form-group mb-2">
                            <label for="id_bagian">Nama Bagian <small class="text-danger">*</small></label>
                            <select name="id_bagian" id="id_bagian" class="form-control" required>
                                <option value="">Pilih Bagian</option>
                                <?php foreach ($bagians as $bagian) : ?>
                                    <option value="<?= $bagian['id_bagian'] ?>" <?= $jobrole['id_bagian'] == $bagian['id_bagian'] ? 'selected' : '' ?>>
                                        <?= $bagian['nama_bagian'] ?> - <?= $bagian['area'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Pilihan Status Tingkatan -->
                        <div class="form-group mb-2">
                            <label for="status">Status Tingkatan</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Pilih Status</option>
                                <option value="Ringan" <?= $jobrole['status'] == 'Ringan' ? 'selected' : '' ?>>Ringan</option>
                                <option value="Standar" <?= $jobrole['status'] == 'Standar' ? 'selected' : '' ?>>Standar</option>
                                <option value="Sulit" <?= $jobrole['status'] == 'Sulit' ? 'selected' : '' ?>>Sulit</option>
                            </select>
                            <small class="text-danger">*kosongkan jika bukan untuk montir</small>
                        </div>

                        <!-- Input Dinamis untuk Keterangan dan Jobdesk -->
                        <div class="form-group mb-2">
                            <label>Keterangan dan Jobdesk <small class="text-danger">*</small></label>
                            <div id="jobdesc-container">
                                <?php foreach ($jobrole['keterangan'] as $index => $keterangan) : ?>
                                    <div class="input-group mb-2 jobdesc-item">
                                        <select name="keterangan[]" class="form-control mr-2" required>
                                            <option value="">Pilih Keterangan</option>
                                            <option value="JOB" <?= $keterangan == 'JOB' ? 'selected' : '' ?>>Job Utama</option>
                                            <option value="6S" <?= $keterangan == '6S' ? 'selected' : '' ?>>6S</option>
                                        </select>
                                        <input type="text" class="form-control" name="jobdesc[]" value="<?= $jobrole['jobdesc'][$index] ?>" required>
                                        <?php if ($index === 0) : ?>
                                            <button type="button" class="btn add-more">+</button>
                                        <?php else : ?>
                                            <button type="button" class="btn remove">−</button>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Tombol Simpan -->
                        <button type="submit" class="btn bg-gradient-info btn-sm w-100"><i class="fas fa-save text-sm opacity-10"></i> Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk menambahkan dan menghapus input dinamis -->
<script>
    $(document).ready(function() {
        // Fungsi untuk menambah input keterangan dan jobdesk baru
        $('.add-more').click(function() {
            $('#jobdesc-container').append(`
                <div class="input-group mb-2 jobdesc-item">
                    <select name="keterangan[]" class="form-control mr-2" required>
                        <option value="">Pilih Keterangan</option>
                        <option value="JOB">Job Utama</option>
                        <option value="6S">6S</option>
                    </select>
                    <input type="text" class="form-control" name="jobdesc[]" placeholder="Jobdesk" required>
                    <button type="button" class="btn remove">−</button>
                </div>
            `);
        });

        // Fungsi untuk menghapus input keterangan dan jobdesk
        $(document).on('click', '.remove', function() {
            $(this).closest('.jobdesc-item').remove();
        });
    });
</script>

<?= $this->endSection(); ?>