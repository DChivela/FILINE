
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
