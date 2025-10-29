<?php
// ajax_check_senha.php
require_once __DIR__ . '../config/conexao.php'; // ajusta path se necessário
session_start(); // para checar se há user logado

header('Content-Type: application/json; charset=utf-8');

// obter q (senha) via GET
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetro q obrigatório.']);
    exit;
}

// se o user estiver logado, podes optar por devolver mais info; aqui devolvemos o mesmo conjunto
$isLogged = !empty($_SESSION['user_id']);

// busca pela senha EXACTA (nunca pesquisa parcial para não expor várias senhas)
$sql = "
    SELECT p.Cod_Pre_Triagem, p.Senha_de_Atendimento,
           p.Nome_Paciente, p.Grupo_Ocorrencia, p.Classificacao_de_Risco, p.Motivos_Classificacao, p.Situacao, p.Data_de_Registo,
           ts.Tipo_Sangue AS Tipo_Sangue_Nome,
           a.Tipo_Alergia AS Alergia_Nome
    FROM pre_triagem p
    LEFT JOIN tb_tipo_sangue ts ON p.Tipo_Sangue = ts.Cod_Tipo_Sangue
    LEFT JOIN tb_alergia a ON p.Alergia = a.Cod_Alergia
    WHERE p.Senha_de_Atendimento = :senha
    LIMIT 1
";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':senha' => $q]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        // não encontrado
        http_response_code(404);
        echo json_encode(['error' => 'Senha não encontrada.']);
        exit;
    }

    // campos públicos a devolver (sem contacto, endereco, etc.)
    $public = [
        'Cod_Pre_Triagem' => (int)$row['Cod_Pre_Triagem'],
        'Senha_de_Atendimento' => $row['Senha_de_Atendimento'],
        'Nome_Paciente' => $row['Nome_Paciente'],
        'Grupo_Ocorrencia' => $row['Grupo_Ocorrencia'],
        'Classificacao_de_Risco' => $row['Classificacao_de_Risco'],
        'Motivos_Classificacao' => $row['Motivos_Classificacao'],
        'Situacao' => $row['Situacao'],
        'Data_de_Registo' => $row['Data_de_Registo'],
        'Tipo_Sangue' => $row['Tipo_Sangue_Nome'],
        'Alergia' => $row['Alergia_Nome'],
        // podes incluir campos adicionais públicos aqui
    ];

    echo json_encode(['ok' => true, 'data' => $public]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor.']);
    // log para debug
    error_log('ajax_check_senha error: ' . $e->getMessage());
    exit;
}
