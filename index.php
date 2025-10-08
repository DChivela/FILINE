<?php
// index.php
require_once __DIR__ . '/src/config.php';
$stmt = $pdo->query("SELECT Cod_Tipo_Sangue, Tipo_Sangue FROM tb_Tipo_Sangue ORDER BY Tipo_Sangue");
$tipos = $stmt->fetchAll();
$stmt = $pdo->query("SELECT Cod_Alergia, Tipo_Alergia FROM Tb_Alergia ORDER BY Tipo_Alergia");
$alergias = $stmt->fetchAll();
$stmt = $pdo->query("SELECT id, morada, rua FROM enderecos ORDER BY morada");
$enderecos = $stmt->fetchAll();
?>
<!doctype html>
<html lang="pt">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pré-Triagem</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="icon" href="public/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="../public/css/styles.css">
  <style>
    body {
      background: #f8f9fa;
    }

    .hero {
      height: 50vh;
      background-image: url('public/img/emergency.jpg');
      /* ajusta aqui */
      background-size: cover;
      background-position: center;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);
    }

    .hero .card {
      background: rgba(0, 0, 0, 0.45);
      border: none;
    }

    footer {
      background: #0b6aa3;
      color: white;
      padding: 10px;
      text-align: center;
      margin-top: 20px;
    }

    .required:after {
      content: " *";
      color: #d00;
    }

    .badge-risk {
      font-weight: 700;
      padding: .45em .7em;
      border-radius: .35rem;
    }

    .group-section {
      border-left: 3px solid #eee;
      padding-left: 12px;
      margin-top: 12px;
    }
  </style>
</head>

<body>
  <div class="topbar d-flex justify-content-between align-items-center">
    <div><strong>Filine-ON</strong></div>
    <div>
      <a class="text-white mr-3" href="index.php">Início</a>
      <a class="text-white mr-3" href="public/list_pretriagem.php">Consultar Espera</a>
      <a class="text-white" href="#">Entrar</a>
    </div>
  </div>
  <div class="hero">
    <div class="card p-3">
      <h2 class="text-center"><i class="fa fa-book" aria-hidden="true"></i> Filine - Serviços de Facilitação à Pré-Triagem</h2>
      <p class="mb-0">Escolha o grupo de ocorrência e marque os sinais relevantes. Os nossos profissionais tratarão do resto enquanto aguarda.</p>
    </div>
  </div>

  <div class="container mt-4 mb-5">
    <form action="controller/processa_pretriagem.php" method="post" id="pretriagemForm" class="needs-validation" novalidate>
      <div class="row">
        <div class="col-md-6">
          <!-- Dados do paciente -->
          <div class="form-row">
            <div class="form-group col-md-6">
              <label class="required">Nome do Paciente</label>
              <input name="Nome_Paciente" class="form-control" required />
            </div>
            <div class="form-group col-md-6">
              <label>Género do Paciente</label>
              <select name="Genero_Paciente" class="form-control">
                <option value="" required>Selecione</option>
                <option>Masculino</option>
                <option>Feminino</option>
                <option>Outro</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label class="required">Contacto Telefónico</label>
              <input name="Contacto" class="form-control" required />
            </div>
            <div class="form-group col-md-6">
              <label>Data de Nascimento</label>
              <input name="Data_Nascimento" type="date" class="form-control" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group form-group col-md-6">
              <label>Endereço (Morada)</label>
              <select name="id_endereco" class="form-control">
                <option value="">Seleccione um Bairro</option>
                <?php foreach ($enderecos as $e): ?>
                  <option value="<?= htmlspecialchars($e['morada']) ?>"><?= htmlspecialchars($e['morada']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group form-group col-md-6">
              <label>Endereço (Morada)</label>
              <input name="Endereco" class="form-control" placeholder="Se o bairro não estiver na lista (Opcional)"/>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Tipo de Sangue</label>
              <select name="Tipo_Sangue" class="form-control">
                <option value="">--</option>
                <?php foreach ($tipos as $t): ?>
                  <option value="<?= htmlspecialchars($t['Cod_Tipo_Sangue']) ?>"><?= htmlspecialchars($t['Tipo_Sangue']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Alergia</label>
              <select name="Alergia" class="form-control">
                <option value="">Nenhuma</option>
                <?php foreach ($alergias as $a): ?>
                  <option value="<?= htmlspecialchars($a['Cod_Alergia']) ?>"><?= htmlspecialchars($a['Tipo_Alergia']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label>Sintoma Principal</label>
            <input name="Sintoma_Principal" class="form-control" />
          </div>

        </div>

        <div class="col-md-6">
          <!-- Grupo de ocorrência -->
          <div class="form-group">
            <label class="required">Ocorrências</label>
            <select name="Grupo_Ocorrencia" id="Grupo_Ocorrencia" class="form-control" required title="Apenas é permitida uma opção por pré-triagem">
              <option value="">-- Seleccione --</option>
              <option value="AGRESSAO">AGRESSÃO</option>
              <option value="ALERGIA">ALERGIA</option>
              <option value="NEUROLOGICO">NEUROLÓGICO</option>
              <option value="CUTANEO">CUTÂNEO</option>
              <option value="HEMATO">HEMATO</option>
              <option value="BEBE_CHORANDO">BEBÊ A CHORAR</option>
              <option value="CONVULSOES">CONVULSÕES</option>
              <option value="CORPO_ESTRANHO">CORPO ESTRANHO</option>
              <option value="DESMAIO">DESMAIO</option>
              <option value="DOR_ABDOMINAL">DOR ABDOMINAL</option>
              <option value="DOR_CERVICAL">DOR CERVICAL</option>
              <option value="DOR_GARGANTA">DOR GARGANTA</option>
              <option value="DOR_EXTREMIDADES">DOR EXTREMIDADES</option>
              <option value="DOR_LOMBAR">DOR LOMBAR</option>
              <option value="DOR_CABECA">DOR CABECA</option>
              <option value="OUTRO">OUTRO</option>
            </select>
          </div>

          <!-- AGRESSAO -->
          <div id="sec_agressao" class="group-section" style="display:none;">
            <h6>Agressão — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ag_obstrucao" name="sinais[agressao][obstrucao]">
              <label class="form-check-label" for="ag_obstrucao">Obstrução das vias aéreas</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ag_resp" name="sinais[agressao][respiracao]">
              <label class="form-check-label" for="ag_resp">Respiração irregular/insuficiente</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ag_hem" name="sinais[agressao][hemorragia_grave]">
              <label class="form-check-label" for="ag_hem">Hemorragia exsanguinante</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ag_choque" name="sinais[agressao][choque]">
              <label class="form-check-label" for="ag_choque">Sinais de choque</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ag_trauma" name="sinais[agressao][trauma_sign]">
              <label class="form-check-label" for="ag_trauma">Mecanismo de trauma significativo</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ag_disp" name="sinais[agressao][dispneia]">
              <label class="form-check-label" for="ag_disp">Dispneia grave</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ag_altc" name="sinais[agressao][alteracao_consciencia]">
              <label class="form-check-label" for="ag_altc">Alteração súbita consciência</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ag_hem_menor" name="sinais[agressao][hemorragia_menor]">
              <label class="form-check-label" for="ag_hem_menor">Hemorragia menor incontrolável</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ag_dor" name="sinais[agressao][dor_moderada]">
              <label class="form-check-label" for="ag_dor">Dor moderada</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ag_def" name="sinais[agressao][deformidade]">
              <label class="form-check-label" for="ag_def">Deformidade / possível fratura</label>
            </div>
          </div>

          <!-- ALERGIA -->
          <div id="sec_alergia" class="group-section" style="display:none;">
            <h6>Alergia — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="al_edema" name="sinais[alergia][edema_faciais]">
              <label class="form-check-label" for="al_edema">Edema facial / língua</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="al_disp" name="sinais[alergia][dispneia]">
              <label class="form-check-label" for="al_disp">Dispneia</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="al_choque" name="sinais[alergia][choque]">
              <label class="form-check-label" for="al_choque">Sinais de choque</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="al_urt" name="sinais[alergia][urticaria_generalizada]">
              <label class="form-check-label" for="al_urt">Urticária generalizada</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="al_pru" name="sinais[alergia][prurido_intenso]">
              <label class="form-check-label" for="al_pru">Prurido intenso</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="al_loc" name="sinais[alergia][sintomas_locais]">
              <label class="form-check-label" for="al_loc">Sintomas locais (eritema/leve)</label>
            </div>
          </div>

          <!-- NEUROLOGICO -->
          <div id="sec_neurologico" class="group-section" style="display:none;">
            <h6>Neurológico — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ne_apneia" name="sinais[neurologico][apneia]">
              <label class="form-check-label" for="ne_apneia">Apneia / Bradipneia / Movimentos paradoxais</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ne_choque" name="sinais[neurologico][choque]">
              <label class="form-check-label" for="ne_choque">Sinais de choque</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ne_hipoglicemia" name="sinais[neurologico][hipoglicemia]">
              <label class="form-check-label" for="ne_hipoglicemia">Hipoglicemia</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ne_convulsao" name="sinais[neurologico][convulsao]">
              <label class="form-check-label" for="ne_convulsao">Convulsão ou atividade convulsiva</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ne_glascow" name="sinais[neurologico][glascow]">
              <label class="form-check-label" for="ne_glascow">Glasgow &lt; 8 / não responde</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ne_deficit" name="sinais[neurologico][deficit_agudo]">
              <label class="form-check-label" for="ne_deficit">Déficit neurológico agudo</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ne_vomitos" name="sinais[neurologico][vomitos_recorrentes]">
              <label class="form-check-label" for="ne_vomitos">Vômitos recorrentes</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ne_consciencia" name="sinais[neurologico][alteracao_consciencia]">
              <label class="form-check-label" for="ne_consciencia">Alteração do nível de consciência</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ne_queda" name="sinais[neurologico][risco_queda]">
              <label class="form-check-label" for="ne_queda">Alto risco de queda</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ne_pos_ictal" name="sinais[neurologico][pos_ictal]">
              <label class="form-check-label" for="ne_pos_ictal">Pós-ictal com déficit focal agudo</label>
            </div>
          </div>

          <!-- CUTANEO -->
          <div id="sec_cutaneo" class="group-section" style="display:none;">
            <h6>Cutâneo — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="cu_resp" name="sinais[cutaneo][respiracao_irregular]">
              <label class="form-check-label" for="cu_resp">Respiração irregular / Choque / Toxemia / Prostração</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="cu_lesao_grave" name="sinais[cutaneo][lesao_grave]">
              <label class="form-check-label" for="cu_lesao_grave">Ferida extensa / exposição óssea / odor fétido / necrose / lesões bolhosas ou petequiais</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="cu_lesao_moderada" name="sinais[cutaneo][lesao_moderada]">
              <label class="form-check-label" for="cu_lesao_moderada">Ferida com sangramento que cede / tumefação dolorosa / lesões ulceradas ou nodulares / erupções escoriantes</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="cu_lesao_leve" name="sinais[cutaneo][lesao_leve]">
              <label class="form-check-label" for="cu_lesao_leve">Lesão infectada sem febre / prurido em área restrita / escore 1-2-3/10</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="cu_prurido" name="sinais[cutaneo][prurido_leve]">
              <label class="form-check-label" for="cu_prurido">Prurido leve / lesões sem sinais sistêmicos</label>
            </div>
          </div>

          <!-- HEMATO -->
          <div id="sec_hemato" class="group-section" style="display:none;">
            <h6>Hemato — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="he_choque" name="sinais[hemato][choque]">
              <label class="form-check-label" for="he_choque">Choque / Convulsão / Glasgow &lt; 8</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="he_dor_intensa" name="sinais[hemato][dor_intensa]">
              <label class="form-check-label" for="he_dor_intensa">Dor intensa / ereção dolorosa / febre ≥ 38ºC / aumento súbito de massa / icterícia com dor</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="he_dor_moderada" name="sinais[hemato][dor_moderada]">
              <label class="form-check-label" for="he_dor_moderada">Icterícia / dor moderada / febre entre 37,5ºC e 38ºC / déficit neurológico novo</label>
            </div>
          </div>

          <!-- BEBE_CHORANDO -->
          <div id="sec_bebe_chorando" class="group-section" style="display:none;">
            <h6>Bebê chorando — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="bc_obstrucao" name="sinais[bebe_chorando][obstrucao]">
              <label class="form-check-label" for="bc_obstrucao">Obstrução de vias aéreas / Respiração irregular / Choque / Criança não reativa</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="bc_laranja" name="sinais[bebe_chorando][postura_hipotonia]">
              <label class="form-check-label" for="bc_laranja">Postura / Hipotonia / Prúrpura / Extensão cutânea fixa</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="bc_amarelo" name="sinais[bebe_chorando][choro_prolongado]">
              <label class="form-check-label" for="bc_amarelo">Choro prolongado / Irritabilidade / História discordante / Dor moderada</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="bc_verde" name="sinais[bebe_chorando][febre_recente]">
              <label class="form-check-label" for="bc_verde">Febre / Comportamento atípico / Evento recente</label>
            </div>
          </div>

          <!-- CONVULSOES -->
          <div id="sec_convulsoes" class="group-section" style="display:none;">
            <h6>Convulsões — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="cv_vermelho" name="sinais[convulsoes][vermelho]">
              <label class="form-check-label" for="cv_vermelho">Obstrução vias aéreas / Respiração irregular / Choque / Hipoglicemia / Criança não reativa</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="cv_laranja" name="sinais[convulsoes][laranja]">
              <label class="form-check-label" for="cv_laranja">Déficit neurológico agudo / Meningismo / Overdose / Erupção cutânea / Criança quente</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="cv_amarelo" name="sinais[convulsoes][amarelo]">
              <label class="form-check-label" for="cv_amarelo">Trauma crânioencefálico / História discordante / História de convulsão</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="cv_verde" name="sinais[convulsoes][verde]">
              <label class="form-check-label" for="cv_verde">Febre / Cefaleia / Dor leve / Criança reativa</label>
            </div>
          </div>

          <!-- CORPO_ESTRANHO -->
          <div id="sec_corpo_estranho" class="group-section" style="display:none;">
            <h6>Corpo estranho — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ce_vermelho" name="sinais[corpo_estranho][vermelho]">
              <label class="form-check-label" for="ce_vermelho">Obstrução vias aéreas / Estridor / Respiração irregular / Hemorragia exsanguinante / Choque</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ce_laranja" name="sinais[corpo_estranho][laranja]">
              <label class="form-check-label" for="ce_laranja">Hemorragia maior / Trauma significativo / Alteração súbita consciência / Trauma ocular / Dor intensa</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ce_amarelo" name="sinais[corpo_estranho][amarelo]">
              <label class="form-check-label" for="ce_amarelo">História discordante / Hemorragia menor / Dor moderada</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ce_verde" name="sinais[corpo_estranho][verde]">
              <label class="form-check-label" for="ce_verde">Inflamação local / Infecção / Dor leve / Evento recente</label>
            </div>
          </div>

          <!-- DESMAIO -->
          <div id="sec_desmaio" class="group-section" style="display:none;">
            <h6>Desmaio — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dm_vermelho" name="sinais[desmaio][vermelho]">
              <label class="form-check-label" for="dm_vermelho">Obstrução vias aéreas / Choque / Hipoglicemia / Convulsão</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dm_laranja" name="sinais[desmaio][laranja]">
              <label class="form-check-label" for="dm_laranja">Dispneia aguda / Saturação baixa / Alteração consciência / Dor cardíaca / Febre</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dm_amarelo" name="sinais[desmaio][amarelo]">
              <label class="form-check-label" for="dm_amarelo">Trauma crânioencefálico / Hiperglicemia / História de convulsão / Dor moderada</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dm_verde" name="sinais[desmaio][verde]">
              <label class="form-check-label" for="dm_verde">Febre / Evento recente</label>
            </div>
          </div>

          <!-- DOR_ABDOMINAL -->
          <div id="sec_dor_abdominal" class="group-section" style="display:none;">
            <h6>Dor abdominal — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="da_vermelho" name="sinais[dor_abdominal][choque]">
              <label class="form-check-label" for="da_vermelho">Sinais de choque</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="da_laranja" name="sinais[dor_abdominal][dor_intensa]">
              <label class="form-check-label" for="da_laranja">Dor intensa / Vômitos incoercíveis / Temperatura ≥ 39,5ºC</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="da_amarelo" name="sinais[dor_abdominal][distensao]">
              <label class="form-check-label" for="da_amarelo">Distensão abdominal / Fezes esbranquiçadas / Dor moderada / Retenção urinária</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="da_verde" name="sinais[dor_abdominal][dor_leve]">
              <label class="form-check-label" for="da_verde">Dor leve / Náuseas / Disúria / Temperatura &lt; 39ºC</label>
            </div>
          </div>

          <!-- DOR_CERVICAL -->
          <div id="sec_dor_cervical" class="group-section" style="display:none;">
            <h6>Dor cervical — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dc_vermelho" name="sinais[dor_cervical][vermelho]">
              <label class="form-check-label" for="dc_vermelho">PCR iminente / Respiração inadequada / Obstrução / Cianose / Choque / Glasgow &lt; 8</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dc_laranja" name="sinais[dor_cervical][laranja]">
              <label class="form-check-label" for="dc_laranja">Meningismo / Déficit agudo / Erupção cutânea / Dor intensa / Saturação ≤ 90 / Febre</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dc_amarelo" name="sinais[dor_cervical][amarelo]">
              <label class="form-check-label" for="dc_amarelo">Trauma direto / Déficit novo / Dor moderada / Febre</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dc_verde" name="sinais[dor_cervical][verde]">
              <label class="form-check-label" for="dc_verde">Dor leve recente</label>
            </div>
          </div>

          <!-- DOR_GARGANTA -->
          <div id="sec_dor_garganta" class="group-section" style="display:none;">
            <h6>Dor de garganta — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dg_vermelho" name="sinais[dor_garganta][vermelho]">
              <label class="form-check-label" for="dg_vermelho">PCR iminente / Respiração inadequada / Obstrução / Cianose / Choque / Glasgow &lt; 8</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dg_laranja" name="sinais[dor_garganta][laranja]">
              <label class="form-check-label" for="dg_laranja">Meningismo / Déficit agudo / Erupção cutânea / Lactente dispneico / Saturação ≤ 92 / Febre</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dg_amarelo" name="sinais[dor_garganta][amarelo]">
              <label class="form-check-label" for="dg_amarelo">Trauma direto / Déficit novo / Dor moderada / Febre</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dg_verde" name="sinais[dor_garganta][verde]">
              <label class="form-check-label" for="dg_verde">Dor leve recente / Criança ativa</label>
            </div>
          </div>

          <!-- DOR_EXTREMIDADES -->
          <div id="sec_dor_extremidades" class="group-section" style="display:none;">
            <h6>Dor em extremidades — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="de_laranja" name="sinais[dor_extremidades][vascular]">
              <label class="form-check-label" for="de_laranja">Comprometimento vascular distal</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="de_amarelo" name="sinais[dor_extremidades][claudicacao]">
              <label class="form-check-label" for="de_amarelo">Dor nas panturrilhas / Artralgia com limitação e sinais flogísticos</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="de_verde" name="sinais[dor_extremidades][trauma_leve]">
              <label class="form-check-label" for="de_verde">Distensão / Contusão / Torção / Artralgia sem sinais flogísticos</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="de_azul" name="sinais[dor_extremidades][dor_cronica]">
              <label class="form-check-label" for="de_azul">Dor superficial crônica com dor à compressão</label>
            </div>
          </div>

          <!-- DOR_LOMBAR -->
          <div id="sec_dor_lombar" class="group-section" style="display:none;">
            <h6>Dor lombar — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dl_vermelho" name="sinais[dor_lombar][vermelho]">
              <label class="form-check-label" for="dl_vermelho">Obstrução vias aéreas / Choque / Respiração inadequada</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dl_laranja" name="sinais[dor_lombar][laranja]">
              <label class="form-check-label" for="dl_laranja">Trauma significativo / Febre / Dor intensa / Incapaz de andar</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dl_amarelo" name="sinais[dor_lombar][amarelo]">
              <label class="form-check-label" for="dl_amarelo">Déficit neurológico / Trauma direto / Cólica / Dor moderada</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dl_verde" name="sinais[dor_lombar][verde]">
              <label class="form-check-label" for="dl_verde">Dor leve recente / Evento recente</label>
            </div>
          </div>

          <!-- DOR_CABECA -->
          <div id="sec_dor_cabeca" class="group-section" style="display:none;">
            <h6>Dor de cabeça — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dcab_vermelho" name="sinais[dor_cabeca][vermelho]">
              <label class="form-check-label" for="dcab_vermelho">Choque / Criança não reativa / Convulsão / Respiração irregular</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dcab_laranja" name="sinais[dor_cabeca][laranja]">
              <label class="form-check-label" for="dcab_laranja">Início abrupto / Dor intensa / Alteração sensorio / Sinais focais / Rigidez nuca / Trauma / Intoxicação / Drogas / Febre</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dcab_amarelo" name="sinais[dor_cabeca][amarelo]">
              <label class="form-check-label" for="dcab_amarelo">Inconsciência / Alterações visuais / Dor moderada / Náuseas / Febre</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dcab_verde" name="sinais[dor_cabeca][verde]">
              <label class="form-check-label" for="dcab_verde">Dor leve / Temperatura entre 37,5°C e 38,4°C</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dcab_azul" name="sinais[dor_cabeca][azul]">
              <label class="form-check-label" for="dcab_azul">Apenas referida / Sem febre / Sinais vitais normais</label>
            </div>
          </div>

          <!-- OUTRO -->
          <div id="sec_outro" class="group-section" style="display:none;">
            <h6>Outro — sinais</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="out_obstr" name="sinais[outro][obstrucao]">
              <label class="form-check-label" for="out_obstr">Obstrução das vias</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="out_resp" name="sinais[outro][respiracao]">
              <label class="form-check-label" for="out_resp">Respiração irregular</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="out_dorint" name="sinais[outro][dor_intensa]">
              <label class="form-check-label" for="out_dorint">Dor intensa</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="out_dormod" name="sinais[outro][dor_moderada]">
              <label class="form-check-label" for="out_dormod">Dor moderada</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="out_def" name="sinais[outro][deformidade]">
              <label class="form-check-label" for="out_def">Deformidade</label>
            </div>
          </div>
          <!-- Fim dos grupos -->

          <div class="mt-3">
            <strong>Classificação sugerida: </strong>
            <span id="badgeRisk" class="badge-risk badge badge-secondary">—</span>
          </div>

        </div>
      </div>

      <!-- campos ocultos que cliente preenche para UX, mas servidor recalcula sempre -->
      <input type="hidden" name="Classificacao_de_Risco" id="Classificacao_de_Risco" value="" />
      <input type="hidden" name="motivos_classificacao" id="motivos_classificacao" value="" />

      <div class="text-right mt-3">
        <button type="submit" class="btn btn-primary">Registar Pré-Triagem</button>
      </div>
    </form>
  </div>

  <footer>© 2024 Filine-ON. Todos os direitos reservados.</footer>

  <script>
    // Show/hide sections
    const sel = document.getElementById('Grupo_Ocorrencia');

    function toggleSections() {
      const g = sel.value;
      document.getElementById('sec_agressao').style.display = (g === 'AGRESSAO') ? 'block' : 'none';
      document.getElementById('sec_alergia').style.display = (g === 'ALERGIA') ? 'block' : 'none';
      document.getElementById('sec_neurologico').style.display = (g === 'NEUROLOGICO') ? 'block' : 'none';
      document.getElementById('sec_cutaneo').style.display = (g === 'CUTANEO') ? 'block' : 'none';
      document.getElementById('sec_hemato').style.display = (g === 'HEMATO') ? 'block' : 'none';
      document.getElementById('sec_bebe_chorando').style.display = (g === 'BEBE_CHORANDO') ? 'block' : 'none';
      document.getElementById('sec_convulsoes').style.display = (g === 'CONVULSOES') ? 'block' : 'none';
      document.getElementById('sec_corpo_estranho').style.display = (g === 'CORPO_ESTRANHO') ? 'block' : 'none';
      document.getElementById('sec_desmaio').style.display = (g === 'DESMAIO') ? 'block' : 'none';
      document.getElementById('sec_dor_abdominal').style.display = (g === 'DOR_ABDOMINAL') ? 'block' : 'none';
      document.getElementById('sec_dor_cervical').style.display = (g === 'DOR_CERVICAL') ? 'block' : 'none';
      document.getElementById('sec_dor_garganta').style.display = (g === 'DOR_GARGANTA') ? 'block' : 'none';
      document.getElementById('sec_dor_extremidades').style.display = (g === 'DOR_EXTREMIDADES') ? 'block' : 'none';
      document.getElementById('sec_dor_lombar').style.display = (g === 'DOR_LOMBAR') ? 'block' : 'none';
      document.getElementById('sec_dor_cabeca').style.display = (g === 'DOR_CABECA') ? 'block' : 'none';
      document.getElementById('sec_outro').style.display = (g === 'OUTRO') ? 'block' : 'none';
      atualizaBadge();
    }
    sel.addEventListener('change', toggleSections);

    // Le todos os sinais e decide classificação cliente (mesma lógica do servidor)
    function calculaClassificacaoCliente() {
      const group = (sel.value || 'OUTRO').toUpperCase();
      // função auxiliar para ler checkboxes de um prefixo
      function chk(prefix, key) {
        const id = prefix + '_' + key;
        const el = document.getElementById(id);
        return el ? el.checked : false;
      }

      let codigo = 'AZUL';
      let motivos = [];

      if (group === 'AGRESSAO') {
        if (chk('ag', 'obstrucao') || chk('ag', 'resp') || chk('ag', 'hem') || chk('ag', 'choque')) {
          codigo = 'VERMELHO';
          if (chk('ag', 'obstrucao')) motivos.push('obstrucao');
          if (chk('ag', 'resp')) motivos.push('respiracao');
          if (chk('ag', 'hem')) motivos.push('hemorragia_grave');
          if (chk('ag', 'choque')) motivos.push('choque');
          return {
            codigo,
            motivos
          };
        }
        if (chk('ag', 'trauma') || chk('ag', 'disp') || chk('ag', 'altc')) {
          codigo = 'LARANJA';
          if (chk('ag', 'trauma')) motivos.push('trauma_sign');
          if (chk('ag', 'disp')) motivos.push('dispneia');
          if (chk('ag', 'altc')) motivos.push('alteracao_consciencia');
          return {
            codigo,
            motivos
          };
        }
        if (chk('ag', 'hem_menor') || chk('ag', 'dor')) {
          codigo = 'AMARELO';
          if (chk('ag', 'hem_menor')) motivos.push('hemorragia_menor');
          if (chk('ag', 'dor')) motivos.push('dor_moderada');
          return {
            codigo,
            motivos
          };
        }
        if (chk('ag', 'def')) {
          return {
            codigo: 'VERDE',
            motivos: ['deformidade']
          };
        }
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_agressao']
        };
      }

      if (group === 'ALERGIA') {
        if (chk('al', 'edema') || chk('al', 'disp') || chk('al', 'choque')) {
          codigo = 'VERMELHO';
          if (chk('al', 'edema')) motivos.push('edema_faciais');
          if (chk('al', 'disp')) motivos.push('dispneia');
          if (chk('al', 'choque')) motivos.push('choque');
          return {
            codigo,
            motivos
          };
        }
        if (chk('al', 'urt') && chk('al', 'pru')) {
          codigo = 'LARANJA';
          if (chk('al', 'urt')) motivos.push('urticaria_generalizada');
          if (chk('al', 'pru')) motivos.push('prurido_intenso');
          return {
            codigo,
            motivos
          };
        }
        if (chk('al', 'loc')) {
          return {
            codigo: 'AMARELO',
            motivos: ['sintomas_locais']
          };
        }
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_alergia']
        };
      }

      if (group === 'NEUROLOGICO') {
        if (chk('ne', 'apneia') || chk('ne', 'choque') || chk('ne', 'hipoglicemia') || chk('ne', 'convulsao') || chk('ne', 'glascow')) {
          codigo = 'VERMELHO';
          if (chk('ne', 'apneia')) motivos.push('apneia');
          if (chk('ne', 'choque')) motivos.push('choque');
          if (chk('ne', 'hipoglicemia')) motivos.push('hipoglicemia');
          if (chk('ne', 'convulsao')) motivos.push('convulsao');
          if (chk('ne', 'glascow')) motivos.push('glascow');
          return {
            codigo,
            motivos
          };
        }
        if (chk('ne', 'deficit') || chk('ne', 'vomitos') || chk('ne', 'consciencia') || chk('ne', 'queda') || chk('ne', 'pos_ictal')) {
          codigo = 'LARANJA';
          if (chk('ne', 'deficit')) motivos.push('deficit_agudo');
          if (chk('ne', 'vomitos')) motivos.push('vomitos_recorrentes');
          if (chk('ne', 'consciencia')) motivos.push('alteracao_consciencia');
          if (chk('ne', 'queda')) motivos.push('risco_queda');
          if (chk('ne', 'pos_ictal')) motivos.push('pos_ictal');
          return {
            codigo,
            motivos
          };
        }
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_neurologico']
        };
      }

      if (group === 'CUTANEO') {
        if (chk('cu', 'resp')) {
          return {
            codigo: 'VERMELHO',
            motivos: ['respiracao_irregular']
          };
        }
        if (chk('cu', 'lesao_grave')) {
          return {
            codigo: 'LARANJA',
            motivos: ['lesao_grave']
          };
        }
        if (chk('cu', 'lesao_moderada')) {
          return {
            codigo: 'AMARELO',
            motivos: ['lesao_moderada']
          };
        }
        if (chk('cu', 'lesao_leve')) {
          return {
            codigo: 'VERDE',
            motivos: ['lesao_leve']
          };
        }
        if (chk('cu', 'prurido')) {
          return {
            codigo: 'AZUL',
            motivos: ['prurido_leve']
          };
        }
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_cutaneo']
        };
      }

      if (group === 'HEMATO') {
        if (chk('he', 'choque')) {
          return {
            codigo: 'VERMELHO',
            motivos: ['choque']
          };
        }
        if (chk('he', 'dor_intensa')) {
          return {
            codigo: 'LARANJA',
            motivos: ['dor_intensa']
          };
        }
        if (chk('he', 'dor_moderada')) {
          return {
            codigo: 'AMARELO',
            motivos: ['dor_moderada']
          };
        }
        return {
          codigo: 'VERDE',
          motivos: ['sem_sinais_hemato']
        };
      }

      if (group === 'BEBE_CHORANDO') {
        if (chk('bc', 'obstrucao')) {
          return {
            codigo: 'VERMELHO',
            motivos: ['obstrucao']
          };
        }
        if (chk('bc', 'laranja')) {
          return {
            codigo: 'LARANJA',
            motivos: ['postura_hipotonia']
          };
        }
        if (chk('bc', 'amarelo')) {
          return {
            codigo: 'AMARELO',
            motivos: ['choro_prolongado']
          };
        }
        if (chk('bc', 'verde')) {
          return {
            codigo: 'VERDE',
            motivos: ['febre_recente']
          };
        }
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_bebe']
        };
      }

      if (group === 'CONVULSOES') {
        if (chk('cv', 'vermelho')) {
          return {
            codigo: 'VERMELHO',
            motivos: ['vermelho']
          };
        }
        if (chk('cv', 'laranja')) {
          return {
            codigo: 'LARANJA',
            motivos: ['laranja']
          };
        }
        if (chk('cv', 'amarelo')) {
          return {
            codigo: 'AMARELO',
            motivos: ['amarelo']
          };
        }
        if (chk('cv', 'verde')) {
          return {
            codigo: 'VERDE',
            motivos: ['verde']
          };
        }
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_convulsao']
        };
      }

      if (group === 'CORPO_ESTRANHO') {
        if (chk('ce', 'vermelho')) return {
          codigo: 'VERMELHO',
          motivos: ['vermelho']
        };
        if (chk('ce', 'laranja')) return {
          codigo: 'LARANJA',
          motivos: ['laranja']
        };
        if (chk('ce', 'amarelo')) return {
          codigo: 'AMARELO',
          motivos: ['amarelo']
        };
        if (chk('ce', 'verde')) return {
          codigo: 'VERDE',
          motivos: ['verde']
        };
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_corpo_estranho']
        };
      }

      if (group === 'DESMAIO') {
        if (chk('dm', 'vermelho')) return {
          codigo: 'VERMELHO',
          motivos: ['vermelho']
        };
        if (chk('dm', 'laranja')) return {
          codigo: 'LARANJA',
          motivos: ['laranja']
        };
        if (chk('dm', 'amarelo')) return {
          codigo: 'AMARELO',
          motivos: ['amarelo']
        };
        if (chk('dm', 'verde')) return {
          codigo: 'VERDE',
          motivos: ['verde']
        };
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_desmaio']
        };
      }

      if (group === 'DOR_ABDOMINAL') {
        if (chk('da', 'vermelho')) return {
          codigo: 'VERMELHO',
          motivos: ['choque']
        };
        if (chk('da', 'laranja')) return {
          codigo: 'LARANJA',
          motivos: ['dor_intensa']
        };
        if (chk('da', 'amarelo')) return {
          codigo: 'AMARELO',
          motivos: ['distensao']
        };
        if (chk('da', 'verde')) return {
          codigo: 'VERDE',
          motivos: ['dor_leve']
        };
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_abdominal']
        };
      }

      if (group === 'DOR_CERVICAL') {
        if (chk('dc', 'vermelho')) return {
          codigo: 'VERMELHO',
          motivos: ['vermelho']
        };
        if (chk('dc', 'laranja')) return {
          codigo: 'LARANJA',
          motivos: ['laranja']
        };
        if (chk('dc', 'amarelo')) return {
          codigo: 'AMARELO',
          motivos: ['amarelo']
        };
        if (chk('dc', 'verde')) return {
          codigo: 'VERDE',
          motivos: ['verde']
        };
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_cervical']
        };
      }

      if (group === 'DOR_GARGANTA') {
        if (chk('dg', 'vermelho')) return {
          codigo: 'VERMELHO',
          motivos: ['vermelho']
        };
        if (chk('dg', 'laranja')) return {
          codigo: 'LARANJA',
          motivos: ['laranja']
        };
        if (chk('dg', 'amarelo')) return {
          codigo: 'AMARELO',
          motivos: ['amarelo']
        };
        if (chk('dg', 'verde')) return {
          codigo: 'VERDE',
          motivos: ['verde']
        };
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_garganta']
        };
      }

      if (group === 'DOR_EXTREMIDADES') {
        if (chk('de', 'laranja')) return {
          codigo: 'LARANJA',
          motivos: ['vascular']
        };
        if (chk('de', 'amarelo')) return {
          codigo: 'AMARELO',
          motivos: ['claudicacao']
        };
        if (chk('de', 'verde')) return {
          codigo: 'VERDE',
          motivos: ['trauma_leve']
        };
        if (chk('de', 'azul')) return {
          codigo: 'AZUL',
          motivos: ['dor_cronica']
        };
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_extremidades']
        };
      }

      if (group === 'DOR_LOMBAR') {
        if (chk('dl', 'vermelho')) return {
          codigo: 'VERMELHO',
          motivos: ['vermelho']
        };
        if (chk('dl', 'laranja')) return {
          codigo: 'LARANJA',
          motivos: ['laranja']
        };
        if (chk('dl', 'amarelo')) return {
          codigo: 'AMARELO',
          motivos: ['amarelo']
        };
        if (chk('dl', 'verde')) return {
          codigo: 'VERDE',
          motivos: ['verde']
        };
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_lombar']
        };
      }

      if (group === 'DOR_CABECA') {
        if (chk('dcab', 'vermelho')) return {
          codigo: 'VERMELHO',
          motivos: ['vermelho']
        };
        if (chk('dcab', 'laranja')) return {
          codigo: 'LARANJA',
          motivos: ['laranja']
        };
        if (chk('dcab', 'amarelo')) return {
          codigo: 'AMARELO',
          motivos: ['amarelo']
        };
        if (chk('dcab', 'verde')) return {
          codigo: 'VERDE',
          motivos: ['verde']
        };
        if (chk('dcab', 'azul')) return {
          codigo: 'AZUL',
          motivos: ['azul']
        };
        return {
          codigo: 'AZUL',
          motivos: ['sem_sinais_cefaleia']
        };
      }
      // OUTRO (genérico)
      if (chk('out', 'obstr') || chk('out', 'resp') || chk('out', 'choque')) {
        codigo = 'VERMELHO';
        if (chk('out', 'obstr')) motivos.push('obstrucao');
        if (chk('out', 'resp')) motivos.push('respiracao');
        if (chk('out', 'choque')) motivos.push('choque');
        return {
          codigo,
          motivos
        };
      }
      if (chk('out', 'dorint') || chk('out', 'dormod')) {
        codigo = 'LARANJA';
        if (chk('out', 'dorint')) motivos.push('dor_intensa');
        if (chk('out', 'dormod')) motivos.push('dor_moderada');
        return {
          codigo,
          motivos
        };
      }
      if (chk('out', 'def')) {
        return {
          codigo: 'VERDE',
          motivos: ['deformidade']
        };
      }
      return {
        codigo: 'AZUL',
        motivos: ['sem_sinais']
      };
    }

    function atualizaBadge() {
      const r = calculaClassificacaoCliente();
      const badge = document.getElementById('badgeRisk');
      const elClass = document.getElementById('Classificacao_de_Risco');
      const elMot = document.getElementById('motivos_classificacao');
      elClass.value = r.codigo;
      elMot.value = r.motivos.join(',');
      badge.textContent = r.codigo;
      badge.className = 'badge-risk badge';
      if (r.codigo === 'VERMELHO') badge.classList.add('badge-danger');
      else if (r.codigo === 'LARANJA') badge.classList.add('badge-warning');
      else if (r.codigo === 'AMARELO') badge.classList.add('badge-warning');
      else if (r.codigo === 'VERDE') badge.classList.add('badge-success');
      else badge.classList.add('badge-primary');
    }

    // atualiza badge quando qualquer checkbox muda
    document.querySelectorAll('input[type=checkbox]').forEach(cb => cb.addEventListener('change', atualizaBadge));

    // quando muda o select, troca secções e recalcula
    sel.addEventListener('change', function() {
      toggleSections();
    });

    // bootstrap validation + garantir preechimento hidden antes do submit
    (function() {
      'use strict';
      window.addEventListener('load', function() {
        document.getElementById('pretriagemForm').addEventListener('submit', function(event) {
          atualizaBadge();
          if (this.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }
          this.classList.add('was-validated');
        }, false);
      }, false);
    })();

    // inicializa
    toggleSections();
  </script>
</body>

</html>