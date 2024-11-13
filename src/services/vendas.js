document.getElementById('venda-form').addEventListener('submit', function(event) {
    event.preventDefault();
  
    const codigo_produto = document.getElementById('codigo_produto').value;
    const quantidade = document.getElementById('quantidade').value;
    const cpf_cliente = document.getElementById('cpf_cliente').value;
    const data_venda = document.getElementById('data_venda').value;
  
    fetch('http://localhost:3002/vendas', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify({ codigo_produto, quantidade, cpf_cliente, data_venda })
    })
    .then(response => response.json())
    .then(data => {
      alert('Venda registrada com sucesso!');
    })
    .catch(error => console.error('Erro ao registrar venda:', error));
  });
  