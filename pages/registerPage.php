<?php
// session_start();
// if (!isset($_SESSION['user'])) {
//     header('Location: registerPage.php');
//     exit;
// }
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="../style/login.css">
</head>

<body>
    <div class="container">
        <h1><span>Create</span> your account</h1>
        <p>Fill the information below</p>

        <form action="../api/login.php" method="POST">
            <input type="text" name="name" placeholder="Full name" required>
            <input type="email" name="email" placeholder="e-mail" required>
            <input type="password" name="password" placeholder="Create a password" required>
            <input type="hidden" name="tipo" value="insert">
            <button type="submit">Sign Up</button>
        </form>

        <p style="margin-top: 15px;">Already registered? <a href="../pages/loginPage.php">Sign In</a></p>
    </div>
</body>

</html>