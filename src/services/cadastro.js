document.getElementById('usuario-form').addEventListener('submit', function(event) {
    event.preventDefault();  // Impede o envio padrão do formulário
  
    // Captura os dados do formulário
    const nome = document.getElementById('nome').value;
    const cpf = document.getElementById('cpf').value;
    const senha = document.getElementById('senha').value;
    const permissao = document.getElementById('permissao').value;
  
    // Envia os dados via fetch para a API de cadastro
    fetch('http://localhost:3002/cadastro', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`  // Token de autenticação
      },
      body: JSON.stringify({ nome, cpf, senha, permissao })
    })
    .then(response => {
      if (response.ok) {
        return response.json();
      }
      throw new Error('Erro ao cadastrar usuário');
    })
    .then(data => {
      alert('Usuário cadastrado com sucesso!');
      // Limpar o formulário ou redirecionar para outra página, se necessário
    })
    .catch(error => {
      console.error('Erro:', error);
      alert('Erro ao cadastrar usuário');
    });
  });
  