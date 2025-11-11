<?php
require_once 'database.php';

class Login
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createUser($name, $email, $password)
    {
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("INSERT INTO users (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);
            return ["success" => true, "message" => "Usuário criado com sucesso!"];
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Erro ao criar usuário: " . $e->getMessage()];
        }
    }

    public function loginUser($email, $password)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['senha'])) {
                session_start();
                $_SESSION['user'] = $user;
                header("Location: ../pages/homePage.php");
                exit;
            } else {
                return ["success" => false, "message" => "E-mail ou senha incorretos."];
            }
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Erro: " . $e->getMessage()];
        }
    }

    public function logout()
    {
        session_start();
        session_destroy();
        // header("Location: ../pages/loginPage.php");
        exit;
    }
}
