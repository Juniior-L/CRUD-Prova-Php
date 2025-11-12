<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$db = Database::getInstance();

if (!isset($_SESSION['user'])) {
    header('Location: ../pages/loginPage.php');
    exit;
}

$userId = (int)$_SESSION['user']['id'];
$method = $_SERVER['REQUEST_METHOD'];

function redirectWithMessage($message, $success = true) {
    $_SESSION['flash'] = [
        'message' => $message,
        'success' => $success
    ];
    header('Location: ../pages/homePage.php');
    exit;
}

if ($method === 'POST') {
    $tipo = $_POST['tipo'] ?? null;

    if ($tipo === 'insert') {
        $title = trim($_POST['title'] ?? '');
        $note = trim($_POST['note'] ?? '');

        if (empty($title) || empty($note)) {
            redirectWithMessage('Preencha todos os campos!', false);
        }

        $arr = [
            'title' => $title,
            'note' => $note,
            'register' => date('Y-m-d H:i:s'),
            'user_id' => $userId
        ];

        $res = $db->create('tasks', $arr);
        if ($res['success']) {
            redirectWithMessage('Tarefa criada com sucesso!');
        } else {
            redirectWithMessage('Erro ao criar tarefa: ' . ($res['message'] ?? ''), false);
        }
    }

    if ($tipo === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $note = trim($_POST['note'] ?? '');

        if (empty($id) || empty($title) || empty($note)) {
            redirectWithMessage('Dados inválidos para atualização.', false);
        }

        $check = $db->read('tasks', ['id' => $id, 'user_id' => $userId]);
        if (empty($check['data'])) {
            redirectWithMessage('Tarefa não encontrada ou sem permissão.', false);
        }

        $res = $db->update('tasks', [
            'title' => $title,
            'note' => $note
        ], ['id' => $id, 'user_id' => $userId]);

        if ($res['success']) {
            redirectWithMessage('Tarefa atualizada com sucesso!');
        } else {
            redirectWithMessage('Erro ao atualizar tarefa: ' . ($res['message'] ?? ''), false);
        }
    }

    if ($tipo === 'delete') {
        $id = (int)($_POST['id'] ?? 0);

        $check = $db->read('tasks', ['user_id' => $userId, 'id' => $id]);
        if (empty($check['data'])) {
            redirectWithMessage('Tarefa não encontrada ou sem permissão.', false);
        }

        $res = $db->delete('tasks', ['id' => $id, 'user_id' => $userId]);
        if ($res['success']) {
            redirectWithMessage('Tarefa deletada com sucesso!');
        } else {
            redirectWithMessage('Erro ao deletar tarefa: ' . ($res['message'] ?? ''), false);
        }
    }
}

redirectWithMessage('Ação inválida.', false);
