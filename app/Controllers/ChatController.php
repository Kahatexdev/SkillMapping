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
        // log_message('debug', 'Messages: ' . print_r($messages, true));

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

        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not logged in']);
        }

        // Hitung hanya pesan yang belum dibaca
        $unreadMessages = $this->messageModel->unreadMessages($userId);

        return $this->response->setJSON([
            'status' => 'success',
            'unread_messages' => $unreadMessages,
        ]);
    }



    public function markMessagesAsRead($contactId)
    {
        $userId = session('id_user'); // ID pengguna yang login

        // Query untuk memperbarui status pesan
        $this->messageModel
            ->where('sender_id', $contactId)
            ->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->set(['is_read' => 1])
            ->update();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Messages marked as read.'
        ]);
    }
    public function getAllContacts()
    {
        // sort by role monitoring, mandor, trainingschool
        $contacts = $this->userModel->select('id_user, username, role')->findAll();
        return $this->response->setJSON($contacts);
    }

    public function checkNewMessages()
    {
        $userId = session('id_user'); // ID pengguna yang login
        $lastCheck = $this->request->getGet('last_check'); // Timestamp terakhir pengecekan

        if (!$userId || !$lastCheck) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        // Ambil pesan baru
        $newMessages = $this->messageModel->getNewMessages($userId, $lastCheck);

        return $this->response->setJSON([
            'status' => 'success',
            'new_messages' => $newMessages
        ]);
    }

    public function longPollNewMessages()
    {
        $userId = session('id_user');
        $lastCheck = $this->request->getGet('last_check');

        if (!$userId || !$lastCheck) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        $startTime = time();
        $timeout = 30; // Maksimum waktu tunggu 30 detik

        while (time() - $startTime < $timeout) {
            // Cek apakah ada pesan baru
            $newMessages = $this->messageModel->getNewMessagesWithSenderName($userId, $lastCheck);

            if (!empty($newMessages)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'new_messages' => $newMessages
                ]);
            }

            sleep(1); // Tunggu 1 detik sebelum mencoba lagi
        }

        // Tidak ada pesan baru setelah timeout
        return $this->response->setJSON([
            'status' => 'success',
            'new_messages' => []
        ]);
    }


}
