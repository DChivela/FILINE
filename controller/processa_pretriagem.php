<?php
// public/store_pretriagem.php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/functions.php';

// Pega post cru
$post = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
if (!$post) {
    header('Location: index.php');
    exit;
}

// sanitize - lida com strings e arrays (aplica sanitize a cada scalar)
foreach ($post as $k => $v) {
    if (is_array($v)) {
        foreach ($v as $ik => $iv) {
            if (is_array($iv)) {
                // suporte a níveis adicionais (sinais[agressao][x] => valor)
                foreach ($iv as $iik => $iiv) {
                    $post[$k][$ik][$iik] = sanitize($iiv);
                }
            } else {
                $post[$k][$ik] = sanitize($iv);
            }
        }
    } else {
        $post[$k] = sanitize($v);
    }
}

// required fields minimal (exige também Grupo_Ocorrencia)
$required = ['Nome_Paciente', 'Contacto', 'Grupo_Ocorrencia'];
$errs = validate_required($post, $required);
if (!empty($errs)) {
    echo "Campos obrigatórios em falta: " . implode(',', $errs);
    exit;
}

// se senha não veio, gera
$senha = $post['Senha_de_Atendimento'] ?? '';
if (empty($senha)) {
    $senha = gera_senha_atendimento($pdo);
}

// Normalizar sinais: esperar estrutura sinais[agressao][campo] etc.
$sinais_input = $post['sinais'] ?? [];
$grupo = strtoupper(trim($post['Grupo_Ocorrencia'] ?? 'OUTRO'));

// Garante que sub-arrays existem
$sinais = [
    'agressao'         => !empty($sinais_input['agressao'])         && is_array($sinais_input['agressao'])         ? $sinais_input['agressao']         : [],
    'alergia'          => !empty($sinais_input['alergia'])          && is_array($sinais_input['alergia'])          ? $sinais_input['alergia']          : [],
    'outro'            => !empty($sinais_input['outro'])            && is_array($sinais_input['outro'])            ? $sinais_input['outro']            : [],
    'neurologico'      => !empty($sinais_input['neurologico'])      && is_array($sinais_input['neurologico'])      ? $sinais_input['neurologico']      : [],
    'cutaneo'          => !empty($sinais_input['cutaneo'])          && is_array($sinais_input['cutaneo'])          ? $sinais_input['cutaneo']          : [],
    'hemato'           => !empty($sinais_input['hemato'])           && is_array($sinais_input['hemato'])           ? $sinais_input['hemato']           : [],
    'bebe_chorando'    => !empty($sinais_input['bebe_chorando'])    && is_array($sinais_input['bebe_chorando'])    ? $sinais_input['bebe_chorando']    : [],
    'convulsoes'       => !empty($sinais_input['convulsoes'])       && is_array($sinais_input['convulsoes'])       ? $sinais_input['convulsoes']       : [],
    'corpo_estranho'   => !empty($sinais_input['corpo_estranho'])   && is_array($sinais_input['corpo_estranho'])   ? $sinais_input['corpo_estranho']   : [],
    'desmaio'          => !empty($sinais_input['desmaio'])          && is_array($sinais_input['desmaio'])          ? $sinais_input['desmaio']          : [],
    'dor_abdominal'    => !empty($sinais_input['dor_abdominal'])    && is_array($sinais_input['dor_abdominal'])    ? $sinais_input['dor_abdominal']    : [],
    'dor_cervical'     => !empty($sinais_input['dor_cervical'])     && is_array($sinais_input['dor_cervical'])     ? $sinais_input['dor_cervical']     : [],
    'dor_garganta'     => !empty($sinais_input['dor_garganta'])     && is_array($sinais_input['dor_garganta'])     ? $sinais_input['dor_garganta']     : [],
    'dor_extremidades' => !empty($sinais_input['dor_extremidades']) && is_array($sinais_input['dor_extremidades']) ? $sinais_input['dor_extremidades'] : [],
    'dor_lombar'       => !empty($sinais_input['dor_lombar'])       && is_array($sinais_input['dor_lombar'])       ? $sinais_input['dor_lombar']       : [],
    'dor_cabeca'       => !empty($sinais_input['dor_cabeca'])       && is_array($sinais_input['dor_cabeca'])       ? $sinais_input['dor_cabeca']       : []
];

// Função que decide classificação por grupo (mesmas regras do cliente/JS)
function decide_classificacao_por_grupo(string $grupo, array $sinais)
{
    $motivos = [];

    switch ($grupo) {
        case 'AGRESSAO':
            // VERMELHO
            if (!empty($sinais['agressao']['obstrucao']) || !empty($sinais['agressao']['respiracao']) || !empty($sinais['agressao']['hemorragia_grave']) || !empty($sinais['agressao']['choque'])) {
                $c = array_intersect_key($sinais['agressao'], array_flip(['obstrucao', 'respiracao', 'hemorragia_grave', 'choque']));
                return ['codigo' => 'VERMELHO', 'motivos' => array_keys($c)];
            }
            // LARANJA
            if (!empty($sinais['agressao']['trauma_sign']) || !empty($sinais['agressao']['dispneia']) || !empty($sinais['agressao']['alteracao_consciencia'])) {
                $c = array_intersect_key($sinais['agressao'], array_flip(['trauma_sign', 'dispneia', 'alteracao_consciencia']));
                return ['codigo' => 'LARANJA', 'motivos' => array_keys($c)];
            }
            // AMARELO
            if (!empty($sinais['agressao']['hemorragia_menor']) || !empty($sinais['agressao']['dor_moderada'])) {
                $c = array_intersect_key($sinais['agressao'], array_flip(['hemorragia_menor', 'dor_moderada']));
                return ['codigo' => 'AMARELO', 'motivos' => array_keys($c)];
            }
            // VERDE
            if (!empty($sinais['agressao']['deformidade'])) {
                return ['codigo' => 'VERDE', 'motivos' => ['deformidade']];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_agressao']];

        case 'ALERGIA':
            if (!empty($sinais['alergia']['edema_faciais']) || !empty($sinais['alergia']['dispneia']) || !empty($sinais['alergia']['choque'])) {
                $c = array_intersect_key($sinais['alergia'], array_flip(['edema_faciais', 'dispneia', 'choque']));
                return ['codigo' => 'VERMELHO', 'motivos' => array_keys($c)];
            }
            if (!empty($sinais['alergia']['urticaria_generalizada']) && !empty($sinais['alergia']['prurido_intenso'])) {
                $c = array_intersect_key($sinais['alergia'], array_flip(['urticaria_generalizada', 'prurido_intenso']));
                return ['codigo' => 'LARANJA', 'motivos' => array_keys($c)];
            }
            if (!empty($sinais['alergia']['sintomas_locais'])) {
                return ['codigo' => 'AMARELO', 'motivos' => array_keys($sinais['alergia'])];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_alergia']];

        case 'NEUROLOGICO':
            if (!empty($sinais['neurologico']['apneia']) || !empty($sinais['neurologico']['choque']) || !empty($sinais['neurologico']['hipoglicemia']) || !empty($sinais['neurologico']['convulsao']) || !empty($sinais['neurologico']['glascow'])) {
                $c = array_intersect_key($sinais['neurologico'], array_flip(['apneia', 'choque', 'hipoglicemia', 'convulsao', 'glascow']));
                return ['codigo' => 'VERMELHO', 'motivos' => array_keys($c)];
            }
            if (!empty($sinais['neurologico']['deficit_agudo']) || !empty($sinais['neurologico']['vomitos_recorrentes']) || !empty($sinais['neurologico']['alteracao_consciencia']) || !empty($sinais['neurologico']['risco_queda']) || !empty($sinais['neurologico']['pos_ictal'])) {
                $c = array_intersect_key($sinais['neurologico'], array_flip(['deficit_agudo', 'vomitos_recorrentes', 'alteracao_consciencia', 'risco_queda', 'pos_ictal']));
                return ['codigo' => 'LARANJA', 'motivos' => array_keys($c)];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_neurologico']];

        case 'CUTANEO':
            if (!empty($sinais['cutaneo']['respiracao_irregular'])) {
                return ['codigo' => 'VERMELHO', 'motivos' => ['respiracao_irregular']];
            }
            if (!empty($sinais['cutaneo']['lesao_grave'])) {
                return ['codigo' => 'LARANJA', 'motivos' => ['lesao_grave']];
            }
            if (!empty($sinais['cutaneo']['lesao_moderada'])) {
                return ['codigo' => 'AMARELO', 'motivos' => ['lesao_moderada']];
            }
            if (!empty($sinais['cutaneo']['lesao_leve'])) {
                return ['codigo' => 'VERDE', 'motivos' => ['lesao_leve']];
            }
            if (!empty($sinais['cutaneo']['prurido_leve'])) {
                return ['codigo' => 'AZUL', 'motivos' => ['prurido_leve']];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_cutaneo']];

        case 'HEMATO':
            if (!empty($sinais['hemato']['choque'])) {
                return ['codigo' => 'VERMELHO', 'motivos' => ['choque']];
            }
            if (!empty($sinais['hemato']['dor_intensa'])) {
                return ['codigo' => 'LARANJA', 'motivos' => ['dor_intensa']];
            }
            if (!empty($sinais['hemato']['dor_moderada'])) {
                return ['codigo' => 'AMARELO', 'motivos' => ['dor_moderada']];
            }
            return ['codigo' => 'VERDE', 'motivos' => ['sem_sinais_hemato']];

        case 'BEBE_CHORANDO':
            if (!empty($sinais['bebe_chorando']['obstrucao'])) {
                return ['codigo' => 'VERMELHO', 'motivos' => ['obstrucao']];
            }
            if (!empty($sinais['bebe_chorando']['postura_hipotonia'])) {
                return ['codigo' => 'LARANJA', 'motivos' => ['postura_hipotonia']];
            }
            if (!empty($sinais['bebe_chorando']['choro_prolongado'])) {
                return ['codigo' => 'AMARELO', 'motivos' => ['choro_prolongado']];
            }
            if (!empty($sinais['bebe_chorando']['febre_recente'])) {
                return ['codigo' => 'VERDE', 'motivos' => ['febre_recente']];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_bebe']];

        case 'CONVULSOES':
            if (!empty($sinais['convulsoes']['vermelho'])) {
                return ['codigo' => 'VERMELHO', 'motivos' => ['vermelho']];
            }
            if (!empty($sinais['convulsoes']['laranja'])) {
                return ['codigo' => 'LARANJA', 'motivos' => ['laranja']];
            }
            if (!empty($sinais['convulsoes']['amarelo'])) {
                return ['codigo' => 'AMARELO', 'motivos' => ['amarelo']];
            }
            if (!empty($sinais['convulsoes']['verde'])) {
                return ['codigo' => 'VERDE', 'motivos' => ['verde']];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_convulsao']];

        case 'CORPO_ESTRANHO':
            if (!empty($sinais['corpo_estranho']['vermelho'])) {
                return ['codigo' => 'VERMELHO', 'motivos' => ['vermelho']];
            }
            if (!empty($sinais['corpo_estranho']['laranja'])) {
                return ['codigo' => 'LARANJA', 'motivos' => ['laranja']];
            }
            if (!empty($sinais['corpo_estranho']['amarelo'])) {
                return ['codigo' => 'AMARELO', 'motivos' => ['amarelo']];
            }
            if (!empty($sinais['corpo_estranho']['verde'])) {
                return ['codigo' => 'VERDE', 'motivos' => ['verde']];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_corpo_estranho']];

        case 'DESMAIO':
            if (!empty($sinais['desmaio']['vermelho'])) {
                return ['codigo' => 'VERMELHO', 'motivos' => ['vermelho']];
            }
            if (!empty($sinais['desmaio']['laranja'])) {
                return ['codigo' => 'LARANJA', 'motivos' => ['laranja']];
            }
            if (!empty($sinais['desmaio']['amarelo'])) {
                return ['codigo' => 'AMARELO', 'motivos' => ['amarelo']];
            }
            if (!empty($sinais['desmaio']['verde'])) {
                return ['codigo' => 'VERDE', 'motivos' => ['verde']];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_desmaio']];

        case 'DOR_ABDOMINAL':
            if (!empty($sinais['dor_abdominal']['choque'])) {
                return ['codigo' => 'VERMELHO', 'motivos' => ['choque']];
            }
            if (!empty($sinais['dor_abdominal']['dor_intensa'])) {
                return ['codigo' => 'LARANJA', 'motivos' => ['dor_intensa']];
            }
            if (!empty($sinais['dor_abdominal']['distensao'])) {
                return ['codigo' => 'AMARELO', 'motivos' => ['distensao']];
            }
            if (!empty($sinais['dor_abdominal']['dor_leve'])) {
                return ['codigo' => 'VERDE', 'motivos' => ['dor_leve']];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_abdominal']];

        case 'DOR_CERVICAL':
            if (!empty($sinais['dor_cervical']['vermelho'])) {
                return ['codigo' => 'VERMELHO', 'motivos' => ['vermelho']];
            }
            if (!empty($sinais['dor_cervical']['laranja'])) {
                return ['codigo' => 'LARANJA', 'motivos' => ['laranja']];
            }
            if (!empty($sinais['dor_cervical']['amarelo'])) {
                return ['codigo' => 'AMARELO', 'motivos' => ['amarelo']];
            }
            if (!empty($sinais['dor_cervical']['verde'])) {
                return ['codigo' => 'VERDE', 'motivos' => ['verde']];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_cervical']];

        case 'DOR_GARGANTA':
            if (!empty($sinais['dor_garganta']['vermelho'])) {
                return ['codigo' => 'VERMELHO', 'motivos' => ['vermelho']];
            }
            if (!empty($sinais['dor_garganta']['laranja'])) {
                return ['codigo' => 'LARANJA', 'motivos' => ['laranja']];
            }
            if (!empty($sinais['dor_garganta']['amarelo'])) {
                return ['codigo' => 'AMARELO', 'motivos' => ['amarelo']];
            }
            if (!empty($sinais['dor_garganta']['verde'])) {
                return ['codigo' => 'VERDE', 'motivos' => ['verde']];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_garganta']];

        case 'DOR_EXTREMIDADES':
            if (!empty($sinais['dor_extremidades']['vascular'])) {
                return ['codigo' => 'LARANJA', 'motivos' => ['vascular']];
            }
            if (!empty($sinais['dor_extremidades']['claudicacao'])) {
                return ['codigo' => 'AMARELO', 'motivos' => ['claudicacao']];
            }
            if (!empty($sinais['dor_extremidades']['trauma_leve'])) {
                return ['codigo' => 'VERDE', 'motivos' => ['trauma_leve']];
            }
            if (!empty($sinais['dor_extremidades']['dor_cronica'])) {
                return ['codigo' => 'AZUL', 'motivos' => ['dor_cronica']];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_extremidades']];

        case 'DOR_LOMBAR':
            if (!empty($sinais['dor_lombar']['vermelho'])) {
                return ['codigo' => 'VERMELHO', 'motivos' => ['vermelho']];
            }
            if (!empty($sinais['dor_lombar']['laranja'])) {
                return ['codigo' => 'LARANJA', 'motivos' => ['laranja']];
            }
            if (!empty($sinais['dor_lombar']['amarelo'])) {
                return ['codigo' => 'AMARELO', 'motivos' => ['amarelo']];
            }
            if (!empty($sinais['dor_lombar']['verde'])) {
                return ['codigo' => 'VERDE', 'motivos' => ['verde']];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_lombar']];

        case 'DOR_CABECA':
            if (!empty($sinais['dor_cabeca']['vermelho'])) {
                return ['codigo' => 'VERMELHO', 'motivos' => ['vermelho']];
            }
            if (!empty($sinais['dor_cabeca']['laranja'])) {
                return ['codigo' => 'LARANJA', 'motivos' => ['laranja']];
            }
            if (!empty($sinais['dor_cabeca']['amarelo'])) {
                return ['codigo' => 'AMARELO', 'motivos' => ['amarelo']];
            }
            if (!empty($sinais['dor_cabeca']['verde'])) {
                return ['codigo' => 'VERDE', 'motivos' => ['verde']];
            }
            if (!empty($sinais['dor_cabeca']['azul'])) {
                return ['codigo' => 'AZUL', 'motivos' => ['azul']];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais_cefaleia']];

        default:
            // OUTRO / genérico
            if (!empty($sinais['outro']['obstrucao']) || !empty($sinais['outro']['respiracao']) || !empty($sinais['outro']['choque'])) {
                $c = array_intersect_key($sinais['outro'], array_flip(['obstrucao', 'respiracao', 'choque']));
                return ['codigo' => 'VERMELHO', 'motivos' => array_keys($c)];
            }
            if (!empty($sinais['outro']['dor_intensa']) || !empty($sinais['outro']['alteracao_consciencia'])) {
                $c = array_intersect_key($sinais['outro'], array_flip(['dor_intensa', 'alteracao_consciencia']));
                return ['codigo' => 'LARANJA', 'motivos' => array_keys($c)];
            }
            if (!empty($sinais['outro']['dor_moderada'])) {
                return ['codigo' => 'AMARELO', 'motivos' => ['dor_moderada']];
            }
            if (!empty($sinais['outro']['deformidade'])) {
                return ['codigo' => 'VERDE', 'motivos' => ['deformidade']];
            }
            return ['codigo' => 'AZUL', 'motivos' => ['sem_sinais']];
    }
}

// decide
$decisao = decide_classificacao_por_grupo($grupo, $sinais);
$classificacao_calculada = $decisao['codigo'];
$motivos_classificacao = implode(',', $decisao['motivos']);

// Prioriza a classificação do servidor sempre
$post['Classificacao_de_Risco'] = $classificacao_calculada;

// Prepara SQL (inclui Grupo_Ocorrencia e Motivos_Classificacao).
$sql_with_motivos = "INSERT INTO Tb_Pre_Triagem
(Nome_Paciente, Nome_Encarregado, Genero_Paciente, Data_Nascimento, Contacto, Endereco, :id_endereco,
 Tipo_Ocorrencia, Sintoma_Principal, Grupo_Ocorrencia, Classificacao_de_Risco, Motivos_Classificacao, Data_de_Registo, Tipo_Sangue, Alergia, Situacao, Senha_de_Atendimento)
 VALUES (:Nome_Paciente, :Nome_Encarregado, :Genero_Paciente, :Data_Nascimento, :Contacto, :Endereco, :id_endereco,
 :Tipo_Ocorrencia, :Sintoma_Principal, :Grupo_Ocorrencia, :Classificacao_de_Risco, :Motivos_Classificacao, :Data_de_Registo, :Tipo_Sangue, :Alergia, :Situacao, :Senha_de_Atendimento)";

$sql_without_motivos = "INSERT INTO Tb_Pre_Triagem
(Nome_Paciente, Nome_Encarregado, Genero_Paciente, Data_Nascimento, Contacto, Endereco, :id_endereco,
 Tipo_Ocorrencia, Sintoma_Principal, Grupo_Ocorrencia, Classificacao_de_Risco, Data_de_Registo, Tipo_Sangue, Alergia, Situacao, Senha_de_Atendimento)
 VALUES (:Nome_Paciente, :Nome_Encarregado, :Genero_Paciente, :Data_Nascimento, :Contacto, :Endereco, :id_endereco,
 :Tipo_Ocorrencia, :Sintoma_Principal, :Grupo_Ocorrencia, :Classificacao_de_Risco, :Data_de_Registo, :Tipo_Sangue, :Alergia, :Situacao, :Senha_de_Atendimento)";

$params_common = [
    ':Nome_Paciente' => $post['Nome_Paciente'] ?? null,
    ':Nome_Encarregado' => $post['Nome_Encarregado'] ?? null,
    ':Genero_Paciente' => $post['Genero_Paciente'] ?? null,
    ':Data_Nascimento' => $post['Data_Nascimento'] ?: null,
    ':Contacto' => $post['Contacto'] ?? null,
    ':Endereco' => $post['Endereco'] ?? null,
    ':id_endereco' => $post['id_endereco'] ?? null,
    ':Tipo_Ocorrencia' => $post['Tipo_Ocorrencia'] ?? null,
    ':Sintoma_Principal' => $post['Sintoma_Principal'] ?? null,
    ':Grupo_Ocorrencia' => $grupo,
    ':Classificacao_de_Risco' => $post['Classificacao_de_Risco'] ?? $classificacao_calculada,
    ':Data_de_Registo' => $post['Data_de_Registo'] ?: date('Y-m-d H:i:s'),
    ':Tipo_Sangue' => $post['Tipo_Sangue'] ?: null,
    ':Alergia' => $post['Alergia'] ?: null,
    ':Situacao' => $post['Situacao'] ?? null,
    ':Senha_de_Atendimento' => $senha
];

try {
    // tenta inserir com Motivos_Classificacao
    $stmt = $pdo->prepare($sql_with_motivos);
    $params = $params_common;
    $params[':Motivos_Classificacao'] = $motivos_classificacao;
    $stmt->execute($params);

    header('Location: ../public/list_pretriagem.php?msg=ok&senha=' . urlencode($senha) . '&class=' . urlencode($post['Classificacao_de_Risco']));
    exit;
} catch (PDOException $e) {
    error_log('Insert com motivos falhou: ' . $e->getMessage());
    // se falha por coluna inexistente, tenta sem motivos
    try {
        $stmt = $pdo->prepare($sql_without_motivos);
        $stmt->execute($params_common);
        header('Location: ../public/list_pretriagem.php?msg=ok&senha=' . urlencode($senha) . '&class=' . urlencode($post['Classificacao_de_Risco']));
        exit;
    } catch (PDOException $e2) {
        error_log('Insert sem motivos falhou: ' . $e2->getMessage());
        echo "Erro ao gravar. Tente novamente.";
        exit;
    }
}
