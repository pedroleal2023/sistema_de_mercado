// Importações necessárias
const express = require('express');
const bcrypt = require('bcrypt');
const connection = require('../db');
const router = express.Router();

// Middleware de autenticação (simples verificação de token)
function verificarAutenticacao(req, res, next) {
  const token = req.headers['authorization'];
  
  if (!token) {
    return res.status(403).json({ message: 'Token não fornecido' });
  }

  // Aqui você pode verificar o token usando uma biblioteca como o `jsonwebtoken`
  // Simulação de verificação de token
  if (token === 'Bearer meu-token-valido') {
    next(); // Token válido, prossegue para a próxima rota
  } else {
    return res.status(401).json({ message: 'Token inválido ou expirado' });
  }
}

// Rota de cadastro de novo usuário (POST)
router.post('/cadastro', async (req, res) => {
  const { cpf, nome, senha, permissao } = req.body;

  // Verificar se o CPF já está cadastrado
  const verificarQuery = 'SELECT * FROM usuarios WHERE cpf = ?';
  connection.query(verificarQuery, [cpf], async (err, results) => {
    if (err) {
      console.error('Erro ao verificar usuário:', err);
      return res.status(500).send('Erro no servidor');
    }
    if (results.length > 0) {
      return res.status(400).send('Usuário já cadastrado');
    }

    // Criptografar a senha antes de armazenar
    const senhaCriptografada = await bcrypt.hash(senha, 10);

    // Inserir o novo usuário na tabela
    const query = 'INSERT INTO usuarios (cpf, nome, senha, permissao) VALUES (?, ?, ?, ?)';
    connection.query(query, [cpf, nome, senhaCriptografada, permissao], (err) => {
      if (err) {
        console.error('Erro ao cadastrar usuário:', err);
        return res.status(500).send('Erro ao cadastrar usuário');
      }
      res.status(201).send('Usuário cadastrado com sucesso!');
    });
  });
});

// Rota para consultar usuário pelo CPF (GET)
router.get('/usuarios/:cpf', (req, res) => {
  const { cpf } = req.params;

  // Consultar o usuário pelo CPF
  const query = 'SELECT * FROM usuarios WHERE cpf = ?';
  connection.query(query, [cpf], (err, results) => {
    if (err) {
      console.error('Erro ao consultar usuário:', err);
      return res.status(500).json({ message: 'Erro no servidor' });
    }
    if (results.length === 0) {
      return res.status(404).json({ message: 'Usuário não encontrado' });
    }
    res.status(200).json(results[0]);  // Retorna os dados do usuário
  });
});

// Rota para listar todos os usuários (GET)
router.get('/usuarios', verificarAutenticacao, (req, res) => {
  const query = 'SELECT * FROM usuarios';
  connection.query(query, (err, results) => {
    if (err) {
      console.error('Erro ao consultar usuários:', err);
      return res.status(500).json({ message: 'Erro no servidor' });
    }
    res.status(200).json(results);  // Retorna todos os usuários cadastrados
  });
});

// Rota para atualizar dados de um usuário (PUT)
router.put('/usuarios/:cpf', verificarAutenticacao, async (req, res) => {
  const { cpf } = req.params;
  const { nome, senha, permissao } = req.body;

  // Verificar se o usuário existe
  const verificarQuery = 'SELECT * FROM usuarios WHERE cpf = ?';
  connection.query(verificarQuery, [cpf], async (err, results) => {
    if (err) {
      console.error('Erro ao verificar usuário:', err);
      return res.status(500).json({ message: 'Erro no servidor' });
    }
    if (results.length === 0) {
      return res.status(404).json({ message: 'Usuário não encontrado' });
    }

    // Criptografar a nova senha, se fornecida
    let senhaCriptografada = senha;
    if (senha) {
      senhaCriptografada = await bcrypt.hash(senha, 10);
    }

    // Atualizar as informações do usuário
    const query = 'UPDATE usuarios SET nome = ?, senha = ?, permissao = ? WHERE cpf = ?';
    connection.query(query, [nome, senhaCriptografada, permissao, cpf], (err) => {
      if (err) {
        console.error('Erro ao atualizar usuário:', err);
        return res.status(500).json({ message: 'Erro ao atualizar usuário' });
      }
      res.status(200).json({ message: 'Usuário atualizado com sucesso!' });
    });
  });
});

// Rota para deletar um usuário (DELETE)
router.delete('/usuarios/:cpf', verificarAutenticacao, (req, res) => {
  const { cpf } = req.params;

  // Verificar se o usuário existe
  const verificarQuery = 'SELECT * FROM usuarios WHERE cpf = ?';
  connection.query(verificarQuery, [cpf], (err, results) => {
    if (err) {
      console.error('Erro ao verificar usuário:', err);
      return res.status(500).json({ message: 'Erro no servidor' });
    }
    if (results.length === 0) {
      return res.status(404).json({ message: 'Usuário não encontrado' });
    }

    // Deletar o usuário
    const query = 'DELETE FROM usuarios WHERE cpf = ?';
    connection.query(query, [cpf], (err) => {
      if (err) {
        console.error('Erro ao deletar usuário:', err);
        return res.status(500).json({ message: 'Erro ao deletar usuário' });
      }
      res.status(200).json({ message: 'Usuário deletado com sucesso!' });
    });
  });
});

module.exports = router;
