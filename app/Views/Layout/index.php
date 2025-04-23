<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= base_url('assets/img/apple-icon.png') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo-ct.png') ?>">
    <title>
        Human Resource System
    </title>
    <!--     Fonts and icons     -->
    <link href="<?php base_url('assets/fonts/open_sans_family.css') ?>" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="<?= base_url('assets/css/nucleo-icons.css') ?>" rel=" stylesheet" />
    <link href="<?= base_url('assets/css/nucleo-svg.css') ?>" rel=" stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="<?= base_url('assets/fa/js/fontawesome.min.js') ?>"></script>
    <link href="<?= base_url('assets/fa/css/all.min.css') ?>" rel=" stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="<?= base_url('assets/css/soft-ui-dashboard.css?v=1.0.7') ?>" rel="stylesheet" />
    <!--  -->
    <script src="<?= base_url('assets/js/jquery/jquery-3.7.1.min.js') ?>" crossorigin="anonymous"></script>
    <link href="<?= base_url('assets/css/dataTables.dataTables.css') ?>" rel="stylesheet">
    <script src="<?= base_url('assets/js/dataTables.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/jquery.dataTables.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/buttons.dataTables.min.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css"> -->
    <style>
        .upload-container {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e3e6f0;
        }

        .upload-area {
            border: 2px dashed #007bff;
            padding: 30px;
            border-radius: 10px;
            background-color: #ffffff;
            color: #007bff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .upload-area:hover {
            background-color: #e9f4ff;
        }

        .upload-area i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .upload-area p {
            font-size: 16px;
            font-weight: bold;
        }

        .browse-link {
            color: #007bff;
            text-decoration: underline;
            cursor: pointer;
        }

        .upload-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .upload-button:hover {
            background-color: #0056b3;
        }
    </style>


</head>

<body class="g-sidenav-show bg-gray-100 bg-opacity-50">

    <?php include('sidebar.php'); ?>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0"><?= $title ?> </h6>
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        <ul class="navbar-nav  justify-content-end">
                            <li class="nav-item d-flex align-items-center">
                                <a href="<?= base_url(session()->get('role') . '/chat') ?>" class="nav-link text-body font-weight-bold px-0">
                                    <i class="fas fa-envelope text-lg opacity-10 me-2 position-relative">
                                        <span id="unread-count" class="badge bg-danger position-absolute top-5 start-100 translate-middle p-1">
                                            <sup>0</sup>
                                        </span>
                                        <!-- <span id="unread-count" class="notification text-danger position-absolute top-5 start-100 translate-middle p-1" style="display: none;">
                                            <sup>0</sup>
                                        </span> -->
                                    </i>
                                </a>
                            </li>



                            <li class="nav-item d-flex align-items-center">
                                <a href="" data-bs-toggle="modal" data-bs-target="#LogoutModal" class=" nav-link text-body font-weight-bold px-0">
                                    <img src="<?= base_url('assets/img/user.png') ?>" alt="User Icon" width="20">
                                    <span class="d-sm-inline d-none"><?= session()->get('username') ?>-<?= session()->get('area') ?></span>
                                </a>
                            </li>
                            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                                <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                                    <div class="sidenav-toggler-inner">
                                        <i class="sidenav-toggler-line"></i>
                                        <i class="sidenav-toggler-line"></i>
                                        <i class="sidenav-toggler-line"></i>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </nav>
        <?= $this->renderSection('content'); ?>
        <div class="modal fade  bd-example-modal-lg" id="LogoutModal" tabindex="-1" role="dialog" aria-labelledby="modalCancel" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Log Out</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url('logout') ?>" method="get">

                            Apakah anda yakin untuk keluar?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-danger">Keluar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!--   Core JS Files   -->
        <footer class="footer pt-3  ">
            <div class="container-fluid">
                <div class="row align-items-center justify-content-lg-between">
                    <div class="col-lg-6 mb-lg-0 mb-4">
                        <div class="copyright text-center text-sm text-muted text-lg-start">
                            ©
                            <script>
                                document.write(new Date().getFullYear())
                            </script>,
                            made with <i class="fas fa-laptop-code"></i> by
                            <a href="https://woz-u.com/wp-content/uploads/2020/04/how-stressful-is-software-development-woz-u-1280x720.jpg" class="font-weight-bold" target="_blank">RnD Team</a>
                            BP System
                        </div>
                    </div>

                </div>
            </div>
        </footer>
        </div>
    </main>

    <!--   Core JS Files   -->
    <!-- SweetAlert2 CDN -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="<?= base_url('assets/js/sweetalert2@11.js') ?>"></script>
    <script src="<?= base_url('assets/js/select2.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/smooth-scrollbar.min.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- <script src="<?= base_url('assets/js/jquery/jquery-3.7.1.min.js') ?>"></script> -->
    <!-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> -->
    <script src="<?= base_url('assets/js/jquery/jquery.dataTables.min.js') ?>"></script>
    <!-- <script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script> -->
    <!-- <script src="<?= base_url('assets/js/dataTables.buttons.min.js') ?>"></script> -->
    <!-- <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script> -->
    <script src="<?= base_url('assets/js/buttons.html5.min.js') ?>"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> -->
    <script>
        const baseUrl = "<?= base_url() ?>"; // Gunakan base_url() dari CodeIgniter
        const role = "<?= session()->get('role') ?>"; // Dapatkan role dari session
        let lastCheck = new Date().toISOString(); // Waktu awal pengecekan

        async function checkNewMessages() {
            try {
                const response = await fetch(`${baseUrl}/${role}/check-new-messages?last_check=${lastCheck}`);
                const data = await response.json();

                if (data.status === 'success' && data.new_messages.length > 0) {
                    data.new_messages.forEach(message => {
                        showNotification(message.sender_name, message.message);
                    });

                    // Perbarui timestamp terakhir
                    lastCheck = new Date().toISOString();
                }
            } catch (error) {
                console.error('Error checking messages:', error);
            }
        }

        function showNotification(senderId, message) {
            if (!("Notification" in window)) {
                alert("Browser Anda tidak mendukung notifikasi desktop.");
                return;
            }
            // Ambil username pengirim pesan
            // var username = document.querySelector(`#contact-${senderId} .contact-name`).textContent;

            if (Notification.permission === "granted") {
                new Notification("Pesan Baru!", {
                    body: `Dari: ${senderId}\nPesan: ${message}`,
                    icon: "<?= base_url('assets/img/user.png') ?>"
                });
            } else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(permission => {
                    if (permission === "granted") {
                        showNotification(senderId, message);
                    }
                });
            }
        }

        // Jalankan pengecekan pesan baru setiap 5 menit
        setInterval(checkNewMessages, 300000);

        // let lastCheck = new Date().toISOString(); // Waktu awal pengecekan

        // async function longPollMessages() {
        //     try {
        //         const response = await fetch(`${baseUrl}/${role}/long-poll-new-messages?last_check=${lastCheck}`);
        //         const data = await response.json();

        //         if (data.status === 'success' && data.new_messages.length > 0) {
        //             data.new_messages.forEach(message => {
        //                 showNotification(message.sender_name, message.message);
        //             });

        //             // Perbarui timestamp terakhir
        //             lastCheck = new Date().toISOString();
        //         }

        //         // Lanjutkan long polling
        //         longPollMessages();
        //     } catch (error) {
        //         console.error('Error checking messages:', error);

        //         // Retry setelah error
        //         setTimeout(longPollMessages, 5000);
        //     }
        // }

        // // Mulai long polling
        // longPollMessages();
    </script>
    <script>
        function fetchUnreadCount() {
            // Tentukan base URL secara dinamis
            const baseUrl = "<?= base_url() ?>"; // Gunakan base_url() dari CodeIgniter
            const role = "<?= session()->get('role') ?>"; // Dapatkan role dari session
            const fetchUrl = `${baseUrl}/${role}/count-unread-messages`; // Gabungkan URL

            fetch(fetchUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        const unreadCountElement = document.getElementById('unread-count');
                        if (unreadCountElement) {
                            unreadCountElement.textContent =
                                data.unread_messages > 0 ? data.unread_messages : '';
                        }
                    } else {
                        // console.error('Error fetching unread messages:', data.message);
                    }
                })
                .catch(error => {
                    // console.error('Error fetching unread count:', error);
                });
        }

        setInterval(fetchUnreadCount, 60000);

        // Panggilan awal saat halaman dimuat
        fetchUnreadCount();
    </script>

    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="<?= base_url('assets/js/buttons.js') ?>"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="<?= base_url('assets/js/soft-ui-dashboard.min.js?v=1.0.7') ?>"></script>


</body>

</html>