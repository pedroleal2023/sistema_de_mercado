// Importações necessárias
const express = require('express');
const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const connection = require('../db');
const router = express.Router();

// Middleware de autenticação (verificação de token JWT)
function verificarAutenticacao(req, res, next) {
  const token = req.headers['authorization'];

  if (!token) {
    return res.status(403).json({ message: 'Token não fornecido' });
  }

  const tokenSemBearer = token.split(' ')[1]; // Se o token começar com "Bearer "

  jwt.verify(tokenSemBearer, 'sua-chave-secreta', (err, decoded) => {
    if (err) {
      return res.status(401).json({ message: 'Token inválido ou expirado' });
    }
    req.user = decoded;  // Salva os dados do usuário no `req.user`
    next();
  });
}

// Rota de cadastro de novo usuário (POST)
router.post('/usuarios', async (req, res) => {
  const { cpf, nome, senha, permissao } = req.body;

  // Verificar se o CPF já está cadastrado
  const verificarQuery = 'SELECT COUNT(*) AS count FROM usuarios WHERE cpf = ?';
  connection.query(verificarQuery, [cpf], async (err, results) => {
    if (err) {
      console.error('Erro ao verificar usuário:', err);
      return res.status(500).send({ message: 'Erro no servidor' });
    }
    if (results[0].count > 0) {
      return res.status(400).send({ message: 'Usuário já cadastrado' });
    }

    // Criptografar a senha antes de armazenar
    try {
      const senhaCriptografada = await bcrypt.hash(senha, 10);

      // Inserir o novo usuário na tabela
      const query = 'INSERT INTO usuarios (cpf, nome, senha, permissao) VALUES (?, ?, ?, ?)';
      connection.query(query, [cpf, nome, senhaCriptografada, permissao], (err) => {
        if (err) {
          console.error('Erro ao cadastrar usuário:', err);
          return res.status(500).send({ message: 'Erro ao cadastrar usuário' });
        }
        res.status(201).json({ status: 'success', message: 'Usuário cadastrado com sucesso!' });
      });
    } catch (err) {
      console.error('Erro ao criptografar a senha:', err);
      return res.status(500).send({ message: 'Erro ao criptografar a senha' });
    }
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
    res.status(200).json({ status: 'success', data: results[0] });  // Retorna os dados do usuário
  });
});

// Rota para listar todos os usuários (GET) com paginação
router.get('/usuarios', verificarAutenticacao, (req, res) => {
  const limit = 10;  // Limite de resultados por página
  const page = parseInt(req.query.page) || 1;  // Página solicitada

  const query = 'SELECT * FROM usuarios LIMIT ?, ?';
  connection.query(query, [(page - 1) * limit, limit], (err, results) => {
    if (err) {
      console.error('Erro ao consultar usuários:', err);
      return res.status(500).json({ message: 'Erro no servidor' });
    }
    res.status(200).json({ status: 'success', data: results });  // Retorna todos os usuários cadastrados
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
    let senhaCriptografada = senha || results[0].senha; // Manter a senha existente, se não houver nova senha
    if (senha) {
      try {
        senhaCriptografada = await bcrypt.hash(senha, 10);
      } catch (err) {
        console.error('Erro ao criptografar a senha:', err);
        return res.status(500).send({ message: 'Erro ao criptografar a senha' });
      }
    }

    // Atualizar as informações do usuário
    const query = 'UPDATE usuarios SET nome = ?, senha = ?, permissao = ? WHERE cpf = ?';
    connection.query(query, [nome, senhaCriptografada, permissao, cpf], (err) => {
      if (err) {
        console.error('Erro ao atualizar usuário:', err);
        return res.status(500).json({ message: 'Erro ao atualizar usuário' });
      }
      res.status(200).json({ status: 'success', message: 'Usuário atualizado com sucesso!' });
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

    // Adicional: Verificar se o usuário logado tem permissão para deletar
    if (req.user.permissao !== 'admin' && req.user.cpf !== cpf) {
      return res.status(403).json({ message: 'Você não tem permissão para deletar este usuário' });
    }

    // Deletar o usuário
    const query = 'DELETE FROM usuarios WHERE cpf = ?';
    connection.query(query, [cpf], (err) => {
      if (err) {
        console.error('Erro ao deletar usuário:', err);
        return res.status(500).json({ message: 'Erro ao deletar usuário' });
      }
      res.status(200).json({ status: 'success', message: 'Usuário deletado com sucesso!' });
    });
  });
});

module.exports = router;
