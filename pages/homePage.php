<?php
require_once '../../CRUD-Prova-Php/config/config.php';
require_once '../../CRUD-Prova-Php/config/database.php';

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: loginPage.php');
    exit;
}


$db = Database::getInstance();
$message = '';

$result = $db->read('tasks');
$tasks = $result['success'] ? $result['data'] : [];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Minhas Tarefas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #7b2ff7, #f107a3);
            margin: 0;
            padding: 0;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        header {
            width: 100%;
            padding: 20px;
            text-align: center;
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(8px);
            font-size: 2rem;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(8px);
            padding: 30px;
            width: 90%;
            max-width: 700px;
            margin-top: 40px;
            position: relative;
        }

        h2 {
            margin-bottom: 15px;
            font-weight: 600;
            color: #fff;
            text-align: center;
        }

        .task {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 15px;
            transition: 0.3s;
        }

        .task:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.02);
        }

        .task-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #fff;
        }

        .task-note {
            color: #ddd;
            font-size: 0.95rem;
            margin-top: 5px;
        }

        .task-date {
            margin-top: 8px;
            font-size: 0.8rem;
            color: #bbb;
            text-align: right;
        }

        .no-tasks {
            text-align: center;
            font-size: 1.1rem;
            color: #eee;
            opacity: 0.8;
            margin-top: 20px;
        }

        .btn-add {
            display: inline-block;
            margin: 20px auto 10px;
            padding: 12px 20px;
            background: #fff;
            color: #7b2ff7;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            cursor: pointer;
        }

        .btn-add:hover {
            background: #7b2ff7;
            color: #fff;
        }

        /* Modal (PHP puro) */
        #popup {
            display:
                <?= isset($_POST['abrir']) || isset($_POST['tipo']) ? 'flex' : 'none' ?>
            ;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 16px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
        }

        .modal-content input,
        .modal-content textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: none;
            border-radius: 8px;
            outline: none;
            font-size: 1rem;
            resize: none;
        }

        .modal-content button {
            margin-top: 15px;
            padding: 10px 18px;
            background: #fff;
            color: #7b2ff7;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .modal-content button:hover {
            background: #7b2ff7;
            color: #fff;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 600;
        }

        .alert.success {
            background: rgba(0, 255, 127, 0.3);
            color: #b4ffb4;
        }

        .alert.error {
            background: rgba(255, 0, 0, 0.3);
            color: #ffb4b4;
        }
    </style>
</head>

<body>
    <header>
        📋 Minhas Tarefas
        <form action="../api/login.php" method="POST" style="float:right;">
            <input type="hidden" name="tipo" value="logout">
            <button type="submit"
                style="background:none; border:none; color:white; font-size:1rem; cursor:pointer;">Sair</button>
        </form>
    </header>


    <div class="container">
        <h2>Lista de Tasks</h2>

        <?= $message ?>

        <div id="task-list">
            <?php if (!empty($tasks)): ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="task">
                        <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>
                        <div class="task-note"><?= htmlspecialchars($task['note']) ?></div>
                        <div class="task-date">📅 <?= date('d/m/Y H:i', strtotime($task['register'])) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-tasks">Nenhuma tarefa cadastrada ainda 😴</div>
            <?php endif; ?>
        </div>

        <form method="post">
            <input type="hidden" name="abrir" value="1">
            <button type="submit" class="btn-add">+ Nova Tarefa</button>
        </form>
    </div>

    <!-- Popup PHP -->
    <div id="popup">
        <div class="modal-content">
            <h2>Nova Tarefa</h2>
            <form method="post" action="../api/tasks.php">
                <input type="hidden" name="tipo" value="insert">
                <input type="text" name="title" placeholder="Título" required>
                <textarea name="note" placeholder="Descrição" rows="4" required></textarea>
                <button type="submit">Salvar</button>
            </form>
            <form method="get">
                <button type="submit" style="background:#f107a3; color:#fff; margin-top:10px;">Cancelar</button>
            </form>
        </div>
    </div>
</body>

</html>