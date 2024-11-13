document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();
  
    const cpf = document.getElementById('cpf').value;
    const senha = document.getElementById('senha').value;
  
    fetch('http://localhost:3002/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ cpf, senha })
    })
    .then(response => response.json())
    .then(data => {
      if (data.token) {
        localStorage.setItem('token', data.token);
        window.location.href = 'dashboard.html';  // Redireciona para o dashboard
      } else {
        alert('Login falhou');
      }
    })
    .catch(error => console.error('Erro no login:', error));
  });
  