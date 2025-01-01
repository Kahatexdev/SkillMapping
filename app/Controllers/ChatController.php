<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\MessageModel;

class ChatController extends BaseController
{
    protected $userModel;
    protected $messageModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->messageModel = new MessageModel();
    }

    public function fetchConversation($userId, $contactId)
    {
        $messages = $this->messageModel
            ->where("(sender_id = $userId AND receiver_id = $contactId) OR (sender_id = $contactId AND receiver_id = $userId)")
            ->orderBy('created_at', 'ASC')
            ->findAll();

        // Log data pesan untuk verifikasi
        log_message('debug', 'Messages: ' . print_r($messages, true));

        return $this->response->setJSON(['status' => 'success', 'messages' => $messages]);
    }

    // Mengambil daftar kontak dengan pesan terakhir
    public function getContactsWithLastMessage()
    {
        $userId = session('id_user'); // ID pengguna yang login
        $contacts = $this->userModel->findAll(); // Ambil semua kontak

        $contactsWithLastMessage = [];

        foreach ($contacts as $contact) {
            if ($contact['id_user'] != $userId) {
                // Ambil pesan terakhir antara user yang login dan kontak ini
                $lastMessage = $this->messageModel
                    ->where("(sender_id = $userId AND receiver_id = {$contact['id_user']}) OR (sender_id = {$contact['id_user']} AND receiver_id = $userId)")
                    ->orderBy('created_at', 'DESC')
                    ->limit(1)
                    ->first();

                $contactsWithLastMessage[] = [
                    'contact' => $contact,
                    'last_message' => $lastMessage
                ];
            }
        }

        return $this->response->setJSON(['status' => 'success', 'contacts' => $contactsWithLastMessage]);
    }


    public function getContacts($userId)
    {
        // Ambil semua kontak yang bukan user yang sedang login
        $contacts = $this->userModel
            ->where('id !=', $userId)  // Mengecualikan user yang sedang login
            ->findAll();  // Ambil semua data kontak

        return $this->response->setJSON(['contacts' => $contacts]);
    }



    public function sendMessage()
    {
        // Ambil data dari input form
        $data = [
            'sender_id' => $this->request->getPost('sender_id'),
            'receiver_id' => $this->request->getPost('receiver_id'),
            'message' => $this->request->getPost('message'),
            'created_at' => date('Y-m-d H:i:s'), // Format waktu
        ];

        // Validasi data
        if (empty($data['sender_id']) || empty($data['receiver_id']) || empty($data['message'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'All fields are required'])->setStatusCode(400);
        }

        // Masukkan data ke database
        if ($this->messageModel->insert($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Message sent']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to send message'])->setStatusCode(500);
        }
    }
}
