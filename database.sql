-- ====================================================
-- SCRIPT DE CRIAÇÃO DO BANCO - ATELIÊ ORÇAMENTOS
-- ====================================================

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS atelie_orcamentos 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Usar o banco criado
USE atelie_orcamentos;

-- ====================================================
-- TABELA: clientes
-- ====================================================
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_telefone (telefone),
    INDEX idx_nome (nome)
) ENGINE=InnoDB;

-- ====================================================
-- TABELA: servicos
-- ====================================================
CREATE TABLE IF NOT EXISTS servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(8,2) NOT NULL,
    categoria VARCHAR(100) DEFAULT 'Costura',
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categoria (categoria),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB;

-- ====================================================
-- TABELA: orcamentos
-- ====================================================
CREATE TABLE IF NOT EXISTS orcamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(50) UNIQUE NOT NULL,
    cliente_id INT NOT NULL,
    data_vencimento DATE NOT NULL,
    status ENUM('pendente', 'aprovado', 'rejeitado', 'em_andamento', 'concluido') DEFAULT 'pendente',
    observacoes TEXT,
    desconto DECIMAL(8,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    INDEX idx_numero (numero),
    INDEX idx_status (status),
    INDEX idx_data_criacao (created_at),
    INDEX idx_cliente_id (cliente_id)
) ENGINE=InnoDB;

-- ====================================================
-- TABELA: itens_orcamento
-- ====================================================
CREATE TABLE IF NOT EXISTS itens_orcamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orcamento_id INT NOT NULL,
    servico_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    preco DECIMAL(8,2) NOT NULL,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (orcamento_id) REFERENCES orcamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE,
    INDEX idx_orcamento_id (orcamento_id),
    INDEX idx_servico_id (servico_id)
) ENGINE=InnoDB;

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
('Troca de Forro', 'Substituição de forro em saias, vestidos e casacos', 45.00, 'Especialidades')
ON DUPLICATE KEY UPDATE
nome = VALUES(nome),
descricao = VALUES(descricao),
preco = VALUES(preco),
categoria = VALUES(categoria);

-- ====================================================
-- INSERIR CLIENTES DE EXEMPLO (OPCIONAL)
-- ====================================================
INSERT IGNORE INTO clientes (nome, telefone, email) VALUES
('Maria Silva', '(11) 99999-1234', 'maria.silva@email.com'),
('João Santos', '(11) 98888-5678', 'joao.santos@email.com'),
('Ana Costa', '(11) 97777-9012', 'ana.costa@email.com');

-- ====================================================
-- VIEWS ÚTEIS
-- ====================================================

-- View para relatório de orçamentos com dados do cliente
CREATE OR REPLACE VIEW vw_orcamentos_completos AS
SELECT 
    o.id,
    o.numero,
    o.data_vencimento,
    o.status,
    o.desconto,
    o.total,
    o.created_at as data_criacao,
    c.nome as cliente_nome,
    c.telefone as cliente_telefone,
    c.email as cliente_email,
    COUNT(i.id) as total_itens,
    SUM(i.quantidade * i.preco) as subtotal
FROM orcamentos o
JOIN clientes c ON o.cliente_id = c.id
LEFT JOIN itens_orcamento i ON o.id = i.orcamento_id
GROUP BY o.id, o.numero, o.data_vencimento, o.status, o.desconto, o.total, o.created_at, c.nome, c.telefone, c.email;

-- View para serviços mais utilizados
CREATE OR REPLACE VIEW vw_servicos_populares AS
SELECT 
    s.id,
    s.nome,
    s.descricao,
    s.preco,
    s.categoria,
    COUNT(i.id) as vezes_usado,
    SUM(i.quantidade) as quantidade_total,
    SUM(i.quantidade * i.preco) as valor_total
FROM servicos s
LEFT JOIN itens_orcamento i ON s.id = i.servico_id
GROUP BY s.id, s.nome, s.descricao, s.preco, s.categoria
ORDER BY vezes_usado DESC;

-- ====================================================
-- FUNÇÕES E PROCEDIMENTOS (MariaDB Compatible)
-- ====================================================

-- Função para calcular total do orçamento
DROP FUNCTION IF EXISTS CalcularTotalOrcamento;
CREATE FUNCTION CalcularTotalOrcamento(p_orcamento_id INT) 
RETURNS DECIMAL(10,2)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE v_subtotal DECIMAL(10,2) DEFAULT 0;
    DECLARE v_desconto DECIMAL(8,2) DEFAULT 0;
    
    -- Calcular subtotal
    SELECT COALESCE(SUM(quantidade * preco), 0) 
    INTO v_subtotal 
    FROM itens_orcamento 
    WHERE orcamento_id = p_orcamento_id;
    
    -- Buscar desconto
    SELECT COALESCE(desconto, 0) 
    INTO v_desconto 
    FROM orcamentos 
    WHERE id = p_orcamento_id;
    
    -- Retornar total calculado
    RETURN GREATEST(0, v_subtotal - v_desconto);
END;

-- ====================================================
-- TRIGGERS (MariaDB Compatible)
-- ====================================================

-- Trigger para recalcular total quando item é inserido
DROP TRIGGER IF EXISTS tr_item_after_insert;
CREATE TRIGGER tr_item_after_insert 
AFTER INSERT ON itens_orcamento
FOR EACH ROW
UPDATE orcamentos 
SET total = (
    SELECT GREATEST(0, COALESCE(SUM(quantidade * preco), 0) - COALESCE(desconto, 0))
    FROM itens_orcamento i, orcamentos o
    WHERE i.orcamento_id = o.id AND o.id = NEW.orcamento_id
)
WHERE id = NEW.orcamento_id;

-- Trigger para recalcular total quando item é atualizado  
DROP TRIGGER IF EXISTS tr_item_after_update;
CREATE TRIGGER tr_item_after_update 
AFTER UPDATE ON itens_orcamento
FOR EACH ROW
UPDATE orcamentos 
SET total = (
    SELECT GREATEST(0, COALESCE(SUM(quantidade * preco), 0) - COALESCE(desconto, 0))
    FROM itens_orcamento i, orcamentos o
    WHERE i.orcamento_id = o.id AND o.id = NEW.orcamento_id
)
WHERE id = NEW.orcamento_id;

-- Trigger para recalcular total quando item é removido
DROP TRIGGER IF EXISTS tr_item_after_delete;
CREATE TRIGGER tr_item_after_delete 
AFTER DELETE ON itens_orcamento
FOR EACH ROW
UPDATE orcamentos 
SET total = (
    SELECT GREATEST(0, COALESCE(SUM(quantidade * preco), 0) - COALESCE(desconto, 0))
    FROM itens_orcamento i, orcamentos o
    WHERE i.orcamento_id = o.id AND o.id = OLD.orcamento_id
)
WHERE id = OLD.orcamento_id;

-- ====================================================
-- ÍNDICES ADICIONAIS PARA PERFORMANCE
-- ====================================================

-- Índices compostos para consultas frequentes
CREATE INDEX idx_orcamentos_status_data ON orcamentos(status, created_at DESC);
CREATE INDEX idx_orcamentos_cliente_data ON orcamentos(cliente_id, created_at DESC);
CREATE INDEX idx_itens_orcamento_servico ON itens_orcamento(orcamento_id, servico_id);

-- ====================================================
-- CONFIGURAÇÕES DE SEGURANÇA (Comentado para XAMPP)
-- ====================================================

-- Usuário específico para produção (descomentado quando necessário)
-- CREATE USER IF NOT EXISTS 'atelie_user'@'localhost' IDENTIFIED BY 'senha_segura_123';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON atelie_orcamentos.* TO 'atelie_user'@'localhost';
-- FLUSH PRIVILEGES;

-- ====================================================
-- VERIFICAÇÃO FINAL
-- ====================================================

-- Mostrar estrutura das tabelas criadas
SHOW TABLES;

-- Contar registros inseridos
SELECT 'Serviços cadastrados:' as info, COUNT(*) as total FROM servicos
UNION ALL
SELECT 'Clientes de exemplo:' as info, COUNT(*) as total FROM clientes;

-- Mensagem de sucesso
SELECT 'Banco de dados criado com sucesso!' as status,
       'Todas as tabelas, views e procedimentos foram configurados.' as detalhes;