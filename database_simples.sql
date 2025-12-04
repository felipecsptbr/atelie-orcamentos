-- ====================================================
-- SCRIPT SIMPLIFICADO PARA MARIADB/XAMPP
-- Sistema de Orçamentos para Ateliê de Costura
-- ====================================================

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS atelie_orcamentos 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Usar o banco criado
USE atelie_orcamentos;

-- ====================================================
-- REMOVER TABELAS EXISTENTES (PARA REINSTALAÇÃO)
-- ====================================================
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS itens_orcamento;
DROP TABLE IF EXISTS orcamentos;
DROP TABLE IF EXISTS servicos;
DROP TABLE IF EXISTS clientes;
SET FOREIGN_KEY_CHECKS = 1;

-- ====================================================
-- TABELA: clientes
-- ====================================================
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- TABELA: servicos
-- ====================================================
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(8,2) NOT NULL,
    categoria VARCHAR(100) DEFAULT 'Costura',
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- TABELA: orcamentos
-- ====================================================
CREATE TABLE orcamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(50) UNIQUE NOT NULL,
    cliente_id INT NOT NULL,
    data_vencimento DATE NOT NULL,
    status ENUM('pendente', 'aprovado', 'rejeitado', 'em_andamento', 'concluido') DEFAULT 'pendente',
    observacoes TEXT,
    desconto DECIMAL(8,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- TABELA: itens_orcamento
-- ====================================================
CREATE TABLE itens_orcamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orcamento_id INT NOT NULL,
    servico_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    preco DECIMAL(8,2) NOT NULL,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (orcamento_id) REFERENCES orcamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- INSERIR SERVIÇOS PADRÃO
-- ====================================================
INSERT INTO servicos (nome, descricao, preco, categoria) VALUES
('Conserto de Calça', 'Ajuste de comprimento, bainha ou reparo geral em calças', 25.00, 'Consertos'),
('Ajuste de Cintura', 'Apertar ou alargar cintura de calças, saias e vestidos', 30.00, 'Ajustes'),
('Bainha de Vestido', 'Ajuste de comprimento de vestidos e saias longas', 35.00, 'Ajustes'),
('Conserto de Zíper', 'Troca ou reparo de zíper em peças diversas', 20.00, 'Consertos'),
('Ajuste de Manga', 'Encurtar ou alargar mangas de camisas, blazers e casacos', 28.00, 'Ajustes'),
('Costura de Rasgo', 'Reparo de rasgos em tecidos diversos', 15.00, 'Consertos'),
('Troca de Botões', 'Substituição de botões em camisas, casacos e vestidos', 12.00, 'Acessórios'),
('Ajuste de Saia', 'Modificação de comprimento ou cintura de saias', 32.00, 'Ajustes'),
('Barra de Calça Jeans', 'Barra especial para calças jeans com acabamento original', 18.00, 'Especialidades'),
('Conserto de Bainha', 'Reparo de bainhas soltas ou danificadas', 22.00, 'Consertos'),
('Ajuste de Decote', 'Modificação de decotes em vestidos e blusas', 25.00, 'Ajustes'),
('Troca de Forro', 'Substituição de forro em saias, vestidos e casacos', 45.00, 'Especialidades');

-- ====================================================
-- INSERIR CLIENTES DE EXEMPLO
-- ====================================================
INSERT INTO clientes (nome, telefone, email) VALUES
('Maria Silva Santos', '(11) 99999-1234', 'maria.silva@email.com'),
('João Pedro Santos', '(11) 98888-5678', 'joao.santos@email.com'),
('Ana Carolina Costa', '(11) 97777-9012', 'ana.costa@email.com'),
('Carlos Roberto Lima', '(11) 96666-3456', 'carlos.lima@email.com'),
('Fernanda Oliveira', '(11) 95555-7890', 'fernanda.oliveira@email.com');

-- ====================================================
-- VIEWS PARA RELATÓRIOS
-- ====================================================

-- View para orçamentos com dados completos
CREATE OR REPLACE VIEW vw_orcamentos_completos AS
SELECT 
    o.id,
    o.numero,
    o.data_vencimento,
    o.status,
    o.desconto,
    o.total,
    o.observacoes,
    o.created_at as data_criacao,
    c.nome as cliente_nome,
    c.telefone as cliente_telefone,
    c.email as cliente_email,
    COUNT(i.id) as total_itens,
    COALESCE(SUM(i.quantidade * i.preco), 0) as subtotal
FROM orcamentos o
JOIN clientes c ON o.cliente_id = c.id
LEFT JOIN itens_orcamento i ON o.id = i.orcamento_id
GROUP BY o.id, o.numero, o.data_vencimento, o.status, o.desconto, o.total, o.observacoes, o.created_at, c.nome, c.telefone, c.email;

-- View para serviços mais utilizados
CREATE OR REPLACE VIEW vw_servicos_populares AS
SELECT 
    s.id,
    s.nome,
    s.descricao,
    s.preco,
    s.categoria,
    COUNT(i.id) as vezes_usado,
    COALESCE(SUM(i.quantidade), 0) as quantidade_total,
    COALESCE(SUM(i.quantidade * i.preco), 0) as valor_total
FROM servicos s
LEFT JOIN itens_orcamento i ON s.id = i.servico_id
GROUP BY s.id, s.nome, s.descricao, s.preco, s.categoria
ORDER BY vezes_usado DESC;

-- ====================================================
-- ÍNDICES PARA MELHOR PERFORMANCE
-- ====================================================
CREATE INDEX idx_clientes_telefone ON clientes(telefone);
CREATE INDEX idx_clientes_nome ON clientes(nome);
CREATE INDEX idx_servicos_categoria ON servicos(categoria);
CREATE INDEX idx_servicos_ativo ON servicos(ativo);
CREATE INDEX idx_orcamentos_numero ON orcamentos(numero);
CREATE INDEX idx_orcamentos_status ON orcamentos(status);
CREATE INDEX idx_orcamentos_data ON orcamentos(created_at DESC);
CREATE INDEX idx_orcamentos_cliente ON orcamentos(cliente_id);
CREATE INDEX idx_itens_orcamento ON itens_orcamento(orcamento_id);
CREATE INDEX idx_itens_servico ON itens_orcamento(servico_id);

-- ====================================================
-- INSERIR ORÇAMENTO DE EXEMPLO (OPCIONAL)
-- ====================================================
INSERT INTO orcamentos (numero, cliente_id, data_vencimento, status, observacoes, desconto, total) 
VALUES ('ORC-20251119-0001', 1, '2025-12-19', 'pendente', 'Orçamento de exemplo para demonstração', 5.00, 50.00);

INSERT INTO itens_orcamento (orcamento_id, servico_id, quantidade, preco, observacoes) VALUES
(1, 1, 1, 25.00, 'Conserto na barra da calça social'),
(1, 2, 1, 30.00, 'Apertar cintura em 3cm');

-- Atualizar total do orçamento de exemplo
UPDATE orcamentos SET total = (
    SELECT GREATEST(0, COALESCE(SUM(quantidade * preco), 0) - COALESCE(desconto, 0))
    FROM itens_orcamento 
    WHERE orcamento_id = 1
) WHERE id = 1;

-- ====================================================
-- VERIFICAÇÃO FINAL
-- ====================================================
SELECT 'Banco criado com sucesso!' as status;
SELECT COUNT(*) as total_servicos FROM servicos;
SELECT COUNT(*) as total_clientes FROM clientes;
SELECT COUNT(*) as total_orcamentos FROM orcamentos;