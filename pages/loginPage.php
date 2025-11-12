<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - TaskManager</title>
    <link rel="stylesheet" href="../style/login.css">
</head>

<body>
    <div class="split-container">

        <div class="side left">
            <div class="branding">
                <h1><span>Flow</span>Forge</h1>
                <p>Organize suas ideias. Simplifique sua rotina.</p>
            </div>
        </div>

        <!-- LADO DIREITO (LOGIN) -->
        <div class="side right">
            <div class="form-box">
                <h2><span>Bem-vindo</span> de volta</h2>
                <p>Entre para continuar organizando suas tarefas</p>

                <form action="../api/login.php" method="POST">
                    <input type="hidden" name="tipo" value="login">
                    <input type="email" name="email" placeholder="Seu e-mail" required>
                    <input type="password" name="password" placeholder="Sua senha" required>

                    <button type="submit" class="btn-primary">Entrar</button>
                </form>

                <p class="swap-link">Não tem uma conta?
                    <a href="../pages/registerPage.php">Cadastre-se</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>