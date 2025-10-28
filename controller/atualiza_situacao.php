<?php
require '../config/conexao.php';

if (isset($_GET['id'], $_GET['acao'])) {
    $id = (int) $_GET['id'];
    $acao = $_GET['acao'];

    if ($acao === 'atender') {
        $situacao = 'Em Andamento';
    } elseif ($acao === 'fechar') {
        $situacao = 'Fechado';
    } else {
        die('Ação inválida.');
    }

    try {
        // Atualiza o estado
        $sql = "UPDATE tb_pre_triagem SET Situacao = :situacao WHERE Cod_Pre_Triagem = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':situacao', $situacao);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Se for atendimento, redireciona para a página de triagem
        if ($acao === 'atender') {
            header("Location: triagem.php?pretriagem_id=$id");
        } else {
            header("Location: ../public/dashboard.php?msg=sucesso");
        }
        exit;
    } catch (PDOException $e) {
        echo 'Erro ao atualizar: ' . $e->getMessage();
    }
} else {
    echo 'Parâmetros inválidos.';
}
?>
