const express = require('express');
const cors = require('cors');
const app = express();
const port = 3002; // Porta do servidor Express

// Middleware para interpretar JSON
app.use(express.json());

// Habilitar CORS para permitir requisições do frontend
app.use(cors());

// Importa as rotas de produtos e login
const productRoutes = require('./routes/products');
const loginRoutes = require('./routes/login'); // Adicionando a rota de login

// Usando as rotas
app.use('/produtos', productRoutes);
app.use('/login', loginRoutes); // A rota de login será acessada em /login

// Rota de exemplo para checar se o servidor está online
app.get('/', (req, res) => {
  res.send('Olá, bem-vindo ao sistema de mercado!');
});

// Inicia o servidor
app.listen(port, () => {
  console.log(`Servidor rodando na porta http://localhost:${port}`);
});
