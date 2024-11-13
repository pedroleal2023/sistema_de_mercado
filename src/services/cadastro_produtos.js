document.getElementById('produto-form').addEventListener('submit', function(event) {
    event.preventDefault();
  
    const nome = document.getElementById('nome').value;
    const preco = document.getElementById('preco').value;
    const quantidade = document.getElementById('quantidade').value;
    const validade = document.getElementById('validade').value;
  
    fetch('http://localhost:3002/produtos', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify({ nome, preco, quantidade, validade })
    })
    .then(response => response.json())
    .then(data => {
      alert('Produto cadastrado com sucesso!');
    })
    .catch(error => console.error('Erro ao cadastrar produto:', error));
  });
  