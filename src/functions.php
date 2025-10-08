<?php
//GERA SENHA DE ATENDIMENTO
function gera_senha_atendimento(PDO $pdo): string
{
    // estratégia: YYYYMMDD + sequential number reset diário
    $date = (new DateTime())->format('Y-m-d');
    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM Tb_Pre_Triagem WHERE DATE(Data_de_Registo) = :d");
    $stmt->execute([':d' => $date]);
    $cnt = (int)$stmt->fetchColumn();
    $seq = $cnt + 1;
    return (new DateTime())->format('Ymd') . str_pad($seq, 3, '0', STR_PAD_LEFT); // ex: 20250815001
}

function sanitize($v)
{
    return trim($v);
}

function validate_required($arr, $fields)
{
    $errors = [];
    foreach ($fields as $f) {
        if (!isset($arr[$f]) || trim($arr[$f]) === '') $errors[] = $f;
    }
    return $errors;
}
