<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table            = 'messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = ['sender_id', 'receiver_id', 'message', 'is_read', 'created_at'];


    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = false;
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function unreadMessages($userId)
    {
        return $this->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    public function getNewMessages($userId, $lastCheck)
    {
        return $this->select('messages.*, user.username AS sender_name')
        ->join('user', 'user.id_user = messages.sender_id') // Join dengan tabel user untuk mendapatkan nama pengirim
        ->where('messages.receiver_id', $userId)
            ->where('messages.is_read', 0) // Hanya pesan yang belum dibaca
            ->where('messages.created_at >', $lastCheck) // Hanya pesan setelah lastCheck
            ->orderBy('messages.created_at', 'ASC')
            ->findAll();
    }

    public function getNewMessagesWithSenderName($userId, $lastCheck)
    {
        return $this->select('messages.*, user.username AS sender_name')
        ->join('user', 'user.id_user = messages.sender_id') // Join dengan tabel user untuk mendapatkan nama pengirim
        ->where('messages.receiver_id', $userId)
            ->where('messages.is_read', 0) // Hanya pesan yang belum dibaca
            ->where('messages.created_at >', $lastCheck) // Hanya pesan setelah lastCheck
            ->orderBy('messages.created_at', 'ASC')
            ->findAll();
    }


}
