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
                                <div class="ms-3">
                                    <h6 class="mb-0 text-truncate"><?= $contactData['contact']['username'] ?></h6>
                                    <small class="text-muted">
                                        <?= $contactData['last_message'] ? $contactData['last_message']['message'] : 'No messages yet' ?>
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
                    <button class="btn btn-sm btn-outline-dark">Options</button>
                </div>
                <div class="card-body overflow-auto" id="chat-area" style="height: 500px; background-color: #f8f9fa;">
                    <p class="text-muted text-center">Select a contact to view conversation</p>
                </div>
                <div class="card-footer">
                    <form id="chat-form" action="<?= base_url('send-message') ?>" method="POST">
                        <input type="text" id="chat-input" name="message" placeholder="Type a message" required />
                        <button type="submit" id="send-button">Send</button>
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
    // Fungsi untuk memuat percakapan
    function loadConversation(senderId, contactId) {
        fetch(`conversation/${senderId}/${contactId}`)
            .then(response => response.json())
            .then(data => {
                const chatArea = document.getElementById('chat-area');
                chatArea.innerHTML = ''; // Kosongkan chat area

                if (data.status === 'error' || !data.messages || data.messages.length === 0) {
                    chatArea.innerHTML = '<p class="text-muted text-center">No messages yet</p>';
                    return;
                }

                const messages = data.messages;

                messages.forEach(msg => {
                    const isSender = msg.sender_id === senderId; // Pesan yang dikirim oleh sender
                    const messageDiv = document.createElement('div');

                    messageDiv.className = `d-flex ${isSender ? 'justify-content-end' : 'justify-content-start'} mb-3`;

                    messageDiv.innerHTML = `
                    <div class="p-3 ${isSender ? 'bg-light border' : 'bg-gradient-info text-white'} rounded w-75">
                        <p class="mb-0">${msg.message}</p>
                        <small class="d-block text-end">${new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</small>
                    </div>
                `;
                    chatArea.appendChild(messageDiv);
                });

                chatArea.scrollTop = chatArea.scrollHeight; // Scroll ke bawah
            })
            .catch(error => {
                console.error('Error loading conversation:', error);
                document.getElementById('chat-area').innerHTML = '<p class="text-danger text-center">Failed to load conversation</p>';
            });
    }

    // Fungsi untuk menangani klik pada kontak
    document.querySelectorAll('.contact-item').forEach(contact => {
        contact.addEventListener('click', () => {
            const contactId = contact.getAttribute('data-contact-id');
            const contactName = contact.getAttribute('data-contact-name');

            // Update nama kontak di header chat
            document.getElementById('chat-contact-name').textContent = contactName;

            // Muat percakapan
            loadConversation(senderId, contactId);
        });
    });

    fetch('contacts')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const contactsList = document.getElementById('contacts-list');
                data.contacts.forEach(item => {
                    const contactItem = document.createElement('li');
                    contactItem.className = 'list-group-item d-flex align-items-center contact-item';
                    contactItem.setAttribute('data-contact-id', item.contact.id_user);
                    contactItem.setAttribute('data-contact-name', item.contact.username);
                    contactItem.style.cursor = 'pointer';

                    contactItem.innerHTML = `
                    <img src="<?= base_url('assets/img/user.png') ?>" alt="Profile" class="rounded-circle" width="40">
                    <div class="ms-3">
                        <h6 class="mb-0 text-truncate">${item.contact.username}</h6>
                        <small class="text-muted">${item.last_message ? item.last_message.message : 'No messages yet'}</small>
                    </div>
                `;
                    contactsList.appendChild(contactItem);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching contacts:', error);
        });


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

            fetch('send-message', {
                    method: 'POST',
                    body: data, // Kirim form data
                })
                .then(response => response.json())
                .then(responseData => {
                    console.log('Response data:', responseData); // Debug respons server
                    if (responseData.status === 'success') {
                        loadConversation(senderId, receiverId); // Refresh percakapan
                        chatInput.value = ''; // Kosongkan input
                    } else {
                        alert(responseData.message); // Pesan error
                    }
                })
                .catch(error => {
                    console.error('Error:', error); // Debug jika ada error
                    alert('Failed to send message');
                });
        } else {
            alert('Message cannot be empty!');
        }
    });
</script>
<?php $this->endSection(); ?>