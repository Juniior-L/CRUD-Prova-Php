<?php
require_once __DIR__ . '/../../CRUD-Prova-Php/config/config.php';
require_once __DIR__ . '/../../CRUD-Prova-Php/config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_theme'])) {
    $current = $_SESSION['theme'] ?? 'light';
    $_SESSION['theme'] = ($current === 'dark') ? 'light' : 'dark';

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (!isset($_SESSION['user'])) {
    header('Location: ../pages/loginPage.php');
    exit;
}

$message = '';
$db = Database::getInstance();
$userId = (int) $_SESSION['user']['id'];

$resposta = $db->read('tasks', ['user_id' => $userId]);
$tasks = $resposta['success'] ? $resposta['data'] : [];

// Modal open 
$openModal = isset($_POST['abrir']) || isset($_POST['abrir_edit']);
$editId = $_POST['edit_id'] ?? '';
$editTitle = $_POST['edit_title'] ?? '';
$editNote = $_POST['edit_note'] ?? '';

// Theme class
$themeClass = (isset($_SESSION['theme']) && $_SESSION['theme'] === 'dark') ? 'dark-theme' : 'light-theme';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Minhas Tarefas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/home.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body class="<?= htmlspecialchars($themeClass) ?>">
    <header class="main-header">
        <div class="header-left">
            <div class="brand">📋 <span>Flow Forge</span></div>
            <div class="subtext">Organize. Faça. Repita.</div>
        </div>

        <div class="header-right">
            <!-- Theme toggle -->
            <form method="POST" class="theme-form" style="display:inline;">
                <input type="hidden" name="toggle_theme" value="1">
                <button type="submit" class="theme-btn" title="Alternar tema">
                    <?php if (isset($_SESSION['theme']) && $_SESSION['theme'] === 'dark'): ?>
                        ☀️
                    <?php else: ?>
                        🌙
                    <?php endif; ?>
                </button>
            </form>

            <!-- Logout -->
            <form action="../api/login.php" method="POST" class="logout-form" style="display:inline;">
                <input type="hidden" name="tipo" value="logout">
                <button type="submit" class="logout-btn">Sair</button>
            </form>
        </div>
    </header>

    <main class="content-area">
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert <?= $_SESSION['flash']['success'] ? 'success' : 'error' ?>">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <?php if (!empty($tasks)): ?>
            <div class="task-grid">
                <?php foreach ($tasks as $task): ?>
                    <div class="task-card">
                        <div class="task-header">
                            <h3><?= htmlspecialchars($task['title']) ?></h3>
                            <span class="task-date"><?= date('d/m/Y H:i', strtotime($task['register'])) ?></span>
                        </div>
                        <p class="task-note"><?= nl2br(htmlspecialchars($task['note'])) ?></p>
                        <div class="task-actions">
                            <form method="POST" action="../api/tasks.php">
                                <input type="hidden" name="id" value="<?= $task['id'] ?>">
                                <input type="hidden" name="tipo" value="delete">
                                <button class="btn-delete" title="Deletar">🗑️</button>
                            </form>

                            <form method="POST">
                                <input type="hidden" name="abrir_edit" value="1">
                                <input type="hidden" name="edit_id" value="<?= $task['id'] ?>">
                                <input type="hidden" name="edit_title" value="<?= htmlspecialchars($task['title']) ?>">
                                <input type="hidden" name="edit_note" value="<?= htmlspecialchars($task['note']) ?>">
                                <button type="submit" class="btn-edit" title="Editar">✏️</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-tasks">Nenhuma tarefa cadastrada ainda 😴</div>
        <?php endif; ?>
    </main>

    <form method="post" class="fab-form">
        <input type="hidden" name="abrir" value="1">
        <button type="submit" class="fab" title="Nova tarefa">＋</button>
    </form>

    <!-- MODAL -->
    <div id="popup" class="<?= $openModal ? 'active' : '' ?>">
        <div class="modal-content">
            <?php if (isset($_POST['abrir'])): ?>
                <h2>Nova Tarefa</h2>
                <form method="post" action="../api/tasks.php" class="task-form">
                    <input type="hidden" name="tipo" value="insert">
                    <input type="text" name="title" placeholder="Título da tarefa" required>
                    <textarea name="note" placeholder="Descrição" rows="4" required></textarea>
                    <button type="submit" class="btn-primary">Salvar</button>
                </form>
            <?php else: ?>
                <h2>Editar Tarefa</h2>
                <form method="POST" action="../api/tasks.php" class="task-form">
                    <input type="hidden" name="tipo" value="update">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($editId) ?>">
                    <input type="text" name="title" placeholder="Título" value="<?= htmlspecialchars($editTitle) ?>" required>
                    <textarea name="note" placeholder="Descrição" rows="4" required><?= htmlspecialchars($editNote) ?></textarea>
                    <button type="submit" class="btn-primary">Salvar</button>
                </form>
            <?php endif; ?>

            <form method="get" class="cancel-form">
                <button type="submit" class="btn-secondary">Cancelar</button>
            </form>
        </div>
    </div>
</body>

</html>
