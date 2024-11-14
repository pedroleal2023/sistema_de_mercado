const express = require('express');
const router = express.Router();
const connection = require('../db');

// Rota de login do funcionário
router.post('/login', (req, res) => {
  const { cpf, senha } = req.body;

  const query = 'SELECT * FROM funcionarios WHERE cpf = ? AND senha = ?';
  
  connection.query(query, [cpf, senha], (err, results) => {
    if (err) {
      console.error('Erro ao realizar login:', err);
      return res.status(500).send('Erro ao realizar login');
    }
    if (results.length === 0) {
      return res.status(401).send('Credenciais inválidas');
    }
    // Remover a senha da resposta por questões de segurança
    const funcionario = results[0];
    delete funcionario.senha;
    res.status(200).json(funcionario);  // Retorna os dados do funcionário, sem a senha
  });
});

// Rota para listar todos os funcionários
router.get('/', (req, res) => {
  const query = 'SELECT * FROM funcionarios';
  
  connection.query(query, (err, results) => {
    if (err) {
      console.error('Erro ao listar funcionários:', err);
      return res.status(500).send('Erro ao listar funcionários');
    }
    res.status(200).json(results);
  });
});

// Rota para atualizar um funcionário (PUT)
router.put('/:cpf', (req, res) => {
  const { nome, senha, permissao, salario, horas_trabalhadas } = req.body;
  const { cpf } = req.params;

  const query = `UPDATE funcionarios SET nome = ?, senha = ?, permissao = ?, salario = ?, horas_trabalhadas = ? WHERE cpf = ?`;

  connection.query(query, [nome, senha, permissao, salario, horas_trabalhadas, cpf], (err, results) => {
    if (err) {
      console.error('Erro ao atualizar funcionário:', err);
      return res.status(500).send('Erro ao atualizar funcionário');
    }
    if (results.affectedRows === 0) {
      return res.status(404).send('Funcionário não encontrado');
    }
    res.status(200).send('Funcionário atualizado com sucesso');
  });
});

// Rota para deletar um funcionário (DELETE)
router.delete('/:cpf', (req, res) => {
  const { cpf } = req.params;

  const query = 'DELETE FROM funcionarios WHERE cpf = ?';

  connection.query(query, [cpf], (err, results) => {
    if (err) {
      console.error('Erro ao deletar funcionário:', err);
      return res.status(500).send('Erro ao deletar funcionário');
    }
    if (results.affectedRows === 0) {
      return res.status(404).send('Funcionário não encontrado');
    }
    res.status(200).send('Funcionário deletado com sucesso');
  });
});

module.exports = router;
