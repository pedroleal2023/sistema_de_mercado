window.onload = function() {
    fetch('http://localhost:3002/salarios', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    })
    .then(response => response.json())
    .then(data => {
      const tbody = document.querySelector('#tabela-salarios tbody');
      data.forEach(funcionario => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${funcionario.nome}</td>
          <td>${funcionario.cargo}</td>
          <td>${funcionario.salario}</td>
        `;
        tbody.appendChild(tr);
      });
    })
    .catch(error => console.error('Erro ao carregar salários:', error));
  };
  