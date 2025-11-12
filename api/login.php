<?php
require_once __DIR__ . '/../config/loginDao.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$login = new Login();

function setFlash($message, $success = true)
{
    $_SESSION['flash'] = [
        'message' => $message,
        'success' => $success
    ];
}

function redirect($url)
{
    header("Location: $url");
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    $tipo = $_POST['tipo'] ?? null;

    if ($tipo === 'insert') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $res = $login->createUser($name, $email, $password);

        if ($res['success']) {
            setFlash('Usuário cadastrado com sucesso! Faça login.');
            redirect('../pages/loginPage.php');
        } else {
            setFlash($res['message'] ?? 'Erro ao cadastrar usuário.', false);
            redirect('../pages/registerPage.php');
        }
    }

    if ($tipo === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $res = $login->loginUser($email, $password);

        if ($res['success']) {
            redirect('../pages/homePage.php');
        } else {
            setFlash($res['message'] ?? 'Credenciais inválidas.', false);
            redirect('../pages/loginPage.php');
        }
    }

    if ($tipo === 'logout') {
        $login->logout();
        setFlash('Logout realizado com sucesso!');
        redirect('../pages/loginPage.php');
    }

    setFlash('Operação inválida.', false);
    redirect('../pages/loginPage.php');
}

if ($method === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    if (isset($_SESSION['user'])) {
        echo json_encode(['success' => true, 'user' => $_SESSION['user']], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sem sessão'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    exit;
}
