<?php

namespace App\Controllers;

use App\Models\TaskModel;

class Tasks extends BaseController
{
    public function index()
    {
        $userId = (int) session()->get('user_id');
        $taskModel = new TaskModel();

        $data['tasks'] = $taskModel->getTasksByUser($userId);

        return view('tasks/index', $data);
    }

    public function create()
    {
        return view('tasks/create');
    }

    public function store()
    {
        $rules = [
            'title'  => 'required|min_length[3]|max_length[150]',
            'status' => 'required|in_list[pendiente,en_progreso,completada]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $taskModel = new TaskModel();

        $taskModel->insert([
            'user_id'     => (int) session()->get('user_id'),
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status')
        ]);

        return redirect()->to('/tasks')->with('success', 'Tarea creada con éxito.');
    }

    public function edit($id)
    {
        $userId = (int) session()->get('user_id');
        $taskModel = new TaskModel();

        $task = $taskModel->getTaskForUser((int) $id, $userId);

        if (!$task) {
            return redirect()->to('/tasks')->with('error', 'Tarea no encontrada.');
        }

        $data['task'] = $task;

        return view('tasks/edit', $data);
    }

    public function update($id)
    {
        $userId = (int) session()->get('user_id');
        $taskModel = new TaskModel();

        $task = $taskModel->getTaskForUser((int) $id, $userId);

        if (!$task) {
            return redirect()->to('/tasks')->with('error', 'Tarea no encontrada.');
        }

        $rules = [
            'title'  => 'required|min_length[3]|max_length[150]',
            'status' => 'required|in_list[pendiente,en_progreso,completada]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $taskModel->update($id, [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status')
        ]);

        return redirect()->to('/tasks')->with('success', 'Tarea actualizada con éxito.');
    }

    public function delete($id)
    {
        $userId = (int) session()->get('user_id');
        $taskModel = new TaskModel();

        $task = $taskModel->getTaskForUser((int) $id, $userId);

        if (!$task) {
            return redirect()->to('/tasks')->with('error', 'Tarea no encontrada.');
        }

        $taskModel->delete($id);

        return redirect()->to('/tasks')->with('success', 'Tarea eliminada con éxito.');
    }
}