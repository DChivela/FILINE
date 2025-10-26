<?php
session_start();
// Se já estiver logado, redireciona
if (isset($_SESSION['Cod_Utilizador'])) {
    header('Location: ../dashboard.php');
    exit();
}

// Captura mensagem de erro, se houver
$erro = isset($_GET['erro']) ? $_GET['erro'] : '';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Ícones Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
    <!-- Favicon -->
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style_second.css">
</head>

<body>

<?php include 'header.php'; ?>

    <div class="login-container">
        <!-- Loader responsável pelo efeito de processamento para mostrar que o pedido está a ser processado-->
        <!-- ?php include 'loader.php'; ? -->
        <!-- Logo -->
        <div class="text-center mb-4">
            <img src="img/Logo.png" alt="Logo" class="logo" style="width: 200px; height: 80px;">
        </div>
        <h3 class="login">Login</h3>
        <?php if ($erro): ?>
            <div class="error-message"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form action="../controller/processa_login.php" method="POST">
            <div class="input-group">
                <label for="email">E‑mail</label>
                <input type="email" name="Email" placeholder="técnico.de.saúde@filine.com" required>
            </div>
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" name="Senha" required>
            </div>
            <button type="submit"><i class="bi bi-box-arrow-in-right"></i> Entrar</button>
            <!-- <div class="mt-2 text-end">
                <a href="tokens/esqueci_senha.php" class="small" title="Redefina a sua password.">Esqueceste a tua senha?</a>
            </div> -->
        </form>
    </div>
</body>


</html>