document.getElementById('funcionario-form').addEventListener('submit', function(event) {
    event.preventDefault();
  
    const nome = document.getElementById('nome').value;
    const cpf = document.getElementById('cpf').value;
    const senha = document.getElementById('senha').value;
    const permissao = document.getElementById('permissao').value;
    const salario_220h = document.getElementById('salario_220h').value;
    const horas_trabalhadas = document.getElementById('horas_trabalhadas').value;
  
    fetch('http://localhost:3002/funcionarios', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify({ nome, cpf, senha, permissao, salario_220h, horas_trabalhadas })
    })
    .then(response => response.json())
    .then(data => {
      alert('Funcionário cadastrado com sucesso!');
    })
    .catch(error => console.error('Erro ao cadastrar funcionário:', error));
  });
  