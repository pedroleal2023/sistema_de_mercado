// routes/login.js
const express = require('express');
const router = express.Router();

// Dados de exemplo (substitua com seu banco de dados)
const usuarios = [
  { cpf: '12345678901', senha: 'senha123', permissao: 'A' },
  { cpf: '09876543210', senha: 'senha456', permissao: 'G' }
];

// Rota de login (sem /api, diretamente /login)
router.post('/', (req, res) => {
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

module.exports = router;
