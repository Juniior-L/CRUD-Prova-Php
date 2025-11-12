<?php
require_once __DIR__ . '/../config/loginDao.php';

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$login = new Login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? null;

    if ($tipo === 'insert') {
        $res = $login->createUser($_POST['name'] ?? '', $_POST['email'] ?? '', $_POST['password'] ?? '');
        if ($res['success']) {
            echo "<script>alert('Usuário cadastrado com sucesso!'); window.location.href='../pages/loginPage.php';</script>";
        } else {
            echo "<script>alert('{$res['message']}'); history.back();</script>";
        }
        exit;
    }

    if ($tipo === 'login') {
        $res = $login->loginUser($_POST['email'] ?? '', $_POST['password'] ?? '');

        if ($res['success']) {
            // se veio de form tradicional, redirecionar
            if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
                echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                header('Location: ../pages/homePage.php');
            }
        } else {
            if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
                echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo "<script>alert('{$res['message']}'); history.back();</script>";
            }
        }
        exit;
    }

    if ($tipo === 'logout') {
        $res = $login->logout();
        header('Location: ../pages/loginPage.php');
        exit;
    }

    echo json_encode(["success" => false, "message" => "Operação inválida"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (session_status() !== PHP_SESSION_ACTIVE)
        session_start();
    if (isset($_SESSION['user'])) {
        echo json_encode(["success" => true, "user" => $_SESSION['user']], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["success" => false, "message" => "Sem sessão"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
