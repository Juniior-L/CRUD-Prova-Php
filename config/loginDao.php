<?php
require_once 'database.php';

class Login
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function createUser($nome, $email, $senha)
    {
        if (empty($nome) || empty($email) || empty($senha)) {
            return ["success" => false, "message" => "Preencha todos os campos"];
        }

        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            return ["success" => false, "message" => "E-mail já cadastrado"];
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (nome, email, senha) VALUES (:nome, :email, :senha)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':nome' => $nome, ':email' => $email, ':senha' => $senhaHash]);

        return ["success" => true, "message" => "Usuário criado com sucesso"];
    }

    public function loginUser($email, $senha)
    {
        if (empty($email) || empty($senha)) {
            return ["success" => false, "message" => "Preencha e-mail e senha"];
        }

        $stmt = $this->pdo->prepare("SELECT id, nome, email, senha FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // criar sessão corretamente
            if (session_status() !== PHP_SESSION_ACTIVE) session_start();
            $_SESSION['user'] = [
                "id" => (int)$usuario['id'],
                "nome" => $usuario['nome'],
                "email" => $usuario['email']
            ];
            return ["success" => true, "message" => "Login realizado com sucesso", "user" => $_SESSION['user']];
        }

        return ["success" => false, "message" => "E-mail ou senha incorretos"];
    }

    public function logout()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_unset();
        session_destroy();
        // garantir que o cookie da sessão também expire
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        return ["success" => true, "message" => "Logout realizado com sucesso"];
    }
}
