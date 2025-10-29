<?php
require '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO tb_triagem 
            (Paciente, Peso, Altura, PA, PB, Edema, Pulso, SatO2, P_Respiratorio, Dor, Temperatura, Data_Triagem, tb_Utilizador_Cod_Utilizador, Cod_Pre_Triagem)
            VALUES (:Paciente, :Peso, :Altura, :PA, :PB, :Edema, :Pulso, :SatO2, :P_Respiratorio, :Dor, :Temperatura, :Data_Triagem, :Usuario, :Cod_Pre_Triagem)");

        $stmt->execute([
            ':Paciente' => $_POST['Nome_Paciente'],
            ':Peso' => $_POST['Peso'],
            ':Altura' => $_POST['Altura'],
            ':PA' => $_POST['PA'],
            ':PB' => $_POST['PB'],
            ':Edema' => $_POST['Edema'],
            ':Pulso' => $_POST['Pulso'],
            ':SatO2' => $_POST['SatO2'],
            ':P_Respiratorio' => $_POST['P_Respiratorio'],
            ':Dor' => $_POST['Dor'],
            ':Temperatura' => $_POST['Temperatura'],
            ':Data_Triagem' => ($_POST['Data_Triagem'] ?? '') !== '' ? $post['Data_Triagem'] : date('Y-m-d H:i:s'),
            ':Usuario' => 1, // futuramente, podes colocar o ID do utilizador logado
            ':Cod_Pre_Triagem' => $_POST['Cod_Pre_Triagem']
        ]);

        // Atualiza o estado para Fechado após gravar triagem
        $update = $pdo->prepare("UPDATE tb_pre_triagem SET Situacao = 'Atendido' WHERE Cod_Pre_Triagem = :id");
        $update->execute([':id' => $_POST['Cod_Pre_Triagem']]);

        header("Location: ../public/dashboard.php?msg=triagem_gravada");
        exit;
    } catch (PDOException $e) {
        echo "Erro ao gravar triagem: " . $e->getMessage();
    }
}
?>
