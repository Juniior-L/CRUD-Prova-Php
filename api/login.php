<?php
require_once '../config/database.php';
require_once '../config/loginDao.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$login = new Login();
$database = Database::getInstance();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $users = $database->read("users");
    if ($users["success"] !== false) {
        echo json_encode(['success' => true, 'data' => $users, 'message' => 'Users recuperados com sucesso'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'data' => null, 'message' => 'Erro ao recuperar usuários'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = $_POST;

    switch ($input["tipo"]) {
        case 'insert':
            $resposta = $login->createUser(
                $input["name"] ?? '',
                $input["email"] ?? '',
                $input["password"] ?? ''
            );

            if ($resposta["success"]) {
                $_SESSION["msg"] = "Usuário cadastrado com sucesso!";
                header("Location: ../pages/loginPage.php");
                exit();
            } else {
                $_SESSION["msg"] = "Erro: " . $resposta["message"];
                header("Location: ../pages/registerPage.php");
                exit();
            }

        case 'login':
            $resposta = $login->loginUser(
                $input["email"] ?? '',
                $input["password"] ?? ''
            );

            if ($resposta["success"]) {
                $_SESSION["user"] = $resposta["user"];
                header("Location: ../pages/homePage.php");
                exit();
            } else {
                $_SESSION["msg"] = "Erro: " . $resposta["message"];
                echo json_encode(["success" => false, "data" => null, "message" => $resposta["message"]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                exit();
            }

        case 'logout':
            $resposta = $login->logout();
            if ($resposta["success"] !== false) {
                echo json_encode(["success" => true, "data" => $resposta, "message" => "Usuário deslogado com sucesso"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(["success" => false, "data" => null, "message" => $resposta["message"]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
            exit();

        default:
            echo json_encode(["success" => false, "data" => null, "message" => $resposta["message"]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit();
    }
}
