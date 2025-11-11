<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../style/login.css">
</head>

<body>
    <div class="container">
        <h1><span>Welcome</span></h1>
        <p>Log in to continue</p>

        <form action="../api/login.php" method="POST">
            <input type="hidden" name="tipo" value="login">
            <input type="email" name="email" placeholder="e-mail" required>
            <input type="password" name="password" placeholder="password" required>

            <button type="submit">Log in</button>
        </form>

        <p style="margin-top: 15px;">Don't have an account? <a href="../pages/registerPage.php">Sign Up</a></p>
    </div>
</body>

</html>