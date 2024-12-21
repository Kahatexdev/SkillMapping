<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<style>
    #jobdesc-container {
        border: 1px solid #dcdcdc;
        padding: 15px;
        border-radius: 8px;
        background-color: #f4f6f9;
        margin-top: 15px;
    }

    .jobdesc-item {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 10px;
        gap: 10px;
    }

    .jobdesc-item select,
    .jobdesc-item input {
        flex: 1;
        border-radius: 4px;
        border: 1px solid #ced4da;
        padding: 8px;
    }

    .drag-handle {
        cursor: grab;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    .drag-handle i {
        font-size: 1.2em;
        color: #6c757d;
    }

    .add-more,
    .remove {
        background-color: #5cb85c;
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

    .remove {
        background-color: #dc3545;
    }

    .add-more:hover {
        background-color: #4cae4c;
    }

    .remove:hover {
        background-color: #c82333;
    }

    @media (max-width: 576px) {

        .jobdesc-item select,
        .jobdesc-item input {
            flex: 1 100%;
        }

        .drag-handle,
        .add-more,
        .remove {
            flex: 0;
        }
    }
</style>



<div class="container-fluid py-4">
    <!-- Notifikasi flashdata success dan error -->
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
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
                    html: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>

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
                                Form Input Data Job Role
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('Monitoring/dataJob') ?>"
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
    <!-- Konten form -->
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('Monitoring/jobroleStore') ?>" method="post">
                        <!-- Pilihan Nama Bagian -->
                        <div class="form-group mb-2">
                            <label for="id_bagian">Nama Bagian <small class="text-danger">*</small></label>
                            <select name="id_bagian" id="id_bagian" class="form-control" required>
                                <option value="">Pilih Bagian</option>
                                <?php foreach ($bagians as $bagian) : ?>
                                    <option value="<?= $bagian['id_bagian'] ?>"><?= $bagian['nama_bagian'] ?> - <?= $bagian['area_utama'] ?> - <?= $bagian['area'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>


                        <!-- Input Dinamis untuk Keterangan dan Jobdesk -->
                        <div class="form-group mb-2">
                            <label>Keterangan dan Jobdesk <small class="text-danger">*</small></label>
                            <div id="jobdesc-container">
                                <div class="input-group mb-2 jobdesc-item">
                                    <button type="button" class="drag-handle"><i class="fas fa-grip-vertical"></i></button>
                                    <select name="keterangan[]" class="form-control" required>
                                        <option value="">Pilih Keterangan</option>
                                        <option value="KNITTER">KNITTER</option>
                                        <option value="OPERATOR">OPERATOR</option>
                                        <option value="C.O">C.O</option>
                                        <option value="Ringan">Ringan</option>
                                        <option value="Standar">Standar</option>
                                        <option value="Sulit">Sulit</option>
                                        <option value="JOB">JOB</option>
                                        <option value="ROSSO">ROSSO</option>
                                        <option value="SETTING">SETTING</option>
                                        <option value="Potong Manual">Potong Manual</option>
                                        <option value="Overdeck">Overdeck</option>
                                        <option value="Obras">Obras</option>
                                        <option value="Single Needle">Single Needle</option>
                                        <option value="Mc Lipat">Mc Lipat</option>
                                        <option value="Mc Kancing">Mc Kancing</option>
                                        <option value="Mc Press">Mc Press</option>
                                        <option value="6S">6S</option>
                                    </select>
                                    <input type="text" class="form-control" name="jobdesc[]" placeholder="Jobdesk" required>
                                    <button type="button" class="btn add-more"><i class="fas fa-plus"></i></button>
                                    <button type="button" class="btn remove">−</button>
                                </div>
                            </div>
                        </div>


                        <!-- Tombol Simpan -->
                        <button type="submit" class="btn bg-gradient-info btn-sm w-100"><i class="fas fa-save text-sm opacity-10"></i> Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<!-- JavaScript untuk menambah dan menghapus input dinamis -->
<script>
    $(document).ready(function() {
        const container = document.getElementById('jobdesc-container');
        Sortable.create(container, {
            animation: 150,
            handle: '.drag-handle', // Drag hanya dapat dilakukan pada tombol handle
            ghostClass: 'sortable-ghost',
        });

        // Event delegation untuk menambahkan input baru
        $('#jobdesc-container').on('click', '.add-more', function() {
            $('#jobdesc-container').append(`
                <div class="input-group mb-2 jobdesc-item">
                    <button type="button" class="drag-handle"><i class="fas fa-grip-vertical"></i></button>
                    <select name="keterangan[]" class="form-control" required>
                        <option value="">Pilih Keterangan</option>
                        <option value="KNITTER">KNITTER</option>
                        <option value="OPERATOR">OPERATOR</option>
                        <option value="C.O">C.O</option>
                        <option value="Ringan">Ringan</option>
                        <option value="Standar">Standar</option>
                        <option value="Sulit">Sulit</option>
                        <option value="JOB">JOB</option>
                        <option value="ROSSO">ROSSO</option>
                        <option value="SETTING">SETTING</option>
                        <option value="Potong Manual">Potong Manual</option>
                        <option value="Overdeck">Overdeck</option>
                        <option value="Obras">Obras</option>
                        <option value="Single Needle">Single Needle</option>
                        <option value="Mc Lipat">Mc Lipat</option>
                        <option value="Mc Kancing">Mc Kancing</option>
                        <option value="Mc Press">Mc Press</option>
                        <option value="6S">6S</option>
                    </select>
                    <input type="text" class="form-control" name="jobdesc[]" placeholder="Jobdesk" required>
                    <button type="button" class="btn add-more"><i class="fas fa-plus"></i></button>
                    <button type="button" class="btn remove">−</button>
                </div>
            `);
        });

        // Event delegation untuk menghapus input
        $('#jobdesc-container').on('click', '.remove', function() {
            $(this).closest('.jobdesc-item').remove();
        });
    });
</script>



<?php $this->endSection(); ?>