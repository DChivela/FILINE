<?php
session_start();
require '../config/conexao.php';

// Configurações de segurança
$tempo_espera = 10; // segundos
$tentativas_maximas = 5;

// Inicializa tentativas
if (!isset($_SESSION['tentativas_login'])) {
    $_SESSION['tentativas_login'] = 0;
    $_SESSION['ultima_tentativa'] = time();
}

// Bloqueio temporário
if ($_SESSION['tentativas_login'] >= $tentativas_maximas) {
    $tempo_restante = $tempo_espera - (time() - $_SESSION['ultima_tentativa']);
    if ($tempo_restante > 0) {
        header('Location: login.php?erro=' . urlencode("Acesso bloqueado. Tente novamente em $tempo_restante segundos."));
        exit();
    } else {
        $_SESSION['tentativas_login'] = 0;
        unset($_SESSION['ultima_tentativa']);
    }
}

// Apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/login.php');
    exit();
}

// Captura dados
$email = trim($_POST['Email']);
$senha = $_POST['Senha'];

// 1) Autentica utilizador
$sql = "SELECT Cod_Utilizador, Senha, Perfil_Acesso, Nome_Enfermeiro, Email, Contacto FROM tb_utilizador WHERE Email = ? LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
$usr = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usr) {
    $_SESSION['tentativas_login']++;
    $_SESSION['ultima_tentativa'] = time();
    header('Location: ../public/login.php?erro=' . urlencode('Usuário não encontrado'));
    exit();
}

if (!password_verify($senha, $usr['Senha'])) {
    $_SESSION['tentativas_login']++;
    $_SESSION['ultima_tentativa'] = time();
    header('Location: ../public/login.php?erro=' . urlencode('Senha incorreta'));
    exit();
}

// Login bem-sucedido
$_SESSION['tentativas_login'] = 0;
unset($_SESSION['ultima_tentativa']);

// 2) Verifica se é técnico (opcional)
$user_id = (int)$usr['Cod_Utilizador'];
$stmt2 = $pdo->prepare("SELECT Cod_Utilizador FROM tb_utilizador WHERE Cod_Utilizador = ? LIMIT 1");
$stmt2->execute([$user_id]);
$tecnico = $stmt2->fetch(PDO::FETCH_ASSOC);

if (!$tecnico) {
    header('Location: ..public/login.php?erro=' . urlencode('Técnico não cadastrado'));
    exit();
}

// 3) Sobe dados na sessão
$_SESSION['Cod_Utilizador']   = $user_id;
$_SESSION['Nome_Enfermeiro']  = $usr['Nome_Enfermeiro'];
$_SESSION['Perfil_Acesso']    = $usr['Perfil_Acesso'];

header('Location: ../public/dashboard.php');
exit();
