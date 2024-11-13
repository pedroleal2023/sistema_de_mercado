const mysql = require('mysql2');

// Configuração da conexão com o banco de dados
const connection = mysql.createConnection({
  host: 'localhost',       // Pode usar '127.0.0.1' se houver problemas
  port: 3307,              // Porta correta do MySQL
  user: 'root',            // Seu usuário MySQL
  password: '',            // Sua senha MySQL
  database: 'sistema_mercado' // Nome do banco de dados
});

// Testando a conexão com o banco de dados
connection.connect((err) => {
  if (err) {
    console.error('Erro ao conectar ao banco de dados:', err);
    return;
  }
  console.log('Conectado ao banco de dados!');
});

module.exports = connection;
