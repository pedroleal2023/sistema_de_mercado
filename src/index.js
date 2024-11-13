const express = require('express');
const app = express();
const port = 3002; // Porta do servidor Express

// Middleware para interpretar JSON
app.use(express.json());

// Importa as rotas
const productRoutes = require('./routes/products');
app.use('/produtos', productRoutes);

// Rota de exemplo para checar se o servidor está online
app.get('/', (req, res) => {
  res.send('Olá, bem-vindo ao sistema de mercado!');
});

// Inicia o servidor
app.listen(port, () => {
  console.log(`Servidor rodando na porta http://localhost:${port}`);
});
