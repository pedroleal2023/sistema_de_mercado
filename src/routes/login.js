const express = require('express');
const app = express();
const cors = require('cors');

app.use(express.json());  // Para lidar com o JSON enviado no body
app.use(cors());          // Habilita CORS para permitir requisições de diferentes origens

// Dados de exemplo (substitua com seu banco de dados)
const usuarios = [
  { cpf: '12345678901', senha: 'senha123', permissao: 'A' },
  { cpf: '09876543210', senha: 'senha456', permissao: 'G' }
];

app.post('/api/login', (req, res) => {
  const { cpf, senha } = req.body;

  const usuario = usuarios.find(u => u.cpf === cpf && u.senha === senha);

  if (usuario) {
    // Gerar token (isso é apenas um exemplo)
    const token = 'seu_token_aqui';
    res.json({ success: true, permissao: usuario.permissao, token });
  } else {
    res.status(401).json({ success: false });
  }
});

app.listen(3002, () => {
  console.log('Servidor rodando na porta 3002');
});
