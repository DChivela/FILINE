
// functions/js/filter_ocorrencias.js
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('search_ocorrencia');
  const clearBtn = document.getElementById('clear_filter');
  const select = document.getElementById('Grupo_Ocorrencia');
  const sections = document.querySelectorAll('.group-section');

  const medicalSynonyms = {
    'respirar': ['respiração', 'dispneia'],
    'sangue': ['hemorragia', 'exsanguinante'],
    'desmaio': ['alteração consciência', 'choque'],
    'dor': ['dor moderada', 'dor abdominal', 'dor cervical', 'dor lombar'],
    'fratura': ['deformidade', 'trauma']
  };

  function expandTerm(term) {
    let terms = [term];
    for (const key in medicalSynonyms) {
      if (term.includes(key)) {
        terms = terms.concat(medicalSynonyms[key]);
      }
    }
    return terms;
  }

  function highlightMatch(text, term) {
    const regex = new RegExp(`(${term})`, 'gi');
    return text.replace(regex, '<mark>$1</mark>');
  }

  function resetLabels() {
    sections.forEach(section => {
      section.querySelectorAll('label').forEach(label => {
        if (label.dataset.originalText) {
          label.innerHTML = label.dataset.originalText;
        }
      });
    });
  }

  searchInput.addEventListener('input', function () {
    const term = this.value.toLowerCase();
    const terms = expandTerm(term);

    // Filtra opções do select
    Array.from(select.options).forEach(option => {
      if (option.value === "") {
        option.style.display = "";
        return;
      }
      const text = option.textContent.toLowerCase();
      option.style.display = terms.some(t => text.includes(t)) ? "" : "none";
    });

    // Filtra seções e sinais
    sections.forEach(section => {
      const checkboxes = section.querySelectorAll('.form-check');
      let matchFound = false;

      checkboxes.forEach(box => {
        const label = box.querySelector('label');
        if (!label.dataset.originalText) {
          label.dataset.originalText = label.textContent;
        }

        const labelText = label.textContent.toLowerCase();
        const match = terms.some(t => labelText.includes(t));

        box.style.display = match ? "block" : "none";
        label.innerHTML = match ? highlightMatch(label.dataset.originalText, term) : label.dataset.originalText;

        if (match) matchFound = true;
      });

      section.style.display = matchFound ? "block" : "none";
    });
  });

  select.addEventListener('change', function () {
    const selected = this.value.toLowerCase();

    sections.forEach(section => {
      const isMatch = section.id === 'sec_' + selected;
      section.style.display = isMatch ? 'block' : 'none';

      if (isMatch) {
        section.querySelectorAll('.form-check').forEach(box => {
          box.style.display = "block";
        });
        resetLabels();
      }
    });
  });

  clearBtn.addEventListener('click', function () {
    searchInput.value = '';
    Array.from(select.options).forEach(option => option.style.display = '');
    sections.forEach(section => {
      section.style.display = 'none';
      section.querySelectorAll('.form-check').forEach(box => {
        box.style.display = 'block';
      });
    });
    resetLabels();
  });

  // Quando um sintoma for clicado, seleciona o grupo correspondente no <select>
document.querySelectorAll('.group-section .form-check-input').forEach(input => {
  input.addEventListener('change', function () {
    if (this.checked) {
      const section = this.closest('.group-section');
      if (!section) return;

      const groupId = section.id.replace('sec_', '').toUpperCase(); // ex: 'agressao' → 'AGRESSAO'

      // Marca o grupo no <select>
      select.value = groupId;

      // Dispara o evento change para exibir a seção correta
      const event = new Event('change');
      select.dispatchEvent(event);
    }
  });
});
});
