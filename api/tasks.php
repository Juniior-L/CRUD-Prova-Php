<?php

include("../../CRUD-Prova-Php/config/config.php");
include("../../CRUD-Prova-Php/config/database.php");

$database = Database::getInstance();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $tasks = $database->read("tasks");
    if ($tasks["success"] !== false) {
        echo json_encode(['success' => true, 'data' => $tasks, 'message' => 'Tasks recuperados com sucesso'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'data' => null, 'message' => 'Erro ao recuperar usuários'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = $_POST;

    switch ($_POST["tipo"]) {
        case 'insert':
            $resposta = $database->create('tasks', ["title" => $input["title"], "note" => $input["note"]]);

            if ($resposta["success"] !== false) {
                
            } else {
                echo json_encode(["success" => false, "data" => null, "message" => $resposta["message"]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
            exit();
        case 'update':
            $resposta = $database->update('tasks', ["title" => $input["title"], "note" => $input["note"]], ["id" => $input["id"]]);
            if ($resposta["success"] !== false) {
                echo json_encode(["success" => true, "data" => $resposta, "message" => "Tarefa atualizada com sucesso"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(["success" => false, "data" => null, "message" => $resposta["message"]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
            exit();
        case 'delete':
            $resposta = $database->delete("tasks", ["id" => $input["id"]]);
            if ($resposta["success"] !== false) {
                echo json_encode(["success" => true, "data" => $resposta, "message" => "Tarefa deletada com sucesso"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(["success" => false, "data" => null, "message" => $resposta["message"]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
            exit();
    }

}


?>