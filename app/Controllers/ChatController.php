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

    public function getContactsWithLastMessage()
    {
        $userId = session('id_user'); // ID pengguna yang login
        $contacts = $this->userModel->findAll(); // Ambil semua kontak

        $contactsWithLastMessage = array_map(function ($contact) use ($userId) {
            if ($contact['id_user'] != $userId) {
                // Ambil pesan terakhir antara user yang login dan kontak ini
                $lastMessage = $this->messageModel
                    ->where('(sender_id = :user_id: AND receiver_id = :contact_id:) OR (sender_id = :contact_id: AND receiver_id = :user_id:)', [
                        'user_id' => $userId,
                        'contact_id' => $contact['id_user']
                    ])
                    ->orderBy('created_at', 'DESC')
                    ->first();
            } else {
                $lastMessage = null;
            }

            return [
                'contact' => $contact,
                'last_message' => $lastMessage
            ];
        }, $contacts);

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

    public function countUnreadMessages()
    {
        $userId = session('id_user'); // ID pengguna yang login

        // Hitung hanya pesan yang belum dibaca
        $unreadCount = $this->messageModel
            ->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();

        log_message('debug', 'Unread messages count: ' . $unreadCount);

        return $this->response->setJSON([
            'status' => 'success',
            'unread_count' => $unreadCount
        ]);
    }




    public function markMessagesAsRead($contactId)
    {
        $userId = session('id_user'); // ID pengguna yang login

        // Query untuk memperbarui status pesan
        $updated = $this->messageModel
            ->where('sender_id', $contactId)
            ->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->set(['is_read' => 1])
            ->update();

        // Debugging: Cek apakah update berhasil
        if ($updated) {
            log_message('debug', 'Messages marked as read successfully.');
        } else {
            log_message('error', 'Failed to mark messages as read.');
        }

        // Debugging: Cek pesan setelah update
        $messagesAfterUpdate = $this->messageModel
            ->where('sender_id', $contactId)
            ->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->findAll();

        log_message('debug', 'Messages after update: ' . json_encode($messagesAfterUpdate));

        // Hitung ulang pesan belum dibaca
        $unreadCount = $this->messageModel
            ->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Messages marked as read.',
            'unread_count' => $unreadCount
        ]);
    }





}
