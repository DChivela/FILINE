
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('search_ocorrencia');
  const select = document.getElementById('Grupo_Ocorrencia');
  const sections = document.querySelectorAll('.group-section');

  searchInput.addEventListener('input', function () {
    const term = this.value.toLowerCase();

    // Filtra opções do select
    Array.from(select.options).forEach(option => {
      if (option.value === "") {
        option.style.display = "";
        return;
      }

      const text = option.textContent.toLowerCase();
      option.style.display = text.includes(term) ? "" : "none";
    });

    // Filtra seções e sinais
    sections.forEach(section => {
      const checkboxes = section.querySelectorAll('.form-check');
      let matchFound = false;

      checkboxes.forEach(box => {
        const label = box.querySelector('label');
        const labelText = label.textContent.toLowerCase();

        if (labelText.includes(term)) {
          box.style.display = "block";
          matchFound = true;
        } else {
          box.style.display = "none";
        }
      });

      // Exibe a seção se houver pelo menos um sinal compatível
      section.style.display = matchFound ? "block" : "none";
    });
  });

  // Exibe a seção correspondente ao grupo selecionado
  select.addEventListener('change', function () {
    const selected = this.value.toLowerCase();

    sections.forEach(section => {
      section.style.display = section.id === 'sec_' + selected ? 'block' : 'none';

      // Ao mudar o grupo, reexibe todos os sinais
      if (section.style.display === 'block') {
        section.querySelectorAll('.form-check').forEach(box => {
          box.style.display = "block";
        });
      }
    });
  });
});
