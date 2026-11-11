<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table         = 'task';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['user_id', 'title', 'description', 'status'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Devuelve las tareas del usuario indicado, más recientes primero.
     */
    public function getTasksByUser(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Devuelve una tarea puntual, validando que pertenezca al usuario (seguridad).
     */
    public function getTaskForUser(int $taskId, int $userId): ?array
    {
        return $this->where('id', $taskId)
            ->where('user_id', $userId)
            ->first();
    }
}