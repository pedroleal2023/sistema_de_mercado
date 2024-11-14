const express = require('express');
const router = express.Router();
const connection = require('../db');

// Rota para cadastrar um produto
router.post('/', (req, res) => {
    const { nome, preco, validade, unid_medida, quantidade } = req.body;

    // Ajustando a consulta para refletir a estrutura correta da tabela
    const query = 'INSERT INTO produtos (nome, preco, validade, unid_medida, quantidade) VALUES (?, ?, ?, ?, ?)';

    // Realizando a consulta
    connection.query(query, [nome, preco, validade, unid_medida, quantidade], (err, results) => {
        if (err) {
            console.error('Erro ao cadastrar produto:', err); // Exibe o erro detalhado no console
            res.status(500).send('Erro ao cadastrar produto');
            return;
        }
        res.status(201).send(`Produto ${nome} cadastrado com sucesso!`);
    });
});

module.exports = router;

// Rota para listar todos os produtos
router.get('/', (req, res) => {
  const query = 'SELECT * FROM produtos';  // Seleciona todos os produtos
  
  connection.query(query, (err, results) => {
    if (err) {
      console.error('Erro ao listar produtos:', err);
      return res.status(500).send('Erro ao listar produtos');
    }
    res.status(200).json(results);  // Retorna os produtos no formato JSON
  });
});

// Rota para atualizar um produto
router.put('/:codigo', (req, res) => {
  const { nome, preco, validade, unid_medida, quantidade } = req.body;
  const { codigo } = req.params;

  const query = `
    UPDATE produtos 
    SET nome = ?, preco = ?, validade = ?, unid_medida = ?, quantidade = ?
    WHERE codigo = ?
  `;

  connection.query(query, [nome, preco, validade, unid_medida, quantidade, codigo], (err, results) => {
    if (err) {
      console.error('Erro ao atualizar produto:', err);
      return res.status(500).send('Erro ao atualizar produto');
    }
    if (results.affectedRows === 0) {
      return res.status(404).send('Produto não encontrado');
    }
    res.status(200).send('Produto atualizado com sucesso');
  });
});

// Rota para deletar um produto
router.delete('/:codigo', (req, res) => {
  const { codigo } = req.params;

  const query = 'DELETE FROM produtos WHERE codigo = ?';

  connection.query(query, [codigo], (err, results) => {
    if (err) {
      console.error('Erro ao deletar produto:', err);
      return res.status(500).send('Erro ao deletar produto');
    }
    if (results.affectedRows === 0) {
      return res.status(404).send('Produto não encontrado');
    }
    res.status(200).send('Produto deletado com sucesso');
  });
});

