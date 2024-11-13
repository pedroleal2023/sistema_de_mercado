const express = require('express');
const jwt = require('jsonwebtoken');  // Certifique-se de importar o jwt
const router = express.Router();
const connection = require('./db');

// Middleware de autenticação
function autenticar(req, res, next) {
  const token = req.headers['authorization']; // Recupera o token do cabeçalho

  if (!token) return res.status(403).send('Token de autenticação não fornecido');

  // Decodificar e verificar o token
  jwt.verify(token, 'seu_segredo', (err, usuario) => {
    if (err) return res.status(403).send('Token inválido');
    req.usuario = usuario; // Armazena as informações do usuário no request
    next();
  });
}

// Rota para listar todas as vendas
router.get('/', (req, res) => {
  const query = 'SELECT * FROM vendas';  // Seleciona todas as vendas
  
  connection.query(query, (err, results) => {
    if (err) {
      console.error('Erro ao listar vendas:', err);
      return res.status(500).send('Erro ao listar vendas');
    }
    res.status(200).json(results);  // Retorna as vendas no formato JSON
  });
});

// Rota para registrar uma venda (POST)
router.post('/', (req, res) => {
  const { codigo, qtde_venda, data_venda, desconto, valor } = req.body;

  if (!codigo || !qtde_venda || !data_venda || !desconto || !valor) {
    return res.status(400).send('Todos os campos são obrigatórios');
  }

  // Inserção no banco com os campos ajustados
  const query = 'INSERT INTO vendas (codigo, qtde_venda, data_venda, desconto, valor) VALUES (?, ?, ?, ?, ?)';

  connection.query(query, [codigo, qtde_venda, data_venda, desconto, valor], (err, results) => {
    if (err) {
      console.error('Erro ao registrar venda:', err);
      return res.status(500).send('Erro ao registrar venda');
    }
    res.status(201).send('Venda registrada com sucesso!');
  });
});

// Rota para atualizar uma venda (PUT)
router.put('/:cod_venda', (req, res) => {
  const { codigo, qtde_venda, data_venda, desconto, valor } = req.body;
  const { cod_venda } = req.params;

  if (!codigo || !qtde_venda || !data_venda || !desconto || !valor) {
    return res.status(400).send('Todos os campos são obrigatórios');
  }

  const query = `UPDATE vendas SET codigo = ?, qtde_venda = ?, data_venda = ?, desconto = ?, valor = ? WHERE cod_venda = ?`;

  connection.query(query, [codigo, qtde_venda, data_venda, desconto, valor, cod_venda], (err, results) => {
    if (err) {
      console.error('Erro ao atualizar venda:', err);
      return res.status(500).send('Erro ao atualizar venda');
    }
    if (results.affectedRows === 0) {
      return res.status(404).send('Venda não encontrada');
    }
    res.status(200).send('Venda atualizada com sucesso');
  });
});

// Rota para deletar uma venda (DELETE)
router.delete('/:cod_venda', (req, res) => {
  const { cod_venda } = req.params;

  const query = 'DELETE FROM vendas WHERE cod_venda = ?';

  connection.query(query, [cod_venda], (err, results) => {
    if (err) {
      console.error('Erro ao deletar venda:', err);
      return res.status(500).send('Erro ao deletar venda');
    }
    if (results.affectedRows === 0) {
      return res.status(404).send('Venda não encontrada');
    }
    res.status(200).send('Venda deletada com sucesso');
  });
});

// Rota para listar todos os usuários (apenas para administradores)
router.get('/usuarios', autenticar, (req, res) => {
  if (req.usuario.permissao !== 'admin') return res.status(403).send('Acesso negado');

  const query = 'SELECT cpf, nome, permissao FROM usuarios';
  connection.query(query, (err, results) => {
    if (err) return res.status(500).send('Erro ao listar usuários');
    res.status(200).json(results);
  });
});

// Rota para registrar um novo usuário (POST)
router.post('/usuarios', (req, res) => {
  const { cpf, nome, senha, permissao } = req.body;

  if (!cpf || !nome || !senha || !permissao) {
    return res.status(400).send('Todos os campos são obrigatórios');
  }

  const query = 'INSERT INTO usuarios (cpf, nome, senha, permissao) VALUES (?, ?, ?, ?)';
  
  connection.query(query, [cpf, nome, senha, permissao], (err, results) => {
    if (err) {
      console.error('Erro ao registrar usuário:', err);
      return res.status(500).send('Erro ao registrar usuário');
    }
    res.status(201).send('Usuário registrado com sucesso!');
  });
});

// Rota para atualizar um usuário existente (PUT)
router.put('/usuarios/:cpf', autenticar, (req, res) => {
  if (req.usuario.permissao !== 'admin') return res.status(403).send('Acesso negado');

  const { cpf } = req.params;
  const { nome, permissao } = req.body;

  const query = 'UPDATE usuarios SET nome = ?, permissao = ? WHERE cpf = ?';
  connection.query(query, [nome, permissao, cpf], (err) => {
    if (err) return res.status(500).send('Erro ao atualizar usuário');
    res.status(200).send('Usuário atualizado com sucesso');
  });
});

// Rota para deletar um usuário (DELETE)
router.delete('/usuarios/:cpf', autenticar, (req, res) => {
  if (req.usuario.permissao !== 'admin') return res.status(403).send('Acesso negado');

  const { cpf } = req.params;
  const query = 'DELETE FROM usuarios WHERE cpf = ?';
  connection.query(query, [cpf], (err) => {
    if (err) return res.status(500).send('Erro ao deletar usuário');
    res.status(200).send('Usuário deletado com sucesso');
  });
});

module.exports = router;
