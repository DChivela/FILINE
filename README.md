# FILINE
## Descrição
Sistema hospitalar com a finalidade de facilitar ao máximo o processo de triagem de cada paciente antes ou de chegar à unidade hospitalar

### Linguagens e Versões
Sistema WEB feito com as linguagens:
- **PHP** - _(linguagem principal - lógica do sistema)_. Versão: ``8.2.12 (cli) (built: Oct 24 2023 21:15:15) (ZTS Visual C++ 2019 x64)``;
- **CSS** - Estilo e aparência da visualização das páginas;
- **JavaScript** - Funcionalidades interativas;

## Modelo de dados (tabelas)
Parte responsável pela base de dados. Tabelas:
- ``pretriagem``: Tabela principal de pré-triagem () — guarda cada registo da páginal principal.
- ``tb_triagem``;
- ``tb_utilizador``;
- ``tb_tipo_sangue``
- ``enderecos``
- ``classificacoes_urgencia``: Tabela de classificações/urgências (opcional, para referências);
- ``regras_triagem``: Tabela de regras (opcional avançado);


## Como decidir a classificação (condicionais)
### Regras / Condicionais (tradução do fluxograma)
Uma vez que a hierarquia é **VERMELHO** > **LARANJA** > **AMARELO** > **VERDE** > **AZUL**. Avaliar sempre do mais grave para o menos.

#### Simulação das regras com a Ocorrência sendo AGRESSÃO
**VERMELHO** — se qualquer uma for true:

- Obstrução da via aérea

- Respiração irregular / insuficiente

- Hemorragia exsanguinante (sangramento que ameaça vida)

- Choque (hipotensão grave, perfusão periférica comprometida)

**LARANJA** — se VERDE não aplicável e qualquer:

- Mecanismo de trauma significativo

- Dispneia grave (dificuldade respiratória importante)

- Hemorragia major incontrolável (não exsanguinante, mas perigosa)

- Alteração súbita do estado de consciência

- Défice neurológico agudo (AVC suspeito)

**AMARELO** — se não for LARANJA e qualquer:

- Hemorragia menor incontrolável

- História de inconsciência recente

- Novo défice neurológico leve

- História discordante / sinais que aumentam risco

- Dor moderada a intensa (avaliar escala dor)

**VERDE** — problemas menores:

- Deformidade sem comprometimento neurovascular

- Dor leve, evento recente sem sinais de gravidade

- Sintomas que permitem aguardar

**AZUL** — rotina / sem urgência

_Importante:_ a avaliação pode combinar fatores (idade, comorbidades); é importante registar os motivos que determinam a categoria.

## Melhores práticas e UX(User Experience)

- Sempre valida no servidor — o JS é só para UX. A decisão final é feita no servidor.

- Campos específicos para sintomas em vez de um único Tipo_Ocorrencia textual — facilita regras. Usou-se checkboxes / radios para sinais críticos.

- Regista motivos (motivos_classificacao) para auditoria, treinamentos e revisão clínica.

- Permite override manual por um profissional (ex.: enfermeiro pode ajustar classificação se discordar).

- Logs e timestamps — regista quando foi classificado e por quem.

- Notificações / filas — quando VERMELHO ou LARANJA, gera um alerta em tempo real (socket/websocket) ou ecrã de "triagem urgente".

- Segurança: usa prepared statements (mostrei prepare/execute). Sanitiza dados(limpa ou trata dados de entrada) antes de mostrar.

- Acessibilidade: bom contraste, labels, e capacidade de usar teclado.

