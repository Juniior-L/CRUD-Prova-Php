<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: ../pages/homePage.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar - TaskManager</title>
    <link rel="stylesheet" href="../style/login.css">
</head>

<body>
    <div class="split-container">

        <div class="side left">
            <div class="branding">
                <h1><span>Flow</span>Forge</h1>
                <p>Crie uma conta e transforme suas tarefas em conquistas.</p>
            </div>
        </div>

        <div class="side right">
            <div class="form-box">
                <h2><span>Crie</span> sua conta ✨</h2>
                <p>Preencha os dados abaixo</p>

                <form action="../api/login.php" method="POST">
                    <input type="hidden" name="tipo" value="insert">
                    <input type="text" name="name" placeholder="Nome completo" required>
                    <input type="email" name="email" placeholder="Seu e-mail" required>
                    <input type="password" name="password" placeholder="Crie uma senha" required>

                    <button type="submit" class="btn-primary">Cadastrar</button>
                </form>

                <p class="swap-link">Já tem uma conta?
                    <a href="../pages/loginPage.php">Entrar</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>