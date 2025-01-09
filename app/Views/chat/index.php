<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<style>
    /* Styling for the form container */
    #chat-form {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 2px solid #ccc;
        padding: 10px;
        background-color: #f9f9f9;
    }

    /* Styling pesan dari pengirim */
    .bg-primary {
        background-color: #007bff !important;
        color: white;
    }

    /* .bg-light {
        background-color: #f8f9fa !important;
        color: black;
    } */

    /* Area chat dengan padding yang memadai */
    /* Area chat dengan padding yang memadai */
    #chat-area {
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        height: 500px;
        overflow-y: auto;
        background-color: #f8f9fa;
    }

    /* Gaya pesan untuk pengirim */
    .d-flex.justify-content-end>.p-3 {
        background-color: #007bff;
        /* Warna biru */
        color: white;
        border-radius: 15px 15px 0 15px;
        max-width: 75%;
        /* Batasi lebar pesan */
        word-wrap: break-word;
    }

    /* Gaya pesan untuk penerima */
    .d-flex.justify-content-start>.p-3 {
        background-color: #f1f1f1;
        /* Warna abu terang */
        color: black;
        border-radius: 15px 15px 15px 0;
        max-width: 75%;
        /* Batasi lebar pesan */
        word-wrap: break-word;
    }

    /* Tambahkan efek margin */
    /* .d-flex {
        margin: 0 10px;
    } */



    /* Styling for the message input */
    #chat-input {
        width: 80%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
        transition: all 0.3s ease;
    }

    #chat-input:focus {
        border-color: #007bff;
    }

    /* Styling for the send button */
    #send-button {
        width: 15%;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        background-color: #007bff;
        color: white;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #send-button:hover {
        background-color: #0056b3;
    }

    #send-button:active {
        background-color: #003d7a;
    }

    /* Responsive styling */
    @media (max-width: 768px) {
        #chat-form {
            flex-direction: column;
            align-items: stretch;
        }

        #chat-input {
            width: 100%;
            margin-bottom: 10px;
        }

        #send-button {
            width: 100%;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar for chat contacts -->
        <div class="col-md-3">
            <div class="card h-100 shadow border-0">
                <div class="card-header">
                    <h5 class="text-dark mb-0">Contacts List</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush" id="contacts-list">
                        <?php foreach ($contacts as $contactData): ?>
                            <?php if ($contactData['contact']['id_user'] == session('id_user')) continue; ?>
                            <li class="list-group-item d-flex align-items-center contact-item"
                                data-contact-id="<?= $contactData['contact']['id_user'] ?>"
                                data-contact-name="<?= $contactData['contact']['username'] ?>"
                                style="cursor: pointer;">
                                <img src="<?= base_url('assets/img/user.png') ?>" alt="Profile" class="rounded-circle" width="40">
                                <div class="ms-3" id="contact-<?= $contactData['contact']['id_user'] ?>">
                                    <h6 class="contact-name"><?= htmlspecialchars($contactData['contact']['username'], ENT_QUOTES, 'UTF-8') ?></h6>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($contactData['last_message']['message'] ?? 'No messages yet', ENT_QUOTES, 'UTF-8') ?>
                                    </small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main chat area -->
        <div class="col-md-9">
            <div class="card h-100 shadow border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-dark mb-0">Chat with <span id="chat-contact-name">Select a contact</span></h5>
                    <!-- <button class="btn btn-sm btn-outline-dark">Hapus Semua Pesan</button> -->
                </div>
                <div class="card-body overflow-auto" id="chat-area" style="height: 500px; background-color: #f8f9fa;">
                    <p class="text-muted text-center">Select a contact to view conversation</p>
                </div>
                <div class="card-footer">
                    <form id="chat-form" action="<?= base_url('send-message') ?>" method="POST">
                        <textarea id="chat-input" name="message" class="form-control" placeholder="Type a message" required></textarea>
                        <button type="submit" id="send-button"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ID pengguna yang login
    let senderId = Number(<?= session('id_user') ?>);
    let receiverId = null; // ID kontak yang dipilih
    const baseUrl = "<?= base_url() ?>"; // Gunakan base_url() dari CodeIgniter
    const role = "<?= session()->get('role') ?>"; // Dapatkan role dari session
    const fetchUrlSendMessage = `${baseUrl}/${role}/send-message`; // URL untuk mengirim pesan

    // const fetchUrlContacts = `${baseUrl}/${role}/contacts`; // URL untuk mengambil daftar kontak

    function sanitizeHTML(str) {
        var temp = document.createElement('div');
        temp.textContent = str;
        return temp.innerHTML;
    }

    // Fungsi untuk memuat percakapan
    function loadConversation(senderId, contactId) {
        const fetchUrlConversation = `${baseUrl}/${role}/conversation/${senderId}/${contactId}`; // Gabungkan URL
        const fetchUrlMarkAsRead = `${baseUrl}/${role}/mark-messages-as-read/${contactId}`; // URL untuk menandai pesan sebagai sudah dibaca
        fetch(fetchUrlConversation)
            .then(response => response.json())
            .then(data => {
                const chatArea = document.getElementById('chat-area');
                chatArea.innerHTML = ''; // Kosongkan area chat

                if (data.status === 'error' || !data.messages || data.messages.length === 0) {
                    chatArea.innerHTML = '<p class="text-muted text-center">No messages yet</p>';
                    return;
                }

                // Update pesan sebagai sudah dibaca
                fetch(fetchUrlMarkAsRead, {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.status !== 'success') {
                            console.error('Failed to mark messages as read:', res.message);
                        }
                    })
                    .catch(error => console.error('Error marking messages as read:', error));

                data.messages.forEach(msg => {
                    const isSender = parseInt(msg.sender_id) === senderId; // Pastikan `sender_id` adalah angka
                    const messageDiv = document.createElement('div');

                    // Tambahkan kelas berdasarkan pengirim atau penerima
                    messageDiv.className = `d-flex ${isSender ? 'justify-content-end' : 'justify-content-start'} mb-3`;

                    // Tambahkan konten pesan
                    messageDiv.innerHTML = `
                    <div class="p-3 ${isSender ? 'bg-primary text-white' : 'bg-light text-dark'} rounded w-50">
                        <p class="mb-0">${sanitizeHTML(msg.message)}</p>
                        <small class="d-block text-end">${new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</small>
                    </div>
                `;
                    chatArea.appendChild(messageDiv);
                });

                chatArea.scrollTop = chatArea.scrollHeight; // Scroll ke bawah
            })
            .catch(error => {
                console.error('Error loading conversation:', error);
                chatArea.innerHTML = '<p class="text-danger text-center">Failed to load conversation</p>';
            });
    }





    // Fungsi untuk menangani klik pada kontak
    document.getElementById('contacts-list').addEventListener('click', (event) => {
        const target = event.target.closest('.contact-item');
        if (target) {
            receiverId = target.getAttribute('data-contact-id');
            const contactName = target.getAttribute('data-contact-name');

            document.getElementById('chat-contact-name').textContent = sanitizeHTML(contactName);
            loadConversation(senderId, receiverId);
        }
    });


    // fetch(fetchUrlContacts)
    //     .then(response => response.json())
    //     .then(data => {
    //         if (data.status === 'success') {
    //             const contactsList = document.getElementById('contacts-list');
    //             data.contacts.forEach(item => {
    //                 const contactItem = document.createElement('li');
    //                 contactItem.className = 'list-group-item d-flex align-items-center contact-item';
    //                 contactItem.setAttribute('data-contact-id', item.contact.id_user);
    //                 contactItem.setAttribute('data-contact-name', item.contact.username);
    //                 contactItem.style.cursor = 'pointer';

    //                 contactItem.innerHTML = `
    //                 <img src="<?= base_url('assets/img/user.png') ?>" alt="Profile" class="rounded-circle" width="40">
    //                 <div class="ms-3">
    //                     <h6 class="mb-0 text-truncate">${item.contact.username}</h6>
    //                     <small class="text-muted">${item.last_message ? item.last_message.message : 'No messages yet'}</small>
    //                 </div>
    //             `;
    //                 contactsList.appendChild(contactItem);
    //             });
    //         }
    //     })
    //     .catch(error => {
    //         console.error('Error fetching contacts:', error);
    //     });


    document.getElementById('chat-form').addEventListener('submit', (event) => {
        event.preventDefault(); // Mencegah form default submit

        const chatInput = document.getElementById('chat-input');
        const message = chatInput.value.trim();

        if (message && receiverId) {
            const data = new FormData();
            data.append('sender_id', senderId);
            data.append('receiver_id', receiverId);
            data.append('message', message);

            console.log('Sending data:', data); // Debug: lihat data yang dikirim

            fetch(fetchUrlSendMessage, {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success') {
                        chatInput.value = ''; // Kosongkan input
                        loadConversation(senderId, receiverId); // Muat ulang percakapan
                    } else {
                        console.error('Failed to send message:', res.message);
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                });
        }
    });
</script>
<?php $this->endSection(); ?>