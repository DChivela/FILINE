<?php
session_start();
if (!isset($_SESSION['Cod_Utilizador'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: ../public/login.php");
    exit();
}
?>

<!-- CÓDIGO DA PÁGINA auth.php NO CPANEL -->
<!--?php 
session_start();
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: https://wafcenter.com/wafreport/login.php");
     exit();
 }
 ?>-->