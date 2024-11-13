fetch('http://localhost:3002/produtos', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${localStorage.getItem('token')}`
  }
})
.then(response => response.json())
.then(data => {
  const produtosList = document.getElementById('produtos-list');
  data.produtos.forEach(produto => {
    const li = document.createElement('li');
    li.textContent = `${produto.nome} - R$ ${produto.preco} - Quantidade: ${produto.quantidade}`;
    produtosList.appendChild(li);
  });
})
.catch(error => console.error('Erro ao listar produtos:', error));
